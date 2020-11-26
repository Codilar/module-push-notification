<?php

namespace Codilar\PushNotification\Api;

use Codilar\PushNotification\Model\PushNotification as Model;

/**
 * interface TokenManagementInterface
 *
 * @description TokenManagementInterface
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * all CURD Operations on Relationship Between Tokens & User
 */
interface TokenManagementInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function getDataBYId($id);

    /**
     * @param $token
     * @return mixed
     */
    public function getDataByToken($token);

    /**
     * @param Model $model
     * @return mixed
     */
    public function save(Model $model);

    /**
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model);

    /**
     * @param $value
     * @param null $field
     * @return mixed
     */
    public function load($value, $field = null);

    /**
     * @return Model $model
     */
    public function create();

    /**
     * @param int $id
     * @return mixed
     */
    public function deleteById($id);

    /**
     * @return mixed
     */
    public function getCollection();


    /**
     * @param string $userTokens
     * @param string $title
     * @param string $message
     * @param string $logoUrl
     * @param string $actionUrl
     * @param string $type
     * @return mixed
     */
    public function sendNotification($userTokens, $title, $message, $logoUrl, $actionUrl, $type);
}
