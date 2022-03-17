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
 * @package     Ced_CsDeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliveryDate\Block\Vsettings;

/**
 * Class Timestamp
 * @package Ced\CsDeliveryDate\Block\Vsettings
 */
class Timestamp extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $session;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory
     */
    public $vsettingsCollectionFactory;

    /**
     * @var \Ced\CsMarketplace\Model\VsettingsFactory
     */
    public $vsettingsFactory;


    /**
     * Timestamp constructor.
     * @param \Magento\Customer\Model\Session $session
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory $vsettingsCollectionFactory
     * @param \Ced\CsMarketplace\Model\VsettingsFactory $vsettingsFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory $vsettingsCollectionFactory,
        \Ced\CsMarketplace\Model\VsettingsFactory $vsettingsFactory,
        \Magento\Framework\View\Element\Template\Context $context
    )
    {
        $this->setTemplate('Ced_CsDeliveryDate::vsettings/timestamp.phtml');
        parent::__construct($context);

        $this->session = $session;
        $this->vsettingsCollectionFactory = $vsettingsCollectionFactory;
        $this->vsettingsFactory = $vsettingsFactory;
    }

}