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

namespace Ced\CsAdvTransaction\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Paytype
 * @package Ced\CsAdvTransaction\Ui\Component\Listing\Column
 */
class Pay extends Column
{
    const STATE_OPEN       = 1;
    const STATE_PAID       = 2;
    const STATE_CANCELED   = 3;
    const STATE_REFUND     = 4;
    const STATE_REFUNDED   = 5;

    /**
     * Paytype constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsOrder\Model\VordersFactory $vordersFactory,
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->scopeConfig = $scopeConfig;
        $this->dateTime = $dateTime;
        $this->vordersFactory = $vordersFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $html = '';
                if ($item['id']){
                    $model = $this->vordersFactory->create()->load($item['id']);
                    if (!$model->canInvoice() && !$model->canShip()) {

                        if ($item['order_payment_state'] == 2 && $item['payment_state'] == self::STATE_OPEN) {
                            $rmaDate = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/refund_policy');
                            $paycycle = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_cycle');
                            $completeCycle = $rmaDate + $paycycle;

                            $date = $this->dateTime->gmtDate();
                            $date = explode(' ', $date);

                            $days = $completeCycle;

                            $afterdate = strtotime("+" . $days . " days", strtotime($item['created_at']));
                            $afterdate = date("Y-m-d", $afterdate);

                            if ($date[0] == $afterdate) {
                                $url = $this->urlBuilder->getUrl('csadvtransaction/pay/order/', ['vendor_id' => $item['vendor_id']]);
                                $html = '<a href=' . $url . ' style="border: 0px solid;
							  padding: 5px;
							  background-color: #e41101;
							  text-decoration: none !important;
							  width:138px;
     						  color: #fff;
     						  border-radius: 22px;
                              display:inline-block;
				              text-align: center;">PAYMENT TODAY</a>';

                            } elseif ($date[0] >= $afterdate) {
                                $html = '';
                                $url = $this->urlBuilder->getUrl('csadvtransaction/pay/order/', ['vendor_id' => $item['vendor_id']]);
                                $html = '<a  href=' . $url . ' class="overdue" style="border: 0px solid;
							  padding: 5px;
							  background-color: #e41101;
							  text-decoration: none !important;
							  width:138px;
     						  color: #fff;
     						  border-radius: 22px;
                              display:inline-block;
				              text-align: center;">PAYMNET OVERDUE</a>';
                            } else {
                                $style = '';
                                $html = "<div class='underpay' style='border: 0px solid;
     						  border-radius: 22px;
							  padding: 5px;
							  background-color: #a2d0ff;
     						  color: #fff;
							  width:116px;
				              cursor:default;
     						  text-align: center;'>Under Pay Cycle</div>";

                            }

                        }elseif ($item['payment_state'] == 2) {

                            $html = "<div class='paid' style='border: 0px solid;
							  padding: 5px;
							  background-color: #3CB861;
							  text-decoration: none !important;
							  width:138px;
     					      cursor:default;
     						  color: #fff;
     						  border-radius: 22px;
				              text-align: center;' >PAID</div>";

                        } elseif ($item['payment_state'] == 4) {
                            $url = $this->urlBuilder->getUrl('csadvtransaction/pay/order/', ['vendor_id' => $item['vendor_id']]);
                            $html = '<a href=' . $url . ' style="border: 0px solid;
     					      border-radius: 22px;
							  padding: 5px;
							  background-color: red;
     					      color: #fff;
		  	 		          width:138px;
		                      cursor:default;
                              display:inline-block;
     					      text-align: center;" class="refund" >REFUND</a>';

                        } elseif ($item['payment_state'] == 5) {

                            $html = "<div class='refunded' style='border: 0px solid;
     				          border-radius: 22px;
							  padding: 5px;
							  background-color: #ffccdb;
		  	 		          width:138px;
     					      color: #fff;
                              cursor:default;
     				          text-align: center;'><span>REFUNDED</span></div>";
                        } elseif ($item['payment_state'] == 3) {

                            $html = "<div class='refunded' style='border: 0px solid;
     				          border-radius: 22px;
							  padding: 5px;
							  background-color: #ffccdb;
		  	 		          width:138px;
     					      color: #fff;
                              cursor:default;
     				          text-align: center;'>CANCELLED</div>";
                        }
                    }else{
                        $html = "<div class='underprocess' style='border: 0px solid;
                              border-radius: 22px;
                              color: #fff;
                              padding: 5px;
                              background-color: #D9512C;
                              width:138px;
                              cursor:default;
                              text-align: center;'>PENDING</div>";
                    }
                } else {
                    $html = "<div class='underprocess' style='border: 0px solid;
     				          border-radius: 22px;
     				          color: #fff;
							  padding: 5px;
							  background-color: #fcf24f;
		  	 		          width:138px;
                              cursor:default;
     				          text-align: center;'>PROCESSING</div>";

                }

                $item[$this->getData('name')] = $html;

            }
        }
        return $dataSource;
    }

}
