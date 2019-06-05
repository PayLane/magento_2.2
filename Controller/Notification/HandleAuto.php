<?php

declare(strict_types=1);

/**
 * File: HandleAuto.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Controller\Notification;

use Exception;
use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\NotificationAuthenticationConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\NotificationConfigProviderInterface;
use LizardMedia\PayLane\Model\Notification\Data;
use LizardMedia\PayLane\Model\TransactionHandler;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\CreditmemoService;
use Psr\Log\LoggerInterface;

/**
 * Class HandleAuto
 * @package LizardMedia\PayLane\Controller\Notification
 */
class HandleAuto extends Action
{
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var NotificationAuthenticationConfigProviderInterface
     */
    private $notificationsAuthenticationConfigProvider;

    /**
     * @var NotificationConfigProviderInterface
     */
    private $notificationsConfigProvider;

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var CreditmemoFactory
     */
    private $creditmemoFactory;

    /**
     * @var CreditmemoService
     */
    private $creditmemoService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * HandleAuto constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param NotificationAuthenticationConfigProviderInterface $notificationAuthenticationConfigProvider
     * @param NotificationConfigProviderInterface $notificationConfigProvider
     * @param Context $context
     * @param TransactionHandler $transactionHandler
     * @param Order $order
     * @param TransactionFactory $transactionFactory
     * @param CreditmemoFactory $creditmemoFactory
     * @param CreditmemoService $creditmemoService
     * @param LoggerInterface $logger
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        NotificationAuthenticationConfigProviderInterface $notificationAuthenticationConfigProvider,
        NotificationConfigProviderInterface $notificationConfigProvider,
        Context $context,
        TransactionHandler $transactionHandler,
        Order $order,
        TransactionFactory $transactionFactory,
        CreditmemoFactory $creditmemoFactory,
        CreditmemoService $creditmemoService,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->notificationsConfigProvider = $notificationConfigProvider;
        $this->notificationsAuthenticationConfigProvider = $notificationAuthenticationConfigProvider;
        $this->transactionHandler = $transactionHandler;
        $this->order = $order;
        $this->transactionFactory = $transactionFactory;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->logger = $logger;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        if ($this->isAutoMode()) {
            //@TODO: Move authorization to separate service
            $username = $this->notificationsAuthenticationConfigProvider->getUsername();
            $password = $this->notificationsAuthenticationConfigProvider->getPassword();

            if (!empty($username) && !empty($password)) {
                if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
                    $this->failAuthorization();
                }

                if ($username != $_SERVER['PHP_AUTH_USER'] || $password != $_SERVER['PHP_AUTH_PW']) {
                    $this->failAuthorization();
                }

                $params = $this->getRequest()->getParams();
                
                $this->log(\json_encode($params));

                if (empty($params['communication_id'])) {
                    $message = __('Empty communication id')->getText();
                    $this->log($message);
                    die($message);
                }

                if (!empty($params['token'])
                    && ($this->notificationsConfigProvider->getNotificationToken() !== $params['token'])) {
                    $message = __('Wrong token')->getText();
                    $this->log($message, [$params['token']]);
                    die($message);
                }

                $messages = $params['content'];
            
                $this->handleAutoMessages($messages);

                $this->log($params['communication_id']);
                die($params['communication_id']);
            }
        }
    }

    /**
     * @TODO: Handle responses more gently, get rid of exit(), die()
     * @param $messages
     * @return void
     * @throws Exception
     */
    protected function handleAutoMessages($messages)
    {
        foreach ($messages as $message) {
            if (empty($message['text'])) {
                continue;
            }

            $this->log(
                __(
                    'Handling message with PayLane Sale ID %1 for order #%2',
                    $message['id_sale'],
                    $message['text']
                )->getText()
            );

            $order = $this->order->loadByIncrementId($message['text']);

            if ($order->getId()) {
                switch ($message['type']) {
                    case TransactionHandler::TYPE_SALE:
                        $orderStatus = $this->generalConfigProvider->getClearedOrderStatus();
                        $comment = __('Order status changed via PayLane module');
                        $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
                        $order->save();
                        $this->log(__('Changed order status to: %1', $orderStatus)->getText());
                        break;

                    case TransactionHandler::TYPE_REFUND:
                        try {
                            $this->handleRefund($order);
                            $order->addStatusHistoryComment(__(
                                'Refund was handled via PayLane module | Refund ID: %1',
                                $message['id']
                            ));

                            $order->save();

                            $this->log(
                                __(
                                    'Order #%1 was refunded to amount %2',
                                    $order->getIncrementId(),
                                    $message['amount']
                                )->getText()
                            );
                        } catch (Exception $exception) {
                            $this->log(__('There was an error in refunding.')->getText(), [$exception->getMessage()]);
                        }

                        break;

                    default:
                        $errorMessage = __('Unrecognized message type.')->getText();
                        $this->log($errorMessage, [$message['type']]);
                        die($errorMessage);
                        break;
                }
            }
        }
    }

    /**
     * @TODO: Move logic to separate class
     * @param Order $order
     * @return bool
     * @throws LocalizedException
     */
    protected function handleRefund(Order $order)
    {
        if ($order) {
            $invoice = $this->initInvoice($order);

            if (!$order->canCreditmemo($order)) {
                return false;
            }

            $creditmemo = $this->creditmemoFactory->createByOrder($order);
            $creditmemo->setInvoice($invoice);
            //do offline refund because it is already done on the PayLane side
            $this->creditmemoService->refund($creditmemo, true);
        }
    }

    /**
     * @TODO: Move logic to separate class
     * @param Invoice | bool $order
     * @return Invoice
     */
    protected function initInvoice($order)
    {
        return $order->getInvoiceCollection()->setOrder('updated_at', Collection::SORT_ORDER_DESC)->getFirstItem();
    }

    /**
     * @return bool
     */
    protected function isAutoMode(): bool
    {
        return $this->notificationsConfigProvider->getNotificationHandlingMode() === Data::MODE_AUTO;
    }

    /**
     * @TODO: Move to separate class responsible for authorization
     * @return void
     */
    protected function failAuthorization()
    {
        // authentication failed
        header("WWW-Authenticate: Basic realm=\"Secure Area\"");
        header("HTTP/1.0 401 Unauthorized");
        exit();
    }

    /**
     * @param string $message
     * @param array $params
     * @return void
     */
    protected function log(string $message, array $params = [])
    {
        if ($this->notificationsConfigProvider->isLoggingEnabled()) {
            $this->logger->info($message, $params);
        }
    }
}
