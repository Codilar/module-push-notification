<?php

namespace Codilar\PushNotification\Plugin;

use Codilar\PushNotification\Model\CommonFactor\AssignTokenToCustomer;
use Magento\Customer\Controller\Account\CreatePost as MagentoCreatePost;
use Magento\Framework\Controller\Result\Redirect;

/**
 * class CreatePost
 *
 * @description Assigning the Token at the Customer Sign Up
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Assigning the Token at the Customer Sign Up
 */

class CreatePost
{
    /**
     * @var AssignTokenToCustomer
     */
    private $assignTokenToCustomer;

    /**
     * CreatePost constructor.
     * @param AssignTokenToCustomer $assignTokenToCustomer
     */
    public function __construct(
        AssignTokenToCustomer $assignTokenToCustomer
    ) {
        $this->assignTokenToCustomer = $assignTokenToCustomer;
    }

    /**
     * Change redirect after login to home instead of dashboard.
     *
     * @param MagentoCreatePost $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(
        MagentoCreatePost $subject,
        $result
    ) {
        $this->assignTokenToCustomer->assignToken();
        return $result;
    }
}
