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

namespace Ced\CsHyperlocal\Controller\Adminhtml\Zipcode;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Zipcode
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $zipcodeFactory;

    /**
     * MassDelete constructor.
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory,
        Action\Context $context
    )
    {
        parent::__construct($context);
        $this->zipcodeFactory = $zipcodeFactory;
    }

    /**
     *  execute
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('id');
        $locationId = $this->getRequest()->getParam('location_id');
        if (count($ids) > 0) {
            foreach ($ids as $id) {

                $zipcodemodel = $this->zipcodeFactory->create()->load($id);
                $zipcodemodel->delete();
            }
            $this->_redirect('*/shiparea/managezipcode', ['id' => $locationId]);
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', count($ids)));
        }
    }
}