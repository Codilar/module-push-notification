<?php

namespace Codilar\PushNotification\Model;

use Codilar\PushNotification\Api\OrderTemplateManagementInterface;
use Codilar\PushNotification\Api\TokenManagementInterface;
use Codilar\PushNotification\Model\OrderTemplate as Model;
use Codilar\PushNotification\Model\OrderTemplateFactory as ModelFactory;
use Codilar\PushNotification\Model\ResourceModel\OrderTemplate as ResourceModel;
use Codilar\PushNotification\Model\ResourceModel\OrderTemplate\Collection as Collection;
use Codilar\PushNotification\Model\ResourceModel\OrderTemplate\CollectionFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderTemplateManagement
 *
 * @description Template Management Class gives access to do all CURD Operations on Templates are done
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * all CURD Operations on Relationship Between Tokens & User
 */

class OrderTemplateManagement implements OrderTemplateManagementInterface
{
    /**
     * @var ModelFactory
     */
    private $modelFactory;
    /**
     * @var ResourceModel
     */
    private $resourceModel;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * TokenManagement constructor.
     * @param ModelFactory $modelFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ModelFactory $modelFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function getDataBYId($id)
    {
        return $this->load($id);
    }


    /**
     * @inheritDoc
     * @throws AlreadyExistsException
     */
    public function save(Model $model)
    {
        $this->resourceModel->save($model);
        return $model;
    }

    /**
     * @inheritDoc
     * @throws AlreadyExistsException
     */
    public function afterSave(Model $model)
    {
        $this->resourceModel->afterSave($model);
        return $model;
    }


    /**
     * @inheritDoc
     */
    public function load($value, $field = null)
    {
        $model = $this->create();
        $this->resourceModel->load($model, $value, $field);
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function create()
    {
        return $this->modelFactory->create();
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function delete(Model $model)
    {
        try {
            $this->resourceModel->delete($model);
        } catch (\Exception $exception) {
            return false;
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function deleteById($id)
    {
        $model = $this->load($id);
        return $this->delete($model);
    }
}
