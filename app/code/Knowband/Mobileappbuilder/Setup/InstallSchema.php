<?php

namespace Knowband\Mobileappbuilder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        $table_name = $installer->getTable('kb_push_notifications_history');
        if ($installer->getConnection()->isTableExists($table_name) != true) {

            $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                    `kb_notification_id` int(11) NOT NULL AUTO_INCREMENT,
                    `title` text NOT NULL,
                    `message` text NOT NULL,
                    `image_type` varchar(50) NOT NULL,
                    `image_url` text NOT NULL,
                    `redirect_activity` varchar(50) NOT NULL,
                    `category_id` int(10) DEFAULT NULL,
                    `category_name` varchar(250) DEFAULT NULL,
                    `product_id` int(10) DEFAULT NULL,
                    `product_name` varchar(250) DEFAULT NULL,
                    `device_type` varchar(25) DEFAULT NULL,
                    `status` varchar(45) DEFAULT NULL,
                    `date_add` datetime NOT NULL,
                    PRIMARY KEY (`kb_notification_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $installer->run($table_script);
        }

        $table_name = $installer->getTable('kb_sliders_banners');
        if ($installer->getConnection()->isTableExists($table_name) != true) {

            $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `kb_banner_id` int(11) NOT NULL AUTO_INCREMENT,
                `status` int(2) NOT NULL,
                `image_type` varchar(50) NOT NULL,
                `image_url` text NOT NULL,
                `type` varchar(50) NOT NULL,
                `redirect_activity` varchar(50) NOT NULL,
                `category_id` int(10) DEFAULT NULL,
                `product_id` int(10) DEFAULT NULL,
                `category_name` varchar(250) DEFAULT NULL,
                `product_name` varchar(250) DEFAULT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`kb_banner_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            $installer->run($table_script);

            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
            $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $currentStore = $storeManager->getStore();
            $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $url = $mediaUrl . 'Knowband_Mobileappbuilder/images/sliders/';

            $table_name = $installer->getTable('kb_sliders_banners');
            $table_script = "INSERT INTO `" . $table_name . "` (`kb_banner_id`, `status`, `image_type`, `image_url`, `type`, `redirect_activity`, `category_id`, `product_id`, `product_name`, `date_add`, `date_upd`) VALUES (NULL, '1', 'url', '" . $url . "sample-slider1.jpg', 'slider', 'category', '11', NULL, NULL, NOW(), NOW());";
            $installer->run($table_script);

            $table_script = "INSERT INTO `" . $table_name . "` (`kb_banner_id`, `status`, `image_type`, `image_url`, `type`, `redirect_activity`, `category_id`, `product_id`, `product_name`, `date_add`, `date_upd`) VALUES (NULL, '1', 'url', '" . $url . "sample-slider2.jpg', 'slider', 'category', '11', NULL, NULL, NOW(), NOW());";
            $installer->run($table_script);

            $table_script = "INSERT INTO `" . $table_name . "` (`kb_banner_id`, `status`, `image_type`, `image_url`, `type`, `redirect_activity`, `category_id`, `product_id`, `product_name`, `date_add`, `date_upd`) VALUES (NULL, '1', 'url', '" . $url . "sample-slider3.jpg', 'slider', 'category', '11', NULL, NULL, NOW(), NOW());";
            $installer->run($table_script);

            $table_script = "INSERT INTO `" . $table_name . "` (`kb_banner_id`, `status`, `image_type`, `image_url`, `type`, `redirect_activity`, `category_id`, `product_id`, `product_name`, `date_add`, `date_upd`) VALUES (NULL, '1', 'url', '" . $url . "sample-banner1.jpg', 'banner', 'category', '11', NULL, NULL, NOW(), NOW());";
            $installer->run($table_script);

            $table_script = "INSERT INTO `" . $table_name . "` (`kb_banner_id`, `status`, `image_type`, `image_url`, `type`, `redirect_activity`, `category_id`, `product_id`, `product_name`, `date_add`, `date_upd`) VALUES (NULL, '1', 'url', '" . $url . "sample-banner2.jpg', 'banner', 'category', '11', NULL, NULL, NOW(), NOW());";
            $installer->run($table_script);
        }

        $table_name = $installer->getTable('kb_fcm_details');
        if ($installer->getConnection()->isTableExists($table_name) != true) {

            $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
               `fcm_details_id` int(11) NOT NULL AUTO_INCREMENT,
                `kb_email` varchar(125) NOT NULL,
                `fcm_id` text NOT NULL,
                `notification_sent_status` int(2) DEFAULT NULL,
                `device_type` varchar(25) DEFAULT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`fcm_details_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $installer->run($table_script);
        }

        $table_name = $installer->getTable('kb_payment_details');
        if ($installer->getConnection()->isTableExists($table_name) != true) {

            $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `kb_payment_id` int(11) NOT NULL AUTO_INCREMENT,
                `kb_payment_code` varchar(125) NOT NULL,
                `kb_payment_name` varchar(125) NOT NULL,
                `status` smallint(1) NOT NULL DEFAULT '0',
                `values` text NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`kb_payment_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $installer->run($table_script);

            $paypal_values = array(
                'payment_method_mode' => 'live',
                'client_id' => '',
                'is_default' => 'no',
                'other_info' => '',
            );

            $table_name = $installer->getTable('kb_payment_details');
            $table_script = "INSERT INTO `" . $table_name . "` (`kb_payment_id`, `kb_payment_code`, `kb_payment_name`, `status`, `values`, `date_add`, `date_upd`) VALUES (NULL, 'paypal', '" . __("Paypal Mobile") . "', '0', '" . json_encode($paypal_values) . "', NOW(), NOW());";
            $installer->run($table_script);

            $table_script = "INSERT INTO `" . $table_name . "` (`kb_payment_id`, `kb_payment_code`, `kb_payment_name`, `status`, `values`, `date_add`, `date_upd`) VALUES (NULL, 'cod', '" . __("COD Mobile") . "', '0', '" . json_encode($paypal_values) . "', NOW(), NOW());";
            $installer->run($table_script);
        }

        $table_name = $installer->getTable('kb_orderstatus_details');
        if ($installer->getConnection()->isTableExists($table_name) != true) {

            $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `kb_orderstatus_id` int(11) NOT NULL AUTO_INCREMENT,
                `order_id` int(10) NOT NULL,
                `order_status` varchar(32) DEFAULT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`kb_orderstatus_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $installer->run($table_script);
        }

        $installer->endSetup();
    }

}
