<?php

declare(strict_types=1);

/**
 * File: Redirect.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Controller\Applepay;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Redirect
 * @package LizardMedia\PayLane\Controller\Applepay
 */
class Redirect extends Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * Redirect constructor.
     *
     * @param Context $context
     * @param Session $session
     */
    public function __construct(
        Context $context,
        Session $session
    ) {
        parent::__construct($context);
        $this->session = $session;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $this->session->setLastQuoteId($params['quote_id']);
        $this->session->setLastSuccessQuoteId($params['quote_id']);
        $this->session->setLastOrderId($params['order_id']);
        $this->session->setLastRealOrderId($params['increment_id']);
        $this->session->setLastOrderStatus($params['order_status']);

        $this->_redirect('checkout/onepage/success', [
            '_nosid' => true,
            '_secure' => true
        ]);
    }
}
