<?php

namespace Codilar\PushNotification\Controller\Adminhtml\Order;

use Codilar\PushNotification\Api\OrderTemplateManagementInterface;
use Codilar\PushNotification\Api\OrderTemplateStoreManagementInterface;
use Magento\Backend\App\Action;

/**
 * class Save
 *
 * @description Save Template
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Controller for Save Template
 */

class Save extends Action
{
    const ADMIN_RESOURCE = "Codilar_PushNotification::pushnotify_order_template";

    /**
     * @var OrderTemplateManagementInterface
     */
    private $templateManagement;
    /**
     * @var OrderTemplateStoreManagementInterface
     */
    private $orderTemplateStoreManagement;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param OrderTemplateManagementInterface $templateManagement
     * @param OrderTemplateStoreManagementInterface $orderTemplateStoreManagement
     */
    public function __construct(
        Action\Context $context,
        OrderTemplateManagementInterface $templateManagement,
        OrderTemplateStoreManagementInterface $orderTemplateStoreManagement
    ) {
        parent::__construct($context);
        $this->templateManagement = $templateManagement;
        $this->orderTemplateStoreManagement = $orderTemplateStoreManagement;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        $model = $this->templateManagement->load($request->getParam('template_id'), 'template_id');
        $model->setNotificationType($request->getParam('notification_type'));
        if ($request->getParam('notification_type') == "popup") {
            $model->setWysiwygMessage($request->getParam('wysiwyg_message'));
        } else {
            $model->setMessage($request->getParam('message'));
        }
        $model->setStatus($request->getParam('status'));
        $model->setIsEnable($request->getParam('is_enable'));
        $model->setTitle($request->getParam('title'));
        if (isset($request->getParam('logo')[0]['url'])) {
            $model->setLogo($request->getParam('logo')[0]['url']);
        }
        $model = $this->templateManagement->save($model);
        $templateId = $request->getParam('template_id')?$request->getParam('template_id'):$model->getId();
        $ids = $this->orderTemplateStoreManagement->getCollection()
            ->addFieldToFilter('template_id', $templateId)
            ->getAllIds();
        foreach ($request->getParam('store_id') as $store) {
            $id = $this->orderTemplateStoreManagement->getCollection()
                ->addFieldToFilter('template_id', $templateId)
                ->addFieldToFilter('store_id', $store)
                ->getFirstItem()->getId();
            if (in_array($id, $ids)) {
                $storeIds[] = $id;
            }
            $orderModel = $this->orderTemplateStoreManagement->load($id);
            $orderModel->setTemplateId($templateId);
            $orderModel->setStoreId($store);
            $this->orderTemplateStoreManagement->save($orderModel);
        }
        if ($ids!=null && $storeIds!=null) {
            $toDelete = array_diff($ids, $storeIds);
            foreach ($toDelete as $id) {
                $orderModel = $this->orderTemplateStoreManagement->deleteById($id);
            }
        }
        return $this->resultRedirectFactory->create()->setPath('pushnotify/order/view');
    }
}
