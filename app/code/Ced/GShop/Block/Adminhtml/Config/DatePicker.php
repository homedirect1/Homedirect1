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
 * @package     Ced_GShop
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Block\Adminhtml\Config;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;

class DatePicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var  Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        //get configuration element
        $html = $element->getElementHtml();
        //check datepicker set or not
        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
            $this->_coreRegistry->registry('datepicker_loaded', 1);
        }
        //add icon on datepicker
        /*$html .= '<button type="button" style="display:none;" class="ui-datepicker-trigger '
            . 'v-middle"><span>Select Date</span></button>';*/
        // add datepicker with element by jquery
        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/ui"], function (jq) {
                jq(document).ready(function () {
                    jq("#' . $element->getHtmlId() . '").datepicker( { dateFormat: "yy-mm-dd" } );
                    jq(".ui-datepicker-trigger").removeAttr("style");
                    jq(".ui-datepicker-trigger").click(function(){
                        jq("#' . $element->getHtmlId() . '").focus();
                    });
                });
            });
            </script>';
        // return datepicker element
        return $html;
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $key = 'date';
        if (!isset($row[$key])) return;
        $rowId = $row['_id'];
        try {
            $sourceDate = \DateTime::createFromFormat('Y-m-d', $row[$key]);
            $renderedDate = $sourceDate->format('d/m/Y');
            $row[$key] = $renderedDate;
            $columnValues = $row['column_values'];
            $columnValues[$this->_getCellInputElementId($rowId, $key)] = $renderedDate;
            $row['column_values'] = $columnValues;
        } catch (\Exception $e) {
            // Just skipping error values
        }
    }
}