var allowedFileSize = '1024';
var allowedExtensions = ['json'];

function validateConfigurationsForm()
{
    jQuery("body").loader("show");
    var is_error = false;
    var general_settings_tab = 0;
    var menu_settings_tab = 0;
    var pn_settings_tab = 0;
    var pn_history_tab = 0;

    jQuery('.kb_error_message').remove();
    jQuery('#mobileappbuilder_view input').removeClass('kb_error_field');
    jQuery('#mobileappbuilder_view input').parent().removeClass('kb_error_field');
    jQuery('#mobileappbuilder_view textarea').removeClass('kb_error_field');
    jQuery('#home_product_list_chosen').removeClass('kb_error_field');
    jQuery('#mobileappbuilder_home_page_layout').removeClass('kb_error_field');
    jQuery('#logo_image').removeClass('kb_error_field');

    /*Knowband validation start*/

//    var selected_wm_cat = jQuery('#home_product_list').val();
//    if (jQuery('#home_product_list').val() == null) {
//        is_error = true;
//        jQuery('#home_product_list_chosen').addClass('kb_error_field');
//        jQuery('#home_product_list_chosen').after('<span class="kb_error_message">' + velovalidation.error('select_field') + '</span>');
//        general_settings_tab = 1;
//    }

    if (jQuery('#mobileappbuilder_enabledlivechat').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_livechatkey'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_livechatkey').addClass('kb_error_field');
            jQuery('#mobileappbuilder_livechatkey').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            general_settings_tab = 1;
        }
    }

    if (jQuery('#mobileappbuilder_enabledwhatsappchat').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_whatsappchatnumber'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_whatsappchatnumber').addClass('kb_error_field');
            jQuery('#mobileappbuilder_whatsappchatnumber').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            general_settings_tab = 1;
        }
    }

    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_home_page_layout'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#mobileappbuilder_home_page_layout').addClass('kb_error_field');
        jQuery('#mobileappbuilder_home_page_layout').after('<p class="kb_error_message">' + disint_mandatory_err + '</p>');
        general_settings_tab = 1;
    }

    if (jQuery('#mobileappbuilder_enablelogo').is(":checked")) {

        var check_logo_image = jQuery('#logo_image').val();
        var get_preview_file = jQuery('#logo_image_preview').attr('src');
        if (get_preview_file.indexOf('no_image') > 0 && check_logo_image == '') {
            is_error = true;
            jQuery('#logo_image').addClass('kb_error_field');
            jQuery('#logo_image').after('<span class="kb_error_message">' + image_empty + '</span>');
            general_settings_tab = 1;
        } else if (check_logo_image != '') {
            var disint_mandatory_err = velovalidation.checkImage(jQuery('#logo_image'), 2097152, 'b');
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#logo_image').addClass('kb_error_field');
                jQuery('#logo_image').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            }
            general_settings_tab = 1;
        }
    }

    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_app_button_color'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#mobileappbuilder_app_button_color').addClass('kb_error_field');
        jQuery('#mobileappbuilder_app_button_color').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        general_settings_tab = 1;
    } else {
        var is_color_err = velovalidation.isColor(jQuery('#mobileappbuilder_app_button_color'));
        if (is_color_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_app_button_color').addClass('kb_error_field');
            jQuery('#mobileappbuilder_app_button_color').parent().after('<span class="kb_error_message">' + is_color_err + '</span>');
            general_settings_tab = 1;
        }
    }

    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_app_button_text_color'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#mobileappbuilder_app_button_text_color').addClass('kb_error_field');
        jQuery('#mobileappbuilder_app_button_text_color').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        general_settings_tab = 1;
    } else {
        var is_color_err = velovalidation.isColor(jQuery('#mobileappbuilder_app_button_text_color'));
        if (is_color_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_app_button_text_color').addClass('kb_error_field');
            jQuery('#mobileappbuilder_app_button_text_color').parent().after('<span class="kb_error_message">' + is_color_err + '</span>');
            general_settings_tab = 1;
        }
    }

    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_app_theme_color'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#mobileappbuilder_app_theme_color').addClass('kb_error_field');
        jQuery('#mobileappbuilder_app_theme_color').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        general_settings_tab = 1;
    } else {
        var is_color_err = velovalidation.isColor(jQuery('#mobileappbuilder_app_theme_color'));
        if (is_color_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_app_theme_color').addClass('kb_error_field');
            jQuery('#mobileappbuilder_app_theme_color').parent().after('<span class="kb_error_message">' + is_color_err + '</span>');
            general_settings_tab = 1;
        }
    }

    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_app_background_color'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#mobileappbuilder_app_background_color').addClass('kb_error_field');
        jQuery('#mobileappbuilder_app_background_color').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        general_settings_tab = 1;
    } else {
        var is_color_err = velovalidation.isColor(jQuery('#mobileappbuilder_app_background_color'));
        if (is_color_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_app_background_color').addClass('kb_error_field');
            jQuery('#mobileappbuilder_app_background_color').parent().after('<span class="kb_error_message">' + is_color_err + '</span>');
            general_settings_tab = 1;
        }
    }

    var customcss_tags_err = velovalidation.checkTags(jQuery('#mobileappbuilder_customcss'));
    if (customcss_tags_err != true)
    {
        is_error = true;
        jQuery('#mobileappbuilder_customcss').addClass('kb_error_field');
        jQuery('#mobileappbuilder_customcss').after('<span class="kb_error_message">' + customcss_tags_err + '</span>');
        general_settings_tab = 1;
    }

    if (jQuery('#menu1_enabled').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#menu1_title'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#menu1_title').addClass('kb_error_field');
            jQuery('#menu1_title').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            menu_settings_tab = 1;
        }
    }

    if (jQuery('#menu2_enabled').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#menu2_title'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#menu2_title').addClass('kb_error_field');
            jQuery('#menu2_title').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            menu_settings_tab = 1;
        }
    }

    if (jQuery('#menu3_enabled').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#menu3_title'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#menu3_title').addClass('kb_error_field');
            jQuery('#menu3_title').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            menu_settings_tab = 1;
        }
    }

    if (jQuery('#menu4_enabled').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#menu4_title'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#menu4_title').addClass('kb_error_field');
            jQuery('#menu4_title').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            menu_settings_tab = 1;
        }
    }


    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#firebase_server_key'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#firebase_server_key').addClass('kb_error_field');
        jQuery('#firebase_server_key').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        pn_settings_tab = 1;
    }

    if (jQuery('#order_create_enable').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#order_create_title'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#order_create_title').addClass('kb_error_field');
            jQuery('#order_create_title').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            pn_settings_tab = 1;
        }

        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_order_create_message'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_order_create_message').addClass('kb_error_field');
            jQuery('#mobileappbuilder_order_create_message').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            pn_settings_tab = 1;
        }
    }

    if (jQuery('#order_status_change_enable').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#order_status_change_title'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#order_status_change_title').addClass('kb_error_field');
            jQuery('#order_status_change_title').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            pn_settings_tab = 1;
        }

        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_order_status_change_message'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_order_status_change_message').addClass('kb_error_field');
            jQuery('#mobileappbuilder_order_status_change_message').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            pn_settings_tab = 1;
        }
    }

    if (jQuery('#abandoned_cart_enable').is(":checked")) {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#abandoned_cart_title'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#abandoned_cart_title').addClass('kb_error_field');
            jQuery('#abandoned_cart_title').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            pn_settings_tab = 1;
        }

        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#mobileappbuilder_abandoned_cart_message'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#mobileappbuilder_abandoned_cart_message').addClass('kb_error_field');
            jQuery('#mobileappbuilder_abandoned_cart_message').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            pn_settings_tab = 1;
        }

        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#abandoned_cart_interval'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#abandoned_cart_interval').addClass('kb_error_field');
            jQuery('#abandoned_cart_interval').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            pn_settings_tab = 1;
        } else {
            var disint_positive_err = velovalidation.isNumeric(jQuery('#abandoned_cart_interval'), true);
            if (disint_positive_err != true) {
                is_error = true;
                jQuery('#abandoned_cart_interval').addClass('kb_error_field');
                jQuery('#abandoned_cart_interval').parent().after('<span class="kb_error_message">' + disint_positive_err + '</span>');
                pn_settings_tab = 1;
            } else {
                if (jQuery('#abandoned_cart_interval').val() == 0) {
                    is_error = true;
                    jQuery('#abandoned_cart_interval').addClass('kb_error_field');
                    jQuery('#abandoned_cart_interval').parent().after('<span class="kb_error_message">' + zero_error + '</span>');
                    pn_settings_tab = 1;
                }
            }
        }
    }


    if (is_error) {
        if (general_settings_tab === 1) {
            jQuery("#mobileappbuilder_view_tabs_mobileappbuilder_general > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        if (menu_settings_tab === 1) {
            jQuery("#mobileappbuilder_view_tabs_mobileappbuilder_mobile_menu_settings > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        if (pn_settings_tab === 1) {
            jQuery("#mobileappbuilder_view_tabs_mobileappbuilder_push_notification_settings > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        jQuery("body").loader("hide");
        return false;
    }

    /*Knowband button validation start*/
    jQuery('#save-mobileappbuilder').attr('disabled', 'disabled');
    /*Knowband button validation end*/

    return true;
}


function validatePushNotifcationForm()
{
    jQuery("body").loader("show");
    var is_error = false;

    jQuery('.kb_error_message').remove();
    jQuery('input').removeClass('kb_error_field');
    jQuery('textarea').removeClass('kb_error_field');
    jQuery('select').removeClass('kb_error_field');

    /*Knowband validation start*/
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#title'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#title').addClass('kb_error_field');
        jQuery('#title').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
    }

    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#message'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#message').addClass('kb_error_field');
        jQuery('#message').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
    }

    var selected_value = jQuery('#image_type').val();
    if (selected_value == '0') {
        is_error = true;
        jQuery('#image_type').addClass('kb_error_field');
        jQuery('#image_type').parent().after('<span class="kb_error_message">' + velovalidation.error('select_field') + '</span>');
    } else {
        if (selected_value == 'url') {
            var check_logo_image = jQuery('#image_url').val();
            if (check_logo_image == '') {
                is_error = true;
                jQuery('#image_url').addClass('kb_error_field');
                jQuery('#image_url').after('<span class="kb_error_message">' + image_empty + '</span>');
            } else {
                var check_url = velovalidation.checkUrl(jQuery('#image_url'));
                if (check_url != true) {
                    is_error = true;
                    jQuery('#image_url').addClass('kb_error_field');
                    jQuery('#image_url').after('<span class="kb_error_message">' + check_url + '</span>');
                }
            }
        } else if (selected_value == 'image') {
            var check_logo_image = jQuery('#is_uploaded').val();
            if (check_logo_image == 0) {
                is_error = true;
                jQuery('#image_upload').addClass('kb_error_field');
                jQuery('#image_upload').after('<span class="kb_error_message">' + image_empty + '</span>');
            } else if (check_logo_image != '') {
                var disint_mandatory_err = velovalidation.checkImage(jQuery('#image_upload'), 2097152, 'mb');
                if (disint_mandatory_err != true) {
                    is_error = true;
                    jQuery('#image_upload').addClass('kb_error_field');
                    jQuery('#image_upload').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
                }
            }
        }
    }


    var selected_value = jQuery('#redirect_activity').val();
    if (selected_value == 'product') {
        var selected_option = jQuery('#product_id').val();
        if (selected_option == '0') {
            is_error = true;
            jQuery('#product_id').addClass('kb_error_field');
            jQuery('#product_id').parent().after('<span class="kb_error_message">' + velovalidation.error('select_field') + '</span>');
        }
    } else if (selected_value == 'category') {
        var selected_option = jQuery('#category_id').val();
        if (selected_option == '0') {
            is_error = true;
            jQuery('#category_id').addClass('kb_error_field');
            jQuery('#category_id').parent().after('<span class="kb_error_message">' + velovalidation.error('select_field') + '</span>');
        }
    }

    if (is_error) {
        jQuery("body").loader("hide");
        return false;
    }

    jQuery('.button_submit_form').attr('disabled', true);
    var form = new FormData(jQuery('#form_send_notification')[0]);
    jQuery.ajax({
        url: send_push_ajax_url,
        type: 'post',
        contentType: false,
        processData: false,
        data: form,
        dataType: 'json',
        success: function (retjson) {
            if (retjson.error) {
                jQuery('#show_error').html('<div class="alert alert-danger" role="alert">' + retjson.msg + '</div>');
                jQuery('.button_submit_form').attr('disabled', false);
            } else {
                jQuery('#new_noti_popup').html("");
                jQuery('#new_noti_popup').html('<div class="alert alert-success" role="alert">' + retjson.msg + '</div>');
                jQuery('.button_submit_form').hide();
                jQuery('.new_noti_popup_close').hide();
                setTimeout(function () {
                    jQuery('.new_noti_popup_close').click();
                    AutoswitchGridJsObject.doFilter();
                },
                        2000)
            }
            jQuery("body").loader("hide");
        },
        error: function () {
            jQuery("body").loader("hide");
            jQuery('.button_submit_form').attr('disabled', false);
        }
    });

    return true;
}

function validateAddLayoutForm()
{
//    jQuery("body").loader("show");
    var is_error = false;

    jQuery('.kb_error_message').remove();
    jQuery('input').removeClass('kb_error_field');
    jQuery('textarea').removeClass('kb_error_field');
    jQuery('select').removeClass('kb_error_field');

    /*Knowband validation start*/
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#layout_name'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#layout_name').addClass('kb_error_field');
        jQuery('#layout_name').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
    }

    if (is_error) {
//        jQuery("body").loader("hide");
        return false;
    }

    jQuery('.button_submit_form').attr('disabled', true);
    var layout_name = jQuery("#layout_name").val();
    var layout_id = jQuery("#layout_id").val();
    var store_id = jQuery("#store_id").val();
//    var form = new FormData(jQuery('#form_new_layout')[0]);
    jQuery.ajax({
        url: send_layout_ajax_url + "?saveLayoutForm=true",
        type: 'post',
        showLoader: true,
//        contentType: false,
//        processData: false,
        data: {
            layout_name: layout_name,
            layout_id: layout_id,
            store_id: store_id,
        },
//        dataType: 'json',
        success: function (retjson) {
            if (retjson.error) {
                jQuery('#show_error').html('<div class="alert alert-danger" role="alert">' + retjson.msg + '</div>');
                jQuery('.button_submit_form').attr('disabled', false);
            } else {
                jQuery('#new_layout_popup').html("");
                jQuery('#new_layout_popup').html('<div class="alert alert-success" role="alert">' + retjson.msg + '</div>');
                jQuery('.button_submit_form').hide();
                jQuery('.new_layout_popup_close').hide();
                setTimeout(function () {
                    jQuery('.new_layout_popup_close').click();
                    AutoswitchLayoutGridJsObject.doFilter();
                },
                    2000)
            }
//            jQuery("body").loader("hide");
        },
        error: function () {
//            jQuery("body").loader("hide");
            jQuery('.button_submit_form').attr('disabled', false);
        }
    });

    return true;
}

function validateBannerForm()
{
    jQuery("body").loader("show");
    var is_error = false;

    jQuery('.kb_error_message').remove();
    jQuery('input').removeClass('kb_error_field');
    jQuery('select').removeClass('kb_error_field');

    var selected_value = jQuery('#banner_popup #image_type').val();
    if (selected_value == '0') {
        is_error = true;
        jQuery('#banner_popup #image_type').addClass('kb_error_field');
        jQuery('#banner_popup #image_type').parent().after('<span class="kb_error_message">' + velovalidation.error('select_field') + '</span>');
    } else {
        if (selected_value == 'url') {
            var check_logo_image = jQuery('#banner_popup #image_url').val();
            if (check_logo_image == '') {
                is_error = true;
                jQuery('#banner_popup #image_url').addClass('kb_error_field');
                jQuery('#banner_popup #image_url').after('<span class="kb_error_message">' + velovalidation.error('empty_field') + '</span>');
            } else {
                var check_url = velovalidation.checkUrl(jQuery('#banner_popup #image_url'));
                if (check_url != true) {
                    is_error = true;
                    jQuery('#banner_popup #image_url').addClass('kb_error_field');
                    jQuery('#banner_popup #image_url').after('<span class="kb_error_message">' + check_url + '</span>');
                }
            }
        } else if (selected_value == 'image') {
            var is_removed = jQuery('#banner_popup #is_removed').val();
            if (is_removed == 1) {
                var is_uploaded = jQuery('#banner_popup #is_uploaded').val();
                if (is_uploaded == 0) {
                    is_error = true;
                    jQuery('#banner_popup #image_upload').addClass('kb_error_field');
                    jQuery('#banner_popup #image_upload').after('<span class="kb_error_message">' + image_empty + '</span>');
                } else if (is_uploaded != '') {
                    var disint_mandatory_err = velovalidation.checkImage(jQuery('#banner_popup #image_upload'), 2097152, 'mb');
                    if (disint_mandatory_err != true) {
                        is_error = true;
                        jQuery('#banner_popup #image_upload').addClass('kb_error_field');
                        jQuery('#banner_popup #image_upload').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
                    }
                }
            }
        }
    }


    var selected_value = jQuery('#banner_popup #redirect_activity').val();
    if (selected_value == 'product') {
        var selected_option = jQuery('#banner_popup #product_id').val();
        if (selected_option == '0') {
            is_error = true;
            jQuery('#banner_popup #product_id').addClass('kb_error_field');
            jQuery('#banner_popup #product_id').parent().after('<span class="kb_error_message">' + velovalidation.error('select_field') + '</span>');
        }
    } else if (selected_value == 'category') {
        var selected_option = jQuery('#banner_popup #category_id').val();
        if (selected_option == '0') {
            is_error = true;
            jQuery('#banner_popup #category_id').addClass('kb_error_field');
            jQuery('#banner_popup #category_id').parent().after('<span class="kb_error_message">' + velovalidation.error('select_field') + '</span>');
        }
    }

    if (is_error) {
        jQuery("body").loader("hide");
        return false;
    }

    jQuery('#banner_popup .button_submit_form_banner').attr('disabled', true);
    var form = new FormData(jQuery('#banner_popup #form_save_banner')[0]);
    jQuery.ajax({
        url: save_banner_ajax_url,
        type: 'post',
        contentType: false,
        processData: false,
        data: form,
        dataType: 'json',
        success: function (retjson) {
            if (retjson.error) {
                jQuery('#banner_popup #show_error').html('<div class="alert alert-danger" role="alert">' + retjson.msg + '</div>');
                jQuery('#banner_popup .button_submit_form_banner').attr('disabled', false);
            } else {
                jQuery('#banner_popup').html("");
                jQuery('#banner_popup').html('<div class="alert alert-success" role="alert">' + retjson.msg + '</div>');
                jQuery('.button_submit_form_banner').hide();
                jQuery('.banner_popup_close').hide();
                setTimeout(function () {
                    jQuery('.banner_popup_close').click();
                    slider_gridJsObject.doFilter();
                    banner_gridJsObject.doFilter();
                },
                        2000)
            }
            jQuery("body").loader("hide");
        },
        error: function () {
            jQuery("body").loader("hide");
            jQuery('#banner_popup .button_submit_form_banner').attr('disabled', false);
        }
    });

    return true;
}
function validatePaymentForm()
{
    jQuery("body").loader("show");
    var is_error = false;

    jQuery('.kb_error_message').remove();
    jQuery('input').removeClass('kb_error_field');
    jQuery('select').removeClass('kb_error_field');

    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#payment_method_name'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#payment_method_name').addClass('kb_error_field');
        jQuery('#payment_method_name').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
    }


    var selected_value = jQuery('#payment_code').val();
    if (selected_value == 'paypal') {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#client_id'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#client_id').addClass('kb_error_field');
            jQuery('#client_id').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        }
    }

    if (is_error) {
        jQuery("body").loader("hide");
        return false;
    }

    jQuery('#payment_popup .button_submit_form_payment').attr('disabled', true);
    var form = new FormData(jQuery('#payment_popup #form_payment_method')[0]);
    jQuery.ajax({
        url: save_payment_ajax_url,
        type: 'post',
        contentType: false,
        processData: false,
        data: form,
        dataType: 'json',
        success: function (retjson) {
            if (retjson.error) {
                jQuery('#payment_popup #show_error').html('<div class="alert alert-danger" role="alert">' + retjson.msg + '</div>');
                jQuery('#payment_popup .button_submit_form_payment').attr('disabled', false);
            } else {
                jQuery('#payment_popup').html("");
                jQuery('#payment_popup').html('<div class="alert alert-success" role="alert">' + retjson.msg + '</div>');
                jQuery('.button_submit_form_payment').hide();
                jQuery('.payment_popup_close').hide();
                setTimeout(function () {
                    jQuery('.payment_popup_close').click();
                    kb_payments_gridJsObject.doFilter()
                },
                        2000)
            }
            jQuery("body").loader("hide");
        },
        error: function () {
            jQuery("body").loader("hide");
            jQuery('#payment_popup .button_submit_form_banner').attr('disabled', false);
        }
    });

    return true;
}

function validateFile(e)
{
    jQuery('.kb_error_message').remove();
    jQuery('#google_json').removeClass('kb_error_field');
    // Getting the values of chosen file
    var fileSize = e.files[0].size / 1000;
    var fileName = e.files[0].name;

    var extension = fileName.split('.').pop();

    var inArrayValue = jQuery.inArray(extension, allowedExtensions);
    if (inArrayValue == -1 || fileSize > allowedFileSize)
    {
        jQuery("#google_json").after('<span class="kb_error_message">' + correct_format + allowedFileSize + " KB. " + extensionString + allowedExtensions + ".</span>");
        jQuery("#google_json").addClass('kb_error_field');
        return false;
    }
    return true;
}

function validateAppId() {
    jQuery('.kb_error_message').remove();
    jQuery('#facebook_app_id').removeClass('kb_error_field');
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#facebook_app_id'));
    if (disint_mandatory_err != true) {
        jQuery('#facebook_app_id').addClass('kb_error_field');
        jQuery('#facebook_app_id').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
    } else {
        jQuery('.fb_validate_button').attr('disabled', false);
        jQuery("body").loader("show");
        jQuery.ajax({
            url: 'https://graph.facebook.com/' + jQuery('#facebook_app_id').val(),
            dataType: 'json',
            type: 'get',
            success: function (retjson) {
                console.log(retjson);
                jQuery('#facebook_app_id').parent().after('<span class="kb_error_message" style="color:green">' + fb_vailadte_success + '</span>');
                jQuery("body").loader("hide");
                jQuery('.fb_validate_button').attr('disabled', false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                jQuery("body").loader("hide");
                jQuery('#facebook_app_id').addClass('kb_error_field');
                jQuery('#facebook_app_id').parent().after('<span class="kb_error_message">' + fb_vailadte_error + '</span>');
                jQuery('.fb_validate_button').attr('disabled', false);
            }
        });
    }
}


/*start:changes made by Bhupendra Singh Bisht on 05th May 2020 to make preview changes*/ 
/*
 *  Js to hide mobile preview from the bottom of the general settings screen to show save button clearly to the users.
 */
/*
 * changes by rishabh jain
 * Removed the jquery code to show hide mobile preview on scroll down as it was throwing js error jquery not defined
 * and moved the sama to phtml file inside ready function
 */