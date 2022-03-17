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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsHyperlocal\Model\Config\Source;

/**
 * Class ShipareaStatus
 * @package Ced\CsHyperlocal\Model\Config\Source
 */
class ShipareaStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options array
     *
     * @var array
     */
    protected $_options;

    /**
     * Return options array
     *
     * @param boolean $isMultiselect
     * @param string|array $foregroundCountries
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        $options = [['value' => \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED, 'label' => __('Enabled')],
                    ['value'=>\Ced\CsHyperlocal\Model\Shiparea::STATUS_DISABLED,'label'=>__('Disabled')]
                    ];
        return $options;
    }
}
