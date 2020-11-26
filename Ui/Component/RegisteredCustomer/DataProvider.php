<?php

namespace Codilar\PushNotification\Ui\Component\RegisteredCustomer;

use Codilar\PushNotification\Model\ResourceModel\PushNotification\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * class DataProvider
 *
 * @description DataProvider for Registered Customers
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * DataProvider for Registered Customers
 */

class DataProvider extends AbstractDataProvider
{
    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collection
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collection,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collection->create();
    }
}
