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
 * @package     Ced_DeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\DeliveryDate\Model\Config;

class CustomHoursForOpeningTime extends \Magento\Framework\View\Element\Html\Select
{

    public function __construct(
        \Magento\Framework\View\Element\Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $time=[];
            for($i=0;$i<24;$i++){
                $time[] = ($i < 10) ? '0'.$i.':00': $i.':00';
            }

            foreach ($time as $key=>$value) {
                $this->addOption($key, $value );
            }
        }
        return parent::_toHtml();
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
