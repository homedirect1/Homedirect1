<?php

 /**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_Perkmshipping
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */


namespace Ced\Perkmshipping\Block\Adminhtml\System\Config\Form;

class Zones extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

	/**
	 * @var \Magento\Framework\View\LayoutInterface
	 */
	protected $_layout;
	
	protected $_renders = array();

  protected $_defaultRenderer;
    protected $_actionRenderer;
    protected $_priceTypeRenderer;
    private $_arrayRowsCache;

    protected function _getDefaultRenderer()
    {

        if (!$this->_actionRenderer) {
            $this->_actionRenderer = $this->getLayout()->createBlock(
                    'Ced\Perkmshipping\Block\Adminhtml\System\Config\Renderer\Select',
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_actionRenderer->setExtraParams('style="width:90px"');
        }
        return $this->_actionRenderer;
    }
   	
    public function _prepareToRender() {     
      //  $layout = $this->_layout;
        $renderer = $this->getLayout()->createBlock('Ced\Perkmshipping\Block\Adminhtml\System\Config\Renderer\Select', '', array('is_render_to_js_template' => true));  

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $renderer->setOptions($objectManager->create('Ced\Perkmshipping\Model\Source\Method\Type')->toOptionArray());

        $this->addColumn('from', array(
            'label' => __('From'),
            'style' => 'width:40px',
        ));
        $this->addColumn('to', array(
            'label' => __('To'),
            'style' => 'width:40px',
        ));
        $this->addColumn('price', array(
            'label' => __('Price'),
            'style' => 'width:40px',
        ));        

        $this->addColumn('type', array(
            'label' => __('Type'),
            'style' => 'width:100px',
        	'renderer' => $this->_getDefaultRenderer(),
        ));
	
        //$this->_renders['type'] = $renderer; 
                                
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Zone');
        
        //parent::__construct();
    }
    
   /* protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {    	

    	foreach ($this->_renders as $key => $render){
        
	        $row->setData(
	            'option_extra_attr_' . $render->calcOptionHash($row->getData($key)),
	            'selected="selected"'
	        );
    	}
    } */

   protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $duration = $row->getType();
        $method = $row->getType();
        $options = [];
        $options['option_' . $this->_getDefaultRenderer()->calcOptionHash($duration)]
        = 'selected="selected"';
        $options['option_' . $this->_getDefaultRenderer()->calcOptionHash($method)]
        = 'selected="selected"';
        //print_r($options);die;
        $row->setData('option_extra_attrs', $options);
        return;
    }

}