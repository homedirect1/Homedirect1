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
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Observer;

use Ced\StorePickup\Block\Extensions;
use Ced\StorePickup\Helper\Data;
use Ced\StorePickup\Helper\Feed;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class BeforeLoadLayout
 * @package Ced\StorePickup\Observer
 */
class BeforeLoadLayout implements ObserverInterface
{
    /**
     * @var null
     */
    protected $_licenseActivateUrl = null;

    /**
     * @var Feed|null
     */
    protected $_feedHelper = null;

    const LICENSE_ACTIVATION_URL_PATH = 'system/license/validate_url';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * BeforeLoadLayout constructor.
     * @param Feed $_feedHelper
     * @param RequestInterface $request
     * @param Data $dataHelper
     */
    public function __construct(
        Feed $_feedHelper,
        RequestInterface $request,
        Data $dataHelper
    ) {
        $this->_feedHelper = $_feedHelper;
        $this->request = $request;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        try {
            $action = $observer->getEvent()->getAction();
            $layout = $observer->getEvent()->getLayout();
            $request = $this->request;
            $controllerModule = strtolower($request->getControllerModule());
            $validateArray = [];
            if ($controllerModule !== 'magento_config' || $request->getParam('section') !== 'carriers') {
                return $this;
            }
            $helper = $this->_feedHelper;
            $modules = $helper->getCedCommerceExtensions();

            foreach ($modules as $moduleName => $releaseVersion) {
                $m = strtolower($moduleName);
                if (!preg_match('/ced/i', $m)) {
                    return $this;
                }

                $h = $this->dataHelper->getStoreConfig(Extensions::HASH_PATH_PREFIX . $m . '_hash');

                for ($i = 1; $i <= (int)$this->dataHelper
                    ->getStoreConfig(Extensions::HASH_PATH_PREFIX . $m . '_level'); $i++) {
                    $h = base64_decode($h);
                }

                $h = json_decode($h, true);
                if (is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && strtolower($h['module_name']) == $m && $h['license'] == $this->dataHelper->getStoreConfig(Extensions::HASH_PATH_PREFIX . $m)) {
                } else {
                    if (count($validateArray) == 0) {
                        $validateArray = $this->autoValidateModules();
                    }

                    if (isset($validateArray[$moduleName]) && isset($validateArray[$moduleName]['valid']) && $validateArray[$moduleName]['valid']) {
                        continue;
                    }

                    $_POST = $_GET = [];
                    $exist = false;
                    foreach ($layout->getUpdate()->getHandles() as $handle) {
                        if ($handle == 'c_e_d_c_o_m_m_e_r_c_e') {
                            $exist = true;
                            break;
                        }
                    }
                    if (!$exist) {
                        $layout->getUpdate()->addHandle('c_e_d_c_o_m_m_e_r_c_e');
                    }
                }
            }
            return $this;
        } catch (Exception $e) {
            return $this;
        }
    }

    /**
     * Retrieve feed data as XML element
     *
     * @return SimpleXMLElement
     */
    private function autoValidateModules($urlParams = [])
    {
        $result = false;

        $body = '';
        $urlParams = array_merge($this->_feedHelper->getEnvironmentInformation(), $urlParams);

        if (is_array($urlParams) && count($urlParams) > 0) {
            $body = $this->_feedHelper->addParams('', $urlParams);
            $body = trim($body, '?');
        }
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getLicenseActivateUrl());
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            $resultArray = [];
            if (isset($info['http_code']) && $info['http_code'] != 200) {
                return false;
            }
            $result = json_decode($result, true);
            foreach ($result as $moduleName => $response) {
                if ($response && isset($response['hash']) && isset($response['level'])) {
                    $json = ['success' => 0, 'message' => __('There is an Error Occurred.'), 'valid' => 0];
                    $valid = $response['hash'];
                    try {
                        for ($i = 1; $i <= $response['level']; $i++) {
                            $valid = base64_decode($valid);
                        }
                        $valid = json_decode($valid, true);

                        if (is_array($valid) &&
                            isset($valid['domain']) &&
                            isset($valid['module_name']) &&
                            isset($valid['license']) &&
                            $valid['module_name'] == $moduleName
                        ) {
                            $path = Extensions::HASH_PATH_PREFIX . strtolower($moduleName) . '_hash';
                            $this->_feedHelper->setDefaultStoreConfig($path, $response['hash'], 0);
                            $path = Extensions::HASH_PATH_PREFIX . strtolower($moduleName) . '_level';
                            $this->_feedHelper->setDefaultStoreConfig($path, $response['level'], 0);
                            $path = Extensions::HASH_PATH_PREFIX . strtolower($moduleName);
                            $this->_feedHelper->setDefaultStoreConfig($path, $valid['license'], 0);
                            $json['success'] = 1;
                            $json['valid'] = 1;
                            $json['message'] = __('Module Activated successfully.');
                        } else {
                            $json['success'] = 0;
                            $json['valid'] = 0;
                            $json['message'] = isset($response['error']['code']) && isset($response['error']['msg']) ? 'Error (' . $response['error']['code'] . '): ' . $response['error']['msg'] : __('Invalid License Key.');
                        }
                    } catch (Exception $e) {
                        $json['success'] = 0;
                        $json['valid'] = 0;
                        $json['message'] = $e->getMessage();
                    }
                }
                $resultArray[$moduleName] = $json;
            }
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/auto_validation.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r($resultArray, true));
        } catch (\Exception $e) {
            return false;
        }

        return $result;
    }

    /**
     * Retrieve local license url
     *
     * @return string
     */
    private function getLicenseActivateUrl()
    {
        if (is_null($this->_licenseActivateUrl)) {
            $this->_licenseActivateUrl = ($this->_feedHelper->getStoreConfig(Extensions::LICENSE_USE_HTTPS_PATH) ? 'https://' : 'http://')
                . $this->_feedHelper->getStoreConfig(self::LICENSE_ACTIVATION_URL_PATH);
        }
        return $this->_licenseActivateUrl;
    }
}
