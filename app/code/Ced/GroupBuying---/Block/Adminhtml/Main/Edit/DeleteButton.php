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
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Block\Adminhtml\Main\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{


    /**
     * Get delete button data
     *
     * @return array
     */
    public function getButtonData(): array
    {
        if ($this->getObjectId() === false) {
            return [];
        }

        return [
            'label'      => __('Delete Group'),
            'class'      => 'delete',
            'on_click'   => 'deleteConfirm( \''.__(
                'Are you sure you want to do this?'
            ).'\', \''.$this->getDeleteUrl().'\')',
            'sort_order' => 20,
        ];

    }//end getButtonData()


}//end class
