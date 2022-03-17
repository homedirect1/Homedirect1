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

namespace Ced\Affiliate\Controller\Paymentsetting;

/**
 * Class Save
 * @package Ced\Affiliate\Controller\Paymentsetting
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_custmerSesion;

    /**
     * @var \Ced\Affiliate\Model\PaymentsettingsFactory
     */
    protected $paymentsettingsFactory;

    /**
     * Save constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory
    )
    {
        $this->_custmerSesion = $session;
        $this->paymentsettingsFactory = $paymentsettingsFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {

        if (!$this->_custmerSesion->getCustomer()->getId()) {
            $this->_redirect('affiliate/account/login');
            return;
        }

        $section = $this->getRequest()->getParam('section', '');
        $groups = $this->getRequest()->getPost('groups', array());
        if (strlen($section) > 0 && $this->_custmerSesion->getCustomer()->getId() && count($groups) > 0) {
            $customer_id = (int)$this->_custmerSesion->getCustomer()->getId();
            try {
                foreach ($groups as $code => $values) {
                    foreach ($values as $name => $value) {
                        $serialized = 0;
                        $key = strtolower($section . '/' . $code . '/' . $name);
                        if (is_array($value)) {
                            $value = serialize($value);
                            $serialized = 1;
                        }
                        $setting = false;
                        $setting = $this->paymentsettingsFactory->create()
                            ->loadByField(array('key', 'customer_id'), array($key, $customer_id));

                        if ($setting && $setting->getId()) {

                            $setting->setCustomerId($customer_id)
                                ->setGroup($section)
                                ->setKey($key)
                                ->setValue($value)
                                ->setSerialized($serialized)
                                ->save();
                        } else {

                            $setting = $this->paymentsettingsFactory->create();
                            $setting->setCustomerId($customer_id)
                                ->setGroup($section)
                                ->setKey($key)
                                ->setValue($value)
                                ->setSerialized($serialized)
                                ->save();
                        }
                    }
                }

                $this->messageManager->addSuccessMessage(__('The setting information has been saved.'));
                $this->_redirect('*/*');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*');
                return;
            }
        }
        $this->_redirect('*/*');
    }
}
