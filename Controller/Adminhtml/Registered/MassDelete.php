<?php

namespace Codilar\PushNotification\Controller\Adminhtml\Registered;

use Codilar\PushNotification\Api\TemplateManagementInterface;
use Codilar\PushNotification\Api\TokenManagementInterface;
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
 * class MassDelete
 *
 * @description Mass Action for Deleting Registered Customers
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Mass Action for Deleting Registered Customers
 */

class MassDelete extends Action implements HttpPostActionInterface
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
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param TemplateManagementInterface $templateManagement
     * @param TokenManagementInterface $tokenManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        TemplateManagementInterface $templateManagement,
        TokenManagementInterface $tokenManagement
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->templateManagement = $templateManagement;
        parent::__construct($context);
        $this->tokenManagement = $tokenManagement;
    }

    /**
     * Execute action
     *
     * @return MagentoRedirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $page) {
            $page->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        return $this->resultRedirectFactory
            ->create()
            ->setPath('pushnotify/registered/customer');
    }
}
