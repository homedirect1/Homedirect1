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
 * @package     Ced_CsDeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliveryDate\Model\Vsettings;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Holiday
 * @package Ced\DeliveryDate\Model
 */
class Deliveryweeks extends AbstractModel
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $weekLists;
     /**
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     */
    public function __construct(\Magento\Framework\Locale\ListsInterface $weekLists)
    {
        $this->weekLists = $weekLists;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->weekLists->getOptionWeekdays();

        return $options;
    }
    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value) {
        return $this->setName($value);
    }
}