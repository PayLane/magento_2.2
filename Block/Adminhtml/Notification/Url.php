<?php

declare(strict_types=1);

/**
 * File: Url.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Block\Adminhtml\Notification;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;

/**
 * Class Url
 * @package LizardMedia\PayLane\Block\Adminhtml\Notification
 */
class Url extends Field
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * Url constructor.
     *
     * @param Context $context
     * @param UrlInterface $url
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->url = $url;
    }

   /**
    * @param AbstractElement $element
    * @return string
    */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $html = sprintf(
            '<input type="text" value ="%s" class="input-text" readonly>',
            $this->buildHandleAutoUrl()
        );
 
        return $html;
    }

    /**
     * @return string
     */
    private function buildHandleAutoUrl(): string
    {
        return $this->url->getUrl('paylane/notification/handleAuto', ['_nosid' => true]);
    }
}
