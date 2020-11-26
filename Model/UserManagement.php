<?php

namespace Codilar\PushNotification\Model;

use Codilar\PushNotification\Api\TokenManagementInterface;
use Codilar\PushNotification\Api\UserManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * class UserManagement
 *
 * @description Collection
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * RestApi for Insert, Update & Remove Tokens
 */

class UserManagement implements UserManagementInterface
{
    /**
     * @var TokenManagementInterface
     */
    private $tokenManagement;
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var FormKey
     */
    private $formKey;
    /**
     * @var SessionFactory
     */
    private $sessionFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * UserManagement constructor.
     * @param TokenManagementInterface $tokenManagement
     * @param Validator $validator
     * @param RequestInterface $request
     * @param SessionFactory $sessionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param FormKey $formKey
     */
    public function __construct(
        TokenManagementInterface $tokenManagement,
        Validator $validator,
        RequestInterface $request,
        SessionFactory $sessionFactory,
        CustomerRepositoryInterface $customerRepository,
        FormKey $formKey
    ) {
        $this->tokenManagement = $tokenManagement;
        $this->validator = $validator;
        $this->request = $request;
        $this->formKey = $formKey;
        $this->sessionFactory = $sessionFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param $currentToken
     * @return array
     */
    public function setUser($currentToken)
    {
        $ary_response = [];
        foreach ($currentToken as $value) {
            $userToken = $value->getToken();
            $formKey = $value->getFormKey();
            $this->request->setParams(['form_key'=>$formKey]);
            $validate = $this->validator->validate($this->request);
            if (isset($userToken) && isset($formKey) && $validate) {
                $customer = $this->sessionFactory->create();
                $customerId = $customer->getCustomerId();
                $valid = $this->createToken($customerId, $userToken);
                $ary_response[] = $valid;
            } else {
                $ary_response[] = ["code"=>"404", "message"=>"invalid Token"];
            }
        }
        return $ary_response;
    }

    /**
     * @inheritDoc
     */
    public function removeToken($currentToken)
    {
        $ary_response = [];
        foreach ($currentToken as $value) {
            $userToken = $value->getToken();
            $formKey = $value->getFormKey();
            $this->request->setParams(['form_key'=>$formKey]);
            $validate = $this->validator->validate($this->request);
            if (isset($userToken) && isset($formKey) && $validate) {
                $ary_response = $this->deleteToken($userToken, $ary_response);
            } else {
                $ary_response[] = ["code"=>"404", "message"=>"invalid Token"];
            }
        }
        return $ary_response;
    }

    /**
     * @param $userTokendele
     * @param array $ary_response
     * @return array
     */
    public function deleteToken($userToken, array $ary_response)
    {
        $model = $this->tokenManagement->load($userToken, 'token');
        if ($model->getToken()) {
            $model = $this->tokenManagement->delete($model);
            $valid = [
                "code" => "200",
                "message" => "Old Token related data has been removed successfully,"
            ];
            $ary_response[] = $valid;
        } else {
            $ary_response[] = ["code" => "404", "message" => "invalid Token"];
        }
        return $ary_response;
    }

    /**
     * @param $customerId
     * @param $userToken
     * @param \Magento\Customer\Model\Session $customer
     * @return array
     */
    public function createToken($customerId, $userToken)
    {
        $model = $this->tokenManagement->load($customerId, 'customer_id');
        $isCustomer = "No";
        $userName = "Guest";
        if (!empty($customerId)) {
            $customer = $this->customerRepository->getById($customerId);
            if ($customer->getId()) {
                $userName = $customer->getFirstname();
                $isCustomer = "Yes";
            }
        }
        if ($model->getUserId()) {
            $model->setCustomerId(null);
            $model->setStatus(0);
            $model->setIsCustomer("No");
            $this->tokenManagement->save($model);
        }
        $model = $this->tokenManagement->load($userToken, 'token');
        $model->setToken($userToken);
        $model->setCustomerId($customerId);
        $model->setIsCustomer($isCustomer);
        $model->setUserName($userName);
        $model = $this->tokenManagement->save($model);
        $valid = [
            "code" => "200",
            "message" => "New Token has been saved successfully.",
            "track-id" => $model->getId(),
        ];
        return $valid;
    }
}
