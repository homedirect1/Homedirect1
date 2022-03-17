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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Block\Adminhtml\Account\Banner\Edit\Tab;

/**
 * Class Main
 * @package Ced\Affiliate\Block\Adminhtml\Account\Banner\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @return $this|\Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        parent::_prepareForm();
        $podata = $this->_coreRegistry->registry('current_banner');
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Banner Information')]);

        $fieldset->addField(
            'banner_name',
            'text',
            [
                'name' => 'banner_name',
                'label' => __('Banner Name'),
                'title' => __('Banner Name'),
                'required' => true,
                'class' => '',
            ]
        );

        $selectField = $fieldset->addField(
            'banner_type',
            'select',
            [
                'name' => 'banner_type',
                'label' => __('Banner Type'),
                'values' => ['image' => "Image", 'text' => 'Text', 'video' => 'video'],
                'title' => __('Banner Type'),
                'required' => true,
                'class' => '',
                'onchange' => 'showHideField()',
                'required' => true,
            ]
        );


        $fieldset->addField(
            'banner_image',
            'file',
            [
                'name' => 'banner_data',
                'label' => __('Banner Source'),
                'title' => __('Banner Source'),
                'class' => '',
                'required' => true,

            ]
        );


        $fieldset->addField(
            'banner_data',
            'textarea',
            [
                'name' => 'banner_data',
                'label' => __('Banner Text'),
                'title' => __('Banner Text'),
                'class' => '',
                'required' => true,
            ]
        );

        $selectField->setAfterElementHtml('
                        <script>
       						window.onload=function(){
       		 				var value = jQuery("#banner_type").val();
       						if(value=="text")
	       					{
	       						jQuery(".field-banner_image").hide();
	       						jQuery(".field-banner_data").show();
       			
       							jQuery("#banner_image").removeClass("required-entry");
	    					}
       						else{
	    						jQuery(".field-banner_image").show();
	       						jQuery(".field-banner_data").hide();
       							jQuery("#banner_image").addClass("required-entry");
	    					}
       						if(jQuery("#images").length){
    								jQuery("#banner_image").removeClass("required-entry");
    							}	
       						}
                        function showHideField() {
	                        var value = jQuery("#banner_type").val();
	       					if(value=="text")
	       					{
	       						jQuery(".field-banner_image").hide();
	       						jQuery(".field-banner_data").show();
       							jQuery("#banner_image").removeClass("required-entry");
	    					}
	       				else{
	    						jQuery(".field-banner_image").show();
	       						jQuery(".field-banner_data").hide();
       							jQuery("#banner_image").addClass("required-entry");
	    					}
       						if(jQuery("#images").length){
    								jQuery("#banner_image").removeClass("required-entry");
    							}
                        }
                        </script>
                    ');

        if ($this->getRequest()->getParam('id') && $podata->getBannerType() != 'text') {
            $fieldset->addField(
                'images',
                'note',
                [
                    'label' => __('Uploaded Files/Images'),
                    'name' => 'images',
                    'text' => '<div id="rating_detail">' . $this->getLayout()->createBlock(
                            'Ced\Affiliate\Block\Adminhtml\Account\Banner\Images'
                        )->toHtml() . '</div>'
                ]
            );
        }

        $fieldset->addField(
            'banner_height',
            'text',
            [
                'name' => 'banner_height',
                'label' => __('Banner Height'),
                'title' => __('Banner Height'),
                'class' => '',
                'required' => true,
            ]
        );


        $fieldset->addField(
            'banner_width',
            'text',
            [
                'name' => 'banner_width',
                'label' => __('Banner Width'),
                'title' => __('Banner Width'),
                'class' => '',
                'required' => true,
            ]
        );


        $fieldset->addField(
            'banner_link',
            'text',
            [
                'name' => 'banner_link',
                'label' => __('Banner Link'),
                'title' => __('Banner Link'),
                'class' => '',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'validity',
            'date',
            [
                'name' => 'validity',
                'label' => __('Banner Validity'),
                'date_format' => 'yyyy-MM-dd',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => ['0' => 'Disable', '1' => 'Enable'],
                'class' => '',
                'required' => true,
            ]
        );

        $form->setValues($podata->getData());
        $this->setForm($form);
        return $this;

    }


}
