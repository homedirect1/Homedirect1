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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ShipareaEditAction
 * @package Ced\CsHyperlocal\Ui\Component\Listing\Columns
 */
class ShipareaEditAction extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
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
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->csmarketplaceHelper = $csmarketplaceHelper;
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
        $filterType = $this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE);

        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {

                if ($item['zipcode_type'] == 'multiple' && $filterType == 'zipcode') {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                'cshyperlocal/shiparea/edit',
                                ['id' => $item['id'], 'store' => $storeId]
                            ),
                            'label' => __('Edit'),
                            'hidden' => false,
                        ], 'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                'cshyperlocal/shiparea/delete',
                                ['id' => $item['id'], 'store' => $storeId]
                            ),
                            'label' => __('Delete'),
                            'hidden' => false,
                        ], 'manage_zipcodes' => [
                            'href' => $this->urlBuilder->getUrl(
                                'cshyperlocal/shiparea/managezipcode',
                                ['id' => $item['id'], 'store' => $storeId]
                            ),
                            'label' => __('Manage Zipcodes'),
                            'hidden' => false,
                        ]];
                } else {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                'cshyperlocal/shiparea/edit',
                                ['id' => $item['id'], 'store' => $storeId]
                            ),
                            'label' => __('Edit'),
                            'hidden' => false,
                        ], 'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                'cshyperlocal/shiparea/delete',
                                ['id' => $item['id'], 'store' => $storeId]
                            ),
                            'label' => __('Delete'),
                            'hidden' => false,
                        ]];
                }
            }
        }

        return $dataSource;
    }
}
