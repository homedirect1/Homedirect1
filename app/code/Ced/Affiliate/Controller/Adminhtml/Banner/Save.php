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
 * @category    Ced
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Ced\Affiliate\Controller\Adminhtml\Banner
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateBannerFactory
     */
    protected $affiliateBannerFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Ced\Affiliate\Model\AffiliateBannerFactory $affiliateBannerFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        Action\Context $context,
        \Ced\Affiliate\Model\AffiliateBannerFactory $affiliateBannerFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Backend\Model\Session $session
    )
    {
        parent::__construct($context);
        $this->affiliateBannerFactory = $affiliateBannerFactory;
        $this->affiliateHelper = $affiliateHelper;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }


    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();
        $bannerdata = $this->getRequest()->getPostValue();
        $affiliatebanner = $this->affiliateBannerFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($bannerdata) {
            if ($this->getRequest()->getParam('id')) {

                $affiliatebanner->load($id);
                if ($bannerdata['banner_type'] != 'text') {
                    $filename = $this->affiliateHelper->uploadFile();
                }
                if (!$filename)
                    $filename = $affiliatebanner->getBannerData();
            } else {
                if ($bannerdata['banner_type'] != 'text') {
                    $filename = $this->affiliateHelper->uploadFile();
                    if (!$filename) {
                        $this->messageManager->addErrorMessage(__('Not Able To Upload Image'));
                        $resultRedirect->setPath('affiliate/banner/index');
                        return $resultRedirect;
                    }
                }
            }
            $affiliatebanner->addData($bannerdata);

            if ($bannerdata['banner_type'] != 'text')
                $affiliatebanner->setBannerData($filename);


            try {
                $affiliatebanner->save();
                $this->messageManager->addSuccessMessage(__('Banner Has Been Saved Successfully'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $affiliatebanner->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/index');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while editing the request.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving data'));
            $resultRedirect->setPath('affiliate/banner/index');
            return $resultRedirect;
        }

    }
}
