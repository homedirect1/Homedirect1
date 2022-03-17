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

namespace Ced\CsHyperlocal\Block\Link;
use Magento\Customer\Model\Session;

/**
 * Block representing link with two possible states.
 * "Current" state means link leads to URL equivalent to URL of currently displayed page.
 *
 * @method string                          getLabel()
 * @method string                          getPath()
 * @method string                          getTitle()
 * @method null|bool                       getCurrent()
 * @method \Magento\Framework\View\Element\Html\Link\Current setCurrent(bool $value)
 */
class Current extends \Ced\CsMarketplace\Block\Link\Current
{
    /**
     * Default path
     *
     * @var \Magento\Framework\App\DefaultPathInterface
     */
    protected $_defaultPath;
    /**
     * @var Session
     */
    protected $_session;
    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context,$defaultPath, $data);
        $this->_defaultPath = $defaultPath;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->_session = $customerSession;
        $this->csSubAccountFactory = $csSubAccountFactory;
    }

    /**
     * Render block HTML
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $subVendor = $this->_session->getSubVendorData();
        
        if (empty($subVendor) && $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())) == 'cssubaccount_profile') {
            return;
        } elseif (!empty($subVendor) && $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())) == 'cssubaccount') {
            return;
        } elseif (!empty($subVendor) && $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())) == 'vendor_profile') {
            return;
        }

        /** check if distance type filter enable */
        $filterType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE);
        if ($this->getName() == 'ced_hyperlocal' && $filterType == 'distance')
            return false;


        $highlight = '';
        if ($this->isCurrent()) {
            $highlight = ' active';
        }

        if (0) {
            $html = '<li id="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())).'" class="nav item">';
            $html .= '<i class="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getFontAwesome())).'"></i>';
            $html .= '<span><strong style="margin-left: 3px;">'
                . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()))
                . '</strong></span>';


            $childHtml = $this->getChildHtml();
            if(strlen($childHtml) > 0) {
                $html .= '<span class="fa arrow"></span>';
            }

            if(strlen($childHtml) > 0) {
                $html .= $childHtml;
                $html .= '<div class="largeview-submenu">';
                $html .= $childHtml;
                $html .= '</div>';
            }

            $html .= '</a>';
            $html .= '</li>';
        } else {
            if (!empty($subVendor)) {
                $resources = $this->csSubAccountFactory->create()->load($subVendor['id'])->getRole();
                if ($resources !== 'all') {
                    $stringpath = $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getPath()));
                    if ($stringpath == "#") {
                        $stringpath = $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getPath())) . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName()));
                    } else {
                        $stringpath = $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getPath()));
                        $stringpath = explode("/", $stringpath);
                        if (!empty($stringpath)) {
                            if (!isset($stringpath[1]) || !$stringpath[1])
                                $stringpath[1] = "index";

                            if (!isset($stringpath[2]) || !$stringpath[2])
                                $stringpath[2] = "index";

                            $stringpath = $stringpath[0] . "/" . $stringpath[1] . "/" . $stringpath[2];
                        }
                    }
                    $role = explode(',', $resources);
                    if (in_array($stringpath, $role) || ($this->getName() === 'vendor_dashboard') || in_array($stringpath.'/', $role)) {
                        $html = '<li id="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())) . '" class="nav item"><a class="' . $highlight . '" href="' . $this->escapeHtml($this->getHref()) . '"';
                        $html .= $this->getTitle()
                            ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                            : '';
                        $html .= '>';
                        $html .= '<i class="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getFontAwesome())) . '"></i>';

                        if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                            $html .= '<span><strong style="margin-left: 3px;">';
                        } else {
                            $html .= '<span style="margin-left: 3px;">';
                        }

                        $html .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));

                        if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                            $html .= '</strong></span>';
                        } else {
                            $html .= '</span>';
                        }

                        $childHtml = '';
                        $childHtml = $this->getChildHtml();

                        if (strlen($childHtml) > 0) {
                            $html .= '<span class="fa arrow"></span>';
                        }

                        $html .= '</a>';

                        if (strlen($childHtml) > 0) {
                            $html .= $childHtml;
                            $html .= '<div class="largeview-submenu">';
                            $html .= $childHtml;
                            $html .= '</div>';
                        }
                        $html .= '</li>';
                        return $html;
                    } else {
                        return;
                    }
                } else {

                    $html = '
                <li id="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())) . '" class="nav item"><a class="' . $highlight . '" href="' . $this->escapeHtml($this->getHref()) . '"';
                    $html .= $this->getTitle()
                        ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                        : '';
                    $html .= '>';
                    $html .= '
                <i class="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getFontAwesome())) . '"></i>';

                    if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                        $html .= '<span><strong style="margin-left: 3px;">';
                    } else {
                        $html .= '<span style="margin-left: 3px;">';
                    }

                    $html .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));

                    if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                        $html .= '</strong></span>';
                    } else {
                        $html .= '</span>';
                    }

                    $childHtml = '';
                    $childHtml = $this->getChildHtml();
                    if (strlen($childHtml) > 0) {
                        $html .= '
                    <span class="fa arrow"></span>';
                    }

                    $html .= '</a>';

                    if (strlen($childHtml) > 0) {
                        $html .= $childHtml;
                        $html .= '<div class="largeview-submenu">';
                        $html .= $childHtml;
                        $html .= '</div>';
                    }
                    $html .= '</li>';
                }
            }
            $html = '<li id="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())).'" class="nav item"><a class="' . $highlight . '" href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                : '';
            $html .= '>';
            $html .= '<i class="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getFontAwesome())).'"></i>';

            if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                $html .= '<span><strong style="margin-left: 3px;">';
            } else {
                $html .= '<span style="margin-left: 3px;">';
            }

            $html .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));

            if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                $html .= '</strong></span>';
            } else {
                $html .= '</span>';
            }

            $childHtml = '';
            $childHtml = $this->getChildHtml();
            if(strlen($childHtml) > 0) {
                $html .= '<span class="fa arrow"></span>';
            }

            $html .= '</a>';

            if(strlen($childHtml) > 0) {
                $html .= $childHtml;
                $html .= '<div class="largeview-submenu">';
                $html .= $childHtml;
                $html .= '</div>';
            }
            $html .= '</li>';
        }

        return $html;
    }
}
