<?php

namespace Codilar\PushNotification\Controller\Adminhtml\Template;

use Codilar\PushNotification\Api\TemplateManagementInterface;
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
    const ADMIN_RESOURCE = "Codilar_PushNotification::pushnotify_template";

    /**
     * @var TemplateManagementInterface
     */
    private $templateManagement;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param TemplateManagementInterface $templateManagement
     */
    public function __construct(
        Action\Context $context,
        TemplateManagementInterface $templateManagement
    ) {
        parent::__construct($context);
        $this->templateManagement = $templateManagement;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        $model = $this->templateManagement->load($request->getParam('template_id'));
        $model->setTitle($request->getParam('title'));
        $model->setNotificationType($request->getParam('notification_type'));
        if ($request->getParam('notification_type') == "popup") {
            $model->setWysiwygMessage($request->getParam('wysiwyg_message'));
        } else {
            $model->setMessage($request->getParam('message'));
        }
        $model->setIsEnable($request->getParam('is_enable'));
        $model->setRedirectUrl($request->getParam('redirect_url'));
        $model->setLogo($request->getParam('logo')[0]['url']);
        $model = $this->templateManagement->save($model);
        return $this->resultRedirectFactory->create()->setPath('pushnotify/template/view');
    }
}
