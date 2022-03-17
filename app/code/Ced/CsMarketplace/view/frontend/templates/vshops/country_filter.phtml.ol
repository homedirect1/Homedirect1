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
 * @package     Ced_CsMarketplace
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

// @codingStandardsIgnoreFile

?>
<?php
$listBlock = $block->getLayout()->createBlock('Ced\CsMarketplace\Block\Vshops\ListBlock');
 $params=$block->getRequest()->setParams(array('_GET'))->getParams();
 $addressParam=array('country_id','region','region_id','estimate_postcode','char');
 foreach( $addressParam as $inputName)
 {
	if(isset($params[$inputName]))
	unset($params[$inputName]);
 }
?>
<div class="shipping">
    <h2><?= /* @noEscape */ __('Search Stores')?></h2>
    <form action="<?= /* @noEscape */ $block->getUrl('csmarketplace/vshops/index',$params) ?>" method="get" id="vshop-filter-form">
        <?= $block->getBlockHtml('formkey'); ?>
            <ul class="form-list" style="padding-left:0px">
                <li>
                <label for="country" class=""><?= /* @noEscape */ __('Country') ?></label>
                    <div class="input-box">
                    <?= /* @noEscape */ $block->getLayout()->createBlock('Magento\Directory\Block\Data')->getCountryHtmlSelect($block->getEstimateCountryId()) ?>
                    </div>
                </li>
           		 <?php if($block->getStateActive()): ?>
                <li>
                    <label for="region_id" ><?= /* @noEscape */ "State/Province" ?></label>
                    <div class="input-box">
                        <select id="region_id" name="region_id" title="<?= /* @noEscape */ __('State/Province') ?>" style="display:none;" >
                            <option value=""><?= /* @noEscape */ __('Please select region, state or province') ?></option>
                        </select>

                        <input type="text" id="region" name="region" value="<?= /* @noEscape */ $block->getRequest()->getParam('region') ?>"  title="<?= /* @noEscape */ __('State/Province') ?>" class="input-text" style="display:none;" />
                   </div>
                </li>
           		 <?php endif; ?>
           		 <?php if($block->getCityActive()): ?>
                <li>
                    <label for="city"<?php if ($block->isCityRequired()) echo ' class="required"' ?>><?php if ($block->isCityRequired()) echo '<em>*</em>' ?><?= /* @noEscape */ __('City') ?></label>
                    <div class="input-box">
                        <input class="input-text<?php if ($block->isCityRequired()):?> required-entry<?php endif;?>" id="city" type="text" name="estimate_city" value="<?= /* @noEscape */ $block->getEstimateCity() ?>" />
                    </div>
                </li>
            	<?php endif; ?>
                <li>
                <label for="postcode"><?= /* @noEscape */ __('Zip/Postal Code') ?></label>
                    <div class="input-box">
                    <input class="input-text validate-postcode" type="text" id="postcode" name="estimate_postcode" value="<?= /* @noEscape */ $block->getRequest()->getParam('estimate_postcode') ?>" />
                    </div>
                </li>
				<li>
                <label for="postcode"><?= /* @noEscape */ __('Name') ?></label>
                    <div class="input-box">
                    <input class="input-text validate-postcode" type="text" id="char" name="char" value="<?= /* @noEscape */ $block->getRequest()->getParam('char') ?>" />

                    </div>
                </li>
            </ul>
            <div class="buttons-set">
            <button type="submit" title="<?= /* @noEscape */ __('Search') ?>"  class="button"><span><span><?= /* @noEscape */ __('Search') ?></span></span></button>

            <?php
            $params = $block->getRequest()->getParams();
            $Url = $block->getUrl('csmarketplace/vshops/index');
            if(isset($params['country_id'])){?>
                <button type="button" onclick="relaod()" title="<?= /* @noEscape */ __('Reset') ?>"  class="button"><span><span><?= /* @noEscape */ __('Reset') ?></span></span></button>
            <?php }
            ?>
            </div>
        </form>
        <script type="text/javascript">
        function relaod(){
            var param = "<?= /* @noEscape */ (isset($params['product_list_mode'])?$params['product_list_mode']:'') ?>";
            var url = "<?= /* @noEscape */ $Url;?>";
            if (param !== '') {
                url = url+"?product_list_mode="+param;
            }
            window.location.href = url;
        }

        </script>
		<script>
        require([
                'mage/url',
                'jquery'
            ], function() {
                jQuery('#country').val('<?= /* @noEscape */ $block->getRequest()->getParam('country_id') ?>');
                jQuery('#country').removeAttr('data-validate');

            });


         require([
            'jquery',
            'jquery/ui',
            'regionUpdater'
           ], function($){

           });
        </script>
        <script type="text/x-magento-init">
            {
                "#vshop-filter-form": {
                    "validation": {}
                },
                "#country_id": {
                    "regionUpdater": {
                        "optionalRegionAllowed": "true",
                        "regionListId": "#region_id",
                        "regionInputId": "#region",
                        "postcodeId": "#char",
                        "form": "#vshop-filter-form",
                        "regionJson": <?= /* @noEscape */ $listBlock->getMagentoDirectoryHelper()->getRegionJson() ?>,
                        "defaultRegion": "<?= /* @noEscape */ $block->getRegionId() ?>",
                        "countriesWithOptionalZip": <?= /* @noEscape */ $listBlock->getMagentoDirectoryHelper()->getCountriesWithOptionalZip(true) ?>
                    }
                }
            }
        </script>
</div>
