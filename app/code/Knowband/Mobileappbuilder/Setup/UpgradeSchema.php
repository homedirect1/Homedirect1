<?php

namespace Knowband\Mobileappbuilder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $table_name = $installer->getTable('kb_mobileapp_banners');
            if ($installer->getConnection()->isTableExists($table_name) != true) {
                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_component` int(11) NOT NULL,
                `id_banner_type` int(11) NOT NULL,
                `countdown` varchar(200) DEFAULT NULL,
                `product_id` int(10) DEFAULT NULL,
                `category_id` int(10) DEFAULT NULL,
                `redirect_activity` varchar(100) NOT NULL,
                `image_url` longtext,
                `image_type` varchar(100) DEFAULT NULL,
                `product_name` varchar(200) DEFAULT NULL,
                `image_path` longtext,
                `image_content_mode` varchar(200) NOT NULL,
                `banner_heading` varchar(200) DEFAULT NULL,
                `background_color` varchar(11) DEFAULT NULL,
                `is_enabled_background_color` int(10) NOT NULL DEFAULT '1',
                `text_color` varchar(11) DEFAULT NULL,
                `position` int(11) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);



                $table_name = $installer->getTable('kb_mobileapp_banners');
                $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
                $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
                $currentStore = $storeManager->getStore();
                $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $url_path = $mediaUrl . 'Knowband_Mobileappbuilder/images/sliders/';
                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `id_component`, `id_banner_type`, `position`, `countdown`, `product_id`, `category_id`, `redirect_activity`, `image_url`, `image_type`, `product_name`, `image_path`, `image_content_mode`, `banner_heading`, `background_color`, `is_enabled_background_color`, `text_color`) VALUES (1, 1, 2, 1, NULL, 0, 0, 'home', '" . $url_path . "/banner1.jpg', 'url', '231', NULL, 'scaleAspectFill', NULL, NULL, 0, NULL);";
                $installer->run($table_script);
                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `id_component`, `id_banner_type`, `position`, `countdown`, `product_id`, `category_id`, `redirect_activity`, `image_url`, `image_type`, `product_name`, `image_path`, `image_content_mode`, `banner_heading`, `background_color`, `is_enabled_background_color`, `text_color`) VALUES (2, 1, 2, 2, NULL, 0, 0, 'home', '" . $url_path . "/banner2.jpg', 'url', '231', NULL, 'scaleAspectFill', NULL, NULL, 0, NULL);";
                $installer->run($table_script);
                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `id_component`, `id_banner_type`, `position`, `countdown`, `product_id`, `category_id`, `redirect_activity`, `image_url`, `image_type`, `product_name`, `image_path`, `image_content_mode`, `banner_heading`, `background_color`, `is_enabled_background_color`, `text_color`) VALUES (3, 2, 2, 3, NULL, 0, 0, 'home', '" . $url_path . "/slider1.jpg', 'url', '231', NULL, 'scaleAspectFill', NULL, NULL, 0, NULL);";
                $installer->run($table_script);
                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `id_component`, `id_banner_type`, `position`, `countdown`, `product_id`, `category_id`, `redirect_activity`, `image_url`, `image_type`, `product_name`, `image_path`, `image_content_mode`, `banner_heading`, `background_color`, `is_enabled_background_color`, `text_color`) VALUES (4, 2, 5, 1, NULL, 0, 0, 'home', '" . $url_path . "/slider2.jpg', 'url', '231', NULL, 'scaleAspectFill', NULL, NULL, 0, NULL);";
                $installer->run($table_script);
                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `id_component`, `id_banner_type`, `position`, `countdown`, `product_id`, `category_id`, `redirect_activity`, `image_url`, `image_type`, `product_name`, `image_path`, `image_content_mode`, `banner_heading`, `background_color`, `is_enabled_background_color`, `text_color`) VALUES (5, 2, 5, 2, NULL, 0, 0, 'home', '" . $url_path . "/slider3.jpg', 'url', '231', NULL, 'scaleAspectFill', NULL, NULL, 0, NULL);";
                $installer->run($table_script);
            }

            $table_name = $installer->getTable('kb_mobileapp_component_types');
            if ($installer->getConnection()->isTableExists($table_name) != true) {
                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `component_name` varchar(200) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);

                $table_name = $installer->getTable('kb_mobileapp_component_types');
                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (1, 'top_category');";
                $installer->run($table_script);

                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (2, 'banner_square');";
                $installer->run($table_script);

                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (3, 'banners_countdown');";
                $installer->run($table_script);

                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (4, 'banners_grid');";
                $installer->run($table_script);

                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (5, 'banner_horizontal_slider');";
                $installer->run($table_script);

                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (6, 'products_square');";
                $installer->run($table_script);

                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (7, 'products_horizontal');";
                $installer->run($table_script);

                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (8, 'products_recent');";
                $installer->run($table_script);

                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (9, 'products_grid');";
                $installer->run($table_script);
            }



            $table_name = $installer->getTable('kb_mobileapp_product_data');
            if ($installer->getConnection()->isTableExists($table_name) != true) {
                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `product_type` varchar(200) NOT NULL,
                `category_products` text,
                `custom_products` text,
                `image_content_mode` varchar(200) NOT NULL,
                `number_of_products` int(11) NOT NULL,
                `id_category` int(11) DEFAULT NULL,
                `id_component` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);


                $table_name = $installer->getTable('kb_mobileapp_product_data');
                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `product_type`, `category_products`, `custom_products`, `image_content_mode`, `number_of_products`, `id_category`, `id_component`) VALUES (1, 'best_seller', NULL, NULL, 'scaleAspectFill', 10, 0, 3);";
                $installer->run($table_script);
                $table_script = "INSERT INTO `" . $table_name . "` (`id`, `product_type`, `category_products`, `custom_products`, `image_content_mode`, `number_of_products`, `id_category`, `id_component`) VALUES (3, 'new_products', NULL, NULL, 'scaleAspectFill', 10, 0, 6);";
                $installer->run($table_script);
            }

            $table_name = $installer->getTable('kb_mobileapp_top_category');
            if ($installer->getConnection()->isTableExists($table_name) != true) {
                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_component` int(11) NOT NULL,
                `id_category` varchar(200) NOT NULL,
                `image_url` longtext,
                `image_content_mode` varchar(200) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);
            }

            $table_name = $installer->getTable('kb_mobileapp_unique_verification');
            if ($installer->getConnection()->isTableExists($table_name) != true) {
                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id_verification` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_customer` int(10) UNSIGNED NOT NULL,
                `store_id` smallint(5) unsigned default '0',
                `mobile_number` varchar(100) DEFAULT NULL,
                `country_code` varchar(10) DEFAULT NULL,
                `fid` varchar(100) DEFAULT NULL,
                `date_added` datetime NOT NULL,
                `date_update` datetime NOT NULL,
                PRIMARY KEY (`id_verification`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);
            }

            $table_name = $installer->getTable('kb_mobileapp_layouts');
            if ($installer->getConnection()->isTableExists($table_name) != true) {
                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id_layout` int(11) NOT NULL AUTO_INCREMENT,
                `layout_name` varchar(200) NOT NULL,
                `store_id` smallint(5) unsigned default '0',
                `date_added` datetime NOT NULL,
                `date_update` datetime NOT NULL,
                PRIMARY KEY (`id_layout`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);



                $table_name = $installer->getTable('kb_mobileapp_layouts');
                $table_script = "INSERT INTO `" . $table_name . "` (`id_layout`, `store_id`, `layout_name`, `date_added`, `date_update`) VALUES (1, 0, 'Default Layout', '2019-01-30 15:10:30', '2019-01-30 15:10:30');";
                $installer->run($table_script);
            }

            $table_name = $installer->getTable('kb_mobileapp_layout_component');
            if ($installer->getConnection()->isTableExists($table_name) != true) {
                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id_component` int(11) NOT NULL AUTO_INCREMENT,
                `id_layout` int(11) NOT NULL,
                `id_component_type` int(11) NOT NULL,
                `position` int(11) NOT NULL,
                `component_heading` varchar(200) DEFAULT NULL,
                PRIMARY KEY (`id_component`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);


                $table_name = $installer->getTable('kb_mobileapp_layout_component');
                $table_script = "INSERT INTO `" . $table_name . "` (`id_component`, `id_layout`, `id_component_type`, `position`, `component_heading`) VALUES (1, 1, 2, 1, NULL);";
                $installer->run($table_script);
                $table_script = "INSERT INTO `" . $table_name . "` (`id_component`, `id_layout`, `id_component_type`, `position`, `component_heading`) VALUES (2, 1, 5, 2, NULL);";
                $installer->run($table_script);
                $table_script = "INSERT INTO `" . $table_name . "` (`id_component`, `id_layout`, `id_component_type`, `position`, `component_heading`) VALUES (3, 1, 6, 3, NULL);";
                $installer->run($table_script);
                $table_script = "INSERT INTO `" . $table_name . "` (`id_component`, `id_layout`, `id_component_type`, `position`, `component_heading`) VALUES (6, 1, 9, 4, NULL);";
                $installer->run($table_script);
            }
        

        $installer->getConnection()
                ->addForeignKey(
                        $installer->getFkName(
                                $installer->getTable('kb_mobileapp_layout_component'), 'id_layout', $installer->getTable('kb_mobileapp_layouts'), 'id_layout'
                        ), $installer->getTable('kb_mobileapp_layout_component'), 'id_layout', $installer->getTable('kb_mobileapp_layouts'), 'id_layout', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()
                ->addForeignKey(
                        $installer->getFkName(
                                $installer->getTable('kb_mobileapp_banners'), 'id_component', $installer->getTable('kb_mobileapp_layout_component'), 'id_component'
                        ), $installer->getTable('kb_mobileapp_banners'), 'id_component', $installer->getTable('kb_mobileapp_layout_component'), 'id_component', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()
                ->addForeignKey(
                        $installer->getFkName(
                                $installer->getTable('kb_mobileapp_product_data'), 'id_component', $installer->getTable('kb_mobileapp_layout_component'), 'id_component'
                        ), $installer->getTable('kb_mobileapp_product_data'), 'id_component', $installer->getTable('kb_mobileapp_layout_component'), 'id_component', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()
                ->addForeignKey(
                        $installer->getFkName(
                                $installer->getTable('kb_mobileapp_top_category'), 'id_component', $installer->getTable('kb_mobileapp_layout_component'), 'id_component'
                        ), $installer->getTable('kb_mobileapp_top_category'), 'id_component', $installer->getTable('kb_mobileapp_layout_component'), 'id_component', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        }
        
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
             $table_name = $installer->getTable('kb_mobileapp_banners');
            if ($installer->getConnection()->isTableExists($table_name)) {
                if (!$installer->getConnection()->tableColumnExists($table_name, 'height')) {
                    $table_script = "ALTER TABLE `" . $table_name . "` ADD `height` varchar(200) NOT NULL AFTER `text_color`;";
                    $installer->run($table_script);
                    $table_script = "ALTER TABLE `" . $table_name . "` ADD `width` varchar(200) NOT NULL AFTER `height`;";
                    $installer->run($table_script);
                    $table_script = "ALTER TABLE `" . $table_name . "` ADD `top_margin` varchar(200) NOT NULL AFTER `width`;";
                    $installer->run($table_script);
                    $table_script = "ALTER TABLE `" . $table_name . "` ADD `bottom_margin` varchar(200) NOT NULL AFTER `top_margin`;";
                    $installer->run($table_script);
                    $table_script = "ALTER TABLE `" . $table_name . "` ADD `right_margin` varchar(200) NOT NULL AFTER `bottom_margin`;";
                    $installer->run($table_script);
                    $table_script = "ALTER TABLE `" . $table_name . "` ADD `left_margin` varchar(200) NOT NULL AFTER `right_margin`;";
                    $installer->run($table_script);
                    $table_script = "ALTER TABLE `" . $table_name . "` ADD `is_sliding` int(2) DEFAULT 0 AFTER `left_margin`;";
                    $installer->run($table_script);
                }
            }
        }
        /*
         * changes started by rishabh jain
         * For adding banner_custom element in the component table
         */
        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $table_name = $installer->getTable('kb_mobileapp_component_types');
            $table_script = "INSERT INTO `" . $table_name . "` (`id`, `component_name`) VALUES (10, 'banner_custom');";
            $installer->run($table_script);
        }
        /*
         * changes over
         */
        $installer->endSetup();
    }

}
