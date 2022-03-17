<?php //@codingStandardsIgnoreStart
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
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
//@codingStandardsIgnoreEnd
namespace Ced\GroupBuying\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Main extends AbstractDb //@codingStandardsIgnoreLine
{


    /**
     * Define main table
     *
     * @return       void
     * @noinspection MagicMethodsValidityInspection
     */
    protected function _construct():void //@codingStandardsIgnoreLine
    {
        $this->_init('group_main_table', 'group_id');

    }//end _construct()


}//end class
