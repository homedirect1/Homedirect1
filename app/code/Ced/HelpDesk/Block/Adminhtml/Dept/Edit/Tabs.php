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
namespace Ced\HelpDesk\Block\Adminhtml\Dept\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /*
     * create tab
     * @return void
     * */
    protected function _construct()
    {   
        parent::_construct();
        $this->setId('General');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Department Information'));
    }

    protected function _prepareLayout(){
        $this->addTab(
                'general',
                [
                'label' => __('General Information'),
                'title' => __('General Information'),
                'content' => $this->getLayout()->createBlock('Ced\HelpDesk\Block\Adminhtml\Dept\Edit\Tab\General')->toHtml(),
                'active' => true
                ]
        );
        $this->addTab(
                'manage_agent',
                [
                'label' => __('Manage Agent'),
                'title' => __('Manage Agent'),
                'content' => $this->getLayout()->createBlock('Ced\HelpDesk\Block\Adminhtml\Dept\Edit\Tab\ManageAgent')->toHtml(),
                'active' => false
                ]
        );
    }
}