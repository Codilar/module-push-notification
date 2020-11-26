<?php

namespace Codilar\PushNotification\Model\ResourceModel\OrderTemplate;

use Codilar\PushNotification\Model\OrderTemplate as Model;
use Codilar\PushNotification\Model\ResourceModel\OrderTemplate as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * class OrderToken
 *
 * @description Collection
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Collection for OrderToken
 */

class Collection extends AbstractCollection
{
    protected $_idFieldName = "template_id";
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
