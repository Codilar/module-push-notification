<?php

namespace Codilar\PushNotification\Model\CommonFactor;

use Codilar\PushNotification\Api\TokenManagementInterface;
use Codilar\PushNotification\Logger\Logger;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Item;

/**
 * class AssignTokenCustomer
 *
 * @description Assign token for a customer
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Update of Token for Customer and Guest Users
 */

class AssignTokenToCustomer
{
    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;
    /**
     * @var TokenManagementInterface
     */
    private $tokenManagement;
    /**
     * @var SessionFactory
     */
    private $sessionFactory;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CreatePost constructor.
     * @param CookieManagerInterface $cookieManager
     * @param TokenManagementInterface $tokenManagement
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param SessionFactory $sessionFactory
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        TokenManagementInterface $tokenManagement,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        SessionFactory $sessionFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->tokenManagement = $tokenManagement;
        $this->sessionFactory = $sessionFactory;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }
    public function assignToken()
    {
        $token = $this->cookieManager->getCookie('tempToken');
        $session = $this->sessionFactory->create();
        $customerId = $session->getCustomerId();
        $model = $this->tokenManagement->load($customerId, 'customer_id');
        if ($model->getUserId()) {
            $model->setCustomerId(null);
            $model->setStatus(0);
            $this->tokenManagement->save($model);
        }
        $model = $this->tokenManagement->load($token, 'token');
        /**
         * @var $session Session
         */

        $model->setCustomerId($customerId);
        $model->setStatus(1);
        $this->tokenManagement->save($model);
    }
    public function getOrderPlacedMessage($order)
    {
        return $this->getOrderMessageByGroupAndField('group_new_order', 'order_new_message_format', $order);
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getOrderCancelMessage($order)
    {
        return $this->getOrderMessageByGroupAndField('group_order_cancel', 'order_cancel_message_format', $order);
    }
    /**
     * @param $order
     * @return mixed
     */
    public function getOrderClosedMessage($order)
    {
        return $this->getOrderMessageByGroupAndField('group_credit', 'credit_order_message_format', $order);
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getOrderHoldMessage($order)
    {
        return $this->getOrderMessageByGroupAndField('group_order_hold', 'order_hold_message_format', $order);
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getOrderShipMessage($order)
    {
        return $this->getOrderMessageByGroupAndField('group_ship_message', 'order_ship_message_format', $order);
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getOrderUnholdMessage($order)
    {
        return $this->getOrderMessageByGroupAndField('group_order_unhold', 'order_unhold_message_format', $order);
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getInvoiceMessage($order)
    {
        return $this->getOrderMessageByGroupAndField('group_invoice', 'invoice_order_message_format', $order);
    }
    public function getOrderMessageByGroupAndField(
        $groupId,
        $messageFieldId,
        $order
    ) {
        try {

            //  $order = $this->orderRepository->get(14);
            $message = $this->scopeConfig
                ->getValue('codilar_push_notification_template/' . $groupId . '/' . $messageFieldId . '');
            if (preg_match_all('~{([^{}]*)}~', $message, $matches)) {
                foreach ($matches[1] as $match) {
                    $messageData = $this->prepareData($match, $order);
                    $message = preg_replace('~{([^{}]*)}~', $messageData, $message, 1);
                }
            }
            if (preg_match_all('~\*([^**]*)\*~', $message, $matches)) {
                foreach ($matches[1] as $match) {
                    $products = $this->getProduct($order, $match);
                    $allProductDetail = implode(', ', $products);
                    $message = preg_replace('~\*([^**]*)\*~', $allProductDetail, $message, 1);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->info($exception->getMessage());
        }

        return $message;
    }
    public function getOrderMessageByOrderTemplate(
        $message,
        $order
    ) {
        try {

            //  $order = $this->orderRepository->get(14);

            if (preg_match_all('~{([^{}]*)}~', $message, $matches)) {
                foreach ($matches[1] as $match) {
                    $messageData = $this->prepareData($match, $order);
                    $message = preg_replace('~{([^{}]*)}~', $messageData, $message, 1);
                }
            }
            if (preg_match_all('~\*([^**]*)\*~', $message, $matches)) {
                foreach ($matches[1] as $match) {
                    $products = $this->getProduct($order, $match);
                    $allProductDetail = implode(', ', $products);
                    $message = preg_replace('~\*([^**]*)\*~', $allProductDetail, $message, 1);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->info($exception->getMessage());
        }

        return $message;
    }
    /**
     * @param $order OrderInterface
     * @param $val
     * @return array
     */
    public function getProduct($order, $val)
    {
        $productName = [];
        foreach ($order->getAllVisibleItems() as $product) {
            $data = $this->prepareData($val, $product);
            $productName[] = $data;
        }
        return $productName;
    }

    /**
     * @param $statement
     * @param $productOrOrder OrderInterface|Item
     * @return mixed
     */
    public function prepareData($statement, $productOrOrder)
    {
        switch ($statement) {
            case '%order.id%':
                return $productOrOrder->getIncrementId();
            case '%customer.firstname%':
                return $productOrOrder->getCustomerFirstname();
            case '%customer.middlename%':
                return $productOrOrder->getCustomerMiddlename();
            case '%customer.lastname%':
                return $productOrOrder->getCustomerLastname();
            case '%customer.dob%':
                return $productOrOrder->getCustomerDob();
            case '%customer.email%':
                return $productOrOrder->getCustomerEmail();
            case '%customer.fullprice%':
                return number_format($productOrOrder->getGrandTotal(), 2);
            case '%products.names%':
                return $productOrOrder->getSku();
            case '%products.sku(s)%':
                return $productOrOrder->getName();
        }
        return null;
    }
}
