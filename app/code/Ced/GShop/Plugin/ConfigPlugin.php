<?php

namespace Ced\GShop\Plugin;


class ConfigPlugin
{
    public function afterSave(
        \Magento\Config\Model\Config $subject
    )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $configPost = $subject->getData();
        $msg = '';
        if (isset($configPost['section']) && $configPost['section'] == 'gxpress_config')
        {
            if(isset($configPost['groups']['product_upload']['fields']['target_country']['value'])
                && empty($configPost['groups']['product_upload']['fields']['target_country']['value'])) {
                $msg .= 'Please Select target Country field'.'<br />';
            }

            if(isset($configPost['groups']['product_upload']['fields']['included_destination']['value'])
                && empty($configPost['groups']['product_upload']['fields']['included_destination']['value'])) {
                $msg .= 'Please Select Product Destination field'.'<br />';
            }
        }
            $messageManager = $objectManager->create('\Magento\Framework\Message\ManagerInterface');
            if (!$msg) {
                $messageManager->addSuccess('Google express Credential Valid');
            } else {
                $messageManager->addError($msg);
            }
    }
}