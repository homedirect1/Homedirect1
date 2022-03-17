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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Block\Adminhtml\Zipcode\Import;

/**
 * Class Edit
 * @package Ced\CsHyperlocal\Block\Adminhtml\Import
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize cms page edit block
     *
     * @return void
     */

    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Ced_CsHyperlocal';
        $this->_controller = 'adminhtml_zipcode_import';
        $this->buttonList->update('save', 'label', __('Save'));
        parent::_construct();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import Zipcodes via CSV');
    }

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/shiparea/managezipcode', ['id' => $this->getRequest()->getParam('location_id')]);
    }
}
