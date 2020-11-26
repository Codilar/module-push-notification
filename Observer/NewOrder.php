<?php

namespace Codilar\PushNotification\Observer;

use Codilar\PushNotification\Api\OrderTemplateManagementInterface;
use Codilar\PushNotification\Api\OrderTemplateStoreManagementInterface;
use Codilar\PushNotification\Api\OrderTokenManagementInterface;
use Codilar\PushNotification\Api\TokenManagementInterface;
use Codilar\PushNotification\Block\DefaultPushNotification;
use Codilar\PushNotification\Model\CommonFactor\AssignTokenToCustomer;
use Codilar\PushNotification\Model\CommonFactor\Configurations;
use Codilar\PushNotification\Logger\Logger;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * class NewOrder
 *
 * @description NewOrder
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Data Manipulation at the time of Order Place & Saving Guest User Data from Order
 */

class NewOrder implements ObserverInterface
{
    const XML_PATH_PUSH_NOTIFICATION_NEW_MESSAGE_ENABLE_STATUS =
        'codilar_push_notification_template/group_new_order/enable';
    const NOTIFICATION_TITLE = 'Order Status';
    /**
     * @var $assignTokenToCustomer AssignTokenToCustomer
     */
    private $assignTokenToCustomer;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Configurations
     */
    private $configurations;
    /**
     * @var Order
     */
    private $order;
    /**
     * @var TokenManagementInterface
     */
    private $tokenManagement;
    /**
     * @var SessionFactory
     */
    private $sessionFactory;
    /**
     * @var OrderTokenManagementInterface
     */
    private $orderTokenManagement;
    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var OrderTemplateStoreManagementInterface
     */
    private $orderTemplateStoreManagement;
    /**
     * @var OrderTemplateStoreManagementInterface
     */
    private $orderTemplateManagement;

    /**
     * Checkout constructor.
     * @param AssignTokenToCustomer $assignTokenToCustomer
     * @param Configurations $configurations
     * @param OrderFactory $order
     * @param CookieManagerInterface $cookieManager
     * @param TokenManagementInterface $tokenManagement
     * @param SessionFactory $sessionFactory
     * @param OrderTokenManagementInterface $orderTokenManagement
     * @param OrderTemplateStoreManagementInterface $orderTemplateManagement
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     * @param Logger $logger
     */
    public function __construct(
        AssignTokenToCustomer $assignTokenToCustomer,
        Configurations $configurations,
        OrderFactory $order,
        CookieManagerInterface $cookieManager,
        TokenManagementInterface $tokenManagement,
        SessionFactory $sessionFactory,
        OrderTokenManagementInterface $orderTokenManagement,
        OrderTemplateManagementInterface $orderTemplateManagement,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Http $request,
        Logger $logger
    ) {
        $this->assignTokenToCustomer = $assignTokenToCustomer;
        $this->logger = $logger;
        $this->configurations = $configurations;
        $this->order = $order;
        $this->tokenManagement = $tokenManagement;
        $this->sessionFactory = $sessionFactory;
        $this->orderTokenManagement = $orderTokenManagement;
        $this->cookieManager = $cookieManager;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->orderTemplateManagement = $orderTemplateManagement;
    }

    /**
     * @param Observer $observer
     * @return NewOrder
     */
    public function execute(Observer $observer)
    {

        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        $previousStatus = $order->getOrigData()['status'];
        $orderId = $order->getIncrementId();
        $tokenFromCookie = $this->cookieManager->getCookie('oldFirebaseMessagingToken');
        $pwaFirebaseTokenFromCookie = $this->cookieManager->getCookie('pwa.oldFirebaseMessagingToken');
        if ($tokenFromCookie) {
            $this->pushNotifyByOrder($orderId, $tokenFromCookie);
        }
        return $this;
    }

    /**
     * @param Order $order
     * @param string $orderId
     */
    public function pushNotifyByOrder($orderId, $tokenFromCookie)
    {
        $order = $this->order->create();
        $order = $order->loadByIncrementId($orderId);
        $status = $order->getStatus();
        $result = [];
        $orderTemplate = $this->orderTemplateManagement->getCollection()->join(
            ['store_template' => 'codilar_push_notification_order_store'],
            'main_table.template_id = store_template.template_id &&
            (main_table.status = "'.$status.'") &&
            (store_template.store_id = 0 || store_template.store_id = ' . $order->getStoreId() .')'
        )->setOrder('store_template.store_id', 'desc')
            ->getFirstItem()
            ->getData();
        if (isset($orderTemplate['notification_type'])) {
            $message =
                $orderTemplate['notification_type']=="popup"?
                    $orderTemplate['wysiwyg_message']:
                    $orderTemplate['message'];
            $NotificationType = $orderTemplate['notification_type'];

            $message = $this->assignTokenToCustomer->getOrderMessageByOrderTemplate(
                $message,
                $order
            );

            if (empty($order->getCustomerId())) {
                $model = $this->orderTokenManagement->load($order->getIncrementId(), 'order_id');
                $model->setOrderId($order->getIncrementId());
                $model->setEmail($order->getCustomerEmail());
                $token = $tokenFromCookie;
                $model->setToken($token);
                $this->orderTokenManagement->save($model);
                $collections = $this->orderTokenManagement->getCollection();
                $collections->addFieldToFilter('email', $order->getCustomerEmail());
                $ids = $collections->getAllIds();
                foreach ($ids as $id) {
                    $model = $this->orderTokenManagement->load($id);
                    $model->setToken($token);
                    $this->orderTokenManagement->save($model);
                }
                $result = [
                    "message" => "token has been registered for the order with " . $orderId. " successfully ",
                    "status" => true,
                    "code" => 200
                ];
            } else {
                try {
                    $token = $this->tokenManagement->load($order->getCustomerId(), 'customer_id')->getToken();
                    $result = [
                        "message" => "token has been registered for the order with " . $orderId. " successfully ",
                        "status" => true,
                        "code" => 200
                    ];
                } catch (\Exception $exception) {
                    $this->logger->info($exception->getMessage());
                }
            }
            try {
                $defaultImage = $orderTemplate['logo'];
                $store = $this->storeManager->getStore();
                $defaultImage = $store->getBaseUrl() .  $defaultImage;

                $url = $this->storeManager->getStore()->getBaseUrl() . 'my-account/dashboard/';
                $magentoUrl =  $this->storeManager->getStore()->getBaseUrl() .'customer/account/';
            } catch (\Exception $exception) {
                $this->logger->info($exception);
            }

            if ($orderTemplate['is_enable']) {

                if (isset($token)) {
                    $data = $this->configurations
                        ->sendNotification(
                            $token,
                            $orderTemplate['title'],
                            $message,
                            $defaultImage,
                            $url,
                            $magentoUrl,
                            $NotificationType
                        );
                }
            }
        }
        return $result;
    }
}
