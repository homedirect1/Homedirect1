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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Model\View\Element;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class BlockFactory
 * @package Ced\Affiliate\Model\View\Element
 */
class BlockFactory extends \Magento\Framework\View\Element\BlockFactory
{

    const XML_PATH_CED_REWRITES = 'ced/rewrites';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $httpRequest;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * BlockFactory constructor.
     * @param \Magento\Framework\App\Request\Http $httpRequest
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager
    )
    {
        parent::__construct($objectManager);
        $this->httpRequest = $httpRequest;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Block Factory
     *
     * @param string $type
     * @param string $name
     * @param array $arguments
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function createBlock($blockName, array $arguments = [])
    {
        $module = $this->httpRequest->getModuleName();
        $controller = $this->httpRequest->getControllerName();
        $action = $this->httpRequest->getActionName();

        $exceptionblocks = '';
        $exceptionblocks = $this->scopeConfig
            ->getValue(self::XML_PATH_CED_REWRITES . "/" . $module . "/" . $controller . "/" . $action);
        if (strlen($exceptionblocks) == 0) {
            $action = "all";
            $exceptionblocks = $this->scopeConfig
                ->getValue(self::XML_PATH_CED_REWRITES . "/" . $module . "/" . $controller . "/" . $action);
        }

        $block = parent::createBlock($blockName, $arguments);
        $exceptionblocks = explode(",", $exceptionblocks);
        if (count($exceptionblocks) > 0) {
            foreach ($exceptionblocks as $exceptionblock) {
                if (strlen($exceptionblock) != 0 && strpos(get_class($block), $exceptionblock) !== false) {
                    $block->setArea('adminhtml');
                }
            }
        }

        return $block;
    }
}
