<?php

namespace Codilar\PushNotification\Controller\Adminhtml\Registered;

use Codilar\PushNotification\Api\TemplateManagementInterface;
use Codilar\PushNotification\Api\TokenManagementInterface;
use Codilar\PushNotification\Block\DefaultPushNotification;
use Codilar\PushNotification\Model\ResourceModel\PushNotification\CollectionFactory;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect as MagentoRedirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * class SendNotification
 *
 * @description Mass Action for Sending Notifications to Customers
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Mass Action for Sending Notifications to Customers
 */

class SendNotification extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = "Codilar_PushNotification::registered_customer";

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var TemplateManagementInterface
     */
    private $templateManagement;
    /**
     * @var TokenManagementInterface
     */
    private $tokenManagement;
    /**
     * @var DefaultPushNotification
     */
    private $defaultPushNotification;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param TemplateManagementInterface $templateManagement
     * @param DefaultPushNotification $defaultPushNotification
     * @param TokenManagementInterface $tokenManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        TemplateManagementInterface $templateManagement,
        DefaultPushNotification $defaultPushNotification,
        TokenManagementInterface $tokenManagement
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->templateManagement = $templateManagement;
        parent::__construct($context);
        $this->tokenManagement = $tokenManagement;
        $this->defaultPushNotification = $defaultPushNotification;
    }

    /**
     * Execute action
     *
     * @return MagentoRedirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        if ($this->defaultPushNotification->getModuleStatus()) {
            $templateId = $this->getRequest()->getParam('template_id');
            $collection = $this
                ->filter
                ->getCollection(
                    $this->collectionFactory->create()
                );
            $collectionSize = $collection->getSize();
            $tokens = [];
            foreach ($collection as $page) {
                $tokens[] = $page->getToken();
            }
            $templateData = $this->templateManagement->getDataBYId($templateId);
            $title = $templateData->getTitle();
            $type = $templateData->getNotificationType();
            $message = $type=="popup"?$templateData->getWysiwygMessage():$templateData->getMessage();
            $redirectUrl = $templateData->getRedirectUrl();
            $logoUrl = $templateData->getLogo();
            $result = $this->tokenManagement->sendNotification(
                $tokens,
                $title,
                $message,
                $logoUrl,
                $redirectUrl,
                $type
            );

            $this->messageManager->addSuccessMessage(
                __(
                    'A total of %1 Customers(s) have been Notified',
                    $result['success']
                )
            );
            if ($result['failure']>0) {
                $this->messageManager->addSuccessMessage(
                    __(
                        '%1 Failed to notify due to some technical reason.',
                        $result['failure']
                    )
                );
            }
        } else {
                $this->messageManager->addSuccessMessage(
                    __(
                        'Please Enable the Module for Sending Notification.'
                    )
                );
        }

        /** @var Redirect $resultRedirect */
        return $this->resultRedirectFactory
            ->create()
            ->setPath('pushnotify/registered/customer');
    }
}
