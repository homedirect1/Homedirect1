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
namespace Ced\CsHyperlocal\Block\Adminhtml;

class Zipcode extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'Ced_CsHyperlocal::zipcode/managezipcode.phtml';

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_getAddButtonOptions();
    }

    /**
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Ced\CsHyperlocal\Block\Adminhtml\Zipcode\Grid', 'grid.zipcode.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     *
     *
     * @return array
     */
    protected function _getAddButtonOptions()
    {
        $locationId = $this->getRequest()->getParam('id');
        $newurl=$this->getUrl('*/zipcode/new',array('location_id'=>$locationId));
        $backtoUrl = $this->getUrl('*/shiparea/index');
        $importurl = $this->getUrl('*/zipcode/import',array('location_id'=>$locationId));
        $this->addButton('back', array(
            'label'   => ('Back to location'),
            'onclick' => "setLocation('{$backtoUrl}')",
            'class'   => 'add',
        ));
        $this->addButton('add', array(
            'label'   => ('Add New Zipcode'),
            'onclick' => "setLocation('{$newurl}')",
            'class'   => 'add',
        ));
        $this->addButton('import_csv', array(
            'label'   => ('Import CSV'),
            'onclick' => "setLocation('{$importurl}')",
            'class'   => 'add',
        ));

    }


    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}