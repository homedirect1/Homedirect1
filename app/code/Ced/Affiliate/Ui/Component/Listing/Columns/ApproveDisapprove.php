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

namespace Ced\Affiliate\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ApproveDisapprove
 * @package Ced\Affiliate\Ui\Component\Listing\Columns
 */
class ApproveDisapprove extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ApproveDisapprove constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['approve'])) {
                    if ($item['approve'] == '0') {
                        $urla = $this->urlBuilder->getUrl("affiliate/manage/approve",
                            ["id" => $item['id'], "store" => $storeId]);
                        $urld = $this->urlBuilder->getUrl("affiliate/manage/disapprove",
                            ["id" => $item['id'], "store" => $storeId]);
                        $item['approve'] = '<a href="javascript:void(0);" onclick="deleteConfirm(\'' . __('Are you sure you want to Approve?') . '\', \'' . $urla . '\');" >' . __('Approve') . "</a>" . ' | ' . '<a href="javascript:void(0);" onclick="deleteConfirm(\'' . __('Are you sure you want to Disapprove?') . '\', \'' . $urld . '\');" >' . __('Disapprove') . "</a>";;
                    } elseif ($item['approve'] == '1') {
                        $url = $this->urlBuilder->getUrl("affiliate/manage/disapprove",
                            ["id" => $item['id'], "store" => $storeId]);
                        $item['approve'] = '<a href="javascript:void(0);" onclick="deleteConfirm(\'' . __('Are you sure you want to Disapprove?') . '\', \'' . $url . '\');" >' . __('Disapprove') . "</a>";
                    } else {
                        $url = $this->urlBuilder->getUrl("affiliate/manage/approve",
                            ["id" => $item['id'], "store" => $storeId]);
                        $item['approve'] = '<a href="javascript:void(0);" onclick="deleteConfirm(\'' . __('Are you sure you want to Disapprove?') . '\', \'' . $url . '\');" >' . __('Approve') . "</a>";
                    }
                }

            }
        }

        return $dataSource;
    }
}
