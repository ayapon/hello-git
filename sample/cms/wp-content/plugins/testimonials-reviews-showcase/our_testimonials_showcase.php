<?php
/*
  Plugin Name: Our Testimonials Showcase
  Plugin URI: https://smartcatdesign.net/downloads/testimonials-reviews-showcase/
  Description: The best way to display your client testimonials & reviews. Multiple templates, star ratings, re-orderable
  Version: 1.2
  Author: SmartCat
  Author URI: http://smartcatdesign.net
  License: GPL v2
  Text Domain: smartcat-testimonials
 * 
 * @author          Bilal Hassan <bilal@smartcat.ca>
 * @copyright       Smartcat Design <http://smartcatdesign.net>
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    die;
}
if (!defined('SMARTCAT_TESTIMONIALS_PATH'))
    define('SMARTCAT_TESTIMONIALS_PATH', plugin_dir_path(__FILE__));
if (!defined('SMARTCAT_TESTIMONIALS_URL'))
    define('SMARTCAT_TESTIMONIALS_URL', plugin_dir_url(__FILE__));

require_once ( plugin_dir_path( __FILE__ ) . 'inc/class/class.smartcat-testimonials.php' );

// activation and de-activation hooks
register_activation_hook( __FILE__, array( 'SmartcatTestimonialsPlugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SmartcatTestimonialsPlugin', 'deactivate' ) );

SmartcatTestimonialsPlugin::instance();


