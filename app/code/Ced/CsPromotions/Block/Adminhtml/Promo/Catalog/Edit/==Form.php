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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Block\Adminhtml\Promo\Catalog\Edit;

/**
 * Adminhtml sales order view plane
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Template
     *
     * @var string
     */

    public function __construct(
         \Magento\Backend\Block\Template\Context $context,
         \Magento\Framework\Registry $registry,
         \Magento\Framework\Data\FormFactory $formFactory,
         array $data = []
    )
    {
        $this->setId('promo_catalog_form');
        $this->setTitle(__('Rule Information'));
        $this->setData('area', 'adminhtml');
        parent::__construct($context, $registry, $formFactory,$data);
    }

    /**
     * @return WidgetForm
     */
    protected function _prepareForm()
    {die('adminhtml');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('catalog_rule/promo_catalog/save'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }


    protected function getSaveUrl()
    {
        return $this->getUrl('cspromotions/promo_catalog/save');
    }

}
