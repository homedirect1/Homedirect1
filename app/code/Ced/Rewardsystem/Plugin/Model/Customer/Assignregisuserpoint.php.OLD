<?php

namespace Ced\Rewardsystem\Plugin\Model\Customer;

use Magento\Framework\App\RequestInterface;

class Assignregisuserpoint
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Ced\Rewardsystem\Model\RegisuserpointFactory
     */
    protected $regisuserpointFactory;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $_customerResource;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\Registry $registry,
        RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Rewardsystem\Model\RegisuserpointFactory $regisuserpointFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->date = $date;
        $this->_customerFactory = $customerFactory;
        $this->_customerResource = $customerResource;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_coreRegistry = $registry;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->regisuserpointFactory = $regisuserpointFactory;
    }

    public function afterCreateAccountWithPasswordHash(
        \Magento\Customer\Model\AccountManagement $subject,
        $customer
    ) {
        $store = $this->scopeConfig;
        $rewardEnable = $store->getValue('reward/setting/enable');
        $referEnable = $store->getValue('reward/referral/referral_enable');
        if (!$rewardEnable) {
            return $customer;
        }

        $exist = $this->_coreRegistry->registry('customerid');
        if (!$exist || $exist == null) {
            $parentId = '';
            $referReward = '';
            $parentEmail = '';
            $parentCustomerPoints = '';
            if ($this->request->getParam('refer_code')) {
                $referCode = $this->request->getParam('refer_code');
                $RewardModel = $this->regisuserpointFactory->create()->load($referCode, 'refer_code')->getData();
                if (count($RewardModel)) {
                    $parentId = $RewardModel['customer_id'];
                    $customerModel = $this->_customerFactory->create();
                    $this->_customerResource->load($customerModel, $parentId);
                    $parentEmail = $customer->getEmail();
                    $referReward = $store->getValue('reward/setting/referal_point');
                    $parentCustomerPoints = $store->getValue('reward/setting/signup_point');
                    if (!$referEnable) {
                        $referReward= 0;
                        $parentCustomerPoints = 0;
                    }
                }
            }
            $today = $this->_timezoneInterface->date()->format('m/d/y H:i:s');

            $productshow = $this->scopeConfig;
            $registrationPoint = $productshow->getValue(
                'reward/setting/registration_point',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $rewardCollection = $this->regisuserpointFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addFieldToFilter('is_register', 1)->getFirstItem()->getData();

            if (!count($rewardCollection)) {
                $model = $this->regisuserpointFactory->create();
                $customerid = $customer->getId();
                $model->setCustomerId($customerid);

                $date = $this->date->gmtDate();
                $expdate = "";
                $add_days = $store->getValue('reward/setting/point_expiration');
                if ($add_days) {
                    $expdate = date('Y-m-d', strtotime($date . ' +' . $add_days . ' days'));
                }
                $userAgent = $this->request->getServer('HTTP_USER_AGENT');

                $registrationPoint = ($registrationPoint > 0) ? $registrationPoint : 0;
                if ($registrationPoint) {
                    $model->setPoint($registrationPoint);
                    $model->setReceivedPoint($registrationPoint);
                    $model->setTitle('You registered on the website. ');
                    $model->setCreatingDate($today);
                    $model->setUpdatedAt($today);
                    $model->setStatus('complete');
                    $model->setReferCode($this->randomString(5));
                    $model->setParentCustomer($parentId);
                    $model->setIsRegister(1);
                    if ($expdate) {
                        $model->setExpirationDate($expdate);
                    }
                    $this->_coreRegistry->register('customerid', $customerid);
                    $model->save();
                }
                if ($referReward) {
                    $rmodel = $this->regisuserpointFactory->create();
                    $rmodel->setTitle('You registered via referral. ');
                    $rmodel->setCreatingDate($today);
                    $rmodel->setUpdatedAt($today);
                    $rmodel->setStatus('complete');
                    $rmodel->setPoint($referReward);
                    $rmodel->setReceivedPoint($referReward);
                    $rmodel->setCustomerId($customerid);
                    if ($expdate) {
                        $rmodel->setExpirationDate($expdate);
                    }
                    $rmodel->save();
                }
                if ($parentCustomerPoints) {
                    $rcmodel = $this->regisuserpointFactory->create();
                    $rcmodel->setTitle('Your referral ' . $parentEmail . ' has registered on the website. ');
                    $rcmodel->setCreatingDate($today);
                    $rcmodel->setUpdatedAt($today);
                    $rcmodel->setStatus('complete');
                    $rcmodel->setPoint($parentCustomerPoints);
                    $rcmodel->setReceivedPoint($parentCustomerPoints);
                    $rcmodel->setCustomerId($parentId);
                    if ($expdate) {
                        $rcmodel->setExpirationDate($expdate);
                    }
                    $rcmodel->save();
                }
            }
        }
        return $customer;
    }

    /**
     * @param $length
     * @return string
     * @throws \Exception
     */
    public function randomString($length)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = random_int(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
}
