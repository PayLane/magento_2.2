<?php

declare(strict_types=1);

/**
 * File: BackUrlBuilder.php
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
 * Class BackUrlBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class BackUrlBuilder implements BuilderInterface
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
                'paylane/transaction/handle/quote/' . $quote->getId(),
                ['_nosid' => true]
            )
        ];

        return $result;
    }
}
