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

use Ced\GroupBuying\Model\Session;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 */
class Edit extends Action
{

    public const ADMIN_RESOURCE = 'Ced_GroupBuying::group_buying_form';

    /**
     * Magento page factory.
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Custom group buying session.
     *
     * @var Session
     */
    private $session;

    /**
     * Request params.
     *
     * @var Http
     */
    private $request;


    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory Magento page factory.
     * @param Session $session Custom group buying session.
     * @param Http $request Request params.
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $session,
        Http $request
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->request           = $request;
        $this->session           = $session;
        parent::__construct($context);

    }//end __construct()


    /**
     * Execute Edit controller.
     *
     * @return       ResponseInterface|ResultInterface|Page
     * @noinspection PhpUndefinedMethodInspection
     */
    public function execute()
    {
        $this->session->setData(
            'group_id',
            $this->request->getParam('group_id')
        );

        return $this->resultPageFactory->create(); //@codingStandardsIgnoreLine

    }//end execute()


}//end class
