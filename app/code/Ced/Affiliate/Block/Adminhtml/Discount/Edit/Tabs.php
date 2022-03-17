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
namespace Ced\Affiliate\Block\Adminhtml\Discount\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs {
	/**
	 *
	 * @return void
	 */
	protected function _construct() {

		parent::_construct ();
		$this->setId ( 'grid_record' );
		$this->setDestElementId ( 'edit_form' );
		$this->setTitle ( __ ( 'Denomination Rule' ) );
		
	}
	protected function _beforeToHtml() {

		$this->addTab ( 'Post', array (
				'label' => __ ( 'Discount Denomination Rule' ),
				'content' => $this->getLayout ()->createBlock ( 'Ced\Affiliate\Block\Adminhtml\Discount\Edit\Tab\Main' )->toHtml () 
		) );
		return parent::_beforeToHtml ();
	}
}