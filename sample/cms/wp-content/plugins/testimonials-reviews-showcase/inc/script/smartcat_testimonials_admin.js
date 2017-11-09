jQuery(document).ready(function ($) {

    $('#smartcat_testimonials_icons .fa').click(function () {
        $('#testimonial_icon').val($(this).attr('class'));
        $('#smartcat_testimonials_icons .fa').removeClass('active');
        $(this).addClass('active');
    });


    smartcat_testimonials_set_display();

    $('#smartcat_testimonials_template').change(function () {
        smartcat_testimonials_set_display();
    });


    function smartcat_testimonials_set_display() {
        if ('hc' == $('#smartcat_testimonials_template').val()) {
            $('#social_icons_row').hide();
            $('#honey-comb-row').show();
            $('#columns-row,#height-row,#margin-row').hide();
        } else {
            $('#social_icons_row').show();
            $('#honey-comb-row').hide();
            $('#columns-row,#height-row,#margin-row').show();
        }
    }


    smartcat_testimonials_set_order();

    $('.smartcat-testimonials-sortable').sortable({
        update: function () {
            smartcat_testimonials_set_order();
        }
    });


    function smartcat_testimonials_set_order() {
        $('.smartcat-testimonials-sortable li').each(function () {
            $(this).attr('sc_member_order', $(this).index());
        });
    }

    $('#set_order').click(function () {
        var post_path = $('.smartcat-testimonials-sortable').attr('data-action');
        // UX
        $(this).attr('disabled', 'disable');

        $('.smartcat_testimonial_update_status .smartcat_testimonial_updating').stop(true, false).fadeIn(200, function () {
            $(this).delay(800).fadeOut(200);
        });

        $('.smartcat-testimonials-sortable li').each(function () {

            var data = {
                action: 'smartcat_testimonials_update_order',
                id: $(this).attr('id'),
                sc_member_order: $(this).attr('sc_member_order')
            };

            jQuery.post('admin-ajax.php', data, function (response) {

                // whatever you need to do; maybe nothing
                $('.smartcat_testimonial_update_status .smartcat_testimonial_updating').hide();
                $('.smartcat_testimonial_update_status .smartcat_testimonial_saved').stop(true, false).fadeIn(200, function () {
                    $(this).delay(1000).fadeOut(200);
                });
                $('#set_order').removeAttr('disabled');


            });
        });
    });
});
