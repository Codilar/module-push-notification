<?php

namespace Codilar\PushNotification\Api\Data;

/**
 * interface CurrentTokenInterface
 *
 * @description CurrentTokenInterface
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Interface for CurrentToken Model
 */

interface CurrentTokenInterface
{
    /**
     * Gets the title.
     *
     * @param $token
     * @return void
     * @api
     */
    public function setToken($token);
    /**
     * Gets the title.
     *
     * @api
     * @return string
     */
    public function getToken();
    /**
     * Gets the title.
     *
     * @param $token
     * @return void
     * @api
     */
    public function setOldToken($token);
    /**
     * Gets the title.
     *
     * @api
     * @return string
     */
    public function getOldToken();

    /**
     * Gets the title.
     *
     * @param $formKey
     * @return void
     * @api
     */
    public function setFormKey($formKey);

    /**
     * Gets the title.
     *
     * @api
     * @return string
     */
    public function getFormKey();
}
