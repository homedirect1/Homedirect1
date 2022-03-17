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
 * @category  Ced
 * @package   Ced_Customnumbers
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\Customnumbers\Model\Plugin;

use Magento\SalesSequence\Model\Manager;

class ManagerPlugin
{
	/**
     * @param Manager $subject
     * @param \Closure $proceed
     * @param $entityType
     * @param $storeId
     */

    public function aroundGetSequence(Manager $subject, \Closure $proceed, $entityType, $storeId)
    {
        $sequenceFactory = $proceed($entityType, $storeId);
        $sequenceFactory->entityType = $entityType;
        $sequenceFactory->storeId = $storeId;
        return $sequenceFactory;
    }

}