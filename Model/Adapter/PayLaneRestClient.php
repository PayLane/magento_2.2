<?php

declare(strict_types=1);

/**
 * File: PayLaneRestClient.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Adapter;

use Exception;

/**
 * Class PayLaneRestClient
 * @see http://devzone.paylane.com
 * @package LizardMedia\PayLane\Model\Adapter
 */
class PayLaneRestClient
{
    /**
     * @var string
     */
    protected $api_url = 'https://direct.paylane.com/rest/';

    /**
     * @var null|string
     */
    protected $username = null;

    /**
     * @var null|string
     */
    protected $password = null;

    /**
     * @var array
     */
    protected $http_errors =
    [
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
    ];

    /**
     * @var bool
     */
    protected $is_success = false;

    /**
     * @var array
     */
    protected $allowed_request_methods = [
        'get',
        'put',
        'post',
        'delete',
    ];

    /**
     * @var boolean
     */
    protected $ssl_verify = true;

    /**
     * Constructor
     *
     * @param string $username Username
     * @param string $password Password
     * @throws Exception
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $validate_params =
        [
            false === extension_loaded('curl') => 'The curl extension must be loaded for using this class!',
            false === extension_loaded('json') => 'The json extension must be loaded for using this class!'
        ];
        $this->checkForErrors($validate_params);
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->api_url = $url;
    }

    /**
     * @param bool $ssl_verify
     */
    public function setSSLverify($ssl_verify)
    {
        $this->ssl_verify = $ssl_verify;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->is_success;
    }

    /**
     * @param array $params Sale Params
     * @return array
     * @throws Exception
     */
    public function cardSale($params)
    {
        return $this->call(
            'cards/sale',
            'post',
            $params
        );
    }

    /**
     * @param array $params Sale Params
     * @return array
     * @throws Exception
     */
    public function cardSaleByToken($params)
    {
        return $this->call(
            'cards/saleByToken',
            'post',
            $params
        );
    }

    /**
     * @param array $params Authorization params
     * @return array
     * @throws Exception
     */
    public function cardAuthorization($params)
    {
        return $this->call(
            'cards/authorization',
            'post',
            $params
        );
    }

    /**
     * @param array $params Authorization params
     * @return array
     * @throws Exception
     */
    public function cardAuthorizationByToken($params)
    {
        return $this->call(
            'cards/authorizationByToken',
            'post',
            $params
        );
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function paypalAuthorization($params)
    {
        return $this->call(
            'paypal/authorization',
            'post',
            $params
        );
    }

    /**
     * @param array $params Capture authorization params
     * @return array
     * @throws Exception
     */
    public function captureAuthorization($params)
    {
        return $this->call(
            'authorizations/capture',
            'post',
            $params
        );
    }

    /**
     * @param array $params Close authorization params
     * @return array
     * @throws Exception
     */
    public function closeAuthorization($params)
    {
        return $this->call(
            'authorizations/close',
            'post',
            $params
        );
    }

    /**
     * @param array $params Refund params
     * @return array
     * @throws Exception
     */
    public function refund($params)
    {
        return $this->call(
            'refunds',
            'post',
            $params
        );
    }

    /**
     * @param array $params Get sale info params
     * @return array
     * @throws Exception
     */
    public function getSaleInfo($params)
    {
        return $this->call(
            'sales/info',
            'get',
            $params
        );
    }

    /**
     * @param array $params Get sale authorization info params
     * @return array
     * @throws Exception
     */
    public function getAuthorizationInfo($params)
    {
        return $this->call(
            'authorizations/info',
            'get',
            $params
        );
    }

    /**
     * @param array $params Check sale status
     * @return array
     * @throws Exception
     */
    public function checkSaleStatus($params)
    {
        return $this->call(
            'sales/status',
            'get',
            $params
        );
    }

    /**
     * @param array $params Direct debit params
     * @return array
     * @throws Exception
     */
    public function directDebitSale($params)
    {
        return $this->call(
            'directdebits/sale',
            'post',
            $params
        );
    }

    /**
     * @param array $params Sofort params
     * @return array
     * @throws Exception
     */
    public function sofortSale($params)
    {
        return $this->call(
            'sofort/sale',
            'post',
            $params
        );
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function idealSale($params)
    {
        return $this->call(
            'ideal/sale',
            'post',
            $params
        );
    }

    /**
     * @return array
     * @throws Exception
     */
    public function idealBankCodes()
    {
        return $this->call(
            'ideal/bankcodes',
            'get',
            []
        );
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function bankTransferSale($params)
    {
        return $this->call(
            'banktransfers/sale',
            'post',
            $params
        );
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function paypalSale($params)
    {
        return $this->call(
            'paypal/sale',
            'post',
            $params
        );
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function paypalStopRecurring($params)
    {
        return $this->call(
            'paypal/stopRecurring',
            'post',
            $params
        );
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function resaleBySale($params)
    {
        return $this->call(
            'resales/sale',
            'post',
            $params
        );
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function resaleByAuthorization($params)
    {
        return $this->call(
            'resales/authorization',
            'post',
            $params
        );
    }

    /**
     * Checks if a card is enrolled in the 3D-Secure program.
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function checkCard3DSecure($params)
    {
        return $this->call(
            '3DSecure/checkCard',
            'get',
            $params
        );
    }

    /**
     * Checks if a card is enrolled in the 3D-Secure program, based on the card's token.
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function checkCard3DSecureByToken($params)
    {
        return $this->call(
            '3DSecure/checkCardByToken',
            'get',
            $params
        );
    }

    /**
     * Performs sale by ID 3d secure authorization
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function saleBy3DSecureAuthorization($params)
    {
        return $this->call(
            '3DSecure/authSale',
            'post',
            $params
        );
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function checkCard($params)
    {
        return $this->call(
            'cards/check',
            'get',
            $params
        );
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function checkCardByToken($params)
    {
        return $this->call(
            'cards/checkByToken',
            'get',
            $params
        );
    }

    /**
     * @param  array $params
     * @return array
     * @throws Exception
     */
    public function applePaySale(array $params)
    {
        return $this->call(
            'applepay/sale',
            'post',
            $params
        );
    }

    /**
     * @param  array $params
     * @return array
     * @throws Exception
     */
    public function applePayAuthorization(array $params)
    {
        return $this->call(
            'applepay/authorization',
            'post',
            $params
        );
    }

    /**
     * @param string $method
     * @param string $request
     * @param array $params
     * @return array
     * @throws Exception
     */
    protected function call($method, $request, $params)
    {
        $this->is_success = false;

        if (is_object($params)) {
            $params = (array) $params;
        }

        $validate_params =
        [
            false === is_string($method) => 'Method name must be string',
            false === $this->checkRequestMethod($request) => 'Not allowed request method type',
        ];

        $this->checkForErrors($validate_params);

        $params_encoded = json_encode($params);

        $response = $this->pushData($method, $request, $params_encoded);

        $response = json_decode($response, true);

        if (isset($response['success']) && $response['success'] === true) {
            $this->is_success = true;
        }

        return $response;
    }

    /**
     * Checking error mechanism
     *
     * @param array $validate_params
     * @throws Exception
     */
    protected function checkForErrors($validate_params)
    {
        foreach ($validate_params as $key => $error) {
            if ($key) {
                throw new Exception($error);
            }
        }
    }

    /**
     * Check if method is allowed
     *
     * @param string $method_type
     * @return bool
     */
    protected function checkRequestMethod($method_type)
    {
        $request_method = strtolower($method_type);

        if (in_array($request_method, $this->allowed_request_methods)) {
            return true;
        }

        return false;
    }

    /**
     * Method responsible for pushing data to REST server
     *
     * @param string $method
     * @param string $method_type
     * @param string $request - JSON
     * @return array
     * @throws Exception
     */
    protected function pushData($method, $method_type, $request)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->api_url . $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method_type));
        curl_setopt($ch, CURLOPT_HTTPAUTH, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verify);

        $response = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (isset($this->http_errors[$http_code])) {
            throw new Exception('Response Http Error - ' . $this->http_errors[$http_code]);
        }

        if (0 < curl_errno($ch)) {
            throw new Exception('Unable to connect to ' . $this->api_url . ' Error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }
}
