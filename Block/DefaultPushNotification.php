<?php

namespace Codilar\PushNotification\Block;

use Codilar\PushNotification\Logger\Logger;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File as DriverInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * class DefaultPushNotification
 *
 * @description DefaultPushNotification
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Provides Feature for Push Notification on frontend
 */

class DefaultPushNotification extends Template
{
    const XML_PATH_PUSH_NOTIFICATION_MODULE_STATUS = 'codilar_push_notification/general/module_status';
    const XML_PATH_PUSH_NOTIFICATION_MODULE_FOR_MAGENTO = 'codilar_push_notification/mage_general/module_status';
    const XML_PATH_PUSH_NOTIFICATION_API_KEY = 'codilar_push_notification/general/api_key';
    const XML_PATH_PUSH_NOTIFICATION_AUTH_DOMAIN = 'codilar_push_notification/general/auth_domain';
    const XML_PATH_PUSH_NOTIFICATION_DATABASE = 'codilar_push_notification/general/database';
    const XML_PATH_PUSH_NOTIFICATION_PROJECT_ID = 'codilar_push_notification/general/project_id';
    const XML_PATH_PUSH_NOTIFICATION_STORAGE_BUCKET_ID = 'codilar_push_notification/general/storage_bucket_id';
    const XML_PATH_PUSH_NOTIFICATION_SENDER_ID = 'codilar_push_notification/general/message_sender';
    const XML_PATH_PUSH_NOTIFICATION_APP_ID = 'codilar_push_notification/general/app_id';
    const XML_PATH_PUSH_NOTIFICATION_MEASUREMENT_ID = 'codilar_push_notification/general/measurement_id';
    const XML_PATH_PUSH_NOTIFICATION_SERVER_KEY = 'codilar_push_notification/general/server_key';
    const XML_PATH_PUSH_NOTIFICATION_PUBLIC_KEY = 'codilar_push_notification/general/public_key';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var FormKey
     */
    private $formKey;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionFactory
     */
    private $sessionFactory;
    /**
     * @var Json
     */
    private $serialize;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var DriverInterface
     */
    private $driver;
    /**
     * @var Reader
     */
    private $moduleReader;
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * DefaultPushNotification constructor.
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param FormKey $formKey
     * @param SessionFactory $sessionFactory
     * @param Json $serialize
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param StoreManagerInterface $storeManager
     * @param OrderRepositoryInterface $orderRepository
     * @param RequestInterface $request
     * @param DriverInterface $driver
     * @param Session $checkoutSession
     * @param Reader $moduleReader
     * @param Logger $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        FormKey $formKey,
        SessionFactory $sessionFactory,
        Json $serialize,
        Filesystem $filesystem,
        DirectoryList $directoryList,
        StoreManagerInterface $storeManager,
        OrderRepositoryInterface $orderRepository,
        RequestInterface $request,
        DriverInterface $driver,
        Session $checkoutSession,
        Reader $moduleReader,
        Logger $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->formKey = $formKey;
        $this->sessionFactory = $sessionFactory;
        $this->serialize = $serialize;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
        $this->driver = $driver;
        $this->request = $request;
        $this->moduleReader = $moduleReader;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getModuleStatus()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_MODULE_STATUS);
    }

    /**
     * get form key
     *
     * @return string
     * @throws LocalizedException
     */
    public function getKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return array
     */
    public function setupManifest()
    {
        $array = [
            "name"=>"codilar push notification",
            "gcm_sender_id"=>$this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_SENDER_ID)
        ];
        $manifest = $this->serialize->serialize($array);
        try {
            $fileContent = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            if ($fileContent->writeFile('codilar/pushnotification/json/manifest.json', $manifest)) {
                $result = [
                    'status'=>true,
                    'path' => $this->storeManager->getStore()
                            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'codilar/pushnotification/json/manifest.json'
                ];
            } else {
                $result = [
                    'status'=>false,
                    'message'=>'something went wrong please contact admin'
                ];
            }
        } catch (FileSystemException $e) {
            $result = [
                'status'=>false,
                'message'=>'something went wrong please contact admin'
            ];
        } catch (NoSuchEntityException $e) {
            $result = [
                'status'=>false,
                'message'=>'something went wrong please contact admin'
            ];
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getConfigData()
    {
        $apiKey = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_API_KEY);
        $authDom = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_AUTH_DOMAIN);
        $database = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_DATABASE);
        $projectId = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_PROJECT_ID);
        $storageBucketId = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_STORAGE_BUCKET_ID);
        $messageSenderId = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_SENDER_ID);
        $fcmAppId = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_APP_ID);
        $measurementId = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_MEASUREMENT_ID);
        $serverKey = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_SERVER_KEY);
        $publicKey = $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_PUBLIC_KEY);
        if (($apiKey!=null) &&
            ($authDom != null) &&
            ($database != null) &&
            ($projectId != null) &&
            ($storageBucketId != null) &&
            ($measurementId != null) &&
            ($messageSenderId != null) &&
            ($fcmAppId != null) &&
            ($serverKey != null) &&
            ($publicKey != null)
        ) {
            $result['status'] = true;
            $result['api_key'] = $apiKey;
            $result['auth_dom'] = $authDom;
            $result['database'] = $database;
            $result['project'] = $projectId;
            $result['storage_bucket_id'] = $storageBucketId;
            $result['message_sender_id'] = $messageSenderId;
            $result['app_id'] = $fcmAppId;
            $result['measurement_id'] = $measurementId;
            $result['server_key'] = $serverKey;
            $result['public_key'] = $publicKey;
            return $result;
        } else {
            $result['status'] = false;
            return $result;
        }
    }

    /**
     * @return int|bool
     */
    public function copyContent()
    {
        try {
            $path = $this->moduleReader->getModuleDir(
                \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
                'Codilar_PushNotification'
            );
            $fileContent = ($this->driver->fileGetContents($path . '/frontend/web/js/firebase-messaging-sw.js'));
            $fh = $this->driver->fileOpen(
                $this->request->getServer('DOCUMENT_ROOT') . '/firebase-messaging-sw.js',
                'w'
            );
            return $this->driver->fileWrite($fh, $fileContent);
        } catch (FileSystemException $e) {
            $this->logger->info('File System Exception:' . $e->getMessage());
            return false;
        }
    }

    /**
     * @return string
     */
    public function getFireBaseServiceProvider()
    {
        return $this->getUrl() . 'firebase-messaging-sw.js';
    }

    /**
     * @return bool
     */
    public function isCheckout()
    {
        return $this->request->getModuleName() == "checkout";
    }


    /**
     * @return mixed
     */
    public function isEnabledForMagento()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PUSH_NOTIFICATION_MODULE_FOR_MAGENTO);
    }
}
