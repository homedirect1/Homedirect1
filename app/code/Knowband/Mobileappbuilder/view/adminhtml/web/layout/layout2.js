/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  Magnto Module
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
            $('.slides').append(top_category_html);
            preview_content();
            scrollToBottom();
        } else {
            var a = "top_category";
            var id_layout = $('#id_layout').val();
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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

        }
        else // Internet Explorer 9 Compatibility
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
            $('.slides').append(banner_square_html);
            preview_content();
            scrollToBottom();
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banner_square";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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
            $('.slides').append(Hbanner_square_html);
            preview_content();
            scrollToBottom();
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banner_horizontal_slider";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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
            $('.slides').append(banner_Grid_html);
            preview_content();
            scrollToBottom();
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banners_grid";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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
            $('.slides').append(banner_countdown_html);
            preview_content();
            scrollToBottom();
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "banners_countdown";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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
            product_square_html = product_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
            product_square_html = product_square_html.replace(/product_square_edit_component_heading/g, 'heading_' + id);
            product_square_html = product_square_html.replace(/product_square_edit_component/g, 'edit_' + id);
            product_square_html = product_square_html.replace(/product_square_delete_component/g, 'delete_' + id);
            $('.slides').append(product_square_html);
            preview_content();
            scrollToBottom();
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "products_square";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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
            $('.slides').append(Hproduct_slide_html);
            preview_content();
            scrollToBottom();
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "products_horizontal";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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
            $('.slides').append(product_Grid_html);
            preview_content();
            scrollToBottom();
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "products_grid";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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
            $('.slides').append(last_accessed_html);
            preview_content();
            scrollToBottom();
        } else {
            num_of_component = parseInt($('#number_of_component').val());
            num_of_component = num_of_component + 1;
            var id_layout = $('#id_layout').val();
            $('#number_of_component').val(num_of_component);
            var a = "products_recent";
            if (num_of_component <= 20) {
                $.ajax({
                    url: ajaxaction + "?assign_component_id=true",
                    data: 'component_type=' + a + '&id_layout=' + id_layout,
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
                            $('.slides').append(last_accessed_html);
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
    require(['jquery'], function ($) {
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



        if (image_type == 'image' && slider_banner_file_error) {
            error_message = select_image_txt;
            slider_banner_error = true;
            $('input[name="filename"]').addClass('kb_error_field');
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
                if (1) {
                    var popup = modal(options, $('#component_form_popup'));
                    $("#component_form_popup").html(output).modal("openModal");
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
                if (1) {
                    var popup = modal(options, $('#component_form_popup'));
                    $("#component_form_popup").html(output).modal("openModal");
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
                if (1) {
                    var popup = modal(options, $('#component_form_popup'));
                    $("#component_form_popup").html(output).modal("openModal");

                    showHideProductType(a);
                    showUrlImage();
                    uploadfile();
                }
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
        var unselected_cat = 0;
        for (i = 1; i <= 8; i++) {
            if ($('#category_id_' + i).val() == 0) {
                unselected_cat = unselected_cat + 1;
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
            success: function (data)
            {
                if (data['error'] != undefined) {
                    showErrorMessage(data['error']);
                }
                if (data['success'] != undefined) {
                    showSuccessMessage(data['success']);
                }
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
                if (data['error'] != undefined) {
                    showErrorMessage(data['error']);
                }
                if (data['success'] != undefined) {
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
                if (1) {
                    var popup = modal(options, $('#component_form_popup'));
                    $("#component_form_popup").html(output).modal("openModal");
                    uploadtopCategoryfile();
                }
                uploadtopCategoryfile();
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
                if (data['error'] != undefined) {
                    showErrorMessage(data['error']);
                }
                if (data['success'] != undefined) {
                    $("#banners_grid").html(data['grid_html']);
                    $("#form_save_banner_square_component").find("input[type=text], select, file").val("");
                    $("#sliderimage").attr("src",no_image_url);
                    $("#slideruploadedfile").val("");
                    $('#redirect_activity').trigger('change');
                    $('#image_type').trigger('change');
                    showSuccessMessage(data['success']);
                }
                showUrlImage();
                uploadfile();
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
                if (data['success'] != undefined) {
                    showSuccessMessage(data['success']);
                }

                if (data['error'] != undefined) {
                    showErrorMessage(data['error']);
                }
            }
        });
        return false;
    });
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
                if (data['error'] != undefined) {
                    showErrorMessage(data['error']);
                }
                if (data['success'] != undefined) {
                    $("#banners_grid").html(data['grid_html']);
                    $("#form_save_banner_square_component").find("input[type=text], select, file").val("");
                    $("#sliderimage").attr("src",no_image_url);
                    $("#slideruploadedfile").val("");
                    $("#form_save_banner_square_component").find("#timer_background_color").css('backgroundColor', '');
                    $("#form_save_banner_square_component").find("#timer_text_color").css('backgroundColor', '');
                    $('#redirect_activity').trigger('change');
                    $('#image_type').trigger('change');
                    showSuccessMessage(data['success']);
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
            },
            type: "post",
            success: function (data)
            {
                if (data['id'] != undefined) {
                    $("#image_type").val(data['image_type']);
                    if(data['image_type'] == 'url'){
                        $("#image_url").val(data['image_url']);
                    }
                    
                    if(data['image_url'] != ''){
                        $("img#sliderimage").attr('src', data['image_url']);
                    }
                    $("#image_type").trigger("change");
                    
                    
                    $("#image_content_mode").val(data['image_content_mode']);
                    $("#redirect_activity").val(data['redirect_activity']);
                    $("#category_id").val(data['category_id']);
                    $("#redirect_banner_product_id").val(data['product_id']);
                    $("#redirect_activity").trigger("change");
                    
                    $("#countdown_validity").val(data['countdown']);
                    if(data['is_enabled_background_color'] == '1'){
                       $("#is_enabled_background_color").prop('checked', true);
                    } else {
                        $("#is_enabled_background_color").prop('checked', false);
                    }
                    
                    $("#timer_background_color").val(data['background_color']);
                    $("#timer_background_color").css("backgroundColor", data['background_color']);
                    $("#timer_text_color").val(data['text_color']);
                    $("#timer_text_color").css("backgroundColor", data['text_color']);
                    
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
            var id_component = $('#id_component_selected').val();
            $.ajax({
                url: ajaxaction + "?deleteSliderBanner=true",
                showLoader: true,
                data: {
                    id_banner: banner_id,
                    id_component: id_component
                },
                type: "post",
                success: function (data)
                {
                    if (data['success'] != undefined) {
                        showSuccessMessage(data['success']);
                        BannerSquareGridJsObject.doFilter()
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

            }
            else // Internet Explorer 9 Compatibility
            {
                $('#notification_error').html(invalid_file_txt);
                file_error = true;
            }
        });
    });

}
function uploadtopCategoryfile() {
    require(['jquery'], function ($) {
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
                    }
                    $('input[name="slideruploadedfile_1"]').parent().find('.kb_error_message').remove();
                }

            }
            else
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
                    }
                    $('input[name="slideruploadedfile_2"]').parent().find('.kb_error_message').remove();
                }

            }
            else // Internet Explorer 9 Compatibility
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
                    }
                    $('input[name="slideruploadedfile_4"]').parent().find('.kb_error_message').remove();
                }

            }
            else // Internet Explorer 9 Compatibility
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
                    }
                    $('input[name="slideruploadedfile_3"]').parent().find('.kb_error_message').remove();
                }

            }
            else // Internet Explorer 9 Compatibility
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

            }
            else // Internet Explorer 9 Compatibility
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

            }
            else // Internet Explorer 9 Compatibility
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

            }
            else // Internet Explorer 9 Compatibility
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

            }
            else // Internet Explorer 9 Compatibility
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
                data: 'id_layout=' + id_layout + '&id_component=' + id_component,
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
