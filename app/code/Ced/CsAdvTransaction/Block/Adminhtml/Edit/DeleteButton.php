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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 * @package Magento\Customer\Block\Adminhtml\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Ced\CsAdvTransaction\Model\Fee
     */
    protected $fee;

    /**
     * DeleteButton constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Ced\CsAdvTransaction\Model\Fee $fee
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Block\Widget\Context $context,
        \Ced\CsAdvTransaction\Model\Fee $fee
    )
    {
        $this->request = $request;
        $this->fee = $fee;
        parent::__construct($context, $fee);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $params = $this->request->getParams();
        $data = [];


        if (isset($params['id'])) {
            $fee = $this->fee->load($params['id']);
            if (!$fee->getIsSystem()) {

                $deleteUrl = $this->getUrl('*/*/delete', ['id' => $params['id']]);
                $data = [
                    'label' => __('Delete Fee'),
                    'class' => 'action-secondary',
                    'id' => 'fee-edit-delete-button',
                    'data_attribute' => [
                        'url' => $deleteUrl
                    ],
                    'on_click' => 'deleteConfirm(\'' . __(
                            'Are you sure you want to do this?'
                        ) . '\', \'' . $deleteUrl . '\')',
                    'sort_order' => 20,
                ];
            }


        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getFeeId()]);
    }
}
