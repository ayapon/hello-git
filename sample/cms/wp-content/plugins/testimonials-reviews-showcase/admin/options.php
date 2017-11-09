<?php
include_once 'setting.php';
extract(get_option('smartcat_testimonials_options'));
?>

<p>To display the Testimonials, copy and paste this shortcode into the page or widget where you want to show it. 
        <input type="text" readonly="readonly" value="[our-testimonials]" style="width: 200px" />
    </p>
    <p><iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FSmartcatDesign&amp;width&amp;layout=standard&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=35&amp;appId=233286813420319" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:35px;" allowTransparency="true"></iframe></p>


<div class="width70 left">
    
    <form name="smartcat_testimonials_post_form" method="post" action="" enctype="multipart/form-data">
        <table class="widefat">
            <thead>
                <tr>
                    <th colspan="2"><b><?php _e('Testimonials Appearance', 'smartcat-testimonials'); ?></b></th>
                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <td><?php _e('Template', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[template]" id="smartcat_testimonials_template">
                            <option><?php _e('Select Template', 'smartcat-testimonials'); ?></option>

                            <option value="centered_list" <?php echo 'centered_list' == esc_attr($template) ? 'selected=selected' : ''; ?>><?php _e('List - Centered', 'smartcat-testimonials'); ?></option>                            
                            <option value="stacked_list" <?php echo 'stacked_list' == esc_attr($template) ? 'selected=selected' : ''; ?>><?php _e('List - Stacked', 'smartcat-testimonials'); ?></option>                            
                            <option value="conversation_list" <?php echo 'conversation_list' == esc_attr($template) ? 'selected=selected' : ''; ?>><?php _e('List - Bubbles', 'smartcat-testimonials'); ?></option>                            
                            <option disabled="disabled"><?php _e('Grid - (pro version)', 'smartcat-testimonials'); ?></option>                            
                            <option disabled="disabled"><?php _e('Slider - Centered (pro version)', 'smartcat-testimonials'); ?></option>                      
                            <option disabled="disabled"><?php _e('Slider - Left Aligned (pro version)', 'smartcat-testimonials'); ?></option>
                            <option disabled="disabled"><?php _e('Slider - Bubbles (pro version)', 'smartcat-testimonials'); ?></option>
                            

                        </select>
                    </td>
                </tr>                

                
                <tr id="show-date-row">
                    <td><?php _e('Display the date ?', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[show_date]">
                            <option value="yes" <?php selected( 'yes', $show_date ); ?>>Yes</option>
                            <option value="no" <?php selected( 'no', $show_date ); ?>>No</option>
                        </select>
                    </td>
                </tr>    
                
                <tr id="use-images-row">
                    <td><?php _e('Display Testimonial Featured Images ?', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[use_images]">
                            <option value="yes" <?php selected('yes', $use_images); ?>>Yes</option>
                            <option value="no" <?php selected('no', $use_images); ?>>No</option>
                        </select>
                    </td>
                </tr>    

                <tr id="image-style-row">
                    <td><?php _e('Image Style', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[image_style]">
                            <option value="circle" <?php selected('circle', $image_style); ?>><?php _e('Circle', 'smartcat-testimonials'); ?></option>
                            <option value="block" <?php selected('block', $image_style); ?>><?php _e('Block', 'smartcat-testimonials'); ?></option>
                            <option value="softedge" <?php selected('softedge', $image_style); ?>><?php _e('Soft Edges', 'smartcat-testimonials'); ?></option>
                        </select>
                    </td>
                </tr>    


                <tr id="image-size-row">
                    <td><?php _e('Image Size', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[image_size]">
                            <option value="smaller" <?php selected('smaller', $image_size); ?>><?php _e('Smaller', 'smartcat-testimonials'); ?></option>
                            <option value="default" <?php selected('default', $image_size); ?>><?php _e('Default', 'smartcat-testimonials'); ?></option>
                            <option value="larger" <?php selected('larger', $image_size); ?>><?php _e('Larger', 'smartcat-testimonials'); ?></option>
                        </select>
                    </td>
                </tr>    

                <tr id="image-greyscale-row">
                    <td><?php _e('Grayscale Effect', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[image_greysacle]">
                            <option value="grayscale" <?php selected('yes', $image_greysacle); ?>><?php _e('Yes', 'smartcat-testimonials'); ?></option>
                            <option value="" <?php selected('no', $image_greysacle); ?>><?php _e('No', 'smartcat-testimonials'); ?></option>
                        </select><br>
                        <em><?php _e('Grayscale effect turns the default image color to black&white, and reverts to the original image on hover', 'smartcat-notifications'); ?></em>
                    </td>
                </tr>    

                <tr id="italic-content-row">
                    <td><?php _e('Italic Content', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[italic_content]">
                            <option value="italic" <?php selected('italic', $italic_content); ?>><?php _e('Yes', 'smartcat-testimonials'); ?></option>
                            <option value="" <?php selected('no', $italic_content); ?>><?php _e('No', 'smartcat-testimonials'); ?></option>
                        </select><br>
                        <em><?php _e('Display the testimonial content in italic', 'smartcat-notifications'); ?></em>
                    </td>
                </tr>    

                <tr>
                    <td><?php _e('Main Color', 'smartcat-testimonials'); ?></td>
                    <td>
                        <input class="wp_popup_color width25" type="text" value="<?php echo esc_attr($main_color); ?>" name="smartcat_testimonials_options[main_color]"/>
                    </td>
                </tr>                

                <tr id="use-ratings-row">
                    <td><?php _e('Display Ratings ?', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[use_ratings]">
                            <option value="yes" <?php selected('yes', $use_ratings); ?>><?php _e('Yes', 'smartcat-testimonials'); ?></option>
                            <option value="no" <?php selected('no', $use_ratings); ?>><?php _e('No', 'smartcat-testimonials'); ?></option>
                        </select>
                    </td>
                </tr>                  

                <tr id="ratings-icon-row">
                    <td><?php _e('Rating Icon', 'smartcat-testimonials'); ?></td>
                    <td>
                        <select name="smartcat_testimonials_options[ratings_icon]">
                            <option value="star" <?php selected('star', $ratings_icon); ?>><?php _e('Star', 'smartcat-testimonials'); ?></option>
                            <option value="heart" <?php selected('heart', $ratings_icon); ?>><?php _e('Heart', 'smartcat-testimonials'); ?></option>
                        </select>
                    </td>
                </tr>                  

                <tr>
                    <td><?php _e('Star/Rating Color', 'smartcat-testimonials'); ?></td>
                    <td>
                        <input class="wp_popup_color width25" type="text" value="<?php echo esc_attr($secondary_color); ?>" name="smartcat_testimonials_options[secondary_color]"/>
                    </td>
                </tr>                


                <tr id="height-row">
                    <td><?php echo _e('Max Word Count', 'smartcat-testimonials'); ?></td>
                    <td>
                        <input type="text" name="smartcat_testimonials_options[word_count]" value="<?php echo esc_attr($word_count); ?>" class="width25"/><br>
                    </td>
                </tr>   


                <tr>
                    <td><?php _e('Number of Testimonials to display', 'smartcat-testimonials'); ?></td>
                    <td>
                        <input type="text" value="<?php echo esc_attr($member_count); ?>" name="smartcat_testimonials_options[member_count]" placeholder="number of members to show"/><br>
                        <em><?php _e('Set to -1 to display all testimonials', 'smartcat-testimonials'); ?></em>
                    </td>
                </tr>




            </tbody>
        </table>


        <table class="widefat">
            <thead>
                <tr>
                    <th colspan="2"><b><?php _e('Frontend Submission Form Settings', 'smartcat-testimonials'); ?><em class="proversion"> Pro Version</em></b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2"><?php _e( 'Use the shortcode [our-testimonials-form] to generate a submission form allowing your site visitors to leave you testimonials and reviews', 'smartcat-testimonials'); ?></td>
                </tr>
                <tr>
                    <td>
                        <?php _e('Prevent Multiple Submissions', 'smartcat-testimonials') ?>
                    </td>
                    <td>
                        <input type="checkbox" value="1" disabled="disabled" checked="checked"/> 
                        <em><?php _e('If checked, users will be limited to 1 review every 24 hrs', 'smartcat-testimonials') ?></em>
                    </td>                    
                </tr>
                <tr>
                    <td>
                        <?php _e('Auto-approve Reviews', 'smartcat-testimonials') ?>
                    </td>
                    <td>
                        <input type="checkbox" value="1" disabled="disabled" /> 
                        <em><?php _e('If checked, reviews will be automatically approved', 'smartcat-testimonials') ?></em>
                    </td>                    
                </tr>
                <tr>
                    <td>
                        <?php _e('Enable Human Verification', 'smartcat-testimonials') ?>
                    </td>
                    <td>
                        <input type="checkbox" value="1" disabled="disabled" checked="checked"/> 
                        <em><?php _e('If checked, a human verification box will be added', 'smartcat-testimonials') ?></em>
                    </td>                    
                </tr>
                <tr>
                    <td>
                        <?php _e('Enable Frontend Image/logo Upload', 'smartcat-testimonials') ?>
                    </td>
                    <td>
                        <input type="checkbox" value="1" disabled="disabled" checked="checked"/> 
                        <em><?php _e('If checked, the form will include an optional upload button', 'smartcat-testimonials') ?></em>
                    </td>                    
                </tr>


            </tbody>        
        </table>        



        <table class="widefat">
            <thead>
                <tr>
                    <th colspan="2"><b><?php _e('Pending Testimonial/Review Notifications', 'smartcat-testimonials'); ?><em class="proversion"> Pro Version</em></b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2"><?php _e( 'You can set up the plugin to notify the site Admin whenever a review is submitted.', 'smartcat-testimonials' ); ?></td>
                </tr>
                <tr>
                    <td>
                        <?php _e('Send Notifications to Admin ?', 'smartcat-testimonials') ?>
                    </td>
                    <td>
                        <input type="checkbox" value="1" disabled="disabled" checked="checked"/>
                        <em><?php _e('Check this box and enter an email address where you want notifications about new review submissions to be sent', 'smartcat-testimonials') ?></em>
                    </td>                    
                </tr>
                <tr>
                    <td>
                        <?php _e('Email Address', 'smartcat-testimonials') ?>
                    </td>
                    <td>
                        <input type="text" value="<?php echo esc_attr($admin_email); ?>" disabled="disabled" />
                    </td>                    
                </tr>
                <tr>
                    <td>
                        <?php _e('Notification Email Subject', 'smartcat-testimonials') ?>
                    </td>
                    <td>
                        <input type="text" disabled="disabled" value="<?php _e( 'A new review has been submitted!', 'smartcat-testimonials' ); ?>"/>
                    </td>                    
                </tr>

            </tbody>        
        </table>



        <input type="hidden" name="smartcat_testimonials_options[redirect]" value=""/>
        <div style="text-align: right">
            <input type="submit" name="smartcat_testimonials_save" value="Update" class="button button-primary" />
        </div>

    </form>

    <div class="clear"></div>
    <br>



</div>

<div class="width25 left">
    
    <table class="widefat">
        <thead>        
            <tr>
                <th><strong><?php _e( 'Quick Reference' ); ?></strong></th>
            </tr>
        </thead>
        <tr>
            <td>
                Add the shortcode <strong>[our-testimonials]</strong> will display the testimonials using the settings on this page
            </td>
        </tr>
        <tr>
            <td>You can categorize your testimonials, and display those from specific groups [our-testimonials group="Name of Group"]</td>
        </tr>
        <tr>
            <td>You can override the settings from the shortcode [our-testimonials group="Name of Group" template="grid" limit="5" images="yes" italic="yes" ratings="yes"]</td>
        </tr>
    </table>
    
    <table class="widefat">
        <thead>
            <tr>
                <th><strong><?php _e( 'Pro Version', 'smartcat-testimonials' ); ?></strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <ul>
                        <li>4 additional Templates</li>
                        <li>Front-end submission form</li>
                        <li>Automatic & manual approval of submissions</li>
                        <li>Human verification</li>
                        <li>Admin Notification</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="<?php echo SMARTCAT_TESTIMONIALS_URL . 'screenshot-6.jpg' ?>" style="width: 100%;" />
                </td>
            </tr>
            <tr>
                <td class="center">
                    <a class="button-secondary" href="https://smartcatdesign.net/our-testimonials-showcase-demo/" target="_BLANK">Demo</a>
                    <a class="button-primary" href="https://smartcatdesign.net/downloads/testimonials-reviews-showcase/" target="_BLANK">Go Pro </a>
                </td>
            </tr>
        </tbody>

    </table>
    
</div>