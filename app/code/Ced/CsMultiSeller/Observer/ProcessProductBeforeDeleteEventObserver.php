<?php

namespace Ced\CsMultiSeller\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProcessProductBeforeDeleteEventObserver implements ObserverInterface
{
    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory
     */
    protected $vproductsCollection;

    /**
     * @var \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vproductFactory;

    public function __construct(
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts $resourceModel,
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollection,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductFactory
    ) {
        $this->vproductsCollection = $vproductsCollection;
        $this->vproductFactory = $vproductFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * Call an API to product delete from ERP
     * after delete product from Magento
     *
     * @param   Observer $observer
     * @return  $this
     */
    public function execute(Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        $productId = $eventProduct->getId();
        if ($productId) {
            $multiseller = $this->vproductsCollection->create()->addFieldToFilter('product_id', ['eq' => $productId])->addFieldToFilter('is_multiseller', ['eq' => 0]);
            if (count($multiseller) != 0) {
                $child_products = $this->vproductsCollection->create()->addFieldToFilter('is_multiseller', ['eq' => 1])->addFieldToFilter('parent_id', ['eq' => $productId]);
                if (count($child_products) != 0) {

                    $id = $child_products->getFirstItem()->getId();

                    $vproductmodel = $this->vproductFactory->create();
                    $resourceModel = $this->resourceModel->load($vproductmodel,$id);
                    $vproductmodel->setIsMultiseller(0);
                    $vproductmodel->setParentId(0);
                    $resourceModel->save($vproductmodel);
                }
            }
        }

        return $this;
    }
}
