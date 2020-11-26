<?php

namespace Codilar\PushNotification\Model\CommonFactor;

use Codilar\PushNotification\Block\DefaultPushNotification;
use Codilar\PushNotification\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * class Configuration
 *
 * @description Methods for Configurations Status & Send Notification via Api
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Configurations & SendNotification Feature
 */

class Configurations
{
    const XML_PATH_PUSH_NOTIFICATION_GROUP_NEW_ORDER_MODULE_STATUS
        = 'codilar_push_notification_template/group_new_order/enable';
    const XML_PATH_PUSH_NOTIFICATION_GROUP_CANCEL_ORDER_MODULE_STATUS
        = 'codilar_push_notification_template/group_order_cancel/enable';
    const XML_PATH_PUSH_NOTIFICATION_GROUP_ORDER_HOLD_MODULE_STATUS
        = 'codilar_push_notification_template/group_order_hold/enable';
    const XML_PATH_PUSH_NOTIFICATION_GROUP_ORDER_UNHOLD_MODULE_STATUS
        = 'codilar_push_notification_template/group_order_unhold/enable';
    const XML_PATH_PUSH_NOTIFICATION_GROUP_ORDER_SHIPMENT_MODULE_STATUS
        = 'codilar_push_notification_template/group_ship_message/enable';
    const XML_PATH_PUSH_NOTIFICATION_GROUP_INVOICE_MODULE_STATUS
        = 'codilar_push_notification_template/group_invoice/enable';
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Curl
     */
    private $curl;
    /**
     * @var Json
     */
    private $serialize;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Configurations constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serialize
     * @param Logger $logger
     * @param Curl $curl
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $serialize,
        Logger $logger,
        Curl $curl
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curl;
        $this->serialize = $serialize;
        $this->logger = $logger;
    }
    public function isEnable($observerType)
    {
        if ($this->scopeConfig->getValue(
            DefaultPushNotification::XML_PATH_PUSH_NOTIFICATION_MODULE_STATUS
        )) {
            switch ($observerType) {
                case "invoice":
                    return
                        $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_GROUP_INVOICE_MODULE_STATUS);
                case "neworder":
                    return
                    $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_GROUP_NEW_ORDER_MODULE_STATUS);
                case "hold":
                    return
                        $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_GROUP_ORDER_HOLD_MODULE_STATUS);
                case "unhold":
                    return
                        $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_GROUP_ORDER_UNHOLD_MODULE_STATUS);
                case "cancel":
                    return
                        $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_GROUP_CANCEL_ORDER_MODULE_STATUS);
                case "ship":
                    return
                        $this->scopeConfig
                            ->getValue(self::XML_PATH_PUSH_NOTIFICATION_GROUP_ORDER_SHIPMENT_MODULE_STATUS);
            }
        }
    }


    /**
     * @param string $userTokens
     * @param string $title
     * @param string $message
     * @param string $logoUrl
     * @param string $actionUrl
     * @param string $type
     * @return array|bool|float|int|mixed|string|null
     */
    public function sendNotification($userTokens, $title, $message, $logoUrl, $actionUrl, $magentoUrl, $type)
    {
        try {
            $this->curl->addHeader(
                'authorization',
                'Key=' . $this->scopeConfig->getValue(DefaultPushNotification::XML_PATH_PUSH_NOTIFICATION_SERVER_KEY)
            );
            $this->curl->addHeader('content-type', 'application/json');
            $payload = [
                'to' => $userTokens,
                'priority' => 'high',
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                    'click_action' => $actionUrl,
                    'icon'=>$logoUrl,
                    'actions' => [
                        'action'=>$actionUrl,
                        'title'=>$title
                    ]
                ],
                'data'=>[
                    'type' => $type,
                    'magento_customer_url'=> $magentoUrl
                ]
            ];
            $this->curl->post('https://fcm.googleapis.com/fcm/send', $this->serialize->serialize($payload));
            $data = $this->curl->getBody();
            return $this->serialize->unserialize($data);
        } catch (\Exception $exception) {
            $this->logger->info($exception->getMessage());
        }
    }
}
