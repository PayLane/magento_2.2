<?php

declare(strict_types=1);

/**
 * File: Ds3SecureBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Ds3SecureBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class Ds3SecureBuilder implements BuilderInterface
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $result = [
            'back_url' => $this->_urlBuilder->getUrl(
                'paylane/creditcard/handle3ds/quote/' . $quote->getId(),
                ['_nosid' => true]
            )
        ];

        return $result;
    }
}
