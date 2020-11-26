<?php

namespace Codilar\PushNotification\Ui\DataProvider;

use Codilar\PushNotification\Api\TemplateManagementInterface;
use Magento\Framework\UrlInterface;

/**
 * class DataProvider
 *
 * @description DataProvider for TemplateActions
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * DataProvider for TemplateActions
 */

class TemplateActions
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var TemplateManagementInterface
     */
    private $templateManagement;

    /**
     * UserActions constructor.
     * @param TemplateManagementInterface $templateManagement
     * @param UrlInterface $urlBuilder
     */
    public function __construct(TemplateManagementInterface $templateManagement, UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
        $this->templateManagement = $templateManagement;
    }

    /**
      * get actions
      *
      * @return array
      */
    public function getActions()
    {
        $actions = [];
        $collections = $this->templateManagement->getCollection()
            ->addFieldToFilter('is_enable', 1);
        foreach ($collections as $collection) {
            $actions[] = [
                'type' => $collection->getTitle(),
                'label' =>  $collection->getTitle() . ' (' . $collection->getNotificationType() . ')',
                'url' => $this->urlBuilder->getUrl(
                    '*/*/sendNotification',
                    ['template_id' => $collection->getTemplateId()]
                ),
                'confirm'=> [
                                'message'
                                    =>__(
                                        'Are you sure you want to send ' .
                                        $collection->getTitle() .
                                        ' notification to selected items ?'
                                    ),
                                'title'=>__('Notify Customer via FCM Push Notification')]
            ];
        }
        return $actions;
    }
}
