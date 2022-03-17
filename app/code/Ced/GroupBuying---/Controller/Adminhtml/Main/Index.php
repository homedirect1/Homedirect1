<?php //@codingStandardsIgnoreStart
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
 * @category    Ced
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
//@codingStandardsIgnoreEnd
namespace Ced\GroupBuying\Controller\Adminhtml\Main;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Index
 */
class Index extends Action
{

    public const ADMIN_RESOURCE = 'Ced_GroupBuying::group_buying_form';

    /**
     * Constructor
     *
     * @param ResultFactory $resultFactory Redirect.
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
        parent::__construct($context);

    }//end __construct()


    /**
     * Execute Group form index.
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultFactory     = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT); //@codingStandardsIgnoreLine
        return $resultFactory->setPath('*/groupbuyinggrid/index'); //@codingStandardsIgnoreLine

    }//end execute()


}//end class
