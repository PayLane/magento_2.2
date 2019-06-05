<?php

declare(strict_types=1);

/**
 * File: GetData.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Controller\Ideal;

use LizardMedia\PayLane\Api\Adapter\PayLaneRestClientFactoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class GetData
 * @package LizardMedia\PayLane\Controller\Ideal
 */
class GetData extends Action
{
    /**
     * @var PayLaneRestClientFactoryInterface
     */
    private $payLaneRestClientFactory;

    /**
     * GetData constructor.
     *
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param Context $context
     */
    public function __construct(
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        Context $context
    ) {
        parent::__construct($context);
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $data = [];

        $client = $this->payLaneRestClientFactory->create();

        $result = $client->idealBankCodes();

        if ($result['success']) {
            foreach ($result['data'] as $bank) {
                $data[] = [
                    'value' => $bank['bank_code'],
                    'label' => $bank['bank_name']
                ];
            }
        } else {
            $data[] = [
                'value' => 'INGBNL2A',
                'label' => 'Test bank'
            ];
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }
}
