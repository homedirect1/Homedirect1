<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Perkmshipping
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ced\Perkmshipping\Block\Adminhtml\System\Config\Renderer;
class Select extends \Magento\Framework\View\Element\Html\Select
{

        public function setInputName($value)
        {
            return $this->setName($value);
        }

        public function _toHtml()
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->setExtraParams('style="width: 150px;"');
            if (!$this->getOptions()) {
        
                $this->addOption($objectManager->create('Ced\Perkmshipping\Model\Source\Method\Type')->toOptionArray(),false);
            }
            return parent::_toHtml();
        }
        
}
