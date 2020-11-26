<?php

namespace Codilar\PushNotification\Model\ResourceModel\PushNotification;

use Codilar\PushNotification\Model\PushNotification as Model;
use Codilar\PushNotification\Model\ResourceModel\PushNotification as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * class PushNotification
 *
 * @description Collection
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Collection for PushNotification
 */

class Collection extends AbstractCollection
{
    protected $_idFieldName = "user_id";
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['customer_entity' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer_entity.entity_id',
            [
                'customer_entity.firstname',
                'customer_entity.middlename',
                'customer_entity.lastname',
                'customer_entity.email',
                'customer_entity.default_billing',
                'customer_entity.group_id'
            ]
        );
        $this->getSelect()->joinLeft(
            ['customer_address_entity' => $this->getTable('customer_address_entity')],
            'customer_address_entity.entity_id = customer_entity.default_billing',
            ['customer_address_entity.country_id']
        );

        $this->getSelect()->joinLeft(
            ['customer_group' => $this->getTable('customer_group')],
            'customer_group.customer_group_id = customer_entity.group_id',
            '*'
        );
    }
}
