<?php

namespace Codilar\PushNotification\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * class OrderMessage
 *
 * @description Source for Order Attributes
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Source for Order Attributes
 */

class OrderMessage implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Select')],
            ['value' => '{%order.id%}', 'label' => __('Order Id')],
            ['value' => '{%customer.firstname%}', 'label' => __('First Name')],
            ['value' => '{%customer.middlename%}', 'label' => __('Middle Name')],
            ['value' => '{%customer.lastname%}' , 'label' => __('Last Name')],
            ['value' => '{%customer.dob%}' , 'label' => __('Date Of Birth')],
            ['value' => '{%customer.email%}' , 'label' => __('Email')],
            ['value' => '{%order.fullprice%}' , 'label' => __('Price')],
            ['value' => '*%products.names%*' , 'label' => __('All Products Name')],
            ['value' => '*%products.sku(s)%*' , 'label' => __('All Products Sku')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [ 0 => 'Select',
            '{%order.id%}' => 'Order Id',
            '{%customer.firstname%}' => 'First Name',
            '{%customer.middlename%}' => 'Middle Name',
            '{%customer.lastname%}' => 'Last Name',
            '{%customer.dob%}' => 'Date Of Birth',
            '{%customer.email%}' => 'Email',
            '{%order.fullprice%}' => 'Price',
            '*%products.names%*' => 'All Products Name',
            '*%products.sku(s)%*' => 'All Products Sku'
        ];
    }
}
