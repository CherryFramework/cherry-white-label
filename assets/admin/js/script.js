jQuery(function($){

    if ('undefined' !== typeof(optionsPageSettings))
    {
        console.log(optionsPageSettings.dashboard_heading);

        if (optionsPageSettings.dashboard_heading)
        {
            var $dashboard =  $('#dashboard-widgets-wrap');

            if (optionsPageSettings.dashboard_logo)
            {
                $dashboard.prev('h2').html('<img style="max-width: 30px; padding-right: 10px; vertical-align: bottom;" src="'+optionsPageSettings.dashboard_logo+'" alt"">'+optionsPageSettings.dashboard_heading);
            }
            else
            {
                $dashboard.prev('h2').text(optionsPageSettings.dashboard_heading);
            }
        }
    }

    $('[name = "visible-welcome-panel"]').bind('click', function(){
        if ($(this).prop('checked'))
        {
            $('#visible-to').slideDown('slow');
        }
        else
        {
            $('#visible-to').slideUp('slow');
        }
    });

    if (!$('[name = "visible-welcome-panel"]').prop('checked'))
    {
        $('#visible-to').hide();
    }

    $('.upload_image_button').click(function(){
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);
        var dataBrowse = $(this).data('browse');

        if ($(button).next().hasClass('remove_image_button'))
        {
            $(button).next().remove();
        }

        wp.media.editor.send.attachment = function(props, attachment) {
            $(button).prev().children('img').attr('src', attachment.url);
            $('<button type="button" class="remove_image_button button button-cancel">Remove</button>').insertAfter(button);
            $('[name = '+dataBrowse+']').val(attachment.url);

            wp.media.editor.send.attachment = send_attachment_bkp;

            $('.remove_image_button').click(function(){
                var r = confirm("You are sure?");
                if (r == true) {
                    var src = $(this).prev().prev().children('img').attr('data-src');
                    $(this).prev().prev().children('img').attr('src', src);
                    $(this).next().val('');
                    $(this).remove();
                }
                return false;
            });
        }
        wp.media.editor.open(button);
        return false;
    });

    $('.remove_image_button').click(function(){
        var r = confirm("You are sure?");
        if (r == true) {
            var src = $(this).prev().prev().children('img').attr('data-src');
            $(this).prev().prev().children('img').attr('src', src);
            $(this).next().val('');
            $(this).remove();
        }
        return false;
    });

});