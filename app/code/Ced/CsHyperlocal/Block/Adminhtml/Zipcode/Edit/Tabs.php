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

namespace Ced\CsHyperlocal\Block\Adminhtml\Zipcode\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;

/**
 * Class Tabs
 * @package Ced\CsHyperlocal\Block\Adminhtml\Zipcode\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('id');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('General Information'));

    }

    /**
     * Tabs constructor.
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        array $data = []
    )
    {
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _beforeToHtml()
    {
        $this->addTab('General', array(
            'label' => __('General'),
            'title' => __('General'),
            'content' => $this->getLayout()
                ->createBlock('Ced\CsHyperlocal\Block\Adminhtml\Zipcode\Edit\Tab\Main')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}