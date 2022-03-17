<?php

/*
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

namespace Ced\GroupBuying\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Ced\GroupBuying\Helper\Data as Helper;

class MassEmail extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $inlineTranslation;

    protected $escaper;

    protected $transportBuilder;

    protected $logger;


    /**
     * TODO
     *
     * @param Context          $context
     * @param StateInterface   $inlineTranslation
     * @param Escaper          $escaper
     * @param TransportBuilder $transportBuilder
     * @param Data             $helper
     */
    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        Helper $helper
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper           = $escaper;
        $this->transportBuilder  = $transportBuilder;
        $this->logger            = $context->getLogger();
        $this->helper            = $helper;

    }//end __construct()


    /**
     * TODO
     *
     * @param integer $groupId
     * @param array   $customerEmailArr
     */
    public function sendEmail(int $groupId, array $customerEmailArr, string $senderName=null)
    {
        try {
            $this->inlineTranslation->suspend();
            $sender    = [
                'name'  => $this->escaper->escapeHtml($senderName),
                'email' => $this->escaper->escapeHtml('vinaykharayat@cedcoss.com'),
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($this->helper->getConfig(Helper::CONFIG_GROUP_EMAIL_TEMPLATE))->setTemplateOptions(
                [
                    'area'  => Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )->setTemplateVars(
                ['groupId' => $groupId]
            )->setFrom($sender)->addTo($customerEmailArr)->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }//end try

    }//end sendEmail()


}//end class
