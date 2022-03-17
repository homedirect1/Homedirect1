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

namespace Ced\Affiliate\Block\Adminhtml\Account\Banner;

/**
 * Class Edit
 * @package Ced\Affiliate\Block\Adminhtml\Account\Banner
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
        $this->_controller = 'adminhtml_account_banner';

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
    public function getSaveUrl()
    {
        return $this->getUrl(
            'affiliate/banner/save',
            ['_current' => true, 'back' => null, 'id' => $this->getRequest()->getParam('id')]
        );
    }
}