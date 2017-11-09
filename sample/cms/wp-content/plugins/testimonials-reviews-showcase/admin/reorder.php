<?php include_once 'setting.php'; ?>

<div class="width70 left">
    <table class="widefat">
        <thead>
            <tr>
                <th><b>Drag & Drop the member's pictures to sort them in the order you want them to appear</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <ul class="smartcat-testimonials-sortable grid" data-action="<?php echo SMARTCAT_TESTIMONIALS_PATH; ?>">
                        <?php
                        $args = array(
                            'post_type' => 'testimonial',
                            'meta_key' => 'sc_member_order',
                            'post_status' => 'publish',
                            'orderby' => 'meta_value_num',
                            'order' => 'ASC',
                            'posts_per_page' => -1,
                        );
                        $members = new WP_Query($args);
                        if ($members->have_posts()) {
                            while ($members->have_posts()) {
                                $members->the_post();
                                $id = get_the_ID();
                                $order = get_post_meta($id, 'sc_member_order', true);
                                if (has_post_thumbnail())
                                    $thumb_url = wp_get_attachment_url(get_post_thumbnail_id($id));
                                else
                                    $thumb_url = SMARTCAT_TESTIMONIALS_PATH . 'img/noprofile.jpg';
                                ?>
                                <li id="<?php echo $id; ?>" itemscope itemtype="http://schema.org/Person" class="sc_testimonial ui-state-default" data-order="<?php echo $order; ?>">
                                    <!--<div class="sc_testimonial_inner">-->
                                        
                                        <img src="<?php echo $thumb_url; ?>" />
                                        
                                        <div class="sc_testimonial_overlay">
                                            <?php the_title() ?>
                                        </div>
                                        
                                        <div itemprop="jobtitle" class="sc_testimonial_jobtitle">
                                            <?php echo get_post_meta($id, 'testimonial_subtitle', true); ?>
                                        </div>
                                        
                                        
                                        <div>
                                            <?php echo wp_trim_words( get_post_field('post_content', $id), 30 ); ?>
                                        </div>
                                        
                                        
                                    <!--</div>-->
                                </li>
                                <?php
                            }
                        } else {
                            _e('There are no reviews to display', 'smartcat-services');
                        }
                        ?>
                    </ul>
            </tr>
        </tbody>
    </table>
    <!--<input type="submit" name="wp_popup_reset" value="Reset" class="button button-primary" onclick="return confirm_reset();"/>-->
    <a class="button button-primary" id="set_order">Save Order</a>
    <p class="smartcat_testimonial_update_status">
        <span class="smartcat_testimonial_updating"><img src="<?php echo SMARTCAT_TESTIMONIALS_URL . 'inc/img/spinner.gif' ?>" class=""/> Saving</span>
        <span class="smartcat_testimonial_saved"><img src="<?php echo SMARTCAT_TESTIMONIALS_URL . 'inc/img/check.png' ?>" class=""/> Saved!</span>
    </p>
</div>
</div>
<script>
    function confirm_reset() {
        if (confirm("Are you sure you want to reset to defaults ?")) {
            return true;
        } else {
            return false;
        }
    }
    jQuery(document).ready(function($) {
        $("#sc_popup_shortcode").focusout(function() {
            var shortcode = jQuery(this).val();
            shortcode = shortcode.replace(/"/g, "").replace(/'/g, "");
            jQuery(this).val(shortcode);
        });

    });

</script>