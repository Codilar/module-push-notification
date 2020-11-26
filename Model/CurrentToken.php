<?php

namespace Codilar\PushNotification\Model;

use Codilar\PushNotification\Api\Data\CurrentTokenInterface;

/**
 * class CurrentToken
 *
 * @description Collection
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Implementing Class for CurrentTokenInterface
 */

class CurrentToken implements CurrentTokenInterface
{
    protected $token;
    protected $_formKey;
    /**
     * @inheritDoc
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @inheritDoc
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function setFormKey($formKey)
    {
        $this->_formKey = $formKey;
    }

    /**
     * @inheritDoc
     */
    public function getFormKey()
    {
        return $this->_formKey;
    }

    /**
     * @inheritDoc
     */
    public function setOldToken($token)
    {
        $this->oldtoken = $token;
    }

    /**
     * @inheritDoc
     */
    public function getOldToken()
    {
        return $this->oldtoken;
    }
}
