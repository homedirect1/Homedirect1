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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Block\Adminhtml\Manage\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /*
     * @return $this
     * */
    protected function _construct()
    {   
        parent::_construct();
        $this->setId('Reply');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Ticket Information'));
    }
    /*
     * prepare layout
     *
     * */

    protected function _prepareLayout(){
        $this->addTab(
            'reply',
            [
            'label' => __('Ticket Reply'),
            'title' => __('Ticket Reply'),
            'content' => $this->getLayout()->createBlock('Ced\HelpDesk\Block\Adminhtml\Manage\Edit\Tab\Reply')->setTemplate('reply.phtml')->toHtml(),
            'active' => true
            ]
        );
    }
}