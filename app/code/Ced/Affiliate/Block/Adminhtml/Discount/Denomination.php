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
namespace Ced\Affiliate\Block\Adminhtml\Discount;

class Denomination extends \Magento\Backend\Block\Widget\Form\Container {
	
	protected $_coreRegistry = null;
	public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, array $data = []) {
		$this->_coreRegistry = $registry;
		parent::__construct ( $context, $data );
	}
	
	/**
	 * Initialize blog post edit block
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_objectId = 'id';
		$this->_blockGroup = 'ced_affiliate';
		$this->_controller = 'adminhtml_discount';
		$this->addButton ( 'save', [ 
				'label' => __ ( 'Save Rule' ),
				'class' => 'save primary',
				'data_attribute' => [ 
						'mage-init' => [ 
								'button' => [ 
										'event' => 'save',
										'target' => '#edit_form' 
								] 
						] 
				] 
		], 1 );
		
		$this->addButton ( 'back', [ 
				'label' => __ ( 'Back' ),
				'onclick' => 'setLocation(\'' . $this->getBackUrl () . '\')',
				'class' => 'back' 
		], - 1 );
	}
	/**
	 * Getter of url for "Save and Continue" button
	 * tab_id will be replaced by desired by JS later
	 *
	 * @return string
	 */
	protected function _getSaveAndContinueUrl() {
		return $this->getUrl ( '*/*/save', [ 
				'_current' => true,
				'back' => 'edit',
				'active_tab' => '{{tab_id}}' 
		] );
	}
	
	/**
	 * Prepare layout
	 *
	 * @return \Magento\Framework\View\Element\AbstractBlock
	 */
	protected function _prepareLayout() {
		$this->_formScripts [] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
		return parent::_prepareLayout ();
	}
	public function getBackUrl() {
		return $this->getUrl ( '*/discount/denomination' );
	}
}