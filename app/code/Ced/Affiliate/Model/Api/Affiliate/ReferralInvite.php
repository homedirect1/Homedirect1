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
 * Class ReferralInvite
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class ReferralInvite implements \Ced\Affiliate\Api\Affiliate\ReferralInviteInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * ReferralInvite constructor.
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_logger = $logger;
    }

    /**
     * @param $parameters
     * @return array|string
     */
    public function sendInvitation($parameters)
    {
        $this->_logger->critical(json_encode($parameters));
        return ['status' => array('type' => 'success')];
    }
}