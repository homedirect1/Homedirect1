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
 * @package     Ced_HelpDesk
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Ui\Renderer;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class StatusBgcolor
 * @package Ced\HelpDesk\Ui\Renderer
 */
class StatusBgcolor extends Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $field = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['bgcolor']) {
                    $statusColor = $item['bgcolor'];
                    $item[$field . '_htmltext'] = "<div style='background:$statusColor;width:37%'class='button'><span>" . $item['bgcolor'] . "</span></div>";
                }
            }
        }
        return $dataSource;
    }
}
