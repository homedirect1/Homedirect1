<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_GShop
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Observer;

use Magento\Framework\Event\ObserverInterface;

class InventoryChange implements ObserverInterface
{
    /** @var \Ced\GShop\Model\Productchange $productChange */
    public $productChange;

    /** @var \Magento\Framework\App\RequestInterface $request */
    public $request;

    /**
     * InventoryChange constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Ced\GShop\Model\Productchange $productChange
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Ced\GShop\Model\Productchange $productChange
    )
    {
        $this->request = $request;
        $this->productChange = $productChange;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $product = $observer->getEvent()->getItem();

            if (empty($product)) {
                return $observer;
            }

            $productids[] = $product->getId();

            //capture stock change
            $orgQty = $product->getOrigData('quantity_and_stock_status');
            $oldValue = (int)$orgQty['qty'];

            $postData = $this->request->getParam('product');
            $newValue = (int)$postData['quantity_and_stock_status']['qty'];

            $isInStock = (boolean)$postData['quantity_and_stock_status']['is_in_stock'];
            //if out of stock then set value to 0
            if (!$isInStock) {
                $newValue = 0;
            }

            if ($oldValue == $newValue) {
                return $observer;
            }

            $productId = $product->getId();
            $type = $this->productChange::CRON_TYPE_INVENTORY;
            $this->productChange->setProductChange($productId, $oldValue, $newValue, $type);
            return $observer;
        } catch (\Exception $e) {
            return $observer;
        }
    }

}
