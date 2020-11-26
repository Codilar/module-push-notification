<?php

namespace Codilar\PushNotification\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

/**
 * class OrderStatus
 *
 * @description Source for Order Status
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Source for Order Status
 */

class OrderStatus implements OptionSourceInterface
{
    /**
     * @var CollectionFactory $statusCollectionFactory
     */
    protected $orderStatusCollectionFactory;

    /**
     * @param CollectionFactory $orderStatusCollectionFactory
     */
    public function __construct(
        CollectionFactory $orderStatusCollectionFactory
    ) {
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
    }

    /**
     * Get order status options
     *
     * @return array
     */
    public function getOrderStatusOptions()
    {
        $options = $this->orderStatusCollectionFactory->create()->toOptionArray();
        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->getOrderStatusOptions();
        return $options;
    }

}
