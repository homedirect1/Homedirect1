/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  Magento Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 Knowband
 * @license   see file: LICENSE.txt
 */
var num_of_component = 0;
require(['jquery'], function ($) {
    $(document).ready(function () {
        $('#top_category').click(function () {
            addTopCategory(0);
        });
        $('#banner_square').click(function () {
            addBannerSquare(0);
        });
        
        $('#banner_custom').click(function () {
            addBannerCustom(0);
        });
        
        $('#banner_HS').click(function () {
            addBannerHorizontalslide(0);
        });
        $('#banner_grid').click(function () {
            addBannergrid(0);
        });
        $('#banner_countdown').click(function () {
            addBannerCountdown(0);
        });
        $('#product_square').click(function () {
            addProductSquare(0);
        });
        $('#product_HS').click(function () {
            addProductHorizontalslide(0);
        });
        $('#product_grid').click(function () {
            addProductGrid(0);
        });
        $('#product_LA').click(function () {
            addLastAccessed(0);
        });

    });
});


function addTopCategory(id) {
    require(['jquery'], function ($) {
        $('#number_of_component').val(num_of_component);
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var top_category_html = $('.top_category').html();
            var id_layout = $('#id_layout').val();
            top_category_html = top_category_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            top_category_html = top_category_html.replace(/top_category_edit_component_heading/g, 'heading_' + id);
            top_category_html = top_category_html.replace(/top_category_edit_component/g, 'edit_' + id);
            top_category_html = top_category_html.replace(/top_category_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            /*
             * To assign the unique ID to top 4 categories div for different component in different layouts
             */
            var id_component = id;
            for (var index = 1; index <= 4; index++) {
                var to_replace1 = 'top_category_' + index;
                var to_replace2 = 'top_category_text_' + index;
                var with_replace1 = 'top_category_' + index + '_' + id_layout + '_' + id;
                var with_replace2 = 'top_category_text_' + index + '_' + id_layout + '_' + id;
                top_category_html = top_category_html.replace(to_replace1, with_replace1);
                top_category_html = top_category_html.replace(to_replace2, with_replace2);
            }
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(top_category_html);
            preview_content();
            scrollToBottom();
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            /*
             * To append preview in the device for already added categories when any layout is edited.
             */
            $.ajax({
                url: ajaxaction + "?getCategoryForm=true",
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                showLoader: true,
                type: "post",
                beforeSend: function () {

                },
                success: function (output)
                {
                    var data = JSON.parse(output);

                    uploadtopCategoryfile(id_component, id_layout);
                    var index = 1;
                    if (data.Added_Categories != null) {
                        $.each(data.Added_Categories, function (key, value) {

                            $('img[id="top_category_' + index + '_' + id_layout + '_' + id_component + '"]').attr('src', value.image_src);
                            if (value.heading != '' && value.heading != undefined) {
                                $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').text(value.heading);
                                $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').css('display', 'block');
                            } else {
                                $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').css('display', 'none');
                            }
                            index++;
                        });
                    }
                    $.ajax({
                        url: ajaxaction + "?getCategoryForm=true",
                        data: {
                            form_key: window.FORM_KEY,
                            id_layout: id_layout,
                            id_component: id_component
                        },
                        showLoader: true,
                        type: "post",
                        success: function (output)
                        {
                            var data = JSON.parse(output);

                            if (data.length > 0) {
                                for (i = 0; i < data.length; i++) {
                                    if (data[i]['name'] == 'sliderimage_1') {
                                        $("#sliderimage_1").attr('src', data[i]['value']);
                                    } else if (data[i]['name'] == 'sliderimage_2') {
                                        $("#sliderimage_2").attr('src', data[i]['value']);
                                    } else if (data[i]['name'] == 'sliderimage_3') {
                                        $("#sliderimage_3").attr('src', data[i]['value']);
                                    } else if (data[i]['name'] == 'sliderimage_4') {
                                        $("#sliderimage_4").attr('src', data[i]['value']);
                                    } else if (data[i]['name'] == 'sliderimage_5') {
                                        $("#sliderimage_5").attr('src', data[i]['value']);
                                    } else if (data[i]['name'] == 'sliderimage_6') {
                                        $("#sliderimage_6").attr('src', data[i]['value']);
                                    } else if (data[i]['name'] == 'sliderimage_7') {
                                        $("#sliderimage_7").attr('src', data[i]['value']);
                                    } else if (data[i]['name'] == 'sliderimage_8') {
                                        $("#sliderimage_8").attr('src', data[i]['value']);
                                    }
                                }
                            }
                            uploadtopCategoryfile(id_component, id_layout);
                        }
                    });

                    uploadtopCategoryfile(id_component, id_layout);
                }
            });
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
        } else {
            var a = "top_category";
            var id_layout = $('#id_layout').val();
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var top_category_html = $('.top_category').html();
                            var id_layout = $('#id_layout').val();
                            top_category_html = top_category_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            top_category_html = top_category_html.replace(/top_category_edit_component_heading/g, 'heading_' + id);
                            top_category_html = top_category_html.replace(/top_category_edit_component/g, 'edit_' + id);
                            top_category_html = top_category_html.replace(/top_category_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            /*
                             * To assign the unique ID to top 4 categories div for different component in different layouts for a new category component
                             */
                            for (var index = 1; index <= 4; index++) {
                                var to_replace1 = 'top_category_' + index;
                                var to_replace2 = 'top_category_text_' + index;
                                var with_replace1 = 'top_category_' + index + '_' + id_layout + '_' + id;
                                var with_replace2 = 'top_category_text_' + index + '_' + id_layout + '_' + id;
                                top_category_html = top_category_html.replace(to_replace1, with_replace1);
                                top_category_html = top_category_html.replace(to_replace2, with_replace2);
                            }
                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(top_category_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}

function showHideImageType(a) {
    require(['jquery'], function ($) {
        if ($(a).val() == 'url') {
            $('#image_url_sec').show();
            $('#image_url_holder').show();
            $('#image_file_sec').hide();
        } else if ($(a).val() == 'image') {
            $('#image_url_sec').hide();
            $('#image_url_holder').show();
            $('#image_file_sec').show();
        } else {
            $('#image_url_sec').hide();
            $('#image_url_holder').hide();
            $('#image_file_sec').hide();
        }
    });

}
function getCategoryproducts(a) {
    require(['jquery'], function ($) {
        var id_category = $('#category_id').val();
        var category_prev_products = $.trim($("#category_prev_products").val());
        $.ajax({
            showLoader: true,
            url: ajaxaction + "?getCategoryProducts=true",
            data: {
                form_key: window.FORM_KEY,
                id_category: id_category,
                category_prev_products: category_prev_products
            },
            type: "post",
            success: function (data)
            {
                if (data['success'] != undefined) {
                    $('#category_products').html('');
                    $('#category_products').append(data["category_product_options"]);
                }
            }
        });
    });
}
function showHideProductType(a) {
    require(['jquery'], function ($) {
        if ($('#product_type').val() == 'category_products') {
            $('#product_list_holder').hide();
            $('#category_id_holder').show();
            $('#category_products_holder').show();

        } else if ($('#product_type').val() == 'custom_products') {
            $('#product_list_holder').show();
            $('#category_id_holder').hide();
            $('#category_products_holder').hide();
        } else {
            $('#product_list_holder').hide();
            $('#category_id_holder').hide();
            $('#category_products_holder').hide();
        }
    });
}
function showUrlImage() {
    require(['jquery'], function ($) {
        $("#image_url").on('blur', function () {
            $('.kb_error_message').remove();
            $('input[name="image_url"]').removeClass('kb_error_field');
            if ($('#image_url').val() != '') {
                var image_url_err = velovalidation.checkUrl($('input[name="image_url"]'));
                $('#sliderimage').attr('src', $('#image_url').val());
                $('#sliderimage').show();
            }
        });
    });

}
function showuploadedimage() {
    require(['jquery'], function ($) {
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files[0];
            var file_data = e.target.files;
            var file_mimetypes = [
                'image/gif',
                'image/jpeg',
                'image/png',
                'application/x-shockwave-flash',
                'image/psd',
                'image/bmp',
                'image/tiff',
                'application/octet-stream',
                'image/jp2',
                'image/iff',
                'image/vnd.wap.wbmp',
                'image/xbm',
                'image/vnd.microsoft.icon',
                'image/webp'
            ];

            var file_format = false;
            for (i = 0; i < file_mimetypes.length; i++) {
                if (files.type == file_mimetypes[i]) {
                    file_format = true;
                }
            }

            if (!file_format)
            {
                $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage').attr('src', e.target.result);
                        $('#sliderimage').show();
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile"]').parent().find('.kb_error_message').remove();
            }

        } else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });
}
function showHideRedirectType(a) {
    require(['jquery'], function ($) {
        if ($(a).val() == 'category') {
            $('#product_id_holder').hide();
            $('#category_id_holder').show();
        } else if ($(a).val() == 'product') {
            $('#product_id_holder').show();
            $('#category_id_holder').hide();
        } else {
            $('#product_id_holder').hide();
            $('#category_id_holder').hide();
        }
    });
}
function addBannerSquare(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var banner_square_html = $('.banner-slide').html();
            var id_layout = $('#id_layout').val();
            banner_square_html = banner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            banner_square_html = banner_square_html.replace(/banner_square_edit_component_heading/g, 'heading_' + id);
            banner_square_html = banner_square_html.replace(/banner_square_edit_component/g, 'edit_' + id);
            banner_square_html = banner_square_html.replace(/banner_square_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            /*
             * To assign the unique ID to banner square slides for different component in different layouts
             */
            var id_component = id;
            banner_square_html = banner_square_html.replace(/bannerSquareBanner_elem_heading/g, 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id);
            banner_square_html = banner_square_html.replace(/bannerSquare_comp_heading/g, 'bannerSquare_comp_heading_' + id_layout + '_' + id);
            banner_square_html = banner_square_html.replace(/bannerSquareBannerimg/g, 'bannerSquareBannerimg_' + id_layout + '_' + id);
            banner_square_html = banner_square_html.replace(/bannerSList/g, 'bannerSList_' + id_layout + '_' + id);

            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(banner_square_html);
            preview_content();
            scrollToBottom();
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $.ajax({
                url: ajaxaction + "?getBannerForm=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                beforeSend: function () {

                },
                success: function (data)
                {

                    var b = JSON.parse(data);

                    if (1) {

                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('.panel-footer').attr('element_type', 'banner_square');
//                $('input[id="heading_of_banner_'+default_language_id+'"]').keyup(function(event) {
//                    $('p[id="bannerSquareBanner_elem_heading_'+id_layout+ '_'+id_component+'"]').html($('input[id="heading_of_banner_'+default_language_id+'"]').val());
//                });
//                $('input[id="banner_heading_'+default_language_id+'"]').keyup(function(event) {
//                    $('p[id="bannerSquare_comp_heading_'+id_layout+ '_'+id_component+'"]').html($('input[id="banner_heading_'+default_language_id+'"]').val());
//                });
                        var element_type = 'bannerSquareBannerimg_';
                        uploadfile(id_layout, id_component, element_type);
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        var element_type = 'bannerSquareBannerimg_';
//                        $('img[id="' + element_type + id_layout + '_' + id_component + '"]').parent().remove();
//                        $('p[id="bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '"]').parent().remove();
                        $.each(b.Added_Banners, function (key, value) {
                            $('img[id="' + element_type + id_layout + '_' + id_component + '"]').remove();
                            $('p[id="bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '"]').parent().remove();
                            if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                                $('div[id="bannerSList_' + id_layout + '_' + id_component + '"]').append('<span class="BSSection"><img id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '" src=""></img><p class="elem_heading" id="' + 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"></p></span>');
                            }
                            if (value.heading != '' && value.heading != undefined) {
                                $('p[id="' + 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                $('p[id="' + 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                            } else {
                                $('p[id="' + 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                            }
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("width", "100%");
                        });
                        if (b.component_heading != null && b.component_heading != undefined) {
                            $('p[id="bannerSquare_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        showUrlImage();

                        setDate();
                    }
                }
            });
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banner_square";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var banner_square_html = $('.banner-slide').html();
                            var id_layout = $('#id_layout').val();
                            banner_square_html = banner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            banner_square_html = banner_square_html.replace(/banner_square_edit_component_heading/g, 'heading_' + id);
                            banner_square_html = banner_square_html.replace(/banner_square_edit_component/g, 'edit_' + id);
                            banner_square_html = banner_square_html.replace(/banner_square_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            banner_square_html = banner_square_html.replace(/bannerSquareBanner_elem_heading/g, 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id);
                            banner_square_html = banner_square_html.replace(/bannerSquare_comp_heading/g, 'bannerSquare_comp_heading_' + id_layout + '_' + id);
                            banner_square_html = banner_square_html.replace(/bannerSquareBannerimg/g, 'bannerSquareBannerimg_' + id_layout + '_' + id);
                            banner_square_html = banner_square_html.replace(/bannerSList/g, 'bannerSList_' + id_layout + '_' + id);

                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(banner_square_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}


function addBannerCustom(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var banner_custom_html = $('.banner-custom').html();
            var id_layout = $('#id_layout').val();
            banner_custom_html = banner_custom_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            banner_custom_html = banner_custom_html.replace(/banner_custom_edit_component_heading/g, 'heading_' + id);
            banner_custom_html = banner_custom_html.replace(/banner_custom_edit_component/g, 'edit_' + id);
            banner_custom_html = banner_custom_html.replace(/banner_custom_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            /*
             * To assign the unique ID to banner square slides for different component in different layouts
             */
            var id_component = id;
            banner_custom_html = banner_custom_html.replace(/bannerCustomBanner_elem_heading/g, 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id);
            banner_custom_html = banner_custom_html.replace(/bannerCustom_comp_heading/g, 'bannerCustom_comp_heading_' + id_layout + '_' + id);
            banner_custom_html = banner_custom_html.replace(/bannerCustomBannerimg/g, 'bannerCustomBannerimg_' + id_layout + '_' + id);
            banner_custom_html = banner_custom_html.replace(/bannerSList/g, 'bannerSList_' + id_layout + '_' + id);

            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(banner_custom_html);
            preview_content();
            scrollToBottom();
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $.ajax({
                url: ajaxaction + "?getBannerForm=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                beforeSend: function () {

                },
                success: function (data)
                {

                    var b = JSON.parse(data);

                    if (1) {

                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('.panel-footer').attr('element_type', 'banner_custom');
//                $('input[id="heading_of_banner_'+default_language_id+'"]').keyup(function(event) {
//                    $('p[id="bannerCustomBanner_elem_heading_'+id_layout+ '_'+id_component+'"]').html($('input[id="heading_of_banner_'+default_language_id+'"]').val());
//                });
//                $('input[id="banner_heading_'+default_language_id+'"]').keyup(function(event) {
//                    $('p[id="bannerCustom_comp_heading_'+id_layout+ '_'+id_component+'"]').html($('input[id="banner_heading_'+default_language_id+'"]').val());
//                });
                        var element_type = 'bannerCustomBannerimg_';
                        uploadfile(id_layout, id_component, element_type);
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        var element_type = 'bannerCustomBannerimg_';
//                        $('img[id="' + element_type + id_layout + '_' + id_component + '"]').parent().remove();
//                        $('p[id="bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '"]').parent().remove();
                        $.each(b.Added_Banners, function (key, value) {
                            $('img[id="' + element_type + id_layout + '_' + id_component + '"]').remove();
                            $('p[id="bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '"]').parent().remove();
                            if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                                $('div[id="bannerSList_' + id_layout + '_' + id_component + '"]').append('<span class="BSSection"><img id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '" src=""></img><p class="elem_heading" id="' + 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"></p></span>');
                            }
                            if (value.heading != '' && value.heading != undefined) {
                                $('p[id="' + 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                $('p[id="' + 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                            } else {
                                $('p[id="' + 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                            }
                            /*
                             * changes by rishabh jain
                             * for fixing the custom banner preview css issue i.e added height , width and margin
                             */
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').closest('.BSSection').css("width", value.banner_width + "%");
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').closest('.BSSection').css("height", value.banner_height + "%");
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image);
                            if (value.insets.top) {
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("margin-top", value.insets.top + "px");
                            }
                            if (value.insets.bottom) {
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("margin-bottom", value.insets.bottom + "px");
                            }
                            if (value.insets.left) {
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("margin-left", value.insets.left + "px");
                            }
                            if (value.insets.right) {
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("margin-top", value.insets.right + "px");
                            }
                            /*
                             * changes over
                             */
                        });
                        if (b.component_heading != null && b.component_heading != undefined) {
                            $('p[id="bannerCustom_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        showUrlImage();

                        setDate();
                    }
                }
            });
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banner_custom";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var banner_custom_html = $('.banner-custom').html();
                            var id_layout = $('#id_layout').val();
                            banner_custom_html = banner_custom_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            banner_custom_html = banner_custom_html.replace(/banner_custom_edit_component_heading/g, 'heading_' + id);
                            banner_custom_html = banner_custom_html.replace(/banner_custom_edit_component/g, 'edit_' + id);
                            banner_custom_html = banner_custom_html.replace(/banner_custom_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            banner_custom_html = banner_custom_html.replace(/bannerCustomBanner_elem_heading/g, 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id);
                            banner_custom_html = banner_custom_html.replace(/bannerCustom_comp_heading/g, 'bannerCustom_comp_heading_' + id_layout + '_' + id);
                            banner_custom_html = banner_custom_html.replace(/bannerCustomBannerimg/g, 'bannerCustomBannerimg_' + id_layout + '_' + id);
                            banner_custom_html = banner_custom_html.replace(/bannerSList/g, 'bannerSList_' + id_layout + '_' + id);

                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(banner_custom_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}

function addBannerHorizontalslide(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var Hbanner_square_html = $('.Hbanner-slide').html();
            var id_layout = $('#id_layout').val();
            Hbanner_square_html = Hbanner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_edit_component_heading/g, 'heading_' + id);
            Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_edit_component/g, 'edit_' + id);
            Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            /*
             * To assign the unique ID to banner horizontal slides for different component in different layouts
             */
            var id_component = id;
            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlide_comp_heading/g, 'bannerHorizontalSlide_comp_heading_' + id_layout + '_' + id);
            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlideimg1/g, 'bannerHorizontalSlideimg1_' + id_layout + '_' + id);
            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlideimg2/g, 'bannerHorizontalSlideimg2_' + id_layout + '_' + id);
            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlide_elem_heading1/g, 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id);
            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlide_elem_heading2/g, 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id);
            Hbanner_square_html = Hbanner_square_html.replace(/slidingBList/g, 'slidingBList_' + id_layout + '_' + id);

            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(Hbanner_square_html);
            preview_content();
            scrollToBottom();
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $.ajax({
                url: ajaxaction + "?getBannerForm=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                beforeSend: function () {

                },
                success: function (data)
                {
                    var b = JSON.parse(data);

                    if (1) {

                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $(".iframe_html li[id='layout_component_" + id_layout + '_' + id_component + "']").show();
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        var element_type = 'bannerHorizontalSlideimg_';
                        var index = 1;
                        $.each(b.Added_Banners, function (key, value) {
                            $('p[id="bannerHorizontalSlide_elem_heading_' + id_layout + '_' + id_component + '"]').remove();
                            $('img[id="' + element_type + id_layout + '_' + id_component + '"]').remove();
                            var element_type1 = 'bannerHorizontalSlideimg1_';

                            var element_type2 = 'bannerHorizontalSlideimg2_';

                            $('img[id="' + element_type1 + id_layout + '_' + id_component + '"]').parent().remove();
                            $('img[id="' + element_type2 + id_layout + '_' + id_component + '"]').parent().remove();
                            $('p[id="bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '"]').remove();
                            $('p[id="bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '"]').remove();
                            if (index == 1) {
                                element_type = 'bannerHorizontalSlideimg1_';
                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {

                                    $('div[id="slidingBList_' + id_layout + '_' + id_component + '"] > #bannerHorizontalSlideList1').append('<span class="BHSSection"><img id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '" src=""></img><p class="elem_heading"  id="' + 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"></p></span>');
                                }
                                if (value.heading != '' && value.heading != undefined) {
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                                } else {
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                                }
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image);
                            } else if (index == 2) {
                                element_type = 'bannerHorizontalSlideimg2_';
                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {

                                    $('div[id="slidingBList_' + id_layout + '_' + id_component + '"] > #bannerHorizontalSlideList2').append('<span class="BHSSection"><img id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '" src=""></img><p class="elem_heading"  id="' + 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"></p></span>');
                                }
                                if (value.heading != '' && value.heading != undefined) {
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                                } else {
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                                }
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image);
                            }
                            index++;
                        });
                        if (b.component_heading != null && b.component_heading != undefined) {
                            $('h4[id="bannerHorizontalSlide_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        showUrlImage();
                        setDate();
                    }
                }
            });
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banner_horizontal_slider";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    },                    
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var Hbanner_square_html = $('.Hbanner-slide').html();
                            var id_layout = $('#id_layout').val();
                            Hbanner_square_html = Hbanner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_edit_component_heading/g, 'heading_' + id);
                            Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_edit_component/g, 'edit_' + id);
                            Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlide_comp_heading/g, 'bannerHorizontalSlide_comp_heading_' + id_layout + '_' + id);
                            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlideimg1/g, 'bannerHorizontalSlideimg1_' + id_layout + '_' + id);
                            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlideimg2/g, 'bannerHorizontalSlideimg2_' + id_layout + '_' + id);
                            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlide_elem_heading1/g, 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id);
                            Hbanner_square_html = Hbanner_square_html.replace(/bannerHorizontalSlide_elem_heading2/g, 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id);
                            Hbanner_square_html = Hbanner_square_html.replace(/slidingBList/g, 'slidingBList_' + id_layout + '_' + id);
                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(Hbanner_square_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}


function addBannergrid(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var banner_Grid_html = $('.banner-grid').html();
            var id_layout = $('#id_layout').val();
            banner_Grid_html = banner_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            banner_Grid_html = banner_Grid_html.replace(/banner_grid_edit_component_heading/g, 'heading_' + id);
            banner_Grid_html = banner_Grid_html.replace(/banner_grid_edit_component/g, 'edit_' + id);
            banner_Grid_html = banner_Grid_html.replace(/banner_grid_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            /*
             * To assign the unique ID to banner grid slides for different component in different layouts
             */
            var id_component = id;
            banner_Grid_html = banner_Grid_html.replace(/bannerGrid_comp_heading/g, 'bannerGrid_comp_heading_' + id_layout + '_' + id);
            banner_Grid_html = banner_Grid_html.replace(/bannerGRow1/g, 'bannerGRow1_' + id_layout + '_' + id);
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(banner_Grid_html);
            preview_content();
            scrollToBottom();
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $.ajax({
                url: ajaxaction + "?getBannerForm=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "POST",
                beforeSend: function () {

                },
                success: function (data)
                {
                    var b = JSON.parse(data);

                    if (1) {
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('.panel-footer').attr('element_type', 'banner_grid');
                        var element_type = 'bannerGridimg1_';
                        var template_html = $('#bannerGRow').html();
                        $.each(b.Added_Banners, function (key, value) {
                            $('div[id="bannerGRow1_' + id_layout + '_' + id_component + '"]').find('#bannerGridimg').parent().remove();
                            var productinfo_html = template_html;
                            productinfo_html = productinfo_html.replace(/bannerGridimg1/g, 'bannerGridimg1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id);
                            productinfo_html = productinfo_html.replace(/bannerGrid_elem_heading/g, 'bannerGrid_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id);


                            if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                                $('div[id="bannerGRow1_' + id_layout + '_' + id_component + '"]').append(productinfo_html);
                            }
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image);

                            if (value.heading != '' && value.heading != undefined) {
                                $('p[id="' + 'bannerGrid_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').text(value.heading);
                                $('p[id="' + 'bannerGrid_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                            } else {
                                $('p[id="' + 'bannerGrid_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                            }
                        });
                        $('div[id="bannerGRow1_' + id_layout + '_' + id_component + '"] .bannerGridList').each(function () {
                            if ($(this).children().length == 0) {
                                $(this).remove();
                            }
                        });
                        if (b.component_heading != null && b.component_heading != undefined) {
                            $('h4[id="bannerGrid_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        showUrlImage();

                        setDate();

                    }
                }
            });
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banners_grid";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var banner_Grid_html = $('.banner-grid').html();
                            var id_layout = $('#id_layout').val();
                            banner_Grid_html = banner_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            banner_Grid_html = banner_Grid_html.replace(/banner_grid_edit_component_heading/g, 'heading_' + id);
                            banner_Grid_html = banner_Grid_html.replace(/banner_grid_edit_component/g, 'edit_' + id);
                            banner_Grid_html = banner_Grid_html.replace(/banner_grid_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            banner_Grid_html = banner_Grid_html.replace(/bannerGrid_comp_heading/g, 'bannerGrid_comp_heading_' + id_layout + '_' + id);
                            //banner_Grid_html = banner_Grid_html.replace(/bannerGridimg/g, 'bannerGridimg_' + id_layout + '_' + id);
                            banner_Grid_html = banner_Grid_html.replace(/bannerGRow1/g, 'bannerGRow1_' + id_layout + '_' + id);
                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(banner_Grid_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);

                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}

function addBannerCountdown(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var banner_countdown_html = $('.banner-countdown').html();
            var id_layout = $('#id_layout').val();
            banner_countdown_html = banner_countdown_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            banner_countdown_html = banner_countdown_html.replace(/banner_countdown_edit_component_heading/g, 'heading_' + id);
            banner_countdown_html = banner_countdown_html.replace(/banner_countdown_edit_component/g, 'edit_' + id);
            banner_countdown_html = banner_countdown_html.replace(/banner_countdown_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            /*
             * To assign the unique ID to banner countdown slides for different component in different layouts
             */
            var id_component = id;
            banner_countdown_html = banner_countdown_html.replace(/bannerCountdown_comp_heading/g, 'bannerCountdown_comp_heading_' + id_layout + '_' + id);
            banner_countdown_html = banner_countdown_html.replace(/bannerCountdownList/g, 'bannerCountdownList_' + id_layout + '_' + id);
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(banner_countdown_html);
            preview_content();
            scrollToBottom();
            $.ajax({
                url: ajaxaction + "?getBannerForm=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "POST",
                beforeSend: function () {

                },
                success: function (data)
                {
                    var b = JSON.parse(data);
                    if (1) {

                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('.panel-footer').attr('element_type', 'banner_countdown');
                        var element_type = 'bannerCountdownimg1_';
                        $(".iframe_html li[id='layout_component_" + id_layout + '_' + id_component + "']").show();
                        $.each(b.Added_Banners, function (key, value) {
                            $('div[id="bannerCountdownList_' + id_layout + '_' + id_component + '"]').find('#bannerCountdownimg').parent().remove();
                            var template_html = $('.countdownlistContentContainer1').html();
                            template_html = template_html.replace(/bannerCountdownimg1/g, 'bannerCountdownimg1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id);
                            template_html = template_html.replace(/bannerCountdown_elem_heading/g, 'bannerCountdown_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id);
                            template_html = template_html.replace(/countdown_banner_img_url/g, value.Image);
                            if (value.is_enabled_background_color == "1") {
                                template_html = template_html.replace(/background_color_of_timer_text/g, value.background_color);
                            } else {
                                template_html = template_html.replace(/background_color_of_timer_text/g, 'transparent');
                            }

                            template_html = template_html.replace(/color_of_timer_text/g, value.timer_text_color);

                            if ($('div[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                                $('div[id="bannerCountdownList_' + id_layout + '_' + id_component + '"]').append(template_html);
                            }
                            if (value.heading != '' && value.heading != undefined) {
                                $('p[id="' + 'bannerCountdown_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                $('p[id="' + 'bannerCountdown_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');

                            } else {
                                $('div[id="' + 'bannerCountdownimg1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('bottom', '5px');
                                $('p[id="' + 'bannerCountdown_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                            }

                        });
                        if (b.component_heading != null && b.component_heading != undefined) {
                            $('h4[id="bannerCountdown_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                    }
                }
            });
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banners_countdown";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var banner_countdown_html = $('.banner-countdown').html();
                            var id_layout = $('#id_layout').val();
                            banner_countdown_html = banner_countdown_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            banner_countdown_html = banner_countdown_html.replace(/banner_countdown_edit_component_heading/g, 'heading_' + id);
                            banner_countdown_html = banner_countdown_html.replace(/banner_countdown_edit_component/g, 'edit_' + id);
                            banner_countdown_html = banner_countdown_html.replace(/banner_countdown_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            banner_countdown_html = banner_countdown_html.replace(/bannerCountdown_comp_heading/g, 'bannerCountdown_comp_heading_' + id_layout + '_' + id);
                            // banner_countdown_html = banner_countdown_html.replace(/bannerCountdownimg/g, 'bannerCountdownimg_' + id_layout + '_' + id);
                            banner_countdown_html = banner_countdown_html.replace(/bannerCountdownList/g, 'bannerCountdownList_' + id_layout + '_' + id);
                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(banner_countdown_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}

function addProductSquare(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var product_square_html = $('.product-square').html();
            var id_layout = $('#id_layout').val();
            var id_component = id;
            product_square_html = product_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            product_square_html = product_square_html.replace(/product_square_edit_component_heading/g, 'heading_' + id);
            product_square_html = product_square_html.replace(/product_square_edit_component/g, 'edit_' + id);
            product_square_html = product_square_html.replace(/product_square_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            /*
             * To assign the unique ID to product square slides for different component in different layouts
             */
            product_square_html = product_square_html.replace(/productSquare_comp_heading/g, 'productSquare_comp_heading_' + id_layout + '_' + id);
            product_square_html = product_square_html.replace(/productSList/g, 'productSList_' + id_layout + '_' + id);
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(product_square_html);
            preview_content();
            scrollToBottom();
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $.ajax({
                showLoader: true,
                url: ajaxaction + "?getProductForm=true",
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                beforeSend: function () {

                },
                success: function (data)
                {
                    var b = JSON.parse(data);
                    if (1) {

                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('.panel-footer').attr('element_type', 'productSquareimg');
                        var element_type = 'productSquareimg1_';
                        var template_html = $('.productSquareimg1').parent().html();
                        $.each(b.added_Products.products_for_preview, function (key, value) {
                            var element_type = 'productSquareimg1_';
                            var template_html = $('.productSquareimg1').parent().html();
                            var img_id = element_type + id_layout + '_' + id_component;
                            var img_id_replaced = img_id.replace(img_id, img_id + '_' + value.id);
                            var productinfo_html = template_html;
                            productinfo_html = productinfo_html.replace(/productSquareimg1/g, 'productSquareimg1_' + value.id);
                            productinfo_html = productinfo_html.replace(/productName/g, 'productName_' + value.id);
                            productinfo_html = productinfo_html.replace(/productPrice/g, 'productPrice_' + value.id);
                            if ($('img[id="' + element_type + value.id + '"]').length == "0") {
                                $('div[id="productSList_' + id_layout + '_' + id_component + '"]').append(productinfo_html);
                            }
                            $('h5[id="productName_' + value.id + '"]').html(value.name.substring(0, 8));
                            $('h6[id="productPrice_' + value.id + '"]').html(value.price);
                            $('img[id="' + element_type + value.id + '"]').attr('src', value.src);
                            $('img[id="' + element_type + value.id + '"]').css('width', '282px');
                            $('img[id="' + element_type + value.id + '"]').css('height', '261px');
                        });
                        if (b.added_Products.products_for_preview.length > 0) {
                            $('div[id="productSList_' + id_layout + '_' + id_component + '"] .productSquareimg').next().remove();
                            $('div[id="productSList_' + id_layout + '_' + id_component + '"] .productSquareimg').remove();
                        }
                        if (b.added_Products.component_heading_preview != null) {
                            $('h4[id="productSquare_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.added_Products.component_heading_preview);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/

                    }
                }
            });
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "products_square";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var product_square_html = $('.product-square').html();
                            var id_layout = $('#id_layout').val();
                            product_square_html = product_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            product_square_html = product_square_html.replace(/product_square_edit_component_heading/g, 'heading_' + id);
                            product_square_html = product_square_html.replace(/product_square_edit_component/g, 'edit_' + id);
                            product_square_html = product_square_html.replace(/product_square_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            product_square_html = product_square_html.replace(/productSquare_comp_heading/g, 'productSquare_comp_heading_' + id_layout + '_' + id);
                            product_square_html = product_square_html.replace(/productSList/g, 'productSList_' + id_layout + '_' + id);
                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(product_square_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}
function addProductHorizontalslide(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var Hproduct_slide_html = $('.Hproduct-slide').html();
            var id_layout = $('#id_layout').val();
            Hproduct_slide_html = Hproduct_slide_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_edit_component_heading/g, 'heading_' + id);
            Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_edit_component/g, 'edit_' + id);
            Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            var id_component = id;
            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingPRow1/g, 'slidingPRow1_' + id_layout + '_' + id);
            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProducts_comp_heading/g, 'slidingProducts_comp_heading_' + id_layout + '_' + id);
            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProductsimg2/g, 'slidingProductsimg2_' + id_layout + '_' + id);
            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProductsimg1/g, 'slidingProductsimg1_' + id_layout + '_' + id);
            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProductsimg3/g, 'slidingProductsimg3_' + id_layout + '_' + id);
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/

            $('.slides').append(Hproduct_slide_html);
            preview_content();
            scrollToBottom();
            $.ajax({
                showLoader: true,
                url: ajaxaction + "?getProductForm=true",
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                beforeSend: function () {

                },
                success: function (data)
                {
                    var b = JSON.parse(data);
                    if (1) {

                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('.panel-footer').attr('element_type', 'slidingProducts');
                        var element_type = 'slidingProductsimg';
                        var template_html = $('#slidingPRow').html();
                        var index = 1;
                        $.each(b.added_Products.products_for_preview, function (key, value) {
                            // $('img[id="'+element_type+ id_layout + '_' + id_component + '"]').remove();
                            var element_type1 = 'slidingProductsimg1_';
                            var element_type2 = 'slidingProductsimg2_';
                            var element_type3 = 'slidingProductsimg3_';
                            $('img[id="' + element_type1 + id_layout + '_' + id_component + '"]').next().remove();
                            $('img[id="' + element_type1 + id_layout + '_' + id_component + '"]').remove();
                            $('img[id="' + element_type2 + id_layout + '_' + id_component + '"]').next().remove();
                            $('img[id="' + element_type2 + id_layout + '_' + id_component + '"]').remove();
                            $('img[id="' + element_type3 + id_layout + '_' + id_component + '"]').next().remove();
                            $('img[id="' + element_type3 + id_layout + '_' + id_component + '"]').remove();
                            if (index == 1) {
                                element_type = 'slidingProductsimg1_';
                                var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList1').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList1').append(productinfo_html);
                                }
                                $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                                $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                            } else if (index == 2) {
                                element_type = 'slidingProductsimg2_';
                                var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList2').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList2').append(productinfo_html);
                                }
                                $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                                $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                            } else if (index == 3) {
                                element_type = 'slidingProductsimg3_';
                                var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList3').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList3').append(productinfo_html);
                                }
                                $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                                $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                            }
                            index++;
                        });
                        if (b.added_Products.component_heading_preview != null) {
                            $('h4[id="slidingProducts_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.added_Products.component_heading_preview);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/

                    }
                }
            });
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "products_horizontal";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var Hproduct_slide_html = $('.Hproduct-slide').html();
                            var id_layout = $('#id_layout').val();
                            Hproduct_slide_html = Hproduct_slide_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_edit_component_heading/g, 'heading_' + id);
                            Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_edit_component/g, 'edit_' + id);
                            Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingPRow1/g, 'slidingPRow1_' + id_layout + '_' + id);
                            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProducts_comp_heading/g, 'slidingProducts_comp_heading_' + id_layout + '_' + id);
                            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProductsimg2/g, 'slidingProductsimg2_' + id_layout + '_' + id);
                            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProductsimg1/g, 'slidingProductsimg1_' + id_layout + '_' + id);
                            Hproduct_slide_html = Hproduct_slide_html.replace(/slidingProductsimg3/g, 'slidingProductsimg3_' + id_layout + '_' + id);
                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(Hproduct_slide_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}
function addProductGrid(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var product_Grid_html = $('.product-grid').html();
            var id_layout = $('#id_layout').val();
            product_Grid_html = product_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            product_Grid_html = product_Grid_html.replace(/product_grid_edit_component_heading/g, 'heading_' + id);
            product_Grid_html = product_Grid_html.replace(/product_grid_edit_component/g, 'edit_' + id);
            product_Grid_html = product_Grid_html.replace(/product_grid_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            var id_component = id;
            product_Grid_html = product_Grid_html.replace(/productGrid_comp_heading/g, 'productGrid_comp_heading_' + id_layout + '_' + id);
            //  product_Grid_html = product_Grid_html.replace(/productGridimg/g, 'productGridimg_' + id_layout + '_' + id);
//        product_Grid_html = product_Grid_html.replace(/productGridRowContainer/g, 'productGridRowContainer_' + id_layout + '_' + id);
//        product_Grid_html = product_Grid_html.replace(/productGridRowContainer/g, 'productGridRowContainer_' + id_layout + '_' + id);
            product_Grid_html = product_Grid_html.replace(/productGRow1/g, 'productGRow1_' + id_layout + '_' + id);
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(product_Grid_html);
            preview_content();
            scrollToBottom();
//        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $.ajax({
                showLoader: true,
                url: ajaxaction + "?getProductForm=true",
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                beforeSend: function () {

                },
                success: function (data)
                {
                    var b = JSON.parse(data);
                    if (1) {
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('.panel-footer').attr('element_type', 'productGridimg');
                        var element_type = 'productGridimg_';
                        var template_html = $('#productGRow').html();
                        var id_ProductArray = [];
////                $(".productGridRow").remove();
                        $.each(b.added_Products.products_for_preview, function (key, value) {
//                    $(".productGridRow1").remove();
                            $('div[id="productGRow1_' + id_layout + '_' + id_component + '"]').find('#productGridimg1').parent().remove();
                            id_ProductArray.push(element_type + id_layout + '_' + id_component + '_' + value.id);
                            var img_id = element_type + id_layout + '_' + id_component;
                            var img_id_replaced = img_id.replace(img_id, img_id + '_' + value.id);
                            var productinfo_html = template_html;
                            productinfo_html = productinfo_html.replace(/productName/g, 'productName_' + value.id);
                            productinfo_html = productinfo_html.replace(/productPrice/g, 'productPrice_' + value.id);
                            productinfo_html = productinfo_html.replace(/productGridimg/g, 'productGridimg_' + id_layout + '_' + id_component + '_' + value.id);
                            if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                $('div[id="productGRow1_' + id_layout + '_' + id_component + '"]').append(productinfo_html);
                            }
                            $('h5[id="productName_' + value.id + '"]').html(value.name.substring(0, 8));
                            $('h6[id="productPrice_' + value.id + '"]').html(value.price);
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                        });

                        if (b.added_Products.component_heading_preview != null) {
                            $('p[id="productGrid_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.added_Products.component_heading_preview);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                    }
                }
            });
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "products_grid";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var product_Grid_html = $('.product-grid').html();
                            var id_layout = $('#id_layout').val();
                            product_Grid_html = product_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            product_Grid_html = product_Grid_html.replace(/product_grid_edit_component_heading/g, 'heading_' + id);
                            product_Grid_html = product_Grid_html.replace(/product_grid_edit_component/g, 'edit_' + id);
                            product_Grid_html = product_Grid_html.replace(/product_grid_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            product_Grid_html = product_Grid_html.replace(/productGrid_comp_heading/g, 'productGrid_comp_heading_' + id_layout + '_' + id);
                            //product_Grid_html = product_Grid_html.replace(/productGridimg/g, 'productGridimg_' + id_layout + '_' + id);
                            // product_Grid_html = product_Grid_html.replace(/productGridRowContainer/g, 'productGridRowContainer_' + id_layout + '_' + id);
                            product_Grid_html = product_Grid_html.replace(/productGRow1/g, 'productGRow1_' + id_layout + '_' + id);
                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(product_Grid_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        }
                    }
                });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });
}
function addLastAccessed(id) {
    require(['jquery'], function ($) {
        if (id) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            $('#number_of_component').val(num_of_component);
            var last_accessed_html = $('.product-lastAccess').html();
            var id_layout = $('#id_layout').val();
            last_accessed_html = last_accessed_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            last_accessed_html = last_accessed_html.replace(/last_access_edit_component_heading/g, 'heading_' + id);
            last_accessed_html = last_accessed_html.replace(/last_access_delete_component/g, 'delete_' + id);
            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            var id_component = id;
            last_accessed_html = last_accessed_html.replace(/slidingRecentPRow1/g, 'slidingRecentPRow1_' + id_layout + '_' + id);
            last_accessed_html = last_accessed_html.replace(/slidingRecentProductsimg2/g, 'slidingRecentProductsimg2_' + id_layout + '_' + id);
            last_accessed_html = last_accessed_html.replace(/slidingRecentProductsimg1/g, 'slidingRecentProductsimg1_' + id_layout + '_' + id);
            last_accessed_html = last_accessed_html.replace(/slidingRecentProductsimg3/g, 'slidingRecentProductsimg3_' + id_layout + '_' + id);
            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            $('.slides').append(last_accessed_html);
            preview_content();
            scrollToBottom();
        /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
        $.ajax({
            url: ajaxaction + "?getRecentProducts=true",
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                id_layout: id_layout,
                id_component: id_component
            },
            type: "post",
        beforeSend: function () {
            
        },
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                
                /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                var element_type = 'slidingRecentProductsimg';
                var template_html = $('#slidingRecentPRow').html();
                    var index = 1;
                    $.each( b.added_Products.products_for_preview, function( key, value ) {
                       // $('img[id="'+element_type+ id_layout + '_' + id_component + '"]').remove();
                        var element_type1 = 'slidingRecentProductsimg1_';
                        var element_type2 = 'slidingRecentProductsimg2_';
                        var element_type3 = 'slidingRecentProductsimg3_';    
                        $('img[id="'+element_type1+ id_layout + '_' + id_component + '"]').next().remove();
                        $('img[id="'+element_type1+ id_layout + '_' + id_component + '"]').remove();
                        $('img[id="'+element_type2+ id_layout + '_' + id_component + '"]').next().remove();
                        $('img[id="'+element_type2+ id_layout + '_' + id_component + '"]').remove();
                        $('img[id="'+element_type3+ id_layout + '_' + id_component + '"]').next().remove();
                        $('img[id="'+element_type3+ id_layout + '_' + id_component + '"]').remove();
                        if (index == 1) {
                            element_type = 'slidingRecentProductsimg1_';
                            var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                            productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                            productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                            if ($('img[id="'+element_type+ id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList1').append('<img id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'" src=""></img>');
                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList1').append(productinfo_html);
                            }
                            $('h5[id="ProductName_' + value.id +'"]').html(value.name.substring(0, 8));
                            $('h6[id="ProductPrice_' + value.id +'"]').html(value.price);
                            $('img[id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'"]').attr('src', value.src);
                        } else if (index == 2) {
                            element_type = 'slidingRecentProductsimg2_';
                            var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                            productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                            productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                            if ($('img[id="'+element_type+ id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList2').append('<img id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'" src=""></img>');
                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList2').append(productinfo_html);
                            }
                            $('h5[id="ProductName_' + value.id +'"]').html(value.name.substring(0, 8));
                            $('h6[id="ProductPrice_' + value.id +'"]').html(value.price);
                            $('img[id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'"]').attr('src', value.src);
                        } else if (index == 3) {
                            element_type = 'slidingRecentProductsimg3_';
                            var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                            productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                            productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                            if ($('img[id="'+element_type+ id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList3').append('<img id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'" src=""></img>');
                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList3').append(productinfo_html);
                            }
                            $('h5[id="ProductName_' + value.id +'"]').html(value.name.substring(0, 8));
                            $('h6[id="ProductPrice_' + value.id +'"]').html(value.price);
                            $('img[id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'"]').attr('src', value.src);
                        } 
                        index++;
                    });
                    
                /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
               
            }
        }
    });
    /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "products_recent";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY,
                        id_layout: id_layout,
                        component_type: a
                    }, 
                    type: "post",
                    success: function (data)
                    {
                        var id = data;
                        if (id) {
                            var last_accessed_html = $('.product-lastAccess').html();
                            var id_layout = $('#id_layout').val();
                            last_accessed_html = last_accessed_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                            last_accessed_html = last_accessed_html.replace(/last_access_delete_component_heading/g, 'heading_' + id);
                            last_accessed_html = last_accessed_html.replace(/last_access_delete_component/g, 'delete_' + id);
                            /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            var id_component = id;
                            last_accessed_html = last_accessed_html.replace(/slidingRecentPRow1/g, 'slidingRecentPRow1_' + id_layout + '_' + id);
                            last_accessed_html = last_accessed_html.replace(/slidingRecentProductsimg2/g, 'slidingRecentProductsimg2_' + id_layout + '_' + id);
                            last_accessed_html = last_accessed_html.replace(/slidingRecentProductsimg1/g, 'slidingRecentProductsimg1_' + id_layout + '_' + id);
                            last_accessed_html = last_accessed_html.replace(/slidingRecentProductsimg3/g, 'slidingRecentProductsimg3_' + id_layout + '_' + id);
                            /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                            $('.slides').append(last_accessed_html);
                            preview_content();
                            scrollToBottom();
                            showSuccessMessage(component_add);
                        /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                        $.ajax({
                        url: ajaxaction + "?getRecentProducts=true",
                        showLoader: true,
                        data: {
                            form_key: window.FORM_KEY,
                            id_layout: id_layout,
                            id_component: id_component
                        },
                        type: "post",
                        beforeSend: function () {

                        },
                        success: function (data)
                        {
                            var b = JSON.parse(data);
                            if (1) {

                                /*start:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                                var element_type = 'slidingRecentProductsimg';
                                var template_html = $('#slidingRecentPRow').html();
                                    var index = 1;
                                    $.each( b.added_Products.products_for_preview, function( key, value ) {
                                       // $('img[id="'+element_type+ id_layout + '_' + id_component + '"]').remove();
                                        var element_type1 = 'slidingRecentProductsimg1_';
                                        var element_type2 = 'slidingRecentProductsimg2_';
                                        var element_type3 = 'slidingRecentProductsimg3_';    
                                        $('img[id="'+element_type1+ id_layout + '_' + id_component + '"]').next().remove();
                                        $('img[id="'+element_type1+ id_layout + '_' + id_component + '"]').remove();
                                        $('img[id="'+element_type2+ id_layout + '_' + id_component + '"]').next().remove();
                                        $('img[id="'+element_type2+ id_layout + '_' + id_component + '"]').remove();
                                        $('img[id="'+element_type3+ id_layout + '_' + id_component + '"]').next().remove();
                                        $('img[id="'+element_type3+ id_layout + '_' + id_component + '"]').remove();
                                        if (index == 1) {
                                            element_type = 'slidingRecentProductsimg1_';
                                            var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                            productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                            productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                            if ($('img[id="'+element_type+ id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList1').append('<img id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'" src=""></img>');
                                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList1').append(productinfo_html);
                        }
                                            $('h5[id="ProductName_' + value.id +'"]').html(value.name.substring(0, 8));
                                            $('h6[id="ProductPrice_' + value.id +'"]').html(value.price);
                                            $('img[id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'"]').attr('src', value.src);
                                        } else if (index == 2) {
                                            element_type = 'slidingRecentProductsimg2_';
                                            var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                            productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                            productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                            if ($('img[id="'+element_type+ id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList2').append('<img id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'" src=""></img>');
                                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList2').append(productinfo_html);
                    }
                                            $('h5[id="ProductName_' + value.id +'"]').html(value.name.substring(0, 8));
                                            $('h6[id="ProductPrice_' + value.id +'"]').html(value.price);
                                            $('img[id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'"]').attr('src', value.src);
                                        } else if (index == 3) {
                                            element_type = 'slidingRecentProductsimg3_';
                                            var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                            productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                            productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                            if ($('img[id="'+element_type+ id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList3').append('<img id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'" src=""></img>');
                                                $('div[id="slidingRecentPRow1_'+ id_layout + '_' + id_component + '"] > #productRecentSlideList3').append(productinfo_html);
                                            }
                                            $('h5[id="ProductName_' + value.id +'"]').html(value.name.substring(0, 8));
                                            $('h6[id="ProductPrice_' + value.id +'"]').html(value.price);
                                            $('img[id="'+ element_type + id_layout + '_' + id_component + '_' +value.id +'"]').attr('src', value.src);
                                       } 
                                        index++;
                });

                                /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/

                            }
                        }
                    });
                    /*end:changes made by Aayushi Agarwal on 27th March 2020 to make preview changes*/
                    }
                }
            });

            } else {
                showErrorMessage(limit_reached);
            }
        }
    });

}
function settingFunction(setting) {
    //$(setting).next('.file-uploader').slideToggle();
}
function trashFunction(trash) {
    require(['jquery'], function ($) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component - 1;
        $('#number_of_component').val(num_of_component)
        $(trash).parents('.slide').remove();
        preview_content();
    });
}
function preview_content() {
    require(['jquery'], function ($) {
        $('.iframe_html').html('');
        var Display_content = $('.slides').html();
        //alert(Display_content);
        $('.iframe_html').append(Display_content);
    });
}


function scrollToBottom() {
    var config = {
        shim: {
            'Knowband_Mobileappbuilder/layout/CustomScrollbar.min': ['jquery'],
            'Knowband_Mobileappbuilder/layout/layout': ['jquery','Knowband_Mobileappbuilder/layout/CustomScrollbar.min'],
            'Knowband_Mobileappbuilder/js/velovalidation': ['jquery']
        }
    };
    require.config(config);

    require(['jquery', 'Knowband_Mobileappbuilder/layout/CustomScrollbar.min'], function (jQuery) {
        jQuery(document).ready(function () {


            var content = jQuery(".layout_gallery"), autoScrollTimer = 200, autoScrollTimerAdjust, autoScroll;
            content.mCustomScrollbar({
                scrollButtons: {
                    enable: true
                },
                theme: "dark",
                callbacks: {
                    whileScrolling: function () {
                        autoScrollTimerAdjust = autoScrollTimer * this.mcs.topPct / 100;
                        privateTop = this.mcs.topPct;
                        if (privateTop >= 90) {
                            jQuery('.goToLastMessage').hide();
                            count = 0;
                        }

                    },
                    onScroll: function () {
                        if (jQuery(this).data("mCS").trigger === "internal") {
                            AutoScrollOff();

                        }
                    }
                }
            });

            content.addClass("auto-scrolling-on auto-scrolling-to-bottom");
            AutoScrollOn("bottom");

            function AutoScrollOn(to, timer) {

                if (!timer) {
                    timer = autoScrollTimer;
                }
                content.addClass("auto-scrolling-on").mCustomScrollbar("scrollTo", to, {
                    scrollInertia: timer,
                    scrollEasing: "easeInOutSmooth"
                });

            }
            function AutoScrollOff() {
                clearTimeout(autoScroll);
                content.removeClass("auto-scrolling-on").mCustomScrollbar("stop");
            }
        });
    });
}
function addLayoutComponents(b) {
    require(['jquery'], function ($) {
        for (i = 0; i < b.length; i++) {
            if (b[i]['type'] == 'banner_square') {
                addBannerSquare(b[i]['id']);
            }else  if (b[i]['type'] == 'banner_custom') {
                addBannerCustom(b[i]['id']);
            } else if (b[i]['type'] == 'top_category') {
                addTopCategory(b[i]['id']);
            } else if (b[i]['type'] == 'banners_countdown') {
                addBannerCountdown(b[i]['id']);
            } else if (b[i]['type'] == 'products_square') {
                addProductSquare(b[i]['id']);
            } else if (b[i]['type'] == 'products_grid') {
                addProductGrid(b[i]['id']);
            } else if (b[i]['type'] == 'products_recent') {
                addLastAccessed(b[i]['id']);
            } else if (b[i]['type'] == 'banners_grid') {
                addBannergrid(b[i]['id']);
            } else if (b[i]['type'] == 'banner_horizontal_slider') {
                addBannerHorizontalslide(b[i]['id']);
            } else if (b[i]['type'] == 'products_horizontal') {
                addProductHorizontalslide(b[i]['id']);
            }
        }
    });
}

function countdownbannerDatepicker() {
    require(['jquery'], function ($) {
        $('.datetimepicker').click(function () {
            $('.ui-datepicker').css('z-index', '99999999');
        });
    });
}
function veloValidateBannerSliderForm(event)
{
    require(['jquery','Knowband_Mobileappbuilder/js/velovalidation'], function ($) {
        $('.kb_error_message').remove();
        $('select[name="image_type"]').removeClass('kb_error_field');
        $('input[name="image_url"]').removeClass('kb_error_field');
        $('select[name="redirect_activity"]').removeClass('kb_error_field');
        $('select[name="category_id"]').removeClass('kb_error_field');
        $('input[name="redirect_banner_product_name"]').removeClass('kb_error_field');
        $('#countdown_validity').removeClass('kb_error_field');
        $('input[name="timer_text_color"]').removeClass('kb_error_field');
        $('input[name="timer_background_color"]').removeClass('kb_error_field');
        $('input[name="filename"]').removeClass('kb_error_field');
        $('input[name="banner_position"]').removeClass('kb_error_field');
        if ($('#width_of_banner').is(":visible")) {
            $('input[name="width_of_banner"]').removeClass('kb_error_field');
            $('input[name="height_of_banner"]').removeClass('kb_error_field');
            $('input[name="margin_right"]').removeClass('kb_error_field');
            $('input[name="margin_left"]').removeClass('kb_error_field');
            $('input[name="margin_bottom"]').removeClass('kb_error_field');
            $('input[name="margin_top"]').removeClass('kb_error_field');
        }
        var slider_banner_error = false;

        var status = $('input[name="status"]:checked').val();
        var image_type = $.trim($('#image_type').val());
        var image_url = $.trim($('#image_url').val());
        var redirect_activity = $.trim($('#redirect_activity').val());
        var redirect_category_id = $.trim($('#category_id').val());
        var redirect_product_name = $.trim($('#redirect_banner_product_name').val());
        var redirect_product_id = $.trim($('#redirect_banner_product_id').val());
        var banner_image = $.trim($('#slideruploadedfile').val());
        var banner_position = $('#banner_position').val();
        var error_message = '';

        if (banner_position == '') {
            slider_banner_error = true;
            $('input[name="banner_position"]').addClass('kb_error_field');
            $('input[name="banner_position"]').after('<span class="kb_error_message">' + required_entry_txt + '</span>');
        } else {
            if (!/^[0-9]+$/.test(banner_position)) {
                slider_banner_error = true;
                $('input[name="banner_position"]').addClass('kb_error_field');
                $('input[name="banner_position"]').after('<span class="kb_error_message">' + integer_value_txt + '</span>');
            }
        }

        if (image_type == 'image' && slider_banner_file_error) {
            slider_banner_error = true;
            error_message = select_image_txt;
            $('input[name="filename"]').addClass('kb_error_field');
            $('input[name="filename"]').after('<span class="kb_error_message">' + error_message + '</span>');
        }
        if ($('#countdown_validity').is(":visible")) {
            if ($('#countdown_validity').val() == '') {
                slider_banner_error = true;
                error_message = required_entry_txt;
                $('#countdown_validity').addClass('kb_error_field');
                $('#countdown_validity').after('<span class="kb_error_message">' + error_message + '</span>');

            }
        }

        if ($('#timer_background_color').is(":visible")) {
            if ($('#timer_background_color').val() == '') {
                slider_banner_error = true;
                error_message = required_entry_txt;
                $('#timer_background_color').addClass('kb_error_field');
                $('#timer_background_color').after('<span class="kb_error_message">' + error_message + '</span>');

            }
        }

        if ($('#timer_text_color').is(":visible")) {
            if ($('#timer_text_color').val() == '') {
                slider_banner_error = true;
                error_message = required_entry_txt;
                $('#timer_text_color').addClass('kb_error_field');
                $('#timer_text_color').after('<span class="kb_error_message">' + error_message + '</span>');

            }
        }
        
        if ($('#background_color').is(":visible")) {
            var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#background_color'));
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#background_color').addClass('kb_error_field');
                jQuery('#background_color').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            } else {
                var is_color_err = velovalidation.isColor(jQuery('#background_color'));
                if (is_color_err != true) {
                    is_error = true;
                    jQuery('#background_color').addClass('kb_error_field');
                    jQuery('#background_color').after('<span class="kb_error_message">' + is_color_err + '</span>');
                }
            }
        }
        
        if ($('#width_of_banner').is(":visible")) {
            var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#width_of_banner'));
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#width_of_banner').addClass('kb_error_field');
                jQuery('#width_of_banner').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            } else {
                var width_of_banner_err = velovalidation.isNumeric(jQuery('#width_of_banner'),true);
                if (width_of_banner_err != true) {
                    is_error = true;
                    jQuery('#width_of_banner').addClass('kb_error_field');
                    jQuery('#width_of_banner').parent().after('<span class="kb_error_message">' + width_of_banner_err + '</span>');
                }
            }
            
            var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#height_of_banner'));
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#height_of_banner').addClass('kb_error_field');
                jQuery('#height_of_banner').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            } else {
                var height_of_banner_err = velovalidation.isNumeric(jQuery('#height_of_banner'),true);
                if (height_of_banner_err != true) {
                    is_error = true;
                    jQuery('#height_of_banner_err').addClass('kb_error_field');
                    jQuery('#height_of_banner_err').parent().after('<span class="kb_error_message">' + height_of_banner_err + '</span>');
                }
            }
            
            var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#margin_top'));
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#margin_top').addClass('kb_error_field');
                jQuery('#margin_top').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            } else {
                var height_of_banner_err = velovalidation.isNumeric(jQuery('#margin_top'),true);
                if (height_of_banner_err != true) {
                    is_error = true;
                    jQuery('#margin_top').addClass('kb_error_field');
                    jQuery('#margin_top').parent().after('<span class="kb_error_message">' + height_of_banner_err + '</span>');
                }
            }
            
            var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#margin_bottom'));
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#margin_bottom').addClass('kb_error_field');
                jQuery('#margin_bottom').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            } else {
                var height_of_banner_err = velovalidation.isNumeric(jQuery('#margin_bottom'),true);
                if (height_of_banner_err != true) {
                    is_error = true;
                    jQuery('#margin_bottom').addClass('kb_error_field');
                    jQuery('#margin_bottom').parent().after('<span class="kb_error_message">' + height_of_banner_err + '</span>');
                }
            }
            
            var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#margin_left'));
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#margin_left').addClass('kb_error_field');
                jQuery('#margin_left').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            } else {
                var height_of_banner_err = velovalidation.isNumeric(jQuery('#margin_left'),true);
                if (height_of_banner_err != true) {
                    is_error = true;
                    jQuery('#margin_left').addClass('kb_error_field');
                    jQuery('#margin_left').parent().after('<span class="kb_error_message">' + height_of_banner_err + '</span>');
                }
            }
            
            var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#margin_right'));
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#margin_right').addClass('kb_error_field');
                jQuery('#margin_right').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            } else {
                var height_of_banner_err = velovalidation.isNumeric(jQuery('#margin_right'),true);
                if (height_of_banner_err != true) {
                    is_error = true;
                    jQuery('#margin_right').addClass('kb_error_field');
                    jQuery('#margin_right').parent().after('<span class="kb_error_message">' + height_of_banner_err + '</span>');
                }
            }
        }

        if (image_type == '') {
            error_message = select_image_type_txt;
            slider_banner_error = true;
            $('select[name="image_type"]').addClass('kb_error_field');
            $('select[name="image_type"]').after('<span class="kb_error_message">' + select_image_type_txt + '</span>');
        }


        if (image_type == 'url' && image_url == '') {
            error_message = image_url_error_txt;
            slider_banner_error = true;
            $('input[name="image_url"]').addClass('kb_error_field');
            $('input[name="image_url"]').after('<span class="kb_error_message">' + image_url_error_txt + '</span>');
        }



//        if (image_type == 'image' && slider_banner_file_error) {
//            error_message = select_image_txt;
//            slider_banner_error = true;
//            $('input[name="filename"]').addClass('kb_error_field');
//        }
            if (image_type == 'image') {
                if ($('#slideruploadedfile')[0].files !== undefined && $('#slideruploadedfile')[0].files.length > 0)
                {
                    var files = $('#slideruploadedfile')[0].files[0];
                    var file_mimetypes = [
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                        'application/x-shockwave-flash',
                        'image/psd',
                        'image/bmp',
                        'image/tiff',
                        'application/octet-stream',
                        'image/jp2',
                        'image/iff',
                        'image/vnd.wap.wbmp',
                        'image/xbm',
                        'image/vnd.microsoft.icon',
                        'image/webp'
                    ];

                    var file_format = false;
                    for (i = 0; i < file_mimetypes.length; i++) {
                        if (files.type == file_mimetypes[i]) {
                            file_format = true;
                        }
                    }

                    if (!file_format)
                    {
                        jQuery('#slideruploadedfile').addClass('kb_error_field');
                        jQuery('#slideruploadedfile').after('<span class="kb_error_message">' + invalid_file_format + '</span>');
                        slider_banner_file_error = true;
                        slider_banner_error = true;

                    } else if (files.size > default_file_size) {
                        jQuery('#slideruploadedfile').addClass('kb_error_field');
                        jQuery('#slideruploadedfile').after('<span class="kb_error_message">' + invalid_file_size + '</span>');
                        slider_banner_file_error = true;
                        slider_banner_error = true;
                    }

                } else // Internet Explorer 9 Compatibility
                {
                    jQuery('#slideruploadedfile').addClass('kb_error_field');
                        jQuery('#slideruploadedfile').after('<span class="kb_error_message">' + file_not_uploaded + '</span>');
                        slider_banner_file_error = true;
                        slider_banner_error = true;
                }
            }


        if (redirect_activity == 'category' && redirect_category_id == '0') {
            error_message = select_category_txt;
            slider_banner_error = true;
            $('select[name="category_id"]').addClass('kb_error_field');
            $('select[name="category_id"]').after('<span class="kb_error_message">' + select_category_txt + '</span>');

        }

        if (redirect_activity == 'product' && redirect_product_id == '') {
            error_message = provide_product_name_txt;
            slider_banner_error = true;
            $('input[name="redirect_banner_product_id"]').addClass('kb_error_field');
            $('input[name="redirect_banner_product_id"]').after('<span class="kb_error_message">' + provide_product_name_txt + '</span>');
        }


        if (slider_banner_error) {
            return false;
        }
        if ($('#countdown_validity').is(":visible")) {
            submitCountdownbannersliderform(event);
        } else {
            submitbannersliderform(event);
        }
        return false;
    });
}
function editBannerSquareComponentFunction(a)
{
    require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
        var str = $(a).attr('id');
        var array = str.split("_");
        var id_component = array[1];
        var id_layout = $('#id_layout').val();
        $('#id_component_selected').val(id_component);

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: edit_component_txt,
            buttons: [
                {
                    text: close_txt,
                    class: 'hidden_element btn btn-warning banner_popup_close',
                    click: function () {
                        this.closeModal();
                    }
                }
            ]
        };

        $.ajax({
            url: ajaxaction + "?getBannerForm=true",
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                id_layout: id_layout,
                id_component: id_component
            },
            type: "POST",
            success: function (output)
            {
                var data = JSON.parse(output);
                if (1) {
                    var popup = modal(options, $('#component_form_popup'));
                    $("#component_form_popup").html(data.html).modal("openModal");
                    showUrlImage();
                    uploadfile();
                }
            }
        });
    });


}
function showHidebackgroundColor() {
    require(['jquery'], function ($) {
        if ($('input[name="is_enabled_background_color"]:checked').val() == "1") {
            $('input[name="timer_background_color"]').parent().parent().show();
        } else {
            $('input[name="timer_background_color"]').parent().parent().hide();
        }
    });
}
function editBannerCountdownComponentFunction(a)
{
    require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
        var str = $(a).attr('id');
        var array = str.split("_");
        var id_component = array[1];
        var id_layout = $('#id_layout').val();
        $('#id_component_selected').val(id_component);
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: edit_component_txt,
            buttons: [
                {
                    text: close_txt,
                    class: 'hidden_element btn btn-warning banner_popup_close',
                    click: function () {
                        this.closeModal();
                    }
                }
            ]
        };
        $.ajax({
            url: ajaxaction + "?getBannerForm=true",
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                id_layout: id_layout,
                id_component: id_component
            },
            type: "POST",
            success: function (output)
            {
                var data = JSON.parse(output);
                if (1) {
                    var popup = modal(options, $('#component_form_popup'));
                    $("#component_form_popup").html(data.html).modal("openModal");
                    showUrlImage();
                    uploadfile();
                }
            }
        });
    });


}

function veloValidateProductForm(a) {
    require(['jquery'], function ($) {
        $('.kb_error_message').remove();
        $('input[name="number_of_products"]').removeClass('kb_error_field');
        $('select[name="category_id"]').removeClass('kb_error_field');
        $('select[name="product_list"]').removeClass('kb_error_field');
        $('select[name="category_products"]').removeClass('kb_error_field');
        var product_form_error = false;

        var number_of_product = $.trim($('#number_of_products').val());
        var category_id = $.trim($('#category_id').val());
        var product_list = $.trim($('#product_list').val());
        var category_products = $.trim($('#category_products').val());
        var product_type = $('#product_type').val();
        var error_message = '';

        if (product_type == 'category_products') {
            if (category_id == 0) {
                product_form_error = true;
                error_message = select_category_txt;
                $('#category_id').addClass('kb_error_field');
                $('#category_id').after('<span class="kb_error_message">' + error_message + '</span>');
            }
        } else if (product_type == 'custom_products') {
            if (product_list == '') {
                product_form_error = true;
                error_message = select_product_txt;
                $('#product_list').addClass('kb_error_field');
                $('#product_list').after('<span class="kb_error_message">' + error_message + '</span>');
            }
        }

        var key_numeric_err = velovalidation.isNumeric($('#number_of_products'), true);
        if (key_numeric_err != true)
        {
            product_form_error = true;
            $('#number_of_products').addClass('kb_error_field');
            $('#number_of_products').after('<span class="kb_error_message">' + key_numeric_err + '</span>');
        }


        if (product_form_error) {
            return false;
        }
        submitProductform(event);

        return false;
    });
}

function editProductHorizontalComponentFunction(a)
{
    require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
        var str = $(a).attr('id');
        var array = str.split("_");
        var id_component = array[1];
        var id_layout = $('#id_layout').val();
        $('#id_component_selected').val(id_component);
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: edit_component_txt,
            buttons: [
                {
                    text: save_txt,
                    class: 'button_submit_form_component btn btn-success',
                    id: 'save_component',
                    click: function () {
                        return veloValidateProductForm(this);
                    }
                },
                {
                    text: close_txt,
                    class: 'hidden_element btn btn-warning banner_popup_close',
                    click: function () {
                        this.closeModal();
                    }
                }
            ]
        };

        $.ajax({
            showLoader: true,
            url: ajaxaction + "?getProductForm=true",
            data: {
                form_key: window.FORM_KEY,
                id_layout: id_layout,
                id_component: id_component
            },
            type: "post",
            success: function (output)
            {
                var b = JSON.parse(output);

                var popup = modal(options, $('#component_form_popup'));
                $("#component_form_popup").html(b.html).modal("openModal");

                showHideProductType(a);

                /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                /*
                 * To append the preview of already added horizontal products
                 */
                $('.panel-footer').attr('element_type', 'slidingProducts');
                var element_type = 'slidingProductsimg';
                var template_html = $('#slidingPRow').html();
                var index = 1;
                $.each(b.added_Products.products_for_preview, function (key, value) {
                    // $('img[id="'+element_type+ id_layout + '_' + id_component + '"]').remove();
                    var element_type1 = 'slidingProductsimg1_';
                    var element_type2 = 'slidingProductsimg2_';
                    var element_type3 = 'slidingProductsimg3_';
                    $('img[id="' + element_type1 + id_layout + '_' + id_component + '"]').next().remove();
                    $('img[id="' + element_type1 + id_layout + '_' + id_component + '"]').remove();
                    $('img[id="' + element_type2 + id_layout + '_' + id_component + '"]').next().remove();
                    $('img[id="' + element_type2 + id_layout + '_' + id_component + '"]').remove();
                    $('img[id="' + element_type3 + id_layout + '_' + id_component + '"]').next().remove();
                    $('img[id="' + element_type3 + id_layout + '_' + id_component + '"]').remove();
                    if (index == 1) {
                        element_type = 'slidingProductsimg1_';
                        var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                        productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                        productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                        if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                            $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList1').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                            $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList1').append(productinfo_html);
                        }
                        $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                        $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                        $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                    } else if (index == 2) {
                        element_type = 'slidingProductsimg2_';
                        var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                        productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                        productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                        if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                            $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList2').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                            $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList2').append(productinfo_html);
                        }
                        $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                        $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                        $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                    } else if (index == 3) {
                        element_type = 'slidingProductsimg3_';
                        var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                        productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                        productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                        if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                            $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList3').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                            $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList3').append(productinfo_html);
                        }
                        $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                        $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                        $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                    }
                    index++;
                });
                if (b.added_Products.component_heading_preview != null) {
                    $('h4[id="slidingProducts_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.added_Products.component_heading_preview);
                }
                /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/

                showUrlImage();
                uploadfile();

            }
        });
    });
}

function setDate() {
    require(['jquery'], function ($) {
        $('.datetimepicker').click(function () {
            $('.ui-datepicker').css('z-index', '99999999');
        });
    });
}
function setCategoryId(a) {
    require(['jquery'], function ($) {
        if ($(a).val != 0) {
            for (i = 1; i < 8; i++) {
                var cat_id = 'category_id_' + i;
                if ($(a).attr('id') != cat_id) {
                    if ($('#category_id_' + i).val() == $(a).val()) {
                        $('#category_id_' + i).val(0);
                    }
                }
            }
        }
    });
}
function veloValidateTopcategoryForm(a) {
    require(['jquery'], function ($) {
        $('.kb_error_message').remove();
        var unselected_cat = 0;
        for (i = 1; i <= 8; i++) {
            $('#slideruploadedfile_' + i).removeClass('kb_error_field');
            if ($('#category_id_' + i).val() == 0) {
                unselected_cat = unselected_cat + 1;
            } else {
                slideruploadedfile_4
                if ($('#slideruploadedfile_' + i)[0].files !== undefined && $('#slideruploadedfile_' + i)[0].files.length > 0) {
                    $('#slideruploadedfile_' + i).removeClass('kb_error_field');
                } else {
                    if ($('#sliderimage_' + i).attr('src') == default_image) {
                        error = true;
                        $('#slideruploadedfile_' + i).addClass('kb_error_field');
//                        $('#slideruploadedfile_' + i).after('<span class="kb_error_message">' + 'Please Upload Image For Category' + '</span>');
                        $('#slideruploadedfile_' + i).after('<span class="kb_error_message">' + blank_category_image + '</span>');
                    }
                    
                }
            }
        }
        if (unselected_cat > 4) {
            var error = true;
            showErrorMessage(min_category_limit);
        }
        if (error) {
            return false;
        } else {
            submitTopCategoryForm();
        }
        return false;
    });
}
function submitTopCategoryForm() {
    require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
        var id_layout = $('#id_layout').val();
        var id_component = $('#id_component_selected').val();
        var id_category_1 = $('#category_id_1').val();
        var id_category_2 = $('#category_id_2').val();
        var id_category_3 = $('#category_id_3').val();
        var id_category_4 = $('#category_id_4').val();
        var id_category_5 = $('#category_id_5').val();
        var id_category_6 = $('#category_id_6').val();
        var id_category_7 = $('#category_id_7').val();
        var id_category_8 = $('#category_id_8').val();
        // changes
        var image_content_mode = $('#image_content_mode').val();
        // changes started
        var fd = new FormData();
        if ($('#slideruploadedfile_1').get(0).files.length > 0 && id_category_1 != 0) {
            fd.append('image_1', $('#slideruploadedfile_1')[0].files[0]);
        }
        if ($('#slideruploadedfile_2').get(0).files.length > 0 && id_category_2 != 0) {
            fd.append('image_2', $('#slideruploadedfile_2')[0].files[0]);
        }
        fd.append('image_content_mode', image_content_mode);
        if ($('#slideruploadedfile_3').get(0).files.length > 0 && id_category_3 != 0) {
            fd.append('image_3', $('#slideruploadedfile_3')[0].files[0]);
        }
        if ($('#slideruploadedfile_4').get(0).files.length > 0 && id_category_4 != 0) {
            fd.append('image_4', $('#slideruploadedfile_4')[0].files[0]);
        }
        if ($('#slideruploadedfile_5').get(0).files.length > 0 && id_category_5 != 0) {
            fd.append('image_5', $('#slideruploadedfile_5')[0].files[0]);
        }
        if ($('#slideruploadedfile_6').get(0).files.length > 0 && id_category_6 != 0) {
            fd.append('image_6', $('#slideruploadedfile_6')[0].files[0]);
        }
        if ($('#slideruploadedfile_7').get(0).files.length > 0 && id_category_7 != 0) {
            fd.append('image_7', $('#slideruploadedfile_7')[0].files[0]);
        }
        if ($('#slideruploadedfile_8').get(0).files.length > 0 && id_category_8 != 0) {
            fd.append('image_8', $('#slideruploadedfile_8')[0].files[0]);
        }
        fd.append('form_key', window.FORM_KEY);
        fd.append('id_layout', id_layout);
        fd.append('id_component', id_component);
        fd.append('id_category_1', id_category_1);
        fd.append('id_category_2', id_category_2);
        fd.append('id_category_3', id_category_3);
        fd.append('id_category_4', id_category_4);
        fd.append('id_category_5', id_category_5);
        fd.append('id_category_6', id_category_6);
        fd.append('id_category_7', id_category_7);
        fd.append('id_category_8', id_category_8);
        $.ajax({
            url: ajaxaction + "?saveTopcategoryFormData=true",
            data: fd,
            type: "post",
            showLoader: true,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#submitOptionsslider2').prop("disabled", true);
            },
            success: function (output)
            {
                var data = JSON.parse(output);
                if (data.error != undefined) {
                    showErrorMessage(data['error']);
                }
                if (data.success != undefined) {
                    showSuccessMessage(data['success']);
                }


                var index = 1;
                $.each(data.Added_Categories, function (key, value) {

                    $('img[id="top_category_' + index + '_' + id_layout + '_' + id_component + '"]').attr('src', value.image_src);
                    if (value.heading != '' && value.heading != undefined) {
                        $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').text(value.heading);
                        $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').css('display', 'block');
                    } else {
                        $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').css('display', 'none');
                    }
                    index++;
                });
            }
        });
        return false;
    });
}

function submitComponentHeadingForm() {
    require(['jquery'], function ($) {
        var id_layout = $('#id_layout').val();
        var id_component = $('#id_component_selected').val();

        var component_heading = $('#component_heading').val();

        $.ajax({
            url: ajaxaction + "?saveHeadingFormData=true",
            data: {
                form_key: window.FORM_KEY,
                id_component: id_component,
                component_heading: component_heading
            },
            type: "post",
            showLoader: true,
            success: function (data)
            {
                var component_name = $("#component_name").val();
                if (data['error'] != undefined) {
                    showErrorMessage(data['error']);
                }
                if (data['success'] != undefined) {
                    if (component_name == 'banner_square') {
                        $('p[id="bannerSquare_comp_heading_' + id_layout + '_' + id_component + '"]').text(component_heading);
                    } else if (component_name == 'banners_countdown') {
                        $('h4[id="bannerCountdown_comp_heading_' + id_layout + '_' + id_component + '"]').text(component_heading);
                    } else if (component_name == 'banners_grid') {
                        $('h4[id="bannerGrid_comp_heading_' + id_layout + '_' + id_component + '"]').text(component_heading);
                    } else if (component_name == 'banner_horizontal_slider') {
                        $('h4[id="bannerHorizontalSlide_comp_heading_' + id_layout + '_' + id_component + '"]').text(component_heading);
                    } else if (component_name == 'products_square') {
                        $('h4[id="productSquare_comp_heading_' + id_layout + '_' + id_component + '"]').text(component_heading);
                    } else if (component_name == 'products_horizontal') {
                        $('h4[id="slidingProducts_comp_heading_' + id_layout + '_' + id_component + '"]').text(component_heading);
                    } else if (component_name == 'products_recent') {

                    } else if (component_name == 'products_grid') {
                        $('p[id="productGrid_comp_heading_' + id_layout + '_' + id_component + '"]').text(component_heading);
                    }
                    jQuery("#productSquare_comp_heading_2_54")
                    showSuccessMessage(data['success']);
                }
            }
        });
        return false;
    });
}

function veloValidateComponentHeadingForm(a) {
    require(['jquery'], function ($) {
        $('.kb_error_message').remove();
        $('input[name="component_heading"]').removeClass('kb_error_field');
        var component_heading_error = false;

        var heading_value = $('#component_heading').val();

        if (heading_value == '') {
            ;
            component_heading_error = true;
            $('input[name="component_heading"]').addClass('kb_error_field');
            $('input[name="component_heading"]').after('<span class="kb_error_message">' + required_entry_txt + '</span>');
        }


        if (component_heading_error) {
            return false;
        }
        submitComponentHeadingForm(event);

        return false;
    });
}

function editComponentHeadingFunction(a)
{
    require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
        var str = $(a).attr('id');
        var array = str.split("_");
        var id_component = array[1];
        $('#id_component_selected').val(id_component);
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: edit_component_txt,
            buttons: [
                {
                    text: save_txt,
                    class: 'button_submit_form_component btn btn-success',
                    id: 'save_component',
                    click: function () {
                        return veloValidateComponentHeadingForm(this);
                    }
                },
                {
                    text: close_txt,
                    class: 'hidden_element btn btn-warning banner_popup_close',
                    click: function () {
                        this.closeModal();
                    }
                }
            ]
        };

        $.ajax({
            url: ajaxaction + "?getComponentHeadingForm=true",
            data: {
                form_key: window.FORM_KEY,
                method: 'edit',
                id_component: id_component
            },
            showLoader: true,
            type: "POST",
            success: function (output)
            {
                if (1) {
                    var popup = modal(options, $('#component_form_popup'));
                    $("#component_form_popup").html(output).modal("openModal");
                }
            }
        });
    });
}

function editTopCategoryComponentFunction(a)
{
    require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
        var str = $(a).attr('id');
        var array = str.split("_");
        var id_component = array[1];
        var id_layout = $('#id_layout').val();
        $('#id_component_selected').val(id_component);

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: edit_component_txt,
            buttons: [
                {
                    text: save_txt,
                    class: 'button_submit_form_component btn btn-success',
                    id: 'save_component',
                    click: function () {
                        return veloValidateTopcategoryForm(this);
                    }
                },
                {
                    text: close_txt,
                    class: 'hidden_element btn btn-warning banner_popup_close',
                    click: function () {
                        this.closeModal();
                    }
                }
            ]
        };

        $.ajax({
            url: ajaxaction + "?getCategoryForm=true",
            data: {
                form_key: window.FORM_KEY,
                method: 'edit',
                id_layout: id_layout,
                id_component: id_component
            },
            showLoader: true,
            type: "POST",
            success: function (output)
            {
                var data = JSON.parse(output);

                var popup = modal(options, $('#component_form_popup'));
                $("#component_form_popup").html(data.html).modal("openModal");

                uploadtopCategoryfile(id_component, id_layout);

                /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                var index = 1;
                if (data.Added_Categories != null) {
                    $.each(data.Added_Categories, function (key, value) {

                        $('img[id="top_category_' + index + '_' + id_layout + '_' + id_component + '"]').attr('src', value.image_src);

                        if (value.heading != '' && value.heading != undefined) {
                            $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').text(value.heading);
                            $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').css('display', 'block');
                        } else {
                            $('p[id="top_category_text_' + index + '_' + id_layout + '_' + id_component + '"]').css('display', 'none');
                        }
                        index++;
                    });
                }
                /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
            }
        });
    });
}



function submitbannersliderform(a)
{
    require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
        var id_layout = $('#id_layout').val();
        var id_component = $('#id_component_selected').val();
        var image_type = $('#image_type').val();
        var image_url = $('#image_url').val();
        var redirect_activity = $('#redirect_activity').val();
        var category_id = $('#category_id').val();
        var redirect_product_id = $('#redirect_banner_product_id').val();
        var image_content_mode = $('#image_content_mode').val();
        var redirect_product_name = $('#redirect_banner_product_name').val();
        var banner_position = $('#banner_position').val();
        var id_banner = $('#id_banner').val();
        var fd = new FormData();
        fd.append('image', $('#slideruploadedfile')[0].files[0]);
        fd.append('form_key', window.FORM_KEY);
        fd.append('id_layout', id_layout);
        fd.append('id_component', id_component);
        fd.append('category_id', category_id);
        fd.append('redirect_activity', redirect_activity);
        fd.append('image_url', image_url);
        fd.append('position', banner_position);
        fd.append('id_banner', id_banner);
        fd.append('banner_heading', $("#banner_heading").val());
        if ($('#countdown_validity').is(":visible")) {
            fd.append('countdown_validity', $('#countdown_validity').val());
            fd.append('is_enabled_background_color', $('input[name="is_enabled_background_color"]:checked').val());
            fd.append('timer_background_color', $('input[name=timer_background_color]').val());
            fd.append('timer_text_color', $('input[name=timer_text_color]').val());
        }
        
        if ($('#width_of_banner').is(":visible")) {
            fd.append('width_of_banner', $('#width_of_banner').val());
            fd.append('height_of_banner', $('#height_of_banner').val());
            fd.append('margin_top', $('#margin_top').val());
            fd.append('margin_bottom', $('#margin_bottom').val());
            fd.append('margin_left', $('#margin_left').val());
            fd.append('margin_right', $('#margin_right').val());
            fd.append('background_color', $('#background_color').val());
        }
        
        fd.append('image_content_mode', image_content_mode);
        fd.append('image_type', image_type);
        fd.append('redirect_product_id', redirect_product_id);
        fd.append('redirect_product_name', redirect_product_name);
        $.ajax({
            url: ajaxaction + "?saveBannerSliderFormData=true",
            data: fd,
            type: "post",
            showLoader: true,
            processData: false,
            contentType: false,
            success: function (data)
            {
                var b = JSON.parse(data);

                if (data.error != undefined) {
                    showErrorMessage(data.error);
                }

                banner_elem = $("#component_name").val();
                if (1) {
                    /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                    debugger;
                    if (banner_elem == 'banner_square') {
                        $('.panel-footer').attr('element_type', 'banner_square');
                        $(".iframe_html li[id='layout_component_" + id_layout + '_' + id_component + "']").show();
                        var element_type = 'bannerSquareBannerimg_';
                        $('img[id="' + element_type + id_layout + '_' + id_component + '"]').parent().remove();
                        $('p[id="bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '"]').parent().remove();
                        $.each(b.Added_Banners, function (key, value) {
                            $('img[id="' + element_type + id_layout + '_' + id_component + '"]').remove();
                            $('p[id="bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '"]').parent().remove();
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').parent().remove();
//                            if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                                $('div[id="bannerSList_' + id_layout + '_' + id_component + '"]').append('<span class="BSSection"><img id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '" src=""></img><p class="elem_heading" id="' + 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"></p></span>');
//                            }


                            if (value.heading != '' && value.heading != undefined) {
                                $('p[id="' + 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                $('p[id="' + 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                            } else {
                                $('p[id="' + 'bannerSquareBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                            }
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("width", "100%");
                        });
                        if (b.component_heading != null && b.component_heading != undefined) {
                            $('p[id="bannerSquare_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                    } else if (banner_elem == 'banner_horizontal_slider') {
                        $('.panel-footer').attr('element_type', 'banner_horizontal_slider');
                        $(".iframe_html li[id='layout_component_" + id_layout + '_' + id_component + "']").show();
                        var element_type = 'bannerHorizontalSlideimg_';
                        var index = 1;
                        $.each(b.Added_Banners, function (key, value) {
                            $('p[id="bannerHorizontalSlide_elem_heading_' + id_layout + '_' + id_component + '"]').remove();
                            $('img[class="' + element_type + id_layout + '_' + id_component + '"]').remove();
                            var element_type1 = 'bannerHorizontalSlideimg1_';
                            var element_type2 = 'bannerHorizontalSlideimg2_';
                            $('img[id="' + element_type1 + id_layout + '_' + id_component + '"]').parent().remove();
                            $('img[id="' + element_type2 + id_layout + '_' + id_component + '"]').parent().remove();
                            $('p[id="bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '"]').remove();
                            $('p[id="bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '"]').remove();
                            if (index == 1) {
                                element_type = 'bannerHorizontalSlideimg1_';
                                var class_name = '.bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component;
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').parent().remove();
//                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                                    $('div[id="slidingBList_' + id_layout + '_' + id_component + '"] > #bannerHorizontalSlideList1').append('<span class="BHSSection"><img id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '" src=""></img><p class="elem_heading"  id="' + 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"></p></span>');
//                                }
                                if (value.heading != '') {
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                                } else {
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                                }
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image);
                            } else if (index == 2) {
                                element_type = 'bannerHorizontalSlideimg2_';
                                var class_name = 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component;
//                            if ($('img[id="'+element_type+ id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
//                                $('div[id="slidingBList_'+ id_layout + '_' + id_component + '"] > #bannerHorizontalSlideList2').append('<img id="'+ element_type + id_layout + '_' + id_component + '_' +value.kb_banner_id +'" src=""></img>');
//                                $('div[id="slidingBList_'+ id_layout + '_' + id_component + '"] > #bannerHorizontalSlideList2').append('<p class="elem_heading"  id="'+ 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' +value.kb_banner_id +'"></p>');
//                            }
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').parent().remove();
//                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {

                                    $('div[id="slidingBList_' + id_layout + '_' + id_component + '"] > #bannerHorizontalSlideList2').append('<span class="BHSSection"><img id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '" src=""></img><p class="elem_heading"  id="' + 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"></p></span>');
//                                }
                                if (value.heading != '') {
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                                } else {
                                    $('p[id="' + 'bannerHorizontalSlide_elem_heading2_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                                }
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image);
                            }
                            index++;
                        });
                        if (b.component_heading != null && b.component_heading != undefined) {
                            $('h4[id="bannerHorizontalSlide_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                    } else if (banner_elem == 'banners_grid') {
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('.panel-footer').attr('element_type', 'banners_grid');
                        var element_type = 'bannerGridimg1_';
                        var template_html = $('#bannerGRow').html();
                        $.each(b.Added_Banners, function (key, value) {
                            $('div[id="bannerGRow1_' + id_layout + '_' + id_component + '"]').find('#bannerGridimg').parent().remove();
                            var productinfo_html = template_html;
                            productinfo_html = productinfo_html.replace(/bannerGridimg1/g, 'bannerGridimg1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id);
                            productinfo_html = productinfo_html.replace(/bannerGrid_elem_heading/g, 'bannerGrid_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id);

                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').parent().remove();
//                            if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                                $('div[id="bannerGRow1_' + id_layout + '_' + id_component + '"]').append(productinfo_html);
//                            }
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image);

                            if (value.heading != '') {
                                $('p[id="' + 'bannerGrid_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').text(value.heading);
                                $('p[id="' + 'bannerGrid_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                            } else {
                                $('p[id="' + 'bannerGrid_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                            }
                        });
                        $('div[id="bannerGRow1_' + id_layout + '_' + id_component + '"] .bannerGridList').each(function () {
                            if ($(this).children().length == 0) {
                                $(this).remove();
                            }
                        });
                        if (b.component_heading != null) {
                            $('h4[id="bannerGrid_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                    } else if (banner_elem == 'banner_custom') {
                        var element_type = 'bannerCustomBannerimg_';
                        uploadfile(id_layout, id_component, element_type);
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        var element_type = 'bannerCustomBannerimg_';
//                        $('img[id="' + element_type + id_layout + '_' + id_component + '"]').parent().remove();
//                        $('p[id="bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '"]').parent().remove();
                        $.each(b.Added_Banners, function (key, value) {
                            $('img[id="' + element_type + id_layout + '_' + id_component + '"]').remove();
                            $('p[id="bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '"]').parent().remove();
                            if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                                $('div[id="bannerSList_' + id_layout + '_' + id_component + '"]').append('<span class="BSSection"><img id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '" src=""></img><p class="elem_heading" id="' + 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"></p></span>');
                            }
                            if (value.heading != '' && value.heading != undefined) {
                                $('p[id="' + 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                                $('p[id="' + 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                            } else {
                                $('p[id="' + 'bannerCustomBanner_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                            }
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').closest('.BSSection').css("width", value.banner_width + "%");
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').closest('.BSSection').css("height", value.banner_height + "%");
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image);
                            if (value.insets.top) {
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("margin-top", value.insets.top + "px");
                            }
                            if (value.insets.bottom) {
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("margin-bottom", value.insets.bottom + "px");
                            }
                            if (value.insets.left) {
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("margin-left", value.insets.left + "px");
                            }
                            if (value.insets.right) {
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').attr('src', value.Image).css("margin-top", value.insets.right + "px");
                            }
                        });
                        if (b.component_heading != null && b.component_heading != undefined) {
                            $('p[id="bannerCustom_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        showUrlImage();

                        setDate();
                    }
                }

                if (b.success != undefined) {
                    $("#banners_grid").html(b.grid_html);
                    $("#form_save_banner_square_component").find("input[type=text], select, file").val("");
                    $("#sliderimage").attr("src", no_image_url);
                    $("#slideruploadedfile").val("");
                    $('#redirect_activity').trigger('change');
                    $('#image_type').trigger('change');
                    showSuccessMessage(b.success);
                }

            }


        });
        return false;
    });
}
function submitProductform(a)
{
    require(['jquery'], function ($) {
        var id_layout = $('#id_layout').val();
        var id_component = $('#id_component_selected').val();
        var category_id = $('#category_id').val();
        var number_of_product = $.trim($('#number_of_products').val());
        var category_id = $.trim($('#category_id').val());
        var product_list = $.trim($('#product_list').val());
        var category_products = $.trim($('#category_products').val());
        var product_type = $('#product_type').val();
        var image_content_mode = $('#image_content_mode').val();
        var fd = new FormData();
        fd.append('number_of_product', number_of_product);
        fd.append('id_component', id_component);
        fd.append('form_key', window.FORM_KEY);
        fd.append('category_id', category_id);
        fd.append('id_layout', id_layout);
        fd.append('component_heading', $('#component_heading').val());
        fd.append('product_type', product_type);
        fd.append('product_list', product_list);
        fd.append('category_products', category_products);
        fd.append('image_content_mode', image_content_mode);
        $.ajax({
            showLoader: true,
            url: ajaxaction + "?saveProductFormData=true",
            data: fd,
            type: "post",
            processData: false,
            contentType: false,
            success: function (data)
            {
                var b = JSON.parse(data);
                if (1) {
                    //$('#category_id').parent().parent().hide();
                    showHideProductType(a);
                    pro_elem = $("#component_name").val();
                    /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                    if (pro_elem == 'products_grid') {
                        $('.panel-footer').attr('element_type', 'productGridimg');
                        var element_type = 'productGridimg_';
                        var template_html = $('#productGRow').html();
                        var id_ProductArray = [];
////                $(".productGridRow").remove();
                        $.each(b.added_Products.products_for_preview, function (key, value) {
//                    $(".productGridRow1").remove();
                            $('div[id="productGRow1_' + id_layout + '_' + id_component + '"]').find('#productGridimg1').parent().remove();
                            id_ProductArray.push(element_type + id_layout + '_' + id_component + '_' + value.id);
                            var img_id = element_type + id_layout + '_' + id_component;
                            var img_id_replaced = img_id.replace(img_id, img_id + '_' + value.id);
                            var productinfo_html = template_html;
                            productinfo_html = productinfo_html.replace(/productName/g, 'productName_' + value.id);
                            productinfo_html = productinfo_html.replace(/productPrice/g, 'productPrice_' + value.id);
                            productinfo_html = productinfo_html.replace(/productGridimg/g, 'productGridimg_' + id_layout + '_' + id_component + '_' + value.id);
                            if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                $('div[id="productGRow1_' + id_layout + '_' + id_component + '"]').append(productinfo_html);
                            }
                            $('h5[id="productName_' + value.id + '"]').html(value.name.substring(0, 8));
                            $('h6[id="productPrice_' + value.id + '"]').html(value.price);
                            $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                        });
                        $('div[id="productGRow1_' + id_layout + '_' + id_component + '"] img').each(function () {
                            if ($.inArray($(this).attr('id'), id_ProductArray) == -1) {
                                $(this).parent().remove();
                            }
                        });
                        if (b.added_Products.component_heading_preview != null) {
                            $('p[id="productGrid_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.added_Products.component_heading_preview);
                        }
                    } else if (pro_elem == 'products_square') {
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        var element_type = 'productSquareimg1_';
                        var template_html = $('.productSquareimg1').parent().html();
                        //$(".iframe_html #productSList_" + id_layout + '_' + id_component).empty();
                        var id_array = [];
                        $.each(b.added_Products.products_for_preview, function (key, value) {

                            var element_type = 'productSquareimg1_';
                            id_array.push(element_type + value.id);
                            var template_html = $('.productSquareimg1').parent().html();
                            var img_id = element_type + id_layout + '_' + id_component;
                            var img_id_replaced = img_id.replace(img_id, img_id + '_' + value.id);
                            var productinfo_html = template_html;
                            productinfo_html = productinfo_html.replace(/productSquareimg1/g, 'productSquareimg1_' + value.id);
                            productinfo_html = productinfo_html.replace(/productName/g, 'productName_' + value.id);
                            productinfo_html = productinfo_html.replace(/productPrice/g, 'productPrice_' + value.id);
                            if ($('img[id="' + element_type + value.id + '"]').length == "0") {
                                $('div[id="productSList_' + id_layout + '_' + id_component + '"]').append(productinfo_html);
                            }
                            $('h5[id="productName_' + value.id + '"]').html(value.name.substring(0, 8));
                            $('h6[id="productPrice_' + value.id + '"]').html(value.price);
                            $('img[id="' + element_type + value.id + '"]').attr('src', value.src);
                            $('img[id="' + element_type + value.id + '"]').css('width', '282px');
                            $('img[id="' + element_type + value.id + '"]').css('height', '261px');
                            //                    
                        });
                        $('div[id="productSList_' + id_layout + '_' + id_component + '"] img').each(function () {
                            if ($.inArray($(this).attr('id'), id_array) == -1) {
                                $(this).next().remove();
                                $(this).remove();
                            }
                        });
                        if (b.added_Products.products_for_preview.length > 0) {
                            $('div[id="productSList_' + id_layout + '_' + id_component + '"] .productSquareimg').next().remove();
                            $('div[id="productSList_' + id_layout + '_' + id_component + '"] .productSquareimg').remove();
                        }
                        if (b.added_Products.component_heading_preview != null) {
                            $('h4[id="productSquare_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.added_Products.component_heading_preview);
                        }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                    } else if (pro_elem == 'products_horizontal') {
                        $('.panel-footer').attr('element_type', 'slidingProducts');
                        var element_type = 'slidingProductsimg';
                        var template_html = $('#slidingPRow').html();
                        var index = 1;
                        var id_ProductHorizontal_array1 = [];
                        var id_ProductHorizontal_array2 = [];
                        var id_ProductHorizontal_array3 = [];
                        $.each(b.added_Products.products_for_preview, function (key, value) {
                            // $('img[id="'+element_type+ id_layout + '_' + id_component + '"]').remove();
                            var element_type1 = 'slidingProductsimg1_';
                            var element_type2 = 'slidingProductsimg2_';
                            var element_type3 = 'slidingProductsimg3_';
                            $('img[id="' + element_type1 + id_layout + '_' + id_component + '"]').next().remove();
                            $('img[id="' + element_type1 + id_layout + '_' + id_component + '"]').remove();
                            $('img[id="' + element_type2 + id_layout + '_' + id_component + '"]').next().remove();
                            $('img[id="' + element_type2 + id_layout + '_' + id_component + '"]').remove();
                            $('img[id="' + element_type3 + id_layout + '_' + id_component + '"]').next().remove();
                            $('img[id="' + element_type3 + id_layout + '_' + id_component + '"]').remove();
                            if (index == 1) {

                                element_type = 'slidingProductsimg1_';
                                var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList1').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList1').append(productinfo_html);
                                }
                                $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                                $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                                id_ProductHorizontal_array1.push(element_type + id_layout + '_' + id_component + '_' + value.id);
                            } else if (index == 2) {

                                element_type = 'slidingProductsimg2_';
                                var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList2').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList2').append(productinfo_html);
                                }
                                $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                                $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                                id_ProductHorizontal_array2.push(element_type + id_layout + '_' + id_component + '_' + value.id);
                            } else if (index == 3) {

                                element_type = 'slidingProductsimg3_';
                                var productinfo_html = $('.productSlideList').find('div[class="productContent"]').html();
                                productinfo_html = productinfo_html.replace(/ProductName/g, 'ProductName_' + value.id);
                                productinfo_html = productinfo_html.replace(/ProductPrice/g, 'ProductPrice_' + value.id);
                                if ($('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').length == "0") {
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList3').append('<img id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '" src=""></img>');
                                    $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList3').append(productinfo_html);
                                }
                                $('h5[id="ProductName_' + value.id + '"]').html(value.name.substring(0, 8));
                                $('h6[id="ProductPrice_' + value.id + '"]').html(value.price);
                                $('img[id="' + element_type + id_layout + '_' + id_component + '_' + value.id + '"]').attr('src', value.src);
                                id_ProductHorizontal_array3.push(element_type + id_layout + '_' + id_component + '_' + value.id);
                            }
                            index++;
                        });
                        console.log(id_ProductHorizontal_array1);
                        // console.log(id_ProductHorizontal_array2);
                        //console.log(id_ProductHorizontal_array3);
                        $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList1 img').each(function () {

                            if ($.inArray($(this).attr('id'), id_ProductHorizontal_array1) == -1) {
                                $(this).next().remove();
                                $(this).remove();
                            }
                        });
                        $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList2 img').each(function () {
                            if ($.inArray($(this).attr('id'), id_ProductHorizontal_array2) == -1) {
                                $(this).next().remove();
                                $(this).remove();
                            }
                        });
                        $('div[id="slidingPRow1_' + id_layout + '_' + id_component + '"] > #productSlideList3 img').each(function () {
                            if ($.inArray($(this).attr('id'), id_ProductHorizontal_array3) == -1) {
                                $(this).next().remove();
                                $(this).remove();
                            }
                        });
                        if (b.added_Products.component_heading_preview != null) {
                            $('h4[id="slidingProducts_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.added_Products.component_heading_preview);
                        }

                    }
                    /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/

                }

                if (b.success != undefined) {
                    showSuccessMessage(b.success);
                }

                if (b.error != undefined) {
                    showErrorMessage(b.error);
                }

            }
        });
    });
    return false;
}

function submitCountdownbannersliderform(a)
{
    require(['jquery'], function ($) {
        var id_layout = $('#id_layout').val();
        var id_component = $('#id_component_selected').val();
        var image_type = $('#image_type').val();
        var image_url = $('#image_url').val();
        var redirect_activity = $('#redirect_activity').val();
        var category_id = $('#category_id').val();
        var redirect_product_id = $('#redirect_banner_product_id').val();
        var image_content_mode = $('#image_content_mode').val();
        var redirect_product_name = $('#redirect_banner_product_name').val();
        var banner_position = $('#banner_position').val();
        var id_banner = $('#id_banner').val();
        var fd = new FormData();
        fd.append('form_key', window.FORM_KEY);
        fd.append('image', $('#slideruploadedfile')[0].files[0]);
        fd.append('id_layout', id_layout);
        fd.append('id_component', id_component);
        fd.append('category_id', category_id);
        fd.append('redirect_activity', redirect_activity);
        fd.append('image_url', image_url);
        fd.append('position', banner_position);
        fd.append('id_banner', id_banner);
        fd.append('banner_heading', $('#banner_heading').val());

        if ($('#countdown_validity').is(":visible")) {
            fd.append('countdown_validity', $('#countdown_validity').val());
            fd.append('is_enabled_background_color', $('input[name="is_enabled_background_color"]:checked').val());
            fd.append('timer_background_color', $('input[name=timer_background_color]').val());
            fd.append('timer_text_color', $('input[name=timer_text_color]').val());
        }
        fd.append('image_content_mode', image_content_mode);
        fd.append('image_type', image_type);
        fd.append('redirect_product_id', redirect_product_id);
        fd.append('redirect_product_name', redirect_product_name);
        $.ajax({
            showLoader: true,
            url: ajaxaction + "?saveBannerSliderFormData=true",
            data: fd,
            type: "post",
            processData: false,
            contentType: false,
            success: function (data)
            {
                var b = JSON.parse(data);
                if (b.error != undefined) {
                    showErrorMessage(data['error']);
                }

                /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                $('.panel-footer').attr('element_type', 'banners_countdown');
                var element_type = 'bannerCountdownimg1_';
                $(".iframe_html li[id='layout_component_" + id_layout + '_' + id_component + "']").show();
                $.each(b.Added_Banners, function (key, value) {
                    $('div[id="bannerCountdownList_' + id_layout + '_' + id_component + '"]').find('#bannerCountdownimg').parent().remove();
                    var template_html = $('.countdownlistContentContainer1').html();
                    template_html = template_html.replace(/bannerCountdownimg1/g, 'bannerCountdownimg1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id);
                    template_html = template_html.replace(/bannerCountdown_elem_heading/g, 'bannerCountdown_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id);
                    template_html = template_html.replace(/countdown_banner_img_url/g, value.Image);
                    if (value.is_enabled_background_color == "1") {
                        template_html = template_html.replace(/background_color_of_timer_text/g, value.background_color);
//                            $("#bannerCountdownimg1_" +id_layout+'_'+id_component+'_'+ value.kb_banner_id).find(".countDownTimer").css('background-color', value.background_color);
                    } else {
                        template_html = template_html.replace(/background_color_of_timer_text/g, 'transparent');
                    }

                    template_html = template_html.replace(/color_of_timer_text/g, value.timer_text_color);
//                         $("#bannerCountdownimg1_" +id_layout+'_'+id_component+'_'+ value.kb_banner_id).css('color', value.timer_text_color);

                    if ($('div[id="' + element_type + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').length == "0") {
                        $('div[id="bannerCountdownList_' + id_layout + '_' + id_component + '"]').append(template_html);
                    }
                    if (value.heading != '') {
                        $('p[id="' + 'bannerCountdown_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').html(value.heading);
                        $('p[id="' + 'bannerCountdown_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'block');
                    } else {
                        $('div[id="' + 'bannerCountdownimg1_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('bottom', '5px');
                        $('p[id="' + 'bannerCountdown_elem_heading_' + id_layout + '_' + id_component + '_' + value.kb_banner_id + '"]').css('display', 'none');
                    }
                });
                if (b.component_heading != null) {
                    $('h4[id="bannerCountdown_comp_heading_' + id_layout + '_' + id_component + '"]').html(b.component_heading);
                }
                /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/


                if (b.success != undefined) {
                    $("#banners_grid").html(b.grid_html);
                    $("#form_save_banner_square_component").find("input[type=text], select, file").val("");
                    $("#sliderimage").attr("src", no_image_url);
                    $("#slideruploadedfile").val("");
                    $("#form_save_banner_square_component").find("#timer_background_color").css('backgroundColor', '');
                    $("#form_save_banner_square_component").find("#timer_text_color").css('backgroundColor', '');
                    $('#redirect_activity').trigger('change');
                    $('#image_type').trigger('change');
                    showSuccessMessage(b.success);
                }
                showUrlImage();
                uploadfile();
            }
        });
        return false;
    });
}

function edit_banner_slider(banner_id) {
    require(['jquery'], function ($) {
        var id_component = $('#id_component_selected').val();
        $.ajax({
            url: ajaxaction + "?getEditBannerFormData=true",
            showLoader: true,
            data: {
                id_banner: banner_id,
                form_key: window.FORM_KEY
            },
            type: "post",
            success: function (data)
            {
                if (data['id'] != undefined) {
                    $("#image_type").val(data['image_type']);
                    if (data['image_type'] == 'url') {
                        $("#image_url").val(data['image_url']);
                    }

                    if (data['image_url'] != '') {
                        $("img#sliderimage").attr('src', data['image_url']);
                    }
                    $("#image_type").trigger("change");


                    $("#image_content_mode").val(data['image_content_mode']);
                    $("#redirect_activity").val(data['redirect_activity']);
                    $("#category_id").val(data['category_id']);
                    $("#redirect_banner_product_id").val(data['product_id']);
                    $("#redirect_activity").trigger("change");

                    $("#countdown_validity").val(data['countdown']);
                    if (data['is_enabled_background_color'] == '1') {
                        $("#is_enabled_background_color").prop('checked', true);
                    } else {
                        $("#is_enabled_background_color").prop('checked', false);
                    }

                    $("#timer_background_color").val(data['background_color']);
                    $("#timer_background_color").css("backgroundColor", data['background_color']);
                    
                    $("#background_color").val(data['background_color']);
                    $("#background_color").css("backgroundColor", data['background_color']);
                    
                    $("#timer_text_color").val(data['text_color']);
                    $("#timer_text_color").css("backgroundColor", data['text_color']);

                    $("#height_of_banner").val(data['height']);
                    $("#width_of_banner").val(data['width']);
                    $("#margin_top").val(data['top_margin']);
                    $("#margin_bottom").val(data['bottom_margin']);
                    $("#margin_left").val(data['left_margin']);
                    $("#margin_right").val(data['right_margin']);
                    
                    $("#banner_position").val(data['position']);

                    $("#id_banner").remove();
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'id_banner',
                        value: banner_id,
                        name: 'id_banner'
                    }).appendTo('form#form_save_banner_square_component');
                    $('.modal-content').animate({scrollTop: $("#form_save_banner_square_component").offset().top}, 300);
                }
                if (data['error'] != undefined) {
                    showErrorMessage(data['error']);
                }
            }
        });
    });
}

function delete_banner_slider(banner_id) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            var a = banner_id;
            var id_layout = $('#id_layout').val();
            var id_component = $('#id_component_selected').val();
            $.ajax({
                url: ajaxaction + "?deleteSliderBanner=true",
                showLoader: true,
                data: {
                    id_banner: banner_id,
                    id_component: id_component,
                    form_key: window.FORM_KEY,
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        showSuccessMessage(data['success']);
                        BannerSquareGridJsObject.doFilter()

                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $("img[id$='" + id_layout + '_' + id_component + '_' + a + "']").next().remove();
                        $("img[id$='" + id_layout + '_' + id_component + '_' + a + "']").remove();
                        $("div[id$='" + id_layout + '_' + id_component + '_' + a + "']").parent().remove();
                        if ($("p[id$='" + id_layout + '_' + id_component + '_' + a + "']").remove()) {
                            var p_length = "1";
                        } else {
                            var p_length = "0";
                        }
//                if ($(".iframe_html div[id$='"+id_layout+'_'+id_component+"']").find('p').length == p_length) {
//                    $(".iframe_html li[id='layout_component_"+id_layout+'_'+id_component+"']").hide();
//                }

//                if ($(".iframe_html div[id$='"+id_layout+'_'+id_component+"']").find('h5').length =="0") {
//                    $(".iframe_html li[id='layout_component_"+id_layout+'_'+id_component+"']").hide();
//                }
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/

                    }
                    if (data['error'] != undefined) {
                        showErrorMessage(data['error']);
                    }
                }
            });
        }
    });
}

function uploadfile() {
    require(['jquery'], function ($) {
        $('#slideruploadedfile').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage').attr('src', e.target.result);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                    }
                    $('input[name="slideruploadedfile"]').parent().find('.kb_error_message').remove();
                }

            } else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
    });

}
/*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
function uploadtopCategoryfile(id_component, id_layout) {
    require(['jquery'], function ($) {
        /*ends:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
        $('#slideruploadedfile_1').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile_"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile_1"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage_1");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage_1').attr('src', e.target.result);
                        }

                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('img[id="top_category_1_' + id_layout + '_' + id_component + '"]').attr('src', $('#sliderimage_1').attr('src'));
                        var image_cat_holder = $('img[id="top_category_1_' + id_layout + '_' + id_component + '"]');

                        image_cat_holder.empty();

                        var reader1 = new FileReader();
                        reader1.onload = function (f) {
                            $('img[id="top_category_1_' + id_layout + '_' + id_component + '"]').attr('src', f.target.result);
                        }
                        image_cat_holder.show();
                        reader1.readAsDataURL($(this)[0].files[0]);
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/

                    }
                    $('input[name="slideruploadedfile_1"]').parent().find('.kb_error_message').remove();
                }

            } else
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
        $('#slideruploadedfile_2').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile_2"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile_2"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage_2");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage_2').attr('src', e.target.result);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('img[id="top_category_2_' + id_layout + '_' + id_component + '"]').attr('src', $('#sliderimage_2').attr('src'));
                        var image_cat_holder = $('img[id="top_category_1_' + id_layout + '_' + id_component + '"]');

                        image_cat_holder.empty();

                        var reader1 = new FileReader();
                        reader1.onload = function (f) {
                            $('img[id="top_category_2_' + id_layout + '_' + id_component + '"]').attr('src', f.target.result);
                        }
                        image_cat_holder.show();
                        reader1.readAsDataURL($(this)[0].files[0]);
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/

                    }
                    $('input[name="slideruploadedfile_2"]').parent().find('.kb_error_message').remove();
                }

            } else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
        $('#slideruploadedfile_4').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile_4"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile_4"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage_2");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage_4').attr('src', e.target.result);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('img[id="top_category_4_' + id_layout + '_' + id_component + '"]').attr('src', $('#sliderimage_4').attr('src'));
                        var image_cat_holder = $('img[id="top_category_4_' + id_layout + '_' + id_component + '"]');

                        image_cat_holder.empty();

                        var reader1 = new FileReader();
                        reader1.onload = function (f) {
                            $('img[id="top_category_4_' + id_layout + '_' + id_component + '"]').attr('src', f.target.result);
                        }
                        image_cat_holder.show();
                        reader1.readAsDataURL($(this)[0].files[0]);
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                    }
                    $('input[name="slideruploadedfile_4"]').parent().find('.kb_error_message').remove();
                }

            } else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
        $('#slideruploadedfile_3').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile_3"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile_3"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage_3");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage_3').attr('src', e.target.result);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                        /*start:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                        $('img[id="top_category_3_' + id_layout + '_' + id_component + '"]').attr('src', $('#sliderimage_3').attr('src'));
                        var image_cat_holder = $('img[id="top_category_3_' + id_layout + '_' + id_component + '"]');

                        image_cat_holder.empty();

                        var reader1 = new FileReader();
                        reader1.onload = function (f) {
                            $('img[id="top_category_3_' + id_layout + '_' + id_component + '"]').attr('src', f.target.result);
                        }
                        image_cat_holder.show();
                        reader1.readAsDataURL($(this)[0].files[0]);
                        /*end:changes made by Bhupendra Singh Bisht on 04 May 2020 to make preview changes*/
                    }
                    $('input[name="slideruploadedfile_3"]').parent().find('.kb_error_message').remove();
                }

            } else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
        $('#slideruploadedfile_5').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile_5"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile_5"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage_5");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage_5').attr('src', e.target.result);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                    }
                    $('input[name="slideruploadedfile_5"]').parent().find('.kb_error_message').remove();
                }

            } else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
        $('#slideruploadedfile_6').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile_6"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile_6"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage_6");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage_6').attr('src', e.target.result);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                    }
                    $('input[name="slideruploadedfile_6"]').parent().find('.kb_error_message').remove();
                }

            } else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
        $('#slideruploadedfile_7').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile_7"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile_7"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage_7");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage_7').attr('src', e.target.result);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                    }
                    $('input[name="slideruploadedfile_7"]').parent().find('.kb_error_message').remove();
                }

            } else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
        $('#slideruploadedfile_8').on('change', function (e) {
            if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
            {
                var files = $(this)[0].files[0];
                var file_data = e.target.files;
                var file_mimetypes = [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                ];

                var file_format = false;
                for (i = 0; i < file_mimetypes.length; i++) {
                    if (files.type == file_mimetypes[i]) {
                        file_format = true;
                    }
                }

                if (!file_format)
                {
                    $('input[name="slideruploadedfile_8"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                    slider_banner_file_error = true;

                } else if (files.size > default_file_size) {
                    $('input[name="slideruploadedfile_8"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                    slider_banner_file_error = true;
                } else {
                    slider_banner_file_error = false;
                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#sliderimage_8");

                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#sliderimage_8').attr('src', e.target.result);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                    }
                    $('input[name="slideruploadedfile_8"]').parent().find('.kb_error_message').remove();
                }

            } else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
    });
}

function trashBannerSquareComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)
            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function trashBannerCountdownComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)
            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function trashBannerGridComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)
            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function trashBannerHorizontalComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)
            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function trashLastAccessComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)
            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function trashProductSquareComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)
            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function trashProductGridComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)
            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function trashProductHorizontalComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)
            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function trashTopcategoryComponentFunction(a) {
    require(['jquery'], function ($) {
        if (confirm(confirm_msg_txt)) {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component - 1;
            $('#number_of_component').val(num_of_component)

            var str = $(a).attr('id');
            var array = str.split("_");
            var id_component = array[1];
            var id_layout = $('#id_layout').val();
            $('#id_component_selected').val(id_component);
            $.ajax({
                url: ajaxaction + "?deleteComponent=true",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id_layout: id_layout,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        $(a).parents('.slide').remove();
                        preview_content();
                        showSuccessMessage(data['success']);
                    } else if (data['error'] != undefined) {
                        showSuccessMessage(data['error']);
                    }
                }
            });
        }
    });

}
function closeLayoutForm()
{
    require(['jquery'], function ($) {
        $('.layout_add_edit_form').slideUp("fast", function () {

        });
    });
}

function showSuccessMessage(message) {
    require(['jquery', "Knowband_Mobileappbuilder/growl_lib/jquery.growl"], function ($) {
        $.growl.notice({message: message});
    });
}


function showErrorMessage(message) {
    require(['jquery', "Knowband_Mobileappbuilder/growl_lib/jquery.growl"], function ($) {
        $.growl.error({message: message});
    });
}
;
