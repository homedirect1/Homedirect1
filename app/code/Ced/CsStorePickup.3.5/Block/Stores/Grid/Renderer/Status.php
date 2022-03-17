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
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Block\Stores\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Status
 * @package Ced\CsStorePickup\Block\Stores\Grid\Renderer
 */
class Status extends AbstractRenderer
{

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $active = $row->getSetIsActive();
        if ($active != 1) {
            $html = "Enabled";
        } else {
            $html = "Disabled";
        }
        return $html;
    }
}