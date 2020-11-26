<?php

namespace Codilar\PushNotification\Api;

use Codilar\PushNotification\Api\Data\CurrentTokenInterface;

/**
 * interface UserManagementInterface
 *
 * @description UserManagementInterface
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Api for Removing and Inserting Token to DB
 */

interface UserManagementInterface
{
    /**
     * @param CurrentTokenInterface[] $currentToken
     * @return array
     */
    public function setUser($currentToken);

    /**
     * @param CurrentTokenInterface[] $currentToken
     * @return array
     */
    public function removeToken($currentToken);
}
