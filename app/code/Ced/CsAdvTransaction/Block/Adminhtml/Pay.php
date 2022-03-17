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

namespace Ced\CsAdvTransaction\Block\Adminhtml;

use Magento\Backend\Block\Template;

/**
 * Class Pay
 * @package Ced\CsAdvTransaction\Block\Adminhtml
 */
class Pay extends Template
{
    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory
     */
    protected $vordersCollection;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory
     */
    protected $vsettingsCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    public $formKey;

    /**
     * Pay constructor.
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollection
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory $vsettingsCollection
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollection,
        \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory $vsettingsCollection,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        $this->vendorFactory = $vendorFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->dateTime = $dateTime;
        $this->vordersCollection = $vordersCollection;
        $this->vsettingsCollection = $vsettingsCollection;
        $this->_storeManager = $context->getStoreManager();
        $this->formKey = $formKey;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getVendorId()
    {
        $vid = $this->getRequest()->getParam('vendor_id');
        $this->closeRequestStatus($vid);
        return $vid;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        $vid = $this->getVendorId();
        $vendor = $this->vendorFactory->create()->load($vid);
        return $vendor;
    }

    /**
     * @return mixed
     */
    public function getVendorEligibleOrders()
    {
        $rmaDate = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/refund_policy');
        $paycycle = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_cycle');
        $completeCycle = $rmaDate + $paycycle;

        $date = $this->dateTime->gmtDate();
        $date = explode(' ', $date);

        $vid = $this->getVendorId();
        $vorders = $this->vordersCollection->create()
            ->addFieldToFilter('payment_state', ['nin' => [2, 5]])
            ->addFieldToFilter('order_payment_state', ['nin' => 1])
            ->addFieldToFilter('vendor_id', $vid)
            ->addFieldToFilter('vendor_earn', ['neq' => 0]);

        $orderIds = [];
        foreach ($vorders as $k => $v) {
            if (!$v->canInvoice() && !$v->canShip()) {

                $days = $completeCycle;

                $afterdate = strtotime("+" . $days . " days", strtotime($v->getCreatedAt()));
                $afterdate = date("Y-m-d", $afterdate);

                if ($date[0] >= $afterdate) {
                    $orderIds[] = $v->getOrderId();
                }
            }
        }
        $orders = $this->vordersCollection->create()
            ->addFieldToFilter('vendor_id', $vid)
            ->addFieldToFilter('order_id', ['in' => $orderIds]);

        return $orders;
    }

    /**
     * @param $vid
     */
    public function closeRequestStatus($vid)
    {
        $settingModel = $this->vsettingsCollection->create()
            ->addFieldToFilter('group', 'csadvancetransaction')
            ->addFieldToFilter('key', 'vendor/payment/request')
            ->addFieldToFilter('vendor_id', $vid)
            ->addFieldToFilter('value', 1)->getFirstItem()->getData();

        if (count($settingModel)) {
            $model = $this->vsettingsCollection->create()->load($settingModel['setting_id']);
            $model->setValue(0);
            $model->save();
        }
    }

}