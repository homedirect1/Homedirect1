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
 * @package     Ced_HelpDesk
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Block\Adminhtml\Manage\Edit\Tab;

/**
 * Class Reply
 * @package Ced\HelpDesk\Block\Adminhtml\Manage\Edit\Tab
 */
class Reply extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    public $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory
     */
    protected $statusCollectionFactory;

    public $_storeManager;

    /**
     * Reply constructor.
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory $statusCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory $statusCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->authSession = $authSession;
        $this->statusCollectionFactory = $statusCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $status = [];
        $username = $this->authSession->getUser()->getData('username');
        $statusModel = $this->statusCollectionFactory->create();
        foreach ($statusModel as $value) {
            $status[] = ['value' => $value->getCode(),
                'label' => $value->getTitle()
            ];
        }
        $model = $this->_coreRegistry->registry('ced_ticket_data');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Reply')]);
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        if ($model = $this->_coreRegistry->registry('ced_ticket_data')) {
            if (isset($model['ticket_id']) && !empty($model['ticket_id'])) {
                $fieldset->addField(
                    'ticket_id',
                    'hidden',
                    ['name' => 'ticket_id',
                        'value' => $model['ticket_id']
                    ]);
            }
            if (isset($username) && !empty($username)) {
                $fieldset->addField(
                    'from',
                    'hidden',
                    ['name' => 'from',
                        'value' => $username
                    ]);
            }
            if (isset($model['customer_email']) && !empty($model['customer_email'])) {
                $fieldset->addField(
                    'to',
                    'hidden',
                    ['name' => 'to',
                        'value' => $model['customer_email']
                    ]);
            }
            if (isset($model['id']) && !empty($model['id'])) {
                $fieldset->addField(
                    'id',
                    'hidden',
                    ['name' => 'id',
                        'value' => $model['id']
                    ]);
            }
        }
        $fieldset->addField(
            'reply_description',
            'editor',
            [
                'name' => 'reply_description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:10em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );
        if ($model = $this->_coreRegistry->registry('ced_ticket_data')) {
            $fieldset->addField(
                'status',
                'select',
                [
                    'name' => 'status',
                    'label' => __('Status'),
                    'title' => __('Status'),
                    'values' => $status,
                    'value' => $model['status']
                ]
            );
        }
        $fieldset->addField(
            'signature',
            'select',
            [
                'name' => 'signature',
                'label' => __('Apply Signature'),
                'title' => __('Apply Signature'),
                'values' => [['label' => __('Yes'), 'value' => 1], ['label' => __('No'), 'value' => 0]],
            ]
        );
        $fieldset->addField(
            'attachment',
            'file', [
                'label' => __('Attach File'),
                'name' => 'attachment',
            ]
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}