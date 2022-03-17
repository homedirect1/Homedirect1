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
namespace Ced\Affiliate\Model\Api\Affiliate;

use Magento\Framework\Model\AbstractExtensibleModel;
use Ced\Affiliate\Api\Affiliate\DocumentInterface;
use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryExtensionInterface;

/**
 * @codeCoverageIgnore
 */
class Document extends AbstractExtensibleModel implements DocumentInterface
{
    /**
    * @return \Magento\Framework\Api\Data\ImageContentInterface|null
    */
    public function getIdfile()
    {
       return $this->getData(self::IDFILE);
    }

    /**
    * Set media gallery content
    *
    * @param $content \Magento\Framework\Api\Data\ImageContentInterface
    * @return $this
    */
    public function setIdfile($idfile)
    {
       return $this->setData(self::IDFILE, $idfile);
    }
    
    
    
    /**
     * Set media gallery content
     *
     * @param $content \Magento\Framework\Api\Data\ImageContentInterface
     * @return $this
     */
    public function setAddressfile($addressfile)
    {
    	return $this->setData(self::ADDRESSFILE, $addressfile);
    }
    
    public function getAddressfile()
    {
    	return $this->getData(self::ADDRESSFILE);
    }
    
    /**
     * Set media gallery content
     *
     * @param $content \Magento\Framework\Api\Data\ImageContentInterface
     * @return $this
     */
    public function setCompanyfile($companyfile)
    {
    	return $this->setData(self::COMPANYFILE, $companyfile);
    }
    public function getCompanyfile()
    {
    	return $this->getData(self::COMPANYFILE);
    }
}