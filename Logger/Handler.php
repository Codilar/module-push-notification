<?php

namespace Codilar\PushNotification\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * class Handler
 *
 * @description Handler for push_notification.log
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Handler for \Codilar\PushNotification\Logger\Logger
 */

class Handler extends Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;
    /**
     * @var string
     */
    protected $fileName = '/var/log/push_notify.log';
}
