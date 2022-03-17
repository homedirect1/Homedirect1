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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Controller\Promo\Quote;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportCouponsXml extends \Ced\CsPromotions\Controller\Promo\Quote
{
    /**
     * Export coupon codes as excel xml file
     *
     * @return \Magento\Framework\App\ResponseInterface|null
     */
    public function execute()
    {
        $this->_initRule();
        $rule = $this->_coreRegistry->registry('current_promo_quote_rule');
        if ($rule->getId()) {
            $fileName = 'coupon_codes.xml';
            $content = $this->_view->getLayout()->createBlock(
                'Ced\CsPromotions\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid'
            )->getExcelFile(
                $fileName
            );
            return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
        } else {
            $this->_redirect('sales_rule/*/detail', ['_current' => true]);
            return;
        }
    }
}
