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
 * @package     Ced_Rewardsystem
 * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Rewardsystem\Block\Colorpicker;

class Color extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * add color picker in admin configuration fields
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string script
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
        require(["jquery"], function ($) {
            $(document).ready(function (e) {
                $("#' . $element->getHtmlId() . '").css("background-color","#' . $value . '");
                $("#' . $element->getHtmlId() . '").colpick({
                    layout:"hex",
                    submit:0,
                    colorScheme:"dark",
                    color: "#' . $value . '",
                    onChange:function(hsb,hex,rgb,el,bySetColor) {
                    $(el).css("background-color","#"+hex);
                    if(!bySetColor) $(el).val(hex);
                  }
                }).keyup(function(){
                    $(this).colpickSetColor(this.value);
                });
            });
        });
        </script>';

        return $html;
    }
}
