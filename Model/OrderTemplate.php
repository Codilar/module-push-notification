<?php

namespace Codilar\PushNotification\Model;

use Codilar\PushNotification\Model\ResourceModel\OrderTemplate as ResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * class OrderTemplate
 *
 * @description Model
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Model for OrderTemplate
 */

class OrderTemplate extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}