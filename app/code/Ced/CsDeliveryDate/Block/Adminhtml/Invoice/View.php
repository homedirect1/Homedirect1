<?php

/**
 *
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
 * @package     Ced_CsDeliveryDate
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliveryDate\Block\Adminhtml\Invoice;

use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;

class View extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var CollectionFactory
     */
    public $itemCollectionFactory;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->itemCollectionFactory = $itemCollectionFactory;
    }

}