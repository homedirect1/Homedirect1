<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_GShop
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Ui\Component\Listing\Columns\Account;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class AccountStatus extends Column
{
    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @var \Ced\GShop\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    /** @var \Ced\GShop\Helper\Data $dataHelper */
    protected $dataHelper;

    protected $objectManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        \Ced\GShop\Helper\Data $dataHelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->dataHelper = $dataHelper;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName])) {
                    if ($item[$fieldName]) {
                        $item[$fieldName . '_html'] = "<div class='grid-severity-notice'><span>Token Fetched</span></div>";
                        $item[$fieldName . '_title'] = __('GShop Account Details');
                        $item[$fieldName . '_accountid'] = $item['id'];
                    } else {
                        $item[$fieldName . '_html'] = "<button class='grid-severity-critical'>Token Not Found</button>";
                        $item[$fieldName . '_title'] = __('GShop Account Details');
                        $item[$fieldName . '_accountid'] = $item['id'];
                        $item[$fieldName . '_accountvalidation'] = $item[$fieldName];
                    }
                } else {
                    $item[$fieldName . '_html'] = '<div class="grid-severity-critical"><span>Please Fetch Token</span></div>';
                    $item[$fieldName . '_title'] = __('GShop Account Details');
                    $item[$fieldName . '_accountid'] = $item['id'];
                }
                $storeManager = $this->objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                $mediaUrl = $storeManager->getStore()->getBaseUrl().'media/';
                try {
                    if ($this->dataHelper->getProcessMode() == 'csv') {
                        $fileName = 'googleshopping_' .$item['account_code'].'_'. $item['content_language'] . '_' . $item['target_country'] . '.txt';
                        $dirPath = $mediaUrl . 'ced_google';
                        $filePath = $dirPath . '/' . $fileName;
                        $item['account_feed_file'] = $filePath;
                    } else {
                        $item['account_feed_file'] = 'No need for URL';
                    }
                } catch (\Exception $exception) {

                }
            }
        }
        return $dataSource;
    }
}
