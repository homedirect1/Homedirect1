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
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Block\Adminhtml\Main\Edit;

use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 */
class GenericButton
{

    // putting all the button methods in here.  No "right", but the whole
    // button/GenericButton thing seems -- not that great -- to begin with


    /**
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;

    }//end __construct()


    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('groupbuying/groupbuyinggrid/index');

    }//end getBackUrl()


    /**
     * Get delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('groupbuying/main/delete', ['group_id' => $this->getObjectId()]);

    }//end getDeleteUrl()


    /**
     * Get url
     *
     * @param string $route
     * @param array  $params
     *
     * @return string
     */
    public function getUrl($route='', $params=[])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);

    }//end getUrl()


    /**
     * Get id
     *
     * @return integer
     */
    public function getObjectId(): int
    {
        return $this->context->getRequest()->getParam('group_id');

    }//end getObjectId()


}//end class
