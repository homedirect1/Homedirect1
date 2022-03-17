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
 * Class Resources
 * @package Ced\CsSubAccount\Block\Customer
 */
class Resources extends \Magento\Framework\View\Element\Template
{
    /**
     * Prepare link attributes as serialized and formatted string
     *
     * @return string
     */
    const XML_PATH_TEMPLATE_ALLOW_SYMLINK = 'dev/template/allow_symlink';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * Resources constructor.
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->csSubAccountFactory = $csSubAccountFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);

        $this->setData('area', 'adminhtml');
        $id = $context->getRequest()->getParam('id', false);
        $role = $this->csSubAccountFactory->create()->load($id)->getRole();
        if ($role) {
            $res = explode(',', $role);
            $this->setSelectedResources($res);
        } else {
            $res = array('all');
            $this->setSelectedResources($res);
        }

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
        return __('Assign Resources To Sub - Vendors');
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
                        'cssubaccount/customer/index') . '"',
                'class' => 'action-back'
            ]
        );

        $this->addChild(
            'send_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Save'),
                'title' => __('Save'),
                'class' => 'action-save primary'
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * @return bool
     */
    public function isEverythingAllowed()
    {
        return in_array('all', $this->getSelectedResources());
    }

    /**
     * Compare two nodes of the Resource Tree
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
    }

    /**
     * Get Node Json
     *
     * @param mixed $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = array();
        $selres = $this->getSelectedResources();

        if ($level != 0) {
            $item['text'] = __((string)$node->title);
            $item['sort_order'] = isset($node->sort_order) ? (string)$node->sort_order : 0;
            $item['id'] = (string)$node->attributes()->aclpath;

            if (in_array($item['id'], $selres))
                $item['checked'] = true;
        }
        if (isset($node->children)) {
            $children = $node->children->children();
        } else {
            $children = $node->children();
        }
        if (empty($children)) {
            return $item;
        }

        if ($children) {
            $item['children'] = array();
            foreach ($children as $child) {
                if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
                    if (!(string)$child->title) {
                        continue;
                    }
                    if ($level != 0) {
                        $item['children'][] = $this->_getNodeJson($child, $level + 1);
                    } else {
                        $item = $this->_getNodeJson($child, $level + 1);
                    }
                }
            }
            if (!empty($item['children'])) {
                usort($item['children'], array($this, '_sortTree'));
            }
        }
        return $item;
    }

    /**
     * @return string[]
     */
    public function notAllowedSections(){
        return [
            "vendor_dashboard",
            "vendor_profile",
            "profile_edit"
        ];
    }
    /**
     * @return array
     */
    public function getTree()
    {
        $root = array();

        $resources = $this->_scopeConfig->getValue('vendor_acl');
        $i = 0;
        foreach ($resources['resources']['vendor']['children'] as $key => $value) {
            $arr2 = [];
            if(in_array($key, $this->notAllowedSections())){
                continue;
            }
            if (isset($value['paths'])) {
                $value['path'] = $value['paths'];

            }
            if (isset($value['ifconfig']) && !$this->_scopeConfig->getValue($value['ifconfig'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && !isset($value['dependsonparent']))
                continue;
            elseif (isset($value['ifconfig']) && !$this->_scopeConfig->getValue($value['ifconfig'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {

                $value = $value['dependsonparent'][$key];
            }

            if (isset($value['children']) && is_array($value['children']) && !empty($value['children'])) {
                $j = 0;
                foreach ($value['children'] as $key2 => $value2) {
                    if (isset($value2['ifconfig']) && !$this->_scopeConfig->getValue($value2['ifconfig'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
                        continue;
                    $children = array('attr' => array('data-id' => $value2['path']), 'data' => $value2['title'], 'state' => 'open', 'path' => $value2['path']);
                    $arr2[$j] = $children;
                    $j++;

                }
                if (isset($value['path'])) {
                    if ($value['path'] == "#")
                        $arr = array('attr' => array('data-id' => $value['path'] . $key), 'data' => $value['title'], 'state' => 'open', 'path' => $value['path'], 'children' => $arr2);
                    else
                        $arr = array('attr' => array('data-id' => $value['path']), 'data' => $value['title'], 'state' => 'open', 'path' => $value['path'], 'children' => $arr2);
                }
            } else {

                $arr = array('attr' => array('data-id' => $value['path']), 'data' => $value['title'], 'state' => 'open', 'path' => $value['path'], 'children' => $arr2);
            }

            $root[$i] = $arr;
            $i++;
        }
        return $root;
    }

}
