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

namespace Ced\GroupBuying\Ui\Component\Listing\Column\Grouplog;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class PageActions
 */
class PageActions extends Column
{


    /**
     * Data source
     *
     * @param  array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items']) === true) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                $id   = 'X';
                if (isset($item['id']) === true) {
                    $id = $item['id'];
                }

                $item[$name]['view'] = [
                    'href'  => $this->getContext()->getUrl(
                        'group_log/grouplog/edit',
                        ['id' => $id]
                    ),
                    'label' => __('Edit'),
                ];
            }
        }

        return $dataSource;

    }//end prepareDataSource()


}//end class
