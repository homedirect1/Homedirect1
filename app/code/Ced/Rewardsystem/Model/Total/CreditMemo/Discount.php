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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Rewardsystem\Model\Total\CreditMemo;

class Discount extends \Magento\Sales\Model\Order\Total\AbstractTotal
{
	protected $_storeManager;
	public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, array $data = [])
	{
		parent::__construct($data);
		$this->_storeManager = $storeManager;
	}
	public function collect(\Magento\Sales\Model\Order\Creditmemo $creditMemo)
	{
		$order = $creditMemo->getOrder();
		$baseRewardAmount = $order->getRewardsystemBaseAmount();
		$rewardAmount = $order->getRewardsystemDiscount();
		$baseRefundedReward = $order->getBaseRewardsystemAmountRefunded();
		$refundedReward = $order->getRewardsystemAmountRefunded();

		$baseDiscount = $baseRewardAmount - $baseRefundedReward;
		$discount = $rewardAmount - $refundedReward;

		if (floatval($baseDiscount)) {

			if ($creditMemo->getGrandTotal() < $discount) {
				$discount = $creditMemo->getGrandTotal();
				if ($discount < 0) {
					$discount = 0;
				}
			}
			if ($creditMemo->getBaseGrandTotal() < $baseDiscount) {
				$baseDiscount = $creditMemo->getBaseGrandTotal();
				if ($baseDiscount < 0) {
					$baseDiscount = 0;
				}
			}
			$baseDiscount = $creditMemo->roundPrice($baseDiscount);
			$discount = $creditMemo->roundPrice($discount);

			$creditMemo->setDiscountAmount($baseDiscount);
			$creditMemo->setBaseDiscountAmount($baseDiscount);
			$creditMemo->setDiscountDescription("Reward Discount");

			$creditMemo->setBaseGrandTotal($creditMemo->getBaseGrandTotal() - $baseDiscount);
			$creditMemo->setGrandTotal($creditMemo->getGrandTotal() - $discount);
		}
		return $this;
	}
}
