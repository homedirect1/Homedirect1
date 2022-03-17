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

namespace Ced\GroupBuying\Ui\Component\Listing\Column\Cedgroupbuyinggrid;

use Ced\GroupBuying\Helper\Data as Helper;
use Ced\GroupBuying\Model\MainFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class PageActions extends Column
{

    private $helper, $mainFactory;


    /**
     * Constructor
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Helper             $helper
     * @param MainFactory        $mainFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Helper $helper,
        MainFactory $mainFactory,
        array $components=[],
        array $data=[]
    ) {
        $this->helper = $helper;

        $this->mainFactory = $mainFactory;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );

    }//end __construct()


    /**
     * Prepare data source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items']) === true) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                $id   = 'X';
                if (isset($item['group_id']) === true) {
                    $id = $item['group_id'];
                }

                $item[$name]['view']   = [
                    'href'  => $this->getContext()->getUrl(
                        'groupbuying/main/edit',
                        ['group_id' => $id]
                    ),
                    'label' => __('Edit'),
                ];
                $item[$name]['delete'] = [
                    'href'    => $this->getContext()->getUrl(
                        'groupbuying/main/delete',
                        ['group_id' => $id]
                    ),
                    'confirm' => [
                        'title'   => __('Delete group'),
                        'message' => __('Are you sure you want to delete this group?'),
                    ],
                    'label'   => __('Delete'),
                ];

                $isApprove = $this->mainFactory->create()->load($id)->getIsApprove();

                switch (true) {
                    // If group approval is enabled and group status is pending.
                    case ($isApprove == 2):
                        $item[$name]['approve']    = [
                            'href'    => $this->getContext()->getUrl(
                                'groupbuying/groupbuyinggrid/approve',
                                [
                                    'approve'  => 1,
                                    'group_id' => $id,
                                ]
                            ),
                            'confirm' => [
                                'title'   => __('Approve group'),
                                'message' => __('Are you sure you want to approve this group?'),
                            ],
                            'label'   => __('Approve'),
                        ];
                        $item[$name]['disapprove'] = [
                            'href'    => $this->getContext()->getUrl(
                                'groupbuying/groupbuyinggrid/approve',
                                [
                                    'approve'  => 0,
                                    'group_id' => $id,
                                ]
                            ),
                            'confirm' => [
                                'title'   => __('Disapprove group'),
                                'message' => __('Are you sure you want to disapprove this group?'),
                            ],
                            'label'   => __('Disapprove'),
                        ];
                    break;

                    // If group approval is enabled and group status is disapproved.
                    case ($isApprove == 0):
                        $item[$name]['approve'] = [
                            'href'    => $this->getContext()->getUrl(
                                'groupbuying/groupbuyinggrid/approve',
                                [
                                    'approve'  => 1,
                                    'group_id' => $id,
                                ]
                            ),
                            'confirm' => [
                                'title'   => __('Approve group'),
                                'message' => __('Are you sure you want to approve this group?'),
                            ],
                            'label'   => __('Approve'),
                        ];
                    break;

                    // If group approval is enabled and group status is approved.
                    case ($isApprove == 1):
                        $item[$name]['disapprove'] = [
                            'href'    => $this->getContext()->getUrl(
                                'groupbuying/groupbuyinggrid/approve',
                                [
                                    'approve'  => 0,
                                    'group_id' => $id,
                                ]
                            ),
                            'confirm' => [
                                'title'   => __('Disapprove group'),
                                'message' => __('Are you sure you want to disapprove this group?'),
                            ],
                            'label'   => __('Disapprove'),
                        ];
                    break;

                    // If group approval is not enabled
                    case (!$this->helper->getConfig(Helper::CONFIG_GROUP_APPROVAL_STATUS)):
                    break;

                    default:
                        break;
                }//end switch
            }//end foreach
        }//end if

        return $dataSource;

    }//end prepareDataSource()


}//end class
