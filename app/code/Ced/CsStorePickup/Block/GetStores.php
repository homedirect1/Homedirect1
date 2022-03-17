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

namespace Ced\CsStorePickup\Block;

use Ced\StorePickup\Model\StoreInfoFactory;
use Ced\StorePickup\Model\StoreFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class GetStores
 * @package Ced\CsStorePickup\Block
 */
class GetStores extends Template
{

    /**
     * @var StoreFactory
     */
    protected $_storesFactory;

    /**
     * @var StoreInfoFactory
     */
    protected $storeInfoFactory;

    /**
     * GetStores constructor.
     * @param Context $context
     * @param StoreFactory $storesFactory
     * @param StoreInfoFactory $storeInfoFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreFactory $storesFactory,
        StoreInfoFactory $storeInfoFactory,
        array $data = []
    )
    {
        $this->_storesFactory = $storesFactory;
        $this->storeInfoFactory = $storeInfoFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getStores()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');

        $collection = $this->storeInfoFactory->create()->getCollection()
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('vendor_id', $vendorId);

        return $collection;
    }

}
