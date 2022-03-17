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

namespace Ced\Affiliate\Model\Api\Affiliate;

/**
 * Class AffiliateCreate
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class AffiliateCreate implements \Ced\Affiliate\Api\Affiliate\AffiliateCreateInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * AffiliateCreate constructor.
     * @param Account $account
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlInterface $urlFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     */
    public function __construct(
        \Ced\Affiliate\Model\Api\Affiliate\Account $account,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
    )
    {
        $this->urlModel = $urlFactory;
        $this->account = $account;
        $this->_logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->customerFactory = $customerFactory;
        $this->directoryList = $directoryList;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
    }

    /**
     * @param \Ced\Affiliate\Api\Affiliate\DocumentInterface $document
     * @param $parameters
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function createAffiliate(\Ced\Affiliate\Api\Affiliate\DocumentInterface $document, $parameters)
    {
        $this->_logger->critical(json_encode($parameters));
        if ($parameters) {
            $data = $parameters;

            if (!isset($data['email']) || !$data['email']) {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => 'email  is missing.',
                                'status' => 'false'
                            )
                        )
                    )
                );
                return $data;
            }
            $customer = $this->account->customerRegister($data);

        } else {
            $data = array(
                'data' => array(
                    'customer' => array(
                        array(
                            'message' => 'some paremer is missing.',
                            'status' => 'false'
                        )
                    )
                )
            );
            return [$data];
        }

        if (!isset($customer['data']['customer']['customer_id'])) {

            return $customer;
        }
        $customerId = $customer['data']['customer']['customer_id'];

        $customerData = $this->customerFactory->create()->load($customerId);

        /*document upload proocess*/

        $entryContentidfile = $document->getIdfile();

        $entryContentaddressfile = $document->getAddressfile();

        $entryContentcompanyfile = $document->getCompanyfile();


        $path_parts_idfile = pathinfo($entryContentidfile->getName());

        $path_parts_address = pathinfo($entryContentaddressfile->getName());

        $path_parts_company = pathinfo($entryContentcompanyfile->getName());

        $idfilename = md5($path_parts_idfile['filename']) . '_' . time() . '_' . $path_parts_idfile['filename'];

        $addressfilename = md5($path_parts_address['filename']) . '_' . time() . '_' . $path_parts_address['filename'];

        $companyfilename = md5($path_parts_company['filename']) . '_' . time() . '_' . $path_parts_company['filename'];

        $decode_doc_idfile = base64_decode($entryContentidfile->getBase64EncodedData());

        $decode_doc_addressfile = base64_decode($entryContentaddressfile->getBase64EncodedData());

        $decode_doc_companyfile = base64_decode($entryContentcompanyfile->getBase64EncodedData());


        $directory_list = $this->directoryList;
        $directory_list->getRoot();
        $path = $directory_list->getPath('media') . '/affiliate/document/' . $customerId . '/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if (isset($path_parts_idfile['extension']) && $path_parts_idfile['extension'] != '') {
            $ext1 = $path_parts_idfile['extension'];
        } else {
            return array('error' => true, 'error_message' => 'Document name must contain the extension.');
        }
        if (isset($path_parts_address['extension']) && $path_parts_address['extension'] != '') {
            $ext2 = $path_parts_address['extension'];
        } else {
            return array('error' => true, 'error_message' => 'Document name must contain the extension.');
        }
        if (isset($path_parts_company['extension']) && $path_parts_company['extension'] != '') {
            $ext3 = $path_parts_company['extension'];
        } else {
            return array('error' => true, 'error_message' => 'Document name must contain the extension.');
        }
        $id_filename = $this->checkfile($idfilename, $ext1, $path);

        $address_filename = $this->checkfile($addressfilename, $ext2, $path);

        $comapny_filename = $this->checkfile($companyfilename, $ext3, $path);

        $idupload = fopen($path . $id_filename, 'w');

        $addressupload = fopen($path . $address_filename, 'w');

        $companyupload = fopen($path . $comapny_filename, 'w');

        fwrite($idupload, $decode_doc_idfile);

        fwrite($addressupload, $decode_doc_addressfile);

        fwrite($companyupload, $decode_doc_companyfile);

        $isApprovalRequired = $this->scopeConfig->getValue('affiliate/admin/approval',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $affiliateid = rand();
        $affiliateurl = $this->urlModel->getUrl('', array('_query' => array('affiliate' => $affiliateid)));

        $affiliateaccount = $this->affiliateAccountFactory->create();
        $affiliateaccount->setCustomerId($customerData->getId());
        $affiliateaccount->setCustomerName($customerData->getFirstname() . ' ' . $customerData->getLastname());
        $affiliateaccount->setReferralWebsite($parameters['referring_website']);
        $affiliateaccount->setCustomerEmail($customerData->getEmail());
        $affiliateaccount->setAffiliateUrl($affiliateurl);
        $affiliateaccount->setCreatedAt(time());
        $affiliateaccount->setAffiliateId($affiliateid);
        $affiliateaccount->setIdentityType($parameters['doc_type']);
        $affiliateaccount->setIdentityfile($id_filename);
        $affiliateaccount->setAddressfile($address_filename);

        if ($comapny_filename)
            $affiliateaccount->setCompanyfile($comapny_filename);

        if ($isApprovalRequired) {
            $affiliateaccount->setStatus(\Ced\Affiliate\Model\AffiliateAccount::PENDING);
            $affiliateaccount->setApprove(\Ced\Affiliate\Model\AffiliateAccount::PENDING);
        } else {
            $affiliateaccount->setStatus(\Ced\Affiliate\Model\AffiliateAccount::APPROVE);
            $affiliateaccount->setApprove(\Ced\Affiliate\Model\AffiliateAccount::APPROVE);
        }
        $affiliateaccount->save();

        if ($isApprovalRequired) {
            $affiliateData['success'] = true;
            $affiliateData['approval_required'] = true;
            return ['data' => $affiliateData];
        } else {
            $affiliateData['success'] = true;
            $affiliateData['approval_required'] = false;
            return ['data' => $affiliateData];
        }

    }


    /**
     * @param $filename
     * @param $ext
     * @param $filepath
     * @return string
     */
    public function checkfile($filename, $ext, $filepath)
    {
        $filename_ext = $filename . '.' . $ext;
        $file = $filepath . $filename_ext;
        if (file_exists($file)) {
            $filename = $filename . rand(0, 9);
            $this->checkfile($filename, $ext, $filepath);
        }
        return $filename_ext;
    }

}