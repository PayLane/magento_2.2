<?php

declare(strict_types=1);

/**
 * File: AbstractAdapter.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Payment\Adapter;

use LizardMedia\PayLane\Api\Adapter\PayLaneRestClientFactoryInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Quote\Model\Quote;

/**
 * Class AbstractAdapter
 * @package LizardMedia\PayLane\Model\Payment\Adapter
 */
abstract class AbstractAdapter
{
    /**
     * @var PayLaneRestClientFactoryInterface
     */
    protected $payLaneRestClientFactory;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * Constructor
     *
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param RedirectInterface $redirect
     */
    public function __construct(
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        RedirectInterface $redirect
    ) {
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
        $this->redirect = $redirect;
    }

    /**
     * @param Quote $quote
     * @return void
     */
    public function setQuote(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return mixed
     */
    public function handleRequest()
    {
        $requestData = $this->buildRequest();
        $responseData = $this->makeRequest($requestData);

        return $responseData;
    }

    /**
     * @return array
     */
    public function getAdditionalFields()
    {
        return [];
    }

    /**
     * @param bool $success
     * @param ResponseInterface $response
     * @return void
     */
    protected function handleRedirect(bool $success, ResponseInterface $response)
    {
        if ($success) {
            $this->redirect->redirect($response, 'checkout/onepage/success', [
                '_nosid' => true,
                '_secure' => true
            ]);
        } else {
            $this->redirect->redirect($response, 'checkout/onepage/failure', [
                '_nosid' => true,
                '_secure' => true
            ]);
        }
    }

    /**
     * @return mixed
     */
    abstract protected function buildRequest();

    /**
     * @param array $requestData
     * @return mixed
     */
    abstract protected function makeRequest(array $requestData);

    /**
     * @param array $responseData
     * @param ResponseInterface $response
     * @return mixed
     */
    abstract public function handleResponse(array $responseData, ResponseInterface $response);
}
