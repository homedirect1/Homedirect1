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

namespace Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer;

/**
 * Class Pay
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer
 */
class Pay extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Ced\CsOrder\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * Pay constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ced\CsOrder\Model\VordersFactory $vordersFactory
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsOrder\Model\VordersFactory $vordersFactory,
        \Magento\Backend\Block\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->dateTime = $dateTime;
        $this->vordersFactory = $vordersFactory;
    }

    /**
     * Return the Order Id Link
     *
     */
    public function render123(\Magento\Framework\DataObject $row)
    {

        if (!$row->canInvoice() && !$row->canShip()) {
            if ($row->getOrderPaymentState() == 2 && $row->getPaymentState() == 1) {

                $rmaDate = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/refund_policy');
                $paycycle = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_cycle');
                $completeCycle = $rmaDate + $paycycle;

                $date = $this->dateTime->gmtDate();
                $date = explode(' ', $date);

                $days = $completeCycle;

                $afterdate = strtotime("+" . $days . " days", strtotime($row->getCreatedAt()));
                $afterdate = date("Y-m-d", $afterdate);
                //echo "curr date ".$date[0]; echo "<br>";echo "after date ".$afterdate;echo "<br>";
                if ($date[0] >= $afterdate) {
                    $url = $this->getUrl("csadvtransaction/pay/order/", array('vendor_id' => $row->getVendorId()));
                    $html = '<a href=' . $url . '>Pay</a>';
                    return $html;
                }

            } elseif ($row->getPaymentState() == 2) {
                echo "Paid";
            } elseif ($row->getPaymentState() == 4) {
                $url = $this->getUrl("csadvtransaction/pay/order/", array('vendor_id' => $row->getVendorId()));
                $html = '<a href=' . $url . '>Refund</a>';
                return $html;
            } else
                return '';
        }

    }


    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {

        if ($row->getId()) {
            $model = $this->vordersFactory->create()->load($row->getId());


            if (!$model->canInvoice() && !$model->canShip()) {

                if ($row->getOrderPaymentState() == 2 && $row->getPaymentState() == 1) {

                    $rmaDate = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/refund_policy');
                    $paycycle = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_cycle');
                    $completeCycle = $rmaDate + $paycycle;

                    $date = $this->dateTime->gmtDate();
                    $date = explode(' ', $date);

                    $days = $completeCycle;

                    $afterdate = strtotime("+" . $days . " days", strtotime($row->getCreatedAt()));
                    $afterdate = date("Y-m-d", $afterdate);


                    if ($date[0] == $afterdate) {
                        $url = $this->getUrl("csadvtransaction/pay/order/", array('vendor_id' => $row->getVendorId()));
                        $html = '<a href=' . $url . '><div class="overdue">PAYMENT TODAY</div></a>';
                        $style = "<style>.overdue {
							  border: 0px solid;
							  padding: 5px;
							  background-color: #e41101;
							  text-decoration: none !important;
							  width:138px;
     						  color: #fff;
     						  border-radius: 22px;
				              text-align: center;
							}</style>";
                        return $html . $style;
                    } elseif ($date[0] >= $afterdate) {
                        $html = '';
                        $url = $this->getUrl("csadvtransaction/pay/order/", array('vendor_id' => $row->getVendorId()));
                        $html = '<a  href=' . $url . '><div class="overdue">PAYMNET OVERDUE</div></a>';
                        $style = "<style>.overdue {
							  border: 0px solid;
							  padding: 5px;
							  background-color: #e41101;
							  text-decoration: none !important;
							  width:138px;
     						  color: #fff;
     						  border-radius: 22px;
				              text-align: center;
							}</style>";
                        return $html . $style;
                    } else {
                        $style = '';
                        $html = "<div class='underpay'><span>Under Pay Cycle</span></div>";
                        $style = "<style>.underpay {
							  border: 0px solid;
     						  border-radius: 22px;
							  padding: 5px;
							  background-color: #a2d0ff;
     						  color: #fff;
							  width:116px;
				              cursor:default;
     						  text-align: center;
							}</style>";
                        return $html . $style;

                    }

                } elseif ($row->getPaymentState() == 2) {

                    $html = "<div class='paid'><span>PAID</span></div>";
                    $style = "<style>.paid {
							  border: 0px solid;
							  padding: 5px;
							  background-color: #3CB861;
							  text-decoration: none !important;
							  width:138px;
     					      cursor:default;
     						  color: #fff;
     						  border-radius: 22px;
				              text-align: center;
							}</style>";
                    return $html . $style;
                } elseif ($row->getPaymentState() == 4) {
                    $url = $this->getUrl("csadvtransaction/pay/order/", array('vendor_id' => $row->getVendorId()));
                    $html = '<a href=' . $url . '><div class="refund">REFUND</div></a>';
                    $style = "<style>.refund {
							  border: 0px solid;
     					      border-radius: 22px;
							  padding: 5px;
							  background-color: red;
     					      color: #fff;
		  	 		          width:138px;
		                      cursor:default;
     					      text-align: center;
							}</style>";
                    return $html . $style;

                } elseif ($row->getPaymentState() == 5) {

                    $html = "<div class='refunded'><span>REFUNDED</span></div>";
                    $style = "<style>.refunded {
							  border: 0px solid;
     				          border-radius: 22px;
							  padding: 5px;
							  background-color: #ffccdb;
		  	 		          width:138px;
     					      color: #fff;
                              cursor:default;
     				          text-align: center;
							}</style>";
                    return $html . $style;


                } elseif ($row->getPaymentState() == 3) {

                    $html = "<div class='refunded'><span>CANCELLED</span></div>";
                    $style = "<style>.refunded {
							  border: 0px solid;
     				          border-radius: 22px;
							  padding: 5px;
							  background-color: #ffccdb;
		  	 		          width:138px;
     					      color: #fff;
                              cursor:default;
     				          text-align: center;
							}</style>";
                    return $html . $style;
                }
            } else {
                $html = "<div class='underprocess'><span>PROCESSING</span></div>";
                $style = "<style>.underprocess {
							  border: 0px solid;
     				          border-radius: 22px;
     				          color: #fff;
							  padding: 5px;
							  background-color: #fcf24f;
		  	 		          width:138px;
                              cursor:default;
     				          text-align: center;
							}</style>";
                return $html . $style;
            }
        }

    }

}
