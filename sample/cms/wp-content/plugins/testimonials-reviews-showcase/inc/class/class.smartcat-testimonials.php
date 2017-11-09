<?php

function smartcat_testimonials_update_order() {
    $post_id = $_POST['id'];
    $sc_member_order = $_POST['sc_member_order'];
    update_post_meta($post_id, 'sc_member_order', $sc_member_order);
    exit();
}

add_action('wp_ajax_smartcat_testimonials_update_order', 'smartcat_testimonials_update_order');

class SmartcatTestimonialsPlugin {

    const VERSION = '1.1';
    const NAME = 'Testimonials & Reviews Showcase';

    private static $instance;
    private $options;

    public static function instance() {
        if (!self::$instance) :
            self::$instance = new self;
            self::$instance->get_options();
            self::$instance->add_hooks();
        endif;
    }

    public static function activate() {

        $options = array(
            'template'              =>  'centered_list',
            'show_date'             =>  'no',
            'use_images'            =>  'yes',
            'image_style'           =>  'circle',
            'image_size'            =>  'default',
            'image_greysacle'       =>  '',
            'use_ratings'           =>  'yes',
            'main_color'            =>  '333333',
            'italic_content'        =>  'italic',
            'secondary_color'       =>  'EB9D02',
            'ratings_icon'          =>  'star',
            'word_count'            =>  30,
            'member_count'          =>  -1,
        );

        if ( !get_option('smartcat_testimonials_options' ) ) {
            add_option('smartcat_testimonials_options', $options);
            $options['redirect'] = true;
            update_option('smartcat_testimonials_options', $options);
        }
    }

    public static function deactivate() {
        
//        delete_option('smartcat_testimonials_options');
        
    }

    private function add_hooks() {
        
        add_action('init', array($this, 'testimonials'));
        add_action('init', array($this, 'localize'));
        add_action('init', array($this, 'testimonial_positions'), 0);
        add_action('admin_init', array($this, 'smartcat_testimonials_activation_redirect'));
        add_action('admin_menu', array($this, 'smartcat_testimonials_menu'));
        add_action('admin_enqueue_scripts', array($this, 'smartcat_testimonials_load_admin_styles_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'smartcat_testimonials_load_styles_scripts'));
        add_shortcode('our-testimonials', array($this, 'display_our_testimonials'));
        add_action('add_meta_boxes', array($this, 'smartcat_testimonial_info_box'));
        add_action('save_post', array($this, 'testimonial_box_save'));
        add_action('wp_ajax_smartcat_testimonials_update_pm', array($this, 'smartcat_testimonials_update_order'));
        add_action('wp_head', array($this, 'smartcat_testimonials_custom_styles'));

        
    }

    private function get_options() {
        if (get_option('smartcat_testimonials_options')) :
            $this->options = get_option('smartcat_testimonials_options');
        endif;
    }

    /**
     * @todo check if redirect option is set and redirect
     */
    public function smartcat_testimonials_activation_redirect() {
        if ($this->options['redirect']) :
            $old_val = $this->options;
            $old_val['redirect'] = false;
            update_option('smartcat_testimonials_options', $old_val);
            wp_safe_redirect(admin_url('edit.php?post_type=testimonial&page=smartcat_testimonials_settings'));
        endif;
    }

    public function localize() {
        load_plugin_textdomain('smartcat-testimonials', false, dirname(plugin_basename(__FILE__)));
    }

    public function smartcat_testimonials_menu() {

        add_submenu_page('edit.php?post_type=testimonial', 'Settings', 'Settings', 'administrator', 'smartcat_testimonials_settings', array($this, 'smartcat_testimonials_settings'));
        add_submenu_page('edit.php?post_type=testimonial', 'Re-Order Testimonials', 'Re-Order Testimonials', 'administrator', 'smartcat_testimonials_reorder', array($this, 'smartcat_testimonials_reorder'));
//        add_submenu_page('edit.php?post_type=testimonial', 'Pending Testimonials', 'Pending Testimonials', 'administrator', 'smartcat_testimonials_pending', array($this, 'smartcat_testimonials_pending'));
        add_submenu_page('edit.php?post_type=testimonial', 'Documentation', 'Documentation', 'administrator', 'smartcat_testimonials_documentation', array($this, 'smartcat_testimonials_documentation'));
    }



    public function smartcat_testimonials_documentation() {
        include_once SMARTCAT_TESTIMONIALS_PATH . 'admin/documentation.php';
    }

    public function smartcat_testimonials_reorder() {
        include_once SMARTCAT_TESTIMONIALS_PATH . 'admin/reorder.php';
    }

    public function smartcat_testimonials_settings() {

        if (isset($_REQUEST['smartcat_testimonials_save']) && $_REQUEST['smartcat_testimonials_save'] == 'Update') :
            update_option('smartcat_testimonials_options', $_REQUEST['smartcat_testimonials_options']);
        endif;

        include_once SMARTCAT_TESTIMONIALS_PATH . 'admin/options.php';
    }

    public function smartcat_testimonials_load_admin_styles_scripts($hook) {
        wp_enqueue_style('smartcat_testimonials_admin_style', SMARTCAT_TESTIMONIALS_URL . 'inc/style/smartcat_testimonials_admin.css');
//        wp_enqueue_style('smartcat_testimonials_fontawesome', SMARTCAT_TESTIMONIALS_URL . 'inc/style/font-awesome.min.css', false, self::VERSION);
        
        wp_enqueue_script('jquery-ui-sortable');
        
        wp_enqueue_script('smartcat_testimonials_color_script', SMARTCAT_TESTIMONIALS_URL . 'inc/script/jscolor/jscolor.js', array('jquery'));
        wp_enqueue_script('smartcat_testimonials_script', SMARTCAT_TESTIMONIALS_URL . 'inc/script/smartcat_testimonials_admin.js', array('jquery'));
    }

    function smartcat_testimonials_load_styles_scripts() {

        // plugin main style
        wp_enqueue_style('smartcat_testimonials_default_style', SMARTCAT_TESTIMONIALS_URL . 'inc/style/smartcat_testimonials.css', false, self::VERSION);
        wp_enqueue_style('smartcat_testimonials_icons', SMARTCAT_TESTIMONIALS_URL . 'inc/style/icons.css', false, self::VERSION);
        wp_enqueue_style('smartcat_testimonials_animate', SMARTCAT_TESTIMONIALS_URL . 'inc/style/animate.min.css', false, self::VERSION);

        // carousel
        wp_enqueue_script('smartcat_testimonials_carousel', SMARTCAT_TESTIMONIALS_URL . 'inc/script/owl.carousel.min.js', array('jquery'), self::VERSION);
        
        // plugin main script
        wp_enqueue_script('smartcat_testimonials_default_script', SMARTCAT_TESTIMONIALS_URL . 'inc/script/smartcat_testimonials.js', array('jquery'), self::VERSION);
    }
    

    function display_our_testimonials( $atts ) {
        extract(shortcode_atts( array(
            'group'     => '',
            'template'  => '',
            'limit'     => '',
            'images'    => '',
            'italic'    => '',
            'ratings'   => '',
            'date'      => '',
            ), $atts)
        );
        global $content;

        ob_start();

        if ($template == '') :
            if ($this->options['template'] === false or $this->options['template'] == '') :
                include SMARTCAT_TESTIMONIALS_PATH . 'inc/template/slider.php';
            else :
                include SMARTCAT_TESTIMONIALS_PATH . 'inc/template/' . $this->options['template'] . '.php';
            endif;
        else :
            include SMARTCAT_TESTIMONIALS_PATH . 'inc/template/' . $template . '.php';
        endif;

        $output = ob_get_clean();
        
        return $output;
    }

    
    function testimonials() {
        $labels = array(
            'name' => _x('Testimonials', 'Post Type General Name', 'smartcat-testimonials'),
            'singular_name' => _x('Testimonial', 'Post Type Singular Name', 'smartcat-testimonials'),
            'menu_name' => __('Testimonials', 'smartcat-testimonials'),
            'name_admin_bar' => __('Testimonials', 'smartcat-testimonials'),
            'parent_item_colon' => __('', 'smartcat-testimonials'),
            'all_items' => __('All Testimonials', 'smartcat-testimonials'),
            'add_new_item' => __('Add New Testimonial', 'smartcat-testimonials'),
            'add_new' => __('Add New', 'smartcat-testimonials'),
            'new_item' => __('New Testimonial', 'smartcat-testimonials'),
            'edit_item' => __('Edit Testimonial', 'smartcat-testimonials'),
            'update_item' => __('Update Testimonial', 'smartcat-testimonials'),
            'view_item' => __('View Testimonial', 'smartcat-testimonials'),
            'search_items' => __('Search Testimonials', 'smartcat-testimonials'),
            'not_found' => __('No testimonials found', 'smartcat-testimonials'),
            'not_found_in_trash' => __('No testimonials found in trash', 'smartcat-testimonials'),
        );
        $args = array(
            'label' => __('testimonial', 'smartcat-testimonials'),
            'description' => __('Create and display your testimonials', 'smartcat-testimonials'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-format-quote',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'rewrite' => false,
            'capability_type' => 'page',
        );
        register_post_type('testimonial', $args);
        flush_rewrite_rules();
    }

    public function testimonial_positions() {
        $labels = array(
            'name' => _x('Groups', 'taxonomy general name'),
            'singular_name' => _x('Group', 'taxonomy singular name'),
            'search_items' => __('Search Groups', 'smartcat-testimonials'),
            'all_items' => __('All Groups', 'smartcat-testimonials'),
            'parent_item' => __('Parent Group', 'smartcat-testimonials'),
            'parent_item_colon' => __('Parent Group:', 'smartcat-testimonials'),
            'edit_item' => __('Edit Group', 'smartcat-testimonials'),
            'update_item' => __('Update Group', 'smartcat-testimonials'),
            'add_new_item' => __('Add New Group', 'smartcat-testimonials'),
            'new_item_name' => __('New Group', 'smartcat-testimonials'),
            'menu_name' => __('Groups', 'smartcat-testimonials'),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
        );
        register_taxonomy('testimonial_position', 'testimonial', $args);
    }

    public function smartcat_testimonial_info_box() {

        add_meta_box(
                'smartcat_testimonial_info_box', __( 'Testimonial Details', 'smartcat-testimonials' ), array( $this, 'smartcat_testimonial_info_box_content' ), 'testimonial', 'normal', 'high'
        );

    }


//    }

    public function smartcat_testimonial_info_box_content( $post ) {
        //nonce
        wp_nonce_field(plugin_basename(__FILE__), 'smartcat_testimonial_info_box_content_nonce');

        //social
?>
        <table class="testimonial-table">
            
            <tr>
                <th><strong><?php _e('Subtitle', 'smartcat-testimonials'); ?></strong></th>
                <td>
                    <input type="text" value="<?php echo esc_attr( get_post_meta($post->ID, 'testimonial_subtitle', true) ); ?>" id="testimonial_title" name="testimonial_subtitle"/><br>
                    <i><?php _e('The subtitle shows below the title of the testimonial. As an example, you can use it to show the title/position of the individual, or a company name.', 'smartcat-testimonials'); ?></i>
                </td>
            </tr>
            
            <tr>
                <th><strong><?php _e('Subtitle URL', 'smartcat-testimonials'); ?></strong></th>
                <td>
                    <input type="text" value="<?php echo esc_url( get_post_meta($post->ID, 'testimonial_subtitle_url', true) ); ?>" id="testimonial_subtitle_url" name="testimonial_subtitle_url"/><br>
                    <i><?php _e('Leave blank if you do not want a link', 'smartcat-testimonials'); ?></i>
                </td>
            </tr>
            
            <tr>
                <th><strong><?php _e('URL Target', 'smartcat-testimonials'); ?></strong></th>
                <td>
                    <select name="testimonial_subtitle_target">
                        <option value=""><?php _e('Same Page', 'smartcat-testimonials'); ?></option>
                        <option value="new" <?php selected( 'new', get_post_meta( $post->ID, 'testimonial_subtitle_target', true ) ); ?>><?php _e('New Page', 'smartcat-testimonials'); ?></option>
                    </select><br>
                    <i><?php _e('Leave blank if you do not want a link', 'smartcat-testimonials'); ?></i>
                </td>
            </tr>
            
            <tr>
                <th><strong><?php _e('Rating', 'smartcat-testimonials'); ?></strong></th>
                <td>
                    <select name="testimonial_rating">
                        
                        <option value="1" <?php selected( 1, get_post_meta( $post->ID, 'testimonial_rating', true ) ); ?>><?php _e('1 Star', 'smartcat-testimonials'); ?></option>
                        <option value="2" <?php selected( 2, get_post_meta( $post->ID, 'testimonial_rating', true ) ); ?>><?php _e('2 Stars', 'smartcat-testimonials'); ?></option>
                        <option value="3" <?php selected( 3, get_post_meta( $post->ID, 'testimonial_rating', true ) ); ?>><?php _e('3 Stars', 'smartcat-testimonials'); ?></option>
                        <option value="4" <?php selected( 4, get_post_meta( $post->ID, 'testimonial_rating', true ) ); ?>><?php _e('4 Stars', 'smartcat-testimonials'); ?></option>
                        <option value="5" <?php selected( 5, get_post_meta( $post->ID, 'testimonial_rating', true ) ); ?>><?php _e('5 Stars', 'smartcat-testimonials'); ?></option>
                        
                    </select><br>
                    
                </td>
            </tr>
            
            
        
        </table>

        
  <?php      

    }

    public function testimonial_box_save($post_id) {

        $slug = 'testimonial';

        if (isset($_POST['post_type'])) {
            if ($slug != $_POST['post_type']) {
                return;
            }
        }
        

        // get var values
        if (get_post_meta($post_id, 'sc_member_order', true) == '' || get_post_meta($post_id, 'sc_member_order', true) === FALSE)
            update_post_meta($post_id, 'sc_member_order', 0);

        
        if (isset($_REQUEST['testimonial_subtitle'])) {
            update_post_meta($post_id, 'testimonial_subtitle', $_POST['testimonial_subtitle']);
        }
        
        if (isset($_REQUEST['testimonial_subtitle_url'])) {
            update_post_meta($post_id, 'testimonial_subtitle_url', $_POST['testimonial_subtitle_url']);
        }
        
        if (isset($_REQUEST['testimonial_subtitle_target'])) {
            update_post_meta($post_id, 'testimonial_subtitle_target', $_POST['testimonial_subtitle_target']);
        }
        
        if (isset($_REQUEST['testimonial_rating'])) {
            update_post_meta($post_id, 'testimonial_rating', $_POST['testimonial_rating']);
        }
        
        
        
        
        
    }


    public function wpb_load_widget() {
        register_widget('smartcat_testimonials_widget');
    }

    public function posts_columns($defaults) {
        $defaults['riv_post_thumbs'] = __('Profile Picture');
        return $defaults;
    }

    public function posts_custom_columns($column_name, $id) {
        if ($column_name === 'riv_post_thumbs') {
            echo the_post_thumbnail('thumbnail');
        }
    }

    public function smartcat_testimonials_update_order() {
        $post_id = $_POST['id'];
        $sc_member_order = $_POST['sc_member_order'];
        update_post_meta($post_id, 'sc_member_order', $sc_member_order);
    }

    public function smartcat_testimonials_custom_styles() {
        ?>
        <style>
            #smartcat-testimonials .smartcat_testimonial_title,
            #smartcat-testimonials .smartcat_testimonial_title a{ color: #<?php echo $this->options['main_color']; ?> }
            #smartcat-testimonials .smartcat_testimonial_rating .testimonial-rating{ color: #<?php echo $this->options['secondary_color']; ?> }
            #smartcat-testimonials.smartcat_boxed_template{ border: 1px solid #<?php echo $this->options['main_color']; ?>; }
        </style>
        <script type="text/javascript">
        
        </script>
        <?php
    }


    public function sc_get_args( $group, $limit ) {
        $args = array(
            'post_type' => 'testimonial',
            'meta_key' => 'sc_member_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'testimonial_position' => $group,
            'posts_per_page' => $limit ? $limit : $this->options['member_count'],
        );
        return $args;
    }

    
    public function smartcat_testimonials_href_this( $content, $url, $target ) { ?>
        
        <a href="<?php echo esc_url( $url ); ?>" <?php echo $target ? 'target="_BLANK"' : ''; ?>>
            <?php echo $content; ?>
        </a>
        
        
    <?php }
    
    public function display_editor() {
        
        
        $args = array (
            
            'media_buttons'     =>  FALSE,
            
        );
        
        return wp_editor('', 'smartcat_testimonial_content', $args );
        
    }
    
    public function get_ratings( $ratings, $id ) {
        
        if( $ratings == 'yes' || ( empty( $ratings ) && $this->options['use_ratings'] == 'yes' ) ) : ?>
            
            <div itemprop="rating" class="smartcat_testimonial_rating">
                <?php $rating = (int)get_post_meta( $id, 'testimonial_rating', TRUE ); ?>
                <?php $unrating = 5 - $rating; ?>

                <?php for( $i = 0; $i < $rating; $i++ ) : ?>
                    <span class="testimonial-rating icon-<?php echo $this->options['ratings_icon']; ?>"></span>
                <?php endfor; ?>
                <?php for( $i = 0; $i < $unrating; $i++ ) : ?>
                    <span class="testimonial-rating icon-<?php echo $this->options['ratings_icon']; ?>-o"></span>
                <?php endfor; ?>
            </div>            
            
        <?php endif;
        
    }


}
