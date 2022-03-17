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
namespace Ced\Affiliate\Block\Adminhtml\Discount\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Blog post edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {
    /**
     *
     * @var Store
     */
    protected $_systemStore;

    /**
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Store $systemStore, \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, array $data = []) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct ( $context, $registry, $formFactory, $data );
    }

    /**
     * Prepare form
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm() {
        $data = $this->_coreRegistry->registry ( 'discount_form_data' );
        $isElementDisabled = false;

        /** @var  $form Form */
        $form = $this->_formFactory->create ();

        $form->setHtmlIdPrefix ( 'page_' );

        $fieldset = $form->addFieldset ( 'base_fieldset', [
            'legend' => __ ( 'Discount Denomination Rule' )
        ] );

        $fieldset->addField ( 'rule_name', 'text', [
            'name' => 'rule_name',
            'label' => __ ( 'Rule Name' ),
            'title' => __ ( 'Rule Name' ),
            'required' => true,
            'disabled' => $isElementDisabled ,
            'class' =>'alphanumeric'
        ] );

        $fieldset->addField ( 'discount_amount', 'text', [
            'name' => 'discount_amount',
            'label' => __ ( 'Discount Amount' ),
            'title' => __ ( 'Discount Amount' ),
            'required' => true,
            'disabled' => $isElementDisabled,
            'class' => 'validate-greater-than-zero'
        ] );

        $fieldset->addField ( 'cart_amount', 'text', [
            'name' => 'cart_amount',
            'label' => __ ( 'Cart Amount' ),
            'title' => __ ( 'Cart Amount' ),
            'required' => true,
            'disabled' => $isElementDisabled,
            'class' => 'validate-greater-than-zero'
        ] );


        $fieldset->addField ( 'status', 'select', [
            'name' => 'status',
            'label' => __ ( 'Status' ),
            'title' => __ ( 'Status' ),
            'required' => true,
            'values' => array (
                '-1' => __('Please Select..'),
                '0' => __('Disable'),
                '1' => __('Enable')
            )
        ] );

        if ($data && !empty($data->getId())) {
            $fieldset->addField ( 'id', 'hidden', [
                'name' => 'id'
            ] );
            $form->setValues($data);
        }

        $this->setForm($form);

        return parent::_prepareForm ();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel() {
        return __ ( 'Discount Denomination' );
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle() {
        return __ ( 'Discount Denomination' );
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function canShowTab() {
        return true;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function isHidden() {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId) {
        return $this->_authorization->isAllowed ( $resourceId );
    }
}