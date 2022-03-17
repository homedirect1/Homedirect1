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

namespace Ced\CsAdvTransaction\Block\Adminhtml\Payments;

use Magento\Backend\Block\Widget\Context;

/**
 * Class View
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Payments
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * View constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    )
    {
        $this->_controller = '';
        parent::__construct($context, $data);
        $this->_headerText = __('Transaction Order View');
        $this->removeButton('reset')
            ->removeButton('delete')
            ->removeButton('save')
            ->removeButton('back');


        $params = $this->getRequest()->getParams();
        $url = $this->getUrl('csadvtransaction/pay/order', ['vendor_id' => $params['vendor_id']]);
        if (isset($params['vpayment_id'])) {
            $url = $this->getUrl('csadvtransaction/pay/details', ['id' => $params['vpayment_id']]);
        }

        $this->updateButton('back', 'onclick', "setLocation('" . $url . "')");
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Form\Container
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild('form', $this->getLayout()->createBlock('Ced\CsAdvTransaction\Block\Adminhtml\Payments\View\Form'));
        return $this;
    }
}