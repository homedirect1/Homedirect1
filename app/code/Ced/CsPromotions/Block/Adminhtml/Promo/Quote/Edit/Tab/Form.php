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

namespace Ced\CsPromotions\Block\Adminhtml\Promo\Quote\Edit;

/**
 * description
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Form
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('promo_quote_form');
        $this->setTitle(__('Rule Information'));
    }

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
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('cspromotions/promo_quote/save'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
