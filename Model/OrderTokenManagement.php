<?php

namespace Codilar\PushNotification\Model;

use Codilar\PushNotification\Api\OrderTokenManagementInterface;
use Codilar\PushNotification\Model\OrderToken as Model;
use Codilar\PushNotification\Model\OrderTokenFactory as ModelFactory;
use Codilar\PushNotification\Model\ResourceModel\OrderToken as ResourceModel;
use Codilar\PushNotification\Model\ResourceModel\OrderToken\Collection as Collection;
use Codilar\PushNotification\Model\ResourceModel\OrderToken\CollectionFactory;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class OrderTokenManagement
 *
 * @description Template Management Class gives access to do all CURD Operations on Templates are done
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * all CURD Operations on Relationship Between Tokens & User
 */

class OrderTokenManagement implements OrderTokenManagementInterface
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
     * OrderToken constructor.
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
     */
    public function deleteById($id)
    {
        $model = $this->load($id);
        return $this->delete($model);
    }
}
