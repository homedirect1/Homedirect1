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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Block\Adminhtml\Category\Checkboxes;

use Magento\Framework\Data\Tree\Node;

/**
 * Class Tree
 * @package Ced\CsPromotions\Block\Adminhtml\Category\Checkboxes
 */
class Tree extends \Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree
{
    /**
     * @var int[]
     */
    protected $_selectedIds = [];

    /**
     * @var array
     */
    protected $_expandedPath = [];

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * Tree constructor.
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        array $data = []
    )
    {
        $this->state = $state;
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $jsonEncoder, $resourceHelper, $backendSession, $data);
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $state = $this->state;
        if ($state->getAreaCode() == 'adminhtml') {
            $this->setTemplate('Magento_Catalog::catalog/category/checkboxes/tree.phtml');
        } else {
            $this->setTemplate('catalog/category/checkboxes/tree.phtml');
        }
    }

    /**
     * @return int[]
     */
    public function getCategoryIds()
    {
        return $this->_selectedIds;
    }

    /**
     * @param mixed $ids
     * @return $this
     */
    public function setCategoryIds($ids)
    {
        if (empty($ids)) {
            $ids = [];
        } elseif (!is_array($ids)) {
            $ids = [(int)$ids];
        }
        $this->_selectedIds = $ids;
        return $this;
    }

    /**
     * @return array
     */
    protected function getExpandedPath()
    {
        return $this->_expandedPath;
    }

    /**
     * @param string $path
     * @return $this
     */
    protected function setExpandedPath($path)
    {
        $this->_expandedPath = array_merge($this->_expandedPath, explode('/', $path));
        return $this;
    }

    /**
     * @param array|Node $node
     * @param int $level
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = [];
        $item['text'] = $this->escapeHtml($node->getName());
        $item['id'] = $node->getId();
        $item['path'] = $node->getData('path');
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $item['allowDrop'] = false;
        $item['allowDrag'] = false;
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $this->setExpandedPath($node->getData('path'));
            $item['checked'] = true;
        }
        if ($node->getLevel() < 2) {
            $this->setExpandedPath($node->getData('path'));
        }
        if ($node->hasChildren()) {
            $item['children'] = [];
            foreach ($node->getChildren() as $child) {

                if ($this->_scopeConfig->getValue('ced_vproducts/general/category_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                    if ($categories = $this->_scopeConfig->getValue('ced_vproducts/general/category', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                        $allowed_categories = explode(',', $categories);

                        if (!empty($allowed_categories)) {
                            if (!in_array($child->getId(), $allowed_categories)) {
                                continue;
                            }
                        }
                    }
                }


                $item['children'][] = $this->_getNodeJson($child, $level + 1);
            }
        }
        if (empty($item['children']) && (int)$node->getChildrenCount() > 0) {
            $item['children'] = [];
        }
        $item['expanded'] = in_array($node->getId(), $this->getExpandedPath());
        return $item;
    }
}
