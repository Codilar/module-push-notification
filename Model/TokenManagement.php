<?php

namespace Codilar\PushNotification\Model;

use Codilar\PushNotification\Api\TokenManagementInterface;
use Codilar\PushNotification\Block\DefaultPushNotification;
use Codilar\PushNotification\Model\PushNotification as Model;
use Codilar\PushNotification\Model\PushNotificationFactory as ModelFactory;
use Codilar\PushNotification\Model\ResourceModel\PushNotification as ResourceModel;
use Codilar\PushNotification\Model\ResourceModel\PushNotification\Collection as Collection;
use Codilar\PushNotification\Model\ResourceModel\PushNotification\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class TokenManagement
 *
 * @description Token Management Class gives access to do all CURD Operations on Relationship Between Tokens & User
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * all CURD Operations on Relationship Between Tokens & User
 */

class TokenManagement implements TokenManagementInterface
{
    /**
     * @var PushNotification
     */
    private $modelFactory;
    /**
     * @var ResourceModel
     */
    private $resourceModel;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var Curl
     */
    private $curl;
    /**
     * @var Json
     */
    private $serialize;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * TokenManagement constructor.
     * @param PushNotificationFactory $modelFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param Json $serialize
     * @param ScopeConfigInterface $scopeConfig
     * @param Curl $curl
     */
    public function __construct(
        ModelFactory $modelFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory,
        Json $serialize,
        ScopeConfigInterface $scopeConfig,
        Curl $curl
    ) {
        $this->modelFactory = $modelFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->curl = $curl;
        $this->serialize = $serialize;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function getDataBYId($id)
    {
        return $this->load($id);
    }

    /**
     * @inheritDoc
     */
    public function getDataByToken($token)
    {
        return $this->load($token, 'token');
    }

    /**
     * @inheritDoc
     * @throws AlreadyExistsException
     */
    public function save(Model $model)
    {
        $this->resourceModel->save($model);
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function load($value, $field = null)
    {
        $model = $this->create();
        $this->resourceModel->load($model, $value, $field);
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function create()
    {
        return $this->modelFactory->create();
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function delete(Model $model)
    {
        try {
            $this->resourceModel->delete($model);
        } catch (\Exception $exception) {
            throw new LocalizedException(__("Error deleting Model with Id : %1", $model->getId()));
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function deleteById($id)
    {
        $model = $this->load($id);
        return $this->delete($model);
    }

    /**
     * @inheritDoc
     */
    public function sendNotification($userTokens, $title, $message, $logoUrl, $actionUrl, $type)
    {
        try {
            $this->curl->addHeader(
                'authorization',
                'Key=' . $this->scopeConfig->getValue(DefaultPushNotification::XML_PATH_PUSH_NOTIFICATION_SERVER_KEY)
            );
            $this->curl->addHeader('content-type', 'application/json');
            $payload = [
                'registration_ids' => $userTokens,
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
                    'type' => $type
                ],
            ];
            $this->curl->post(
                'https://fcm.googleapis.com/fcm/send',
                $this->serialize->serialize($payload)
            );
            return $this->serialize->unserialize($this->curl->getBody());
        } catch (\Exception $exception) {
            return [
                'failure'=>count($userTokens),
                'success'=>0
            ];
        }
    }
}
