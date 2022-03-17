<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsProductReview
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProductReview\Block\Review;

/**
 * Review edit form
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Review action pager
     *
     * @var \Magento\Review\Helper\Action\Pager
     */
    protected $_reviewActionPager = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Review model factory
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Helper\Action\Pager $reviewActionPager
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Helper\Action\Pager $reviewActionPager,
        \Magento\Framework\Registry $registry,
    	\Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
    	$this->_moduleManager = $moduleManager;
        $this->_coreRegistry = $registry;
        $this->_reviewActionPager = $reviewActionPager;
        $this->_reviewFactory = $reviewFactory;
        parent::__construct($context, $data);
        $this->setData('area','adminhtml');
    }

    /**
     * Initialize edit review
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        parent::_construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'Ced_CsProductReview';
        $this->_controller = 'review';

        /** @var $actionPager \Magento\Review\Helper\Action\Pager */
        $actionPager = $this->_reviewActionPager;
        $actionPager->setStorageId('reviews');

        $reviewId = $this->getRequest()->getParam('id');
        $prevId = $actionPager->getPreviousItemId($reviewId);
        $nextId = $actionPager->getNextItemId($reviewId);
        if ($prevId !== false) {
            $this->addButton(
                'previous',
                [
                    'label' => __('Previous'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('productreview/*/*', ['id' => $prevId]) . '\')'
                ],
                3,
                10
            );

            $this->addButton(
                'save_and_previous',
                [
                    'label' => __('Save and Previous'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => ['action' => ['args' => ['next_item' => $prevId]]],
                            ],
                        ],
                    ]
                ],
                3,
                11
            );
        }
        if ($nextId !== false) {
            $this->addButton(
                'save_and_next',
                [
                    'label' => __('Save and Next'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => ['action' => ['args' => ['next_item' => $nextId]]],
                            ],
                        ],
                    ]
                ],
                3,
                100
            );

            $this->addButton(
                'next',
                [
                    'label' => __('Next'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('productreview/*/*', ['id' => $nextId]) . '\')'
                ],
                3,
                105
            );
        }
        $this->buttonList->update('save', 'label', __('Save Review'));
        $this->buttonList->update('save', 'id', 'save_button');
        $this->buttonList->update('delete', 'label', __('Delete Review'));

        if ($this->getRequest()->getParam('productId', false)) {
	        	if($this->_moduleManager->isEnabled('Ced_CsProduct')){
	            $this->buttonList->update(
	                'back',
	                'onclick',
	                'setLocation(\'' . $this->getUrl(
	                    'csproduct/vproducts/edit',
	                    ['id' => $this->getRequest()->getParam('productId', false)]
	                ) . '\')'
	            );
        	}else{
        		$this->buttonList->update(
        				'back',
        				'onclick',
        				'setLocation(\'' . $this->getUrl(
        						'csmarketplace/vproducts/edit',
        						['id' => $this->getRequest()->getParam('productId', false)]
        				) . '\')'
        		);
        	}
        }

        if ($this->getRequest()->getParam('customerId', false)) {
            $this->buttonList->update(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl(
                    'customer/index/edit',
                    ['id' => $this->getRequest()->getParam('customerId', false)]
                ) . '\')'
            );
        }

        if ($this->getRequest()->getParam('ret', false) == 'pending') {
            $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getUrl('catalog/*/pending') . '\')');
            $this->buttonList->update(
                'delete',
                'onclick',
                'deleteConfirm(' . '\'' . __(
                    'Are you sure you want to do this?'
                ) . '\' ' . '\'' . $this->getUrl(
                    '*/*/delete',
                    [$this->_objectId => $this->getRequest()->getParam($this->_objectId), 'ret' => 'pending']
                ) . '\'' . ')'
            		
            );
            $this->_coreRegistry->register('ret', 'pending');
        }

        if ($this->getRequest()->getParam($this->_objectId)) {
            $reviewData = $this->_reviewFactory->create()->load($this->getRequest()->getParam($this->_objectId));
            $this->_coreRegistry->register('review_data', $reviewData);
        }

        $this->_formInitScripts[] = '
            var review = {
                updateRating: function() {
                        elements = [
                            $("select_stores"),
                            $("rating_detail").getElementsBySelector("input[type=\'radio\']")
                        ].flatten();
                        $(\'save_button\').disabled = true;
                        new Ajax.Updater(
                            "rating_detail",
                            "' .
            $this->getUrl(
                'review/*/ratingItems',
                ['_current' => true]
            ) .
            '",
                            {
                                parameters:Form.serializeElements(elements),
                                evalScripts:true,
                                onComplete:function(){ $(\'save_button\').disabled = false; }
                            }
                        );
                    }
           }
           
        ';
    }

    /**
     * Get edit review header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $reviewData = $this->_coreRegistry->registry('review_data');
        if ($reviewData && $reviewData->getId()) {
            return __("Edit Review '%1'", $this->escapeHtml($reviewData->getTitle()));
        } else {
            return __('New Review');
        }
    }
}
