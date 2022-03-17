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

namespace Ced\CsHyperlocal\Controller\Adminhtml\Shiparea;

use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Shiparea
 */
class Delete extends \Magento\Backend\App\Action
{

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaFactory;

    /**
     * Delete constructor.
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory,
        Action\Context $context
    )
    {
        $this->shipareaFactory = $shipareaFactory;
        parent::__construct($context);
    }

    /**
     *  execute
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->shipareaFactory->create()->load($id);
        $model->setId($id)->delete();
        $this->_redirect('*/*/index');
        $this->messageManager->addSuccessMessage(__('Ship area has been deleted'));
    }

}