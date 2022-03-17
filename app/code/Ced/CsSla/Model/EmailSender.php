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
 * @package     Ced_CsSla
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsSla\Model;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Mail\MessageInterface;

class EmailSender extends \Magento\Framework\Mail\Template\TransportBuilder
{
    

     /**
     * Set mail from address
     *
     * @param string|array $from
     * @return $this
     */
	public function setFrom($from)
    {
        $result = $this->_senderResolver->resolve($from);
        $this->message->setFrom($result['email'], $result['name']);
    }
       
    
    /**
     * Prepare message
     *
     * @return $this
     */
	protected function prepareMessage()
    {
        $template = $this->getTemplate();
        $body = $template->processTemplate();
        switch ($template->getType()) {
            case TemplateTypesInterface::TYPE_TEXT:
                $this->message->setBodyText($body);
                break;

            case TemplateTypesInterface::TYPE_HTML:
                $this->message->setBodyHtml($body);
                break;

            default:
                throw new LocalizedException(
                    new Phrase('Unknown template type')
                );
        }
        $this->message->setSubject(html_entity_decode($template->getSubject(), ENT_QUOTES));
        return $this;
    }
}
