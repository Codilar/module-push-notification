<?php

namespace Codilar\PushNotification\Model;

use Codilar\PushNotification\Model\ResourceModel\OrderStore as ResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * class OrderStore
 *
 * @description Model
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Model for OrderStore
 */

class OrderStore extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}