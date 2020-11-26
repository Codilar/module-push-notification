<?php

namespace Codilar\PushNotification\Api;

use Codilar\PushNotification\Model\OrderToken as Model;
use Codilar\PushNotification\Model\ResourceModel\OrderToken\Collection;

/**
 * interface OrderTokenManagementInterface
 *
 * @description OrderTokenManagementInterface
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Interface for OrderTokenManagement
 */
interface OrderTokenManagementInterface
{
    /**
     * @param $id
     * @return Model
     */
    public function getDataBYId($id);

    /**
     * @param Model $model
     * @return Model
     */
    public function save(Model $model);

    /**
     * @param Model $model
     * @return Model
     */
    public function delete(Model $model);

    /**
     * @param $value
     * @param null $field
     * @return Model
     */
    public function load($value, $field = null);

    /**
     * @return Model $model
     */
    public function create();

    /**
     * @param int $id
     * @return Model
     */
    public function deleteById($id);

    /**
     * @return Collection
     */
    public function getCollection();
}
