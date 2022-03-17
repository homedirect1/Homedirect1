<?php

namespace Ced\CsSms\Block\System\Config\Form\Field;

/**
 * Backend system config array field renderer
 */
class Countrycode extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $_country;

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
        $this->_country = $this->_objectManager->create('\Magento\Directory\Model\ResourceModel\Country\Collection');
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn('country', ['label' => __('Country')]);
        $this->addColumn('code', ['label' => __('Code')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Country Codes');
        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'country' && isset($this->_columns[$columnName])) {

            $options = $this->_country->toOptionArray('Select Country');
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
