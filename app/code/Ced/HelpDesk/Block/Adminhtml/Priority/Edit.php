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
namespace Ced\HelpDesk\Block\Adminhtml\Priority;
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /*
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    protected function _construct()
    {  
        $this->_objectId = 'id';
        $this->_blockGroup = 'Ced_HelpDesk';
        $this->_controller = 'adminhtml_priority';
 
        parent::_construct();
 
        if ($this->_isAllowedAction('Ced_HelpDesk::newpriority')) {
            $this->buttonList->update('save', 'label', __('Save Priority'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

    }
    /*
     * Change header text
     * @return string
     * */

    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('ced_priority')->getId()) {
            return __("Edit Status '%1'", $this->escapeHtml($this->_coreRegistry->registry('ced_priority')->getTitle()));
        } else {
            return __('New Status');
        }
    }
    /*
     * @param $resourceId
     * @return boolean
     * */
 
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    /*
     * Change save and continue url
     * @return string
     * */

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/savepriority', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
    /*
     * Change back url
     * @return string
     * */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/priorityinfo');
    }
    /*
     * Change delete url
     * @return string
     * */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [$this->_objectId => $this->getRequest()->getParam($this->_objectId)]);
    }

}