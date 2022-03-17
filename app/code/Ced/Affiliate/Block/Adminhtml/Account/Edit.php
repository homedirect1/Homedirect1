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

namespace Ced\Affiliate\Block\Adminhtml\Account;

/**
 * Class Edit
 * @package Ced\Affiliate\Block\Adminhtml\Account
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'ced_affiliate';
        $this->_controller = 'adminhtml_account';

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

            $this->addButton(
                'save_and_edit_button',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ]
            );
        }
        $this->buttonList->update('save', 'label', __('Save'));

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
    public function getDeleteUrl()
    {

        return $this->getUrl('affiliate/manage/delete', array('id' => $this->getRequest()->getParam('id')));
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('affiliate/manage/account');
    }
}