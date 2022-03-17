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

namespace Ced\OrderDelete\Block\Adminhtml;


class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    protected function _construct()
    {
        parent::_construct();
        $message = __('Are you sure you want to delete this order');
        $onClick = "confirmSetLocation('{$message}', '{$this->getDeleteUrl()}')";
       
        if (1) {
            $this->buttonList->add(
                'order_delete',
                [
                    'label' => __('Delete'),
                    'class' => 'delete',
                    'onclick' => $onClick,
                ]
            );
        }
    }
    /**
    * @return order delete Url
    **/    
    public function getDeleteUrl()
    {
        return $this->getUrl('orderdelete/orderdelete/delete', ['order_id'=>$this->getOrder()->getId()]);
    }
}
