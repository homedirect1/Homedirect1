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

namespace Ced\CsHyperlocal\Block\Shiparea\Renderer;

/**
 * Class ColumnAction
 * @package Ced\CsHyperlocal\Block\Shiparea\Renderer
 */
class ColumnAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * ColumnAction constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->csmarketplaceHelper = $csmarketplaceHelper;
    }

    /**
     * Render action
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $filterType = $this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE);
        $html = '<button class="shiparea-action" id="action'.$row->getId().'">'.__("Select").'</button><ul class="action-ul">
                    <li value="edit"><a href="' . $this->getUrl('*/*/edit', ['id' => $row->getId()]) . '">' . __('edit') . '</a></li>
                    <li value="delete"><a href="' . $this->getUrl('*/*/delete', ['id' => $row->getId()]) . '">' . __('Delete') . '</a></li>';
        if ($row->getZipcodeType() == 'multiple' && $filterType == 'zipcode') {
            $html .= '<li value="manage_zipcode"><a href="' . $this->getUrl('*/*/managezipcode', ['id' => $row->getId()]) . '">' . __('Manage Zipcodes') . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }
}

