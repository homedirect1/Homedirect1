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

namespace Ced\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class BeforeLoadLayout
 * @package Ced\Affiliate\Observer
 */
Class BeforeLoadLayout implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Ced\Affiliate\Model\AffiliateTrafficFactory
     */
    protected $affiliateTrafficFactory;

    /**
     * BeforeLoadLayout constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ced\Affiliate\Model\AffiliateTrafficFactory $affiliateTrafficFactory
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ced\Affiliate\Model\AffiliateTrafficFactory $affiliateTrafficFactory
    )
    {
        $this->_request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->affiliateTrafficFactory = $affiliateTrafficFactory;
    }

    /**
     *Product Assignment Tab
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_request->getParam('affiliate')) {
            $this->checkoutSession->setAffiliateId($this->_request->getParam('affiliate'));
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $traffic = $this->affiliateTrafficFactory->create();

            if (count($traffic->getCollection()->getData()) > 0) {
                $traffic = $this->affiliateTrafficFactory->create();
                $traffic->load($actual_link, 'shared_url');
                if ($traffic->getData()) {

                    $googlecount = $traffic->getGoogleClick();
                    $fbcount = $traffic->getFacebookClick();
                    $twitercount = $traffic->getTwitterClick();
                    $mailcount = $traffic->getEmailClick();
                    $totalClick = $traffic->getTotalClick();
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                            'facebook.com') !== false) {
                        $fbcount = $fbcount + 1;

                    }
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                            'plus.google.com') !== false) {

                        $googlecount = $googlecount + 1;
                    }
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                            'twitter.com') !== false) {

                        $twitercount = $twitercount + 1;
                    }
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                            'mail.google.com') !== false) {

                        $mailcount = $mailcount + 1;
                    }

                    $totalClick = $fbcount + $twitercount + $googlecount + $mailcount;
                    $traffic->setFacebookClick($fbcount);
                    $traffic->setGoogleClick($googlecount);
                    $traffic->setTwitterClick($twitercount);
                    $traffic->setEmailClick($mailcount);
                    $traffic->setTotalClick($totalClick);
                    $traffic->save();
                } else {

                    $traffic = $this->affiliateTrafficFactory->create();
                    $googlecount = 0;
                    $fbcount = 0;
                    $twitercount = 0;
                    $mailcount = 0;
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                            'facebook.com') !== false) {
                        $fbcount = 1;

                    }
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                            'plus.google.com') !== false) {

                        $googlecount = 1;
                    }
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                            'twitter.com') !== false) {

                        $twitercount = 1;
                    }
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                            'mail.google.com') !== false) {

                        $mailcount = 1;
                    }
                    $totalClick = $fbcount + $twitercount + $googlecount + $mailcount;
                    $traffic->setSharedUrl($actual_link);
                    $traffic->setFacebookClick($fbcount);
                    $traffic->setGoogleClick($googlecount);
                    $traffic->setTwitterClick($twitercount);
                    $traffic->setEmailClick($mailcount);
                    $traffic->setTotalClick($totalClick);
                    $traffic->setAffiliateId($this->_request->getParam('affiliate'));
                    $traffic->save();
                }
            } else {
                $googlecount = 0;
                $fbcount = 0;
                $twitercount = 0;
                $mailcount = 0;
                if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                        'facebook.com') !== false) {
                    $fbcount = 1;

                }
                if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                        'plus.google.com') !== false) {

                    $googlecount = 1;
                }
                if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                        'twitter.com') !== false) {

                    $twitercount = 1;
                }
                if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],
                        'mail.google.com') !== false) {

                    $mailcount = 1;
                }
                $totalClick = $fbcount + $twitercount + $googlecount + $mailcount;
                $traffic->setSharedUrl($actual_link);
                $traffic->setFacebookClick($fbcount);
                $traffic->setGoogleClick($googlecount);
                $traffic->setTwitterClick($twitercount);
                $traffic->setEmailClick($mailcount);
                $traffic->setTotalClick($totalClick);
                $traffic->setAffiliateId($this->_request->getParam('affiliate'));
                $traffic->save();
            }
        }

    }
}    

