<?php

namespace Codilar\PushNotification\Model\DataProvider;

use Codilar\PushNotification\Api\TemplateManagementInterface;
use Codilar\PushNotification\Model\ResourceModel\NotificationTemplate\CollectionFactory;
use Codilar\PushNotification\Model\ResourceModel\NotificationTemplateFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * class TemplateDataProvider
 *
 * @description Data Provider for Template while editing the form
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Image Thumbnail is Provided
 */

class TemplateDataProvider extends AbstractDataProvider
{
    protected $loadedData;

    private $request;

    /**
     * @var Collection
     */
    private $collectionFactory;
    /**
     * @var NotificationTemplateFactory
     */
    private $resourceFactory;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param NotificationTemplateFactory $resourceFactory
     * @param TemplateManagementInterface $detailsRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        NotificationTemplateFactory $resourceFactory,
        TemplateManagementInterface $detailsRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->resourceFactory = $resourceFactory;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $id = $this->request->getParam('template_id');
        $items = $this->collectionFactory->create()->addFieldToFilter('template_id', $id)->getItems();
        foreach ($items as $item) {
            $templateData = $item->getData();
            $template_img = $templateData['logo'];
            unset($templateData['logo']);
            $templateData['logo'][0]['name'] = $template_img;
            $templateData['logo'][0]['url'] = $template_img;
            $this->loadedData[$item->getId()] = $templateData;
        }
        return $this->loadedData;
    }
}
