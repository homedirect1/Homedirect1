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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Block\Adminhtml\Withdrawl;

/**
 * Class Edit
 * @package Ced\Affiliate\Block\Adminhtml\Withdrawl
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'ced_affiliate';
        $this->_controller = 'adminhtml_withdrawl';

        parent::_construct();
        if ($this->getRequest()->getParam('popup')) {
            $this->buttonList->remove('back');


            if ($this->getRequest()->getParam('product_tab') != 'variations') {
                $this->addButton(
                    'close',
                    [
                        'label' => __('Close Window'),
                        'class' => 'cancel',
                        'onclick' => 'window.close()',
                        'level' => -1
                    ],
                    -100
                );
            }
        } else {


        }

        if ($this->_coreRegistry->registry('withdrawl_request')->getStatus() == 0) {

            $this->getToolbar()->addChild(
                'cancel',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Cancel'),
                    'class' => 'cancel',
                    'onclick' => 'deleteConfirm(\'' . __(
                            'Are you sure you want to cancel?'
                        ) . '\', \'' . $this->getCancelUrl() . '\')'
                ]
            );
            $this->buttonList->remove('delete');
            $this->buttonList->remove('reset');

            $this->buttonList->update('save', 'label', __('Pay Offline'));
        } else {
            $this->buttonList->remove('save');
            $this->buttonList->remove('delete');
            $this->buttonList->remove('reset');
        }

    }

    /**
     * {@inheritdoc}
     */
    public function addButton($buttonId, $data, $level = 0, $sortOrder = 0, $region = 'toolbar')
    {
        if ($this->getRequest()->getParam('popup')) {
            $region = 'header';
        }
        parent::addButton($buttonId, $data, $level, $sortOrder, $region);
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {

        return $this->getUrl('affiliate/withdrawl/cancel', array('id' => $this->getRequest()->getParam('id')));
    }
}