<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\Adminhtml\Plans\Edit\Tab;

/**
 * Webkul Recurring Viewall Block
 */
class Terms extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_template = 'plan/terms.phtml';

    /**
     * Get terms data
     *
     * @return array
     */
    public function getTermsData()
    {

        $model = $this->_coreRegistry->registry('recurring_data');
        $termData = $this->_coreRegistry->registry('terms_data');
        if (!(bool)empty($termData)) {
            foreach ($termData as $term) {
                $term['id'] = $term['entity_id'] ;
                $terms [] = $term;
            }
        } else {
            $terms [] = [
                   "id"=> 0,
                   "term"=>"",
                   "title"=>"",
                   "price"=>"",
                   "repeat"=>"",
                   "time_span"=>"",
                   "payment_before_days"=>"",
                   "price_per_term"=>"",
                   "price_type"=>"0",
                   "no_of_terms"=>"",
                   "sort_order"=>""
                   ];
        }
        return $terms;
    }
    
    /**
     * Get data for types
     *
     * @return array
     */
    public function getTypeData()
    {
        return [
            [
                'value' => 0,
                'label' => __('Fixed')
            ],
            [
                'value' => 1,
                'label' => __('Percent')
            ]
        ];
    }
}
