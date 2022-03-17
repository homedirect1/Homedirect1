<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\CsMarketplace\Ui\Component\Listing;

use Magento\Ui\Component\Control\Action;

/**
 * Class MassAction
 */
class MassAction extends Action
{
    /**
     * Prepare
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $context = $this->getContext();
        $config = $this->getConfiguration();

        $actions = [];
        foreach ($this->getChildComponents() as $actionComponent) {
            $actionConfig = $actionComponent->getConfiguration();
            if(isset($actionConfig['type'])
                && $actionConfig['type'] == 'delete'
                && $context->getRequestParam('check_status')
            ){
                $actionConfig['url'] = $context->getUrl(
                    'csmarketplace/vproducts/massDelete',
                    ['check_status' => $context->getRequestParam('check_status')]
                );
            }
            $actions[] = $actionConfig;
        }
        $config['actions'] = $actions;
        $this->setData('config', $config);

    }
}
