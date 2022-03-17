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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */


namespace Ced\CsHyperlocal\Block\Adminhtml\System\Config\Form\Field;


use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Location extends Field
{

    /**
     * @var string
     */
    protected $_template = 'system/config/form/field/location.phtml';

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    /*public function render(AbstractElement $element)
    {
        $html = parent::render($element);
        $html .= sprintf(
            '<td colspan="%d" id="%s">%s</td>',
            '',
            $element->getHtmlId(),
            $this->_renderValue($element)
        );

        return $this->_decorateRowHtml($element, $html);
    }
    */

    /**
     * Render element value
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element): string
    {
        $html = parent::_renderValue($element);
        return $html . $this->_toHtml();
    }

}
