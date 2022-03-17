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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Block\Adminhtml;

/**
 * Class Config
 * @package Ced\CsHyperlocal\Block\Adminhtml
 */
class Config extends \Magento\Config\Block\System\Config\Form\Field
{

    protected $_request;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $csmarketplaceHelper;

    /**
     * Config constructor.
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     */
    public function __construct(
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
    )
    {
        $this->csmarketplaceHelper = $csmarketplaceHelper;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $apiKey = $this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::API_KEY);
        $html = '';
        $html .= "<script src='https://maps.googleapis.com/maps/api/js?libraries=places&key=" . $apiKey . "' type='text/javascript'></script><script>
			
                var input = document.getElementById('ced_cshyperlocal_admin_default_address_location');
                var autocomplete = new google.maps.places.Autocomplete(input);
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    var place = autocomplete.getPlace();
                    var latitude = place.geometry.location.lat();
                    var longitude = place.geometry.location.lng();

                    jQuery('#ced_cshyperlocal_admin_default_address_latitude').val(latitude);
                    jQuery('#ced_cshyperlocal_admin_default_address_longitude').val(longitude);

                    var arrAddress = place.address_components;
                    jQuery('#ced_cshyperlocal_admin_default_address_city').val('');
                    jQuery('#ced_cshyperlocal_admin_default_address_state').val('');
                    jQuery('#ced_cshyperlocal_admin_default_address_country').val('');
                    jQuery('#ced_cshyperlocal_admin_default_address_zipcode').val('');
                    jQuery.each(arrAddress, function (i, address_component) {
                        if (address_component.types[0] == 'administrative_area_level_2') {
                            jQuery('#ced_cshyperlocal_admin_default_address_city').val(address_component.long_name);
                        }
                        if (address_component.types[0] == 'administrative_area_level_1') {
                            jQuery('#ced_cshyperlocal_admin_default_address_state').val(address_component.long_name);
                        }
                        if (address_component.types[0] == 'country') {
                            jQuery('#ced_cshyperlocal_admin_default_address_country').val(address_component.long_name);
                        }
                        if (address_component.types[0] == 'postal_code') {
                            jQuery('#ced_cshyperlocal_admin_default_address_zipcode').val(address_component.long_name);
                        }
                    });
                });
                function gm_authFailure()
                {
                    alert('" . __('There has been an error in map API key, please check the map api key.') . "');
                }
                require([
                    'jquery'
                ],
                function ($) {

                    $('#ced_cshyperlocal_admin_default_address_location').on('focus', function () {
                            selectedLocation = false;
                    }).on('blur', function () {
                        if (!selectedLocation) {
                            $(this).val('');
                        }
                    });
                });
              
				</script>";
        return $html;
    }
}
