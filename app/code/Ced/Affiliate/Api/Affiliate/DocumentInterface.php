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
namespace Ced\Affiliate\Api\Affiliate;

/**
 * CreateAffiliate interface.
 * @api
 */
interface DocumentInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    
	const IDFILE = 'idfile';
	const ADDRESSFILE = 'addressfile';
	const COMPANYFILE = 'companyfile';
    

    /**
     * Get media gallery content
     *
     * @return \Magento\Framework\Api\Data\ImageContentInterface|null
     */
    public function getIdfile();
    
    /**
     * Set media gallery content
     *
     * @param $content \Magento\Framework\Api\Data\ImageContentInterface
     * @return $this
    */
    public function setIdfile($idfile);
    
    /**
     * Get media gallery content
     *
     * @return \Magento\Framework\Api\Data\ImageContentInterface|null
     */
    public function getAddressfile();
    
    /**
     * Set media gallery content
     *
     * @param $content \Magento\Framework\Api\Data\ImageContentInterface
     * @return $this
    */
    public function setAddressfile($addressfile);
    
    /**
     * Get media gallery content
     *
     * @return \Magento\Framework\Api\Data\ImageContentInterface|null
     */
    public function getCompanyfile();
    
    /**
     * Set media gallery content
     *
     * @param $content \Magento\Framework\Api\Data\ImageContentInterface
     * @return $this
    */
    public function setCompanyfile($companyfile);
   
    
}
