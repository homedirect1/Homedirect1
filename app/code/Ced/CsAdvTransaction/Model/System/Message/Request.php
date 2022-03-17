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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Model\System\Message;

/**
 * Class Request
 * @package Ced\CsAdvTransaction\Model\System\Message
 */
class Request implements \Magento\Framework\Notification\MessageInterface
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory
     */
    protected $vsettingsCollection;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * Request constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory $vsettingsCollection
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory $vsettingsCollection,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->vsettingsCollection = $vsettingsCollection;
        $this->vendorFactory = $vendorFactory;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        // Retrieve unique message identity
        return md5('CsAdvTransaction_Request');
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        // Return true to show your message, false to hide it
        $settingModel = $this->vsettingsCollection->create()
            ->addFieldToFilter('group', 'csadvancetransaction')
            ->addFieldToFilter('value', 1)
            ->addFieldToFilter('key', 'vendor/payment/request')->getData();

        if (count($settingModel))
            return true;
        else
            return false;
    }

    /**
     * @return string
     */
    public function getText()
    {
        // Retrieve message text
        $settingModel = $this->vsettingsCollection->create()
            ->addFieldToFilter('group', 'csadvancetransaction')
            ->addFieldToFilter('value', 1)
            ->addFieldToFilter('key', 'vendor/payment/request')->getData();

        $html = '';
        foreach ($settingModel as $settings) {
            $vendor = $this->vendorFactory->create()->load($settings['vendor_id'])->getName();
            $html .= "Vendor " . $vendor . " has requested for Payment" . '<a href="' . $this->_urlBuilder->getUrl('csadvtransaction/pay/order', ['vendor_id' => $settings['vendor_id']]) . '">' . __(' Vendor Pending Payment') . '</a>' . '<br>';
        }
        return $html;
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        // Possible values: SEVERITY_CRITICAL, SEVERITY_MAJOR, SEVERITY_MINOR, SEVERITY_NOTICE
        return self::SEVERITY_MAJOR;
    }
}