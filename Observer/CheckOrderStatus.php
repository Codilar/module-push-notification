<?php

namespace Codilar\PushNotification\Observer;

use Codilar\PushNotification\Api\OrderTemplateManagementInterface;
use Codilar\PushNotification\Api\OrderTokenManagementInterface;
use Codilar\PushNotification\Api\TokenManagementInterface;
use Codilar\PushNotification\Block\DefaultPushNotification;
use Codilar\PushNotification\Model\CommonFactor\AssignTokenToCustomer;
use Codilar\PushNotification\Model\CommonFactor\Configurations;
use Codilar\PushNotification\Logger\Logger;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * class CheckoutStatus
 *
 * @description CheckoutStatus
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Notify Customer Based on the backend Config. for Respective Order Status
 */

class CheckOrderStatus implements ObserverInterface
{
    const XML_PATH_PUSH_NOTIFICATION_DEFAULT_IMAGE =
        'codilar_push_notification/general/codilar_push_notify_default_image';
    const XML_PATH_PUSH_NOTIFICATION_CANCEL_MESSAGE_ENABLE_STATUS =
        'codilar_push_notification_template/group_order_cancel/enable';
    const XML_PATH_PUSH_NOTIFICATION_HOLD_MESSAGE_ENABLE_STATUS =
        'codilar_push_notification_template/group_order_hold/enable';
    const XML_PATH_PUSH_NOTIFICATION_UNHOLD_MESSAGE_ENABLE_STATUS =
        'codilar_push_notification_template/group_order_unhold/enable';
    const XML_PATH_PUSH_NOTIFICATION_SHIP_MESSAGE_ENABLE_STATUS =
        'codilar_push_notification_template/group_ship_message/enable';
    const XML_PATH_PUSH_NOTIFICATION_INVOICE_MESSAGE_ENABLE_STATUS =
        'codilar_push_notification_template/group_invoice/enable';
    const XML_PATH_PUSH_NOTIFICATION_CREDIT_MEMO_MESSAGE_ENABLE_STATUS =
        'codilar_push_notification_template/group_credit/enable';
    protected $orderRepository;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var AssignTokenToCustomer
     */
    private $assignTokenToCustomer;
    /**
     * @var TokenManagementInterface
     */
    private $tokenManagement;
    /**
     * @var Configurations
     */
    private $configurations;
    /**
     * @var OrderTokenManagementInterface
     */
    private $orderTokenManagement;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var UrlInterface
     */
    private $urlInterface;
    /**
     * @var DefaultPushNotification
     */
    private $defaultPushNotification;
    /**
     * @var OrderTemplateManagementInterface
     */
    private $orderTemplateManagement;

    /**
     * CheckOrderStatus constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param AssignTokenToCustomer $assignTokenToCustomer
     * @param TokenManagementInterface $tokenManagement
     * @param OrderTokenManagementInterface $orderTokenManagement
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Configurations $configurations
     * @param UrlInterface $urlInterface
     * @param DefaultPushNotification $defaultPushNotification
     * @param OrderTemplateManagementInterface $orderTemplateManagement
     * @param Logger $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        AssignTokenToCustomer $assignTokenToCustomer,
        TokenManagementInterface $tokenManagement,
        OrderTokenManagementInterface $orderTokenManagement,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Configurations $configurations,
        UrlInterface $urlInterface,
        DefaultPushNotification $defaultPushNotification,
        OrderTemplateManagementInterface $orderTemplateManagement,
        Logger $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->assignTokenToCustomer = $assignTokenToCustomer;
        $this->tokenManagement = $tokenManagement;
        $this->configurations = $configurations;
        $this->orderTokenManagement = $orderTokenManagement;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->urlInterface = $urlInterface;
        $this->defaultPushNotification = $defaultPushNotification;
        $this->orderTemplateManagement = $orderTemplateManagement;
    }

    /**
     * @param Observer $observer
     * @return CheckOrderStatus
     */
    public function execute(Observer $observer)
    {

        try {
            $order = $observer->getEvent()->getOrder();
            $previousStatus = $order->getOrigData()['status'];
            $customerId = $order->getCustomerId();
            $OrderStatus = $order->getStatus();
            $status = $order->getStatus();
            $orderIncrementId = $order->getIncrementId();
            $shippingMethod = $order->getShippingMethod();
            if ($previousStatus != $status) {
                try {
                    $orderTemplate = $this->orderTemplateManagement->getCollection()->join(
                        ['store_template' => 'codilar_push_notification_order_store'],
                        'main_table.template_id = store_template.template_id &&
            (main_table.status = "'.$status.'") &&
            (store_template.store_id = 0 || store_template.store_id = ' . $order->getStoreId() .')'
                    )->setOrder('store_template.store_id', 'desc')
                        ->getFirstItem()
                        ->getData();
                } catch (\Exception $exception) {
                    $this->logger->info($exception);
                }
                if (isset($orderTemplate['notification_type'])) {
                    $message =
                        $orderTemplate['notification_type']=="popup"?
                            $orderTemplate['wysiwyg_message']:
                            $orderTemplate['message'];
                    $message = $this->assignTokenToCustomer->getOrderMessageByOrderTemplate(
                        $message,
                        $order
                    );

                    try {
                        $defaultImage = $orderTemplate['logo'];
                        $store = $this->storeManager->getStore();
                        $defaultImage = $store->getBaseUrl() .  $defaultImage;
                    } catch (\Exception $exception) {
                        $this->logger->info($exception);
                    }

                    if ($orderTemplate['is_enable']) {

                        $this->notifyToCustomer(
                            $customerId,
                            $message,
                            $defaultImage,
                            $orderTemplate['title'],
                            $orderIncrementId,
                            $orderTemplate['notification_type']
                        );
                    }
                }
                return $this;
            }
        } catch (Exception $exception) {
            return $this;
        }
        return $this;
    }

    /**
     * @param $customerId
     * @param $message
     * @param $orderIncrementId
     */
    public function notifyToCustomer(
        $customerId,
        $message,
        $logo,
        $title,
        $orderIncrementId,
        $type
    ) {
        if ($this->defaultPushNotification->getModuleStatus()) {
            if (isset($customerId)) {
                try {
                    $token = $this->tokenManagement->load($customerId, 'customer_id')->getToken();
                } catch (Exception $exception) {
                    $this->logger->info($exception->getMessage());
                }
            } else {
                $token = $this->orderTokenManagement->load($orderIncrementId, 'order_id')->getToken();
            }
            try {
                $defaultImage = $logo;
                $store = $this->storeManager->getStore();
                $defaultImage = $store->getBaseUrl() . $defaultImage;
                $url = $this->storeManager->getStore()->getBaseUrl() . 'my-account/dashboard/';
                $magentoUrl = $this->storeManager->getStore()->getBaseUrl() . 'customer/account/';
            } catch (\Exception $exception) {
                $this->logger->info($exception);
            }

            if (isset($token)) {
                $response = $this->configurations
                    ->sendNotification(
                        $token,
                        $title,
                        $message,
                        $defaultImage,
                        $url,
                        $magentoUrl,
                        $type
                    );
            }
        }
    }
}
