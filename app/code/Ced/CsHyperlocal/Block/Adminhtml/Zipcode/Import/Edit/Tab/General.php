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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Block\Adminhtml\Zipcode\Import\Edit\Tab;

/**
 * Class General
 * @package Ced\PincodeChecker\Block\Adminhtml\Import\Edit\Tab
 */
class General extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * General constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        if ($this->_isAllowedAction('Magento_Cms::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');


        $fieldset = $form->addFieldset('import', ['legend' => __('Import CSV')]);

        $fieldset->addField('location_id', 'hidden', ['name' => 'location_id']);

        $fieldset->addField(
            'import_csv',
            'file',
            [
                'name' => 'import_csv',
                'label' => __('Import CSV'),
                'title' => __('Import CSV'),
                'required' => false
            ]
        );

        $fieldset = $form->addFieldset('export', ['legend' => __('Export CSV')]);

        $locationId = $this->getRequest()->getParam('location_id');
        $locationIdArray = ['location_id' => $locationId];
        $form->setValues($locationIdArray);

        $script = $fieldset->addField(
            'export_csv',
            'button',
            [
                'name' => 'export_csv',
                'label' => __('Export CSV'),
                'title' => __('Export CSV'),
                'required' => false,
                /*'class' => 'action-default scalable',*/
                'value' => __('Export CSV'),
                'onclick' => "setLocation('" . $this->getUrl('*/*/export', ['id' => $locationId]) . "')",
                'after_element_html' => '<p class="note"><span>Export the format of CSV Before Importing.<span></p>'
            ]
        );


        $script->setAfterElementHtml("<script type=\"text/javascript\">
            document.getElementById('save').onclick = function() {
                document.getElementById('import_form').submit();
            };
            </script>");


        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
