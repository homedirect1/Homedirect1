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
 * @package     Ced_DeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\DeliveryDate\Block\Adminhtml\System;

class Timestamp extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    protected $_elementFactory;

    protected $_columns = [];
    protected $_customerGroupRenderer;
    protected $_customHours;
    protected $_customHoursForOpeningTime;
    protected $_addAfter = true;
    protected $_addButtonLabel;

    protected function _prepareToRender()
    {

        $this->addColumn('startTime',
            ['label' => __('Opening Time'),
                'renderer' => $this->getCustomHoursforOpeningTime(),
            ]);
        $this->addColumn('endTime',
            ['label' => __('Closing Time'),
                'renderer' => $this->getCustomHours(),
            ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Timestamp');
    }

    protected function getCustomHoursforOpeningTime()
    {
        if (!$this->_customHoursForOpeningTime) {
            $this->_customHoursForOpeningTime = $this->getLayout()->createBlock(
                '\Ced\DeliveryDate\Model\Config\CustomHoursForOpeningTime',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_customHoursForOpeningTime;
    }

    protected function getCustomHours()
    {
        if (!$this->_customHours) {
            $this->_customHours = $this->getLayout()->createBlock(
                '\Ced\DeliveryDate\Model\Config\CustomHoursForClosingTime',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_customHours;
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {

        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getCustomHoursforOpeningTime()->calcOptionHash($row->getData('startTime'))] =
            'selected="selected"';

        $optionExtraAttr['option_' . $this->getCustomHours()->calcOptionHash($row->getData('endTime'))] =
            'selected="selected"';
        $row->setData('option_extra_attrs', $optionExtraAttr);

    }
}