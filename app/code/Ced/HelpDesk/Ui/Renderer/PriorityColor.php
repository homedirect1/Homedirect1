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
 * Class PriorityColor
 * @package Ced\HelpDesk\Ui\Renderer
 */
class PriorityColor extends Column
{
    /**
     * @var \Ced\HelpDesk\Model\PriorityFactory
     */
    protected $priorityFactory;

    /**
     * PriorityColor constructor.
     * @param \Ced\HelpDesk\Model\PriorityFactory $priorityFactory
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Ced\HelpDesk\Model\PriorityFactory $priorityFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],

        array $data = []
    )
    {
        $this->priorityFactory = $priorityFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $field = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $priority = $this->priorityFactory->create()->load($item['priority'], 'code');
                $PriorityColor = $priority->getData('bgcolor');
                $item[$field . '_htmltext'] = "<div style='background:$PriorityColor'class='button'><span>" . $priority->getTitle() . "</span></div>";
            }
        }
        return $dataSource;
    }
}
