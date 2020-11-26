<?php

namespace Codilar\PushNotification\Model\ResourceModel\OrderStore;

use Codilar\PushNotification\Model\OrderStore as Model;
use Codilar\PushNotification\Model\ResourceModel\OrderStore as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * class OrderStore
 *
 * @description Collection
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Collection for OrderStore
 */

class Collection extends AbstractCollection
{
    protected $_idFieldName = "id";
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
