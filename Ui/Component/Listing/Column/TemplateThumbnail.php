<?php


namespace Codilar\PushNotification\Ui\Component\Listing\Column;

use Codilar\PushNotification\Api\OrderTemplateStoreManagementInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * class TemplateThumbnail
 *
 * @description Thumbnail for Template Grid
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Thumbnail for Template Grid
 */

class TemplateThumbnail extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $data;
    /**
     * @var OrderTemplateStoreManagementInterface
     */
    private $orderTemplateStoreManagement;

    /**
     * Thumbnail constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param OrderTemplateStoreManagementInterface $orderTemplateStoreManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        OrderTemplateStoreManagementInterface $orderTemplateStoreManagement,
        array $components = [],
        array $data = []
    ) {

        $this->context = $context;
        $this->uiComponentFactory = $uiComponentFactory;
        $this->components = $components;
        $this->urlBuilder = $urlBuilder;
        $this->data = $data;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->orderTemplateStoreManagement = $orderTemplateStoreManagement;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item['store_id'] = $this->orderTemplateStoreManagement
                                            ->getCollection()
                                            ->addFieldToFilter('template_id', $item["template_id"])
                                            ->getColumnValues('store_id');
                $item[$fieldName . '_src'] = $item['logo'];
                $item[$fieldName . '_alt'] = $item['title'];
                $item[$fieldName . '_orig_src'] = $item['logo'];
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'pushnotify/template/edit',
                    ['template_id' => $item['template_id'], 'store' => $this->context->getRequestParam('store')]
                );
            }
        }
        return $dataSource;
    }
}
