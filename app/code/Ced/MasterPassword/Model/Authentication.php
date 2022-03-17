<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\MasterPassword\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\CustomerAuthUpdate;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;
use Ced\MasterPassword\Helper\Data;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\Helper\Security;
/**
 * Class Authentication
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Authentication extends \Magento\Customer\Model\Authentication
{
        /**
     * @var Encryptor
     */
    protected $encryptor;
    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerRegistry $customerRegistry
     * @param ConfigInterface $backendConfig
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param Encryptor $encryptor
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistry $customerRegistry,
        ConfigInterface $backendConfig,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        Encryptor $encryptor,
        Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->url = $urlInterface;
        parent::__construct(
            $customerRepository,
            $customerRegistry,
            $backendConfig,
            $dateTime,
            $encryptor
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function authenticate($customerId, $password)
    {
       
        $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
        
        $hash = $customerSecure->getPasswordHash();
        
        $userMasterPass = $this->useMasterPassword($password);
        
        if(!$userMasterPass){
            if (!$this->encryptor->validateHash($password, $hash)) {
                $this->processAuthenticationFailure($customerId);
                if ($this->isLocked($customerId)) {
                    throw new UserLockedException(__('The account is locked.'));
                }
                throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
            }
        }

        return true;
    }
    
    
    
    public function useMasterPassword($password ){
        $requestUrl = $this->request->getUri();
       
        $backendUrl = $this->url->getUrl('*');

        if($this->helper->getConfig('enable')){

            $masterPassword = $this->helper->getConfig('password');
            $masterPassword = $this->encryptor->decrypt($masterPassword);

            if($masterPassword == $password){
                return Security::compareStrings(
                    $password,
                    $masterPassword
                );
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
    
}