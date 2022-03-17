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
 * @package   Ced_CsSubAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Block\Customer;

/**
 * Class NewBlock
 * @package Ced\CsSubAccount\Block\Customer
 */
class NewBlock extends \Magento\Framework\View\Element\Template
{

    /**
     * Prepare link attributes as serialized and formatted string
     *
     * @return string
     */
    const XML_PATH_TEMPLATE_ALLOW_SYMLINK = 'dev/template/allow_symlink';

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * NewBlock constructor.
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        $this->customerCollectionFactory = $customerCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function getCustomerCollection()
    {
        return $this->customerCollectionFactory->create();

    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeader()
    {
        return __('Send Invitation for sub-vendor role');
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {


        $this->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Back'),
                'title' => __('Back'),
                'onclick' => 'window.location.href="' . $this->getUrl(
                        'cssubaccount/customer/') . '"',
                'class' => 'action-back'
            ]
        );

        $this->addChild(
            'send_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Send'),
                'title' => __('Send'),
                'class' => 'action-save primary'
            ]
        );

        return parent::_prepareLayout();
    }

}
