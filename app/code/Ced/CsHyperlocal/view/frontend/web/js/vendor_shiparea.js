require(
    [
        "jquery"
    ],
    function ($,
              modal) {
        
        
        var shiparea = setInterval(function(){
            if ($('#cshyperlocalshiparea').length > 0)
            {
                $('#cshyperlocalshiparea').addClass('shiparea-table-wrapper');
                clearInterval(shiparea);
            }
        },500);

        $('.cshyperlocal-shiparea-index').on("click", function (e) {
            if (!$(e.target).hasClass('shiparea-action')) {
                $('.shiparea-action').removeClass('active');
                $('.action-ul').removeClass('active');
            }
        });
        
        var shipareaTableWrapper = setInterval(function(){
                if ($('#cshyperlocalshiparea_table').length > 0)
                {
                    $('#cshyperlocalshiparea_table').on('click', '[id^=action]', function () {
                        if ($(this).parent().find('ul').hasClass('active')) {
                            $(this).parent().removeClass('active');
                            $(this).parent().find('ul').removeClass('active');
                        } else {
                            $(this).parent().addClass('active');
                            $('.action-ul').removeClass('active');
                            $(this).parent().find('ul').addClass('active');
                        }
                    });
                    clearInterval(shipareaTableWrapper);
                }
        },500);
        
    });