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
  * @category  Ced
  * @package   Ced_OrderDelete
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\OrderDelete\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Construct
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    public function deleteOrder($order)
    {
        $orderId=$order->getId();
        if($orderId) {
            $invoices = $order->getInvoiceCollection();
            if(count($invoices->getData())) {
                foreach ($invoices as $invoice){
                    $items = $invoice->getAllItems(); 
                    foreach ($items as $item) {
                        $item->delete();
                    }
                    $invoice->delete();
                }
            }
            
            $creditnotes = $order->getCreditmemosCollection();
            if(count($creditnotes->getData())) {
                foreach ($creditnotes as $creditnote){
                    $items = $creditnote->getAllItems(); 
                    foreach ($items as $item) {
                        $item->delete();
                    }
                    $creditnote->delete();
                }
            }
            
            $shipments = $order->getShipmentsCollection();
            if(count($shipments->getData())) {
                foreach ($shipments as $shipment){
                    $items = $shipment->getAllItems(); 
                    foreach ($items as $item) {
                        $item->delete();
                    }
                    $shipment->delete();
                }
            }
            $order->delete();
            return true;
        }else{
            return false;
        }     
    }
    public function deleteMassOrder($orderCollection)
    {
        $orderCount=0;
        foreach ($orderCollection as $order) {
            $orderId=$order->getId();
            if($orderId) {
                $invoices = $order->getInvoiceCollection();
                if(count($invoices->getData())) {
                    foreach ($invoices as $invoice){
                        $items = $invoice->getAllItems(); 
                        foreach ($items as $item) {
                            $item->delete();
                        }
                        $invoice->delete();
                    }
                }
                
                $creditnotes = $order->getCreditmemosCollection();
                if(count($creditnotes->getData())) {
                    foreach ($creditnotes as $creditnote){
                        $items = $creditnote->getAllItems(); 
                        foreach ($items as $item) {
                            $item->delete();
                        }
                        $creditnote->delete();
                    }
                }
                
                $shipments = $order->getShipmentsCollection();
                if(count($shipments->getData())) {
                    foreach ($shipments as $shipment){
                        $items = $shipment->getAllItems(); 
                        foreach ($items as $item) {
                            $item->delete();
                        }
                        $shipment->delete();
                    }
                }
                $order->delete();
                $orderCount++;
            }else{
                continue;
            }     
        }
        if($orderCount) {
            return $orderCount;
        }else{
            return false;
        }
    }
}
