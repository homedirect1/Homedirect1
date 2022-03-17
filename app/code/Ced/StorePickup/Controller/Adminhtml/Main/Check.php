<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_StorePickup
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Controller\Main;

use Magento\Framework\App\Action\Context;

/**
 * Class Check
 * @package Ced\StorePickup\Controller\Main
 */
class Check extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ced\StorePickup\Helper\Feed
     */
    protected $feedHelper;

    /**
     * Check constructor.
     * @param Context $context
     * @param \Ced\StorePickup\Helper\Feed $feedHelper
     */
    public function __construct(Context $context, \Ced\StorePickup\Helper\Feed $feedHelper)
    {
        $this->feedHelper = $feedHelper;
        parent::__construct($context);
    }

    /**
     * Index action
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $json = array('success' => 0, 'module_name' => '', 'module_license' => '');
        if ($data && isset($data['module_name'])) {
            $json['module_name'] = strtolower($data['module_name']);
            $json['module_license'] = $this->feedHelper
                ->getStoreConfig(\Ced\StorePickup\Block\Extensions::HASH_PATH_PREFIX . strtolower($data['module_name']));
            if (strlen($json['module_license']) > 0) $json['success'] = 1;
            $this->getResponse()->setHeader('Content-type', 'application/json');
            echo json_encode($json);
            die;
        } else {
            $this->_forward('noroute');
        }
    }
}