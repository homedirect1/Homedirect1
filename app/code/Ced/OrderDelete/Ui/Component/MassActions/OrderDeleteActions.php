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
 * @category  Ced
 * @package   Ced_OrderDelete
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\OrderDelete\Ui\Component\MassActions;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class OrderDeleteActions
 */
class OrderDeleteActions extends \Magento\Ui\Component\MassAction
{
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param ContextInterface   $context
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $components, $data);
    }

    public function prepare()
    {
        parent::prepare();
        $config = $this->getConfiguration();

        $module_enable = $this->scopeConfig->getValue('order_section/order_group/order_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$module_enable) {
            foreach ($config['actions'] as $key => $action) {
                if ( $action['type'] != 'delete' ) {
                    $allowedActions[] = $action;
                }
            }
            $config['actions'] = $allowedActions;
            $this->setData('config', (array) $config);
        }
    }
}