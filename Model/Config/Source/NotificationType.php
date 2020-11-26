<?php


namespace Codilar\PushNotification\Model\Config\Source;


use Magento\Framework\Data\OptionSourceInterface;

/**
 * class NotificationType
 *
 * @description Source for Order Attributes
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Source for Notification type
 */

class NotificationType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'notification', 'label' => __('Notification')],
            ['value' => 'popup', 'label' => __('Popup')],
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
