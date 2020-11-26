<?php

namespace Codilar\PushNotification\Ui\Component\Listing\MassActions;

use Magento\Ui\Component\Action;

/**
 * class DynamicAction
 *
 * @description Data Manipulation for Registered Customer Row data
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Hierarchical Option In Template by Mass Action
 */

class DynamicAction extends Action
{
    /**
     * @inheritDoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if (isset($config['action_resource'])) {
            $this->actions = $config['action_resource']->getActions();
        }
        parent::prepare();
    }
}
