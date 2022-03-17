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

namespace Ced\StorePickup\Model;

/**
 * Class Feed
 * @package Ced\StorePickup\Model
 */
class Feed extends \Magento\AdminNotification\Model\Feed
{

    const XML_USE_HTTPS_PATH = 'system/adminnotification/use_https';
    const XML_FEED_URL_PATH = 'system/csmarketplace/feed_url';
    const XML_FREQUENCY_PATH = 'system/csmarketplace/frequency';
    const XML_LAST_UPDATE_PATH = 'system/csmarketplace/last_update';
    const XML_FEED_TYPES = 'cedcore/feeds_group/feeds';
    const XML_PATH_INSTALLATED_MODULES = 'module';

    /**
     * Feed url
     *
     * @var string
     */
    protected $_feedUrl;
    /**
     * @var \Magento\Framework\Component\ComponentRegistrarInterface
     */
    protected $moduleRegistry;
    /**
     * @var \Magento\AdminNotification\Model\InboxFactory
     */
    protected $_inboxFactory;
    /**
     * @var \Ced\StorePickup\Helper\Feed
     */
    protected $feedHelper;

    /**
     * Feed constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\ConfigInterface $backendConfig
     * @param \Magento\AdminNotification\Model\InboxFactory $inboxFactory
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Component\ComponentRegistrarInterface $moduleRegistry
     * @param \Ced\StorePickup\Helper\Feed $feedHelper
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Component\ComponentRegistrarInterface $moduleRegistry,
        \Ced\StorePickup\Helper\Feed $feedHelper
    ) {
        $this->moduleRegistry = $moduleRegistry;
        $this->_inboxFactory = $inboxFactory;
        $this->feedHelper = $feedHelper;
        parent::__construct(
            $context,
            $registry,
            $backendConfig,
            $inboxFactory,
            $curlFactory,
            $deploymentConfig,
            $productMetadata,
            $urlBuilder
        );
    }

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = (
                $this->_backendConfig->isSetFlag(self::XML_USE_HTTPS_PATH)
                    ? 'https://' : 'http://'
                )
                . $this->_backendConfig->getValue(self::XML_FEED_URL_PATH);
        }
        return $this->_feedUrl;
    }

    /**
     * Check feed for modification
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function checkUpdate()
    {
        if (!isset($_GET['testdev'])) {
            if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
                return $this;
            }
        }

        $feedData = [];
        $feed = [];
        $feedXml = $this->getFeedData($this->feedHelper->getEnvironmentInformation());
        $allowedFeedType = explode(',', $this->_backendConfig->getValue(self::XML_FEED_TYPES));
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            if (isset($_GET['testdev'])) {
                print_r($feedXml->channel->item);
                die;
            }
            $installedModules = $this->feedHelper->getCedCommerceExtensions();
            foreach ($feedXml->channel->item as $item) {
                if (!isset($installedModules[(string)$item->module])) {
                    continue;
                }
                if ($this->feedHelper->isAllowedFeedType($item)) {
                    if (strlen(trim($item->module)) > 0) {
                        if (isset($feedData[trim((string)$item->module)]) && isset($feedData[trim((string)$item->module)]['release_version']) && strlen((string)$item->release_version) > 0 && version_compare($feedData[trim((string)$item->module)]['release_version'], trim((string)$item->release_version), '>') === true) {
                            continue;
                        }
                        $feedData[trim((string)$item->module)] = array(
                            'severity' => (int)$item->severity,
                            'date_added' => $this->getDate((string)$item->pubDate),
                            'title' => (string)$item->title,
                            'description' => (string)$item->description,
                            'url' => (string)$item->link,
                            'module' => (string)$item->module,
                            'release_version' => (string)$item->release_version,
                            'update_type' => (string)$item->update_type,
                        );
                        if (strlen((string)$item->warning) > 0) {
                            $feedData[trim((string)$item->module)]['warning'] = (string)$item->warning;
                        }

                        if (strlen((string)$item->product_url) > 0) {
                            $feedData[trim((string)$item->module)]['url'] = (string)$item->product_url;
                        }

                    }

                    $feed[] = array(
                        'severity' => (int)$item->severity,
                        'date_added' => $this->getDate((string)$item->pubDate),
                        'title' => (string)$item->title,
                        'description' => (string)$item->description,
                        'url' => (string)$item->link
                    );
                }
            }

            if ($feed) {
                $this->_inboxFactory->create()->parse(array_reverse($feed));
            }
            if ($feedData) {
                $this->_cacheManager->save(serialize($feedData), 'all_extensions_by_cedcommerce');
            }

        }
        $this->setLastUpdate();

        return $this;
    }


    /**
     * Retrieve DB date from RSS date
     *
     * @param string $rssDate
     * @return string YYYY-MM-DD YY:HH:SS
     */
    public function getDate($rssDate)
    {
        return gmdate('Y-m-d H:i:s', strtotime($rssDate));
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return $this->_backendConfig->getValue(self::XML_FREQUENCY_PATH) * 3600;
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->_cacheManager->load('ced_notifications_lastcheck');
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), 'ced_notifications_lastcheck');
        return $this;
    }

    /**
     * Retrieve feed data as XML element
     *
     * @return SimpleXMLElement
     */
    public function getFeedData($urlParams = array())
    {
        $curl = $this->curlFactory->create();
        $curl->setConfig(
            array(
                'timeout' => 10
            )
        );
        $body = '';
        if (is_array($urlParams) && count($urlParams) > 0) {
            $body = $this->addParams('', $urlParams);
            $body = trim($body, '?');
        }

        try {


            $curl->write(\Zend_Http_Client::POST, $this->getFeedUrl(), '1.1', array(), $body);
            $data = $curl->read();

            if ($data === false) {
                return false;
            }
            //$data = file_get_contents("http://cedcommerce.com/blog/notifications/feed/");//remove this
            //uncomment this
            $data = preg_split('/^\r?$/m', $data, 2);

            $data = trim($data[1]);


            if (trim($data) == '') {
                return false;
            }


            if ($curl->getInfo() || true) {
                $xml = new \SimpleXMLElement((string)$data);
            } else {
                return false;
            }
            $curl->close();
        } catch (\Exception $e) {
            return false;
        }

        return $xml;
    }

    /**
     * Add params into url string
     *
     * @param string $url (default '')
     * @param array $params (default array())
     * @param boolean $urlencode (default true)
     * @return string | array
     */
    public function addParams($url = '', $params = array(), $urlencode = true)
    {
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                if (parse_url($url, PHP_URL_QUERY)) {
                    if ($urlencode) {
                        $url .= '&' . $key . '=' . $this->prepareParams($value);
                    } else {
                        $url .= '&' . $key . '=' . $value;
                    }
                } else {
                    if ($urlencode) {
                        $url .= '?' . $key . '=' . $this->prepareParams($value);
                    } else {
                        $url .= '?' . $key . '=' . $value;
                    }
                }
            }
        }
        return $url;
    }

    /**
     * Url encode the parameters
     *
     * @param string | array
     * @return string | array | boolean
     */
    public function prepareParams($data)
    {
        if (!is_array($data) && strlen($data)) {
            return urlencode($data);
        }
        if ($data && is_array($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $data[$key] = urlencode($value);
            }
            return $data;
        }
        return false;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getFeedXml()
    {
        try {
            $data = $this->getFeedData();
            if (trim($data) != '') {
                $xml = new \SimpleXMLElement((string)$data);
            } else {
                $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>');
            }
        } catch (Exception $e) {
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>');
        }

        return $xml;
    }


    /**
     * @param $module
     * @return bool|string
     */
    public function getReleaseVersion($module)
    {
        $modulePath = $this->moduleRegistry->getPath(self::XML_PATH_INSTALLATED_MODULES, $module);
        $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, "$modulePath/etc/module.xml");
        $source = new \Magento\Framework\Simplexml\Config($filePath);
        if ($source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version) {
            return $source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version->__toString();
        }
        return false;
    }
}
