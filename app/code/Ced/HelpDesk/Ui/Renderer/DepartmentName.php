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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class DepartmentName
 * @package Ced\HelpDesk\Ui\Renderer
 */
class DepartmentName extends Column
{
    /**
     * @var \Ced\HelpDesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * DepartmentName constructor.
     * @param \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->departmentFactory = $departmentFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['department']) {
                    $item['department'] = $this->departmentFactory->create()
                        ->load($item['department'], 'code')
                        ->getName();
                } else {
                    $item['department'] = $item['department'];
                }
            }
        }
        return $dataSource;
    }
}
