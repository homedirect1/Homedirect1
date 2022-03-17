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
namespace Ced\HelpDesk\Block\Adminhtml\Status\Edit;
 
class Form extends  \Magento\Backend\Block\Widget\Form\Generic
{
    /*
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     * */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('status_form');
        $this->setTitle(__('Status Information'));
    }
    /*
     * prepare form
     * @return $this
     * */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ced_status');
        if ($model['id']!=null){
        	
        	$form = $this->_formFactory->create(
        			['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/status/savestatus'), 'method' => 'post']]
        			);
        	 
        	
        } else{
        	
        	$form = $this->_formFactory->create(
        			['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/status/savestatus'), 'method' => 'post']]
        			);
        	 
        }
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Status Information')]
        );
 
        $fieldset->addField(
            'title',
            'text',
            [   
                'name' => 'title', 
                'label' => __('Status Title'),
                'required' => true
            ]
        );
 
        $fieldset->addField(
            'code',
            'text',
            [	
                'name' => 'code',
            	'label' => __('Status Code'), 
            	'required' => true	
            ]
        );
        
        
        $fieldset->addField(
        	'status',
        	'select',
        	[
        		'name' => 'status', 
        		'label' => __('Status'),
        		'required' => true,
        		'options' => [  1 =>'Enable', 
                                0 =>'Disable'
                             ]			
        	]
        );

        $baseUrl = $this->getBaseUrl();
        $icon = $baseUrl.'pub/static/adminhtml/Magento/backend/en_US/Ced_HelpDesk/image/';
        $fieldset->addField(
            'bgcolor',
            'text',
            [
                'name' => 'bgcolor', 
                'label' => __('Background Colour'),
                'required' => true, 
        		'after_element_html' =>'<style type="text/css">input.jscolor { background-image: url('.$icon.'icon.png) !important; background-position: calc(100% - 8px) center; background-repeat: no-repeat; padding-right: 44px !important; } input.jscolor.disabled,input.jscolor[disabled] { pointer-events: none; }</style><script type="text/javascript">
                var el = document.getElementById("bgcolor");
                el.className = el.className + " jscolor";
            </script>'
            ]
        );
        $fieldset->addField('id', 'hidden', ['name' => 'id']);
       
	 	if($model['id']!=null){	
	 		$form->setValues($model);		
	 	}
        $form->setUseContainer(true);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
}