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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\System\Config;

/**
 * Class Tax
 * @package Ced\CsAdvTransaction\Block\System\Config
 */
class Tax extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $yesno;

    /**
     * Tax constructor.
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        $this->_elementFactory = $elementFactory;
        $this->yesno = $yesno;
        parent::__construct($context, $data);

    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn('tax', ['label' => __('Tax')]);
        $this->addColumn('enable', ['label' => __('Enable')]);
        $this->addColumn('amount', ['label' => __('Amount in %')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');

    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'enable') {

            $options = $this->yesno->toOptionArray();
            $element = $this->_elementFactory->create('select');
            $element->setForm(
                $this->getForm()
            )->setName(
                $this->_getCellInputElementName($columnName)
            )->setHtmlId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setValues(
                $options
            );
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }
}
