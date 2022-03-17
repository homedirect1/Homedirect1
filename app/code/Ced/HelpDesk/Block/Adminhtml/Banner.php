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
 * @category    Ced
 * @package     Ced_HelpDesk
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Block\Adminhtml;

/**
 * Class Banner
 * @package Ced\HelpDesk\Block\Adminhtml
 */
class Banner extends \Magento\Backend\Block\Template
{
    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    public $_storeManager;

    /**
     * Banner constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Magento\Backend\Block\Template\Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Magento\Backend\Block\Template\Context $context
    )
    {
        $this->helpdeskHelper = $helpdeskHelper;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function bannerInfo()
    {
        $bannerImage = $this->helpdeskHelper->getStoreConfig('helpdesk/frontend/banner');
        return $bannerImage;
    }

    /**
     * @return mixed
     */
    public function welcomeMessage()
    {
        return $this->helpdeskHelper->getStoreConfig('helpdesk/frontend/welcome_msg');
    }

    /**
     * @return mixed
     */
    public function welcomeDesc()
    {
        return $this->helpdeskHelper->getStoreConfig('helpdesk/frontend/welcone_desc');
    }
}