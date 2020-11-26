<?php

namespace Codilar\PushNotification\Model\ResourceModel\NotificationTemplate;

use Codilar\PushNotification\Model\NotificationTemplate as Model;
use Codilar\PushNotification\Model\ResourceModel\NotificationTemplate as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * class Collection
 *
 * @description Collection
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Collection for Template
 */

class Collection extends AbstractCollection
{
    protected $_idFieldName = "template_id";
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
