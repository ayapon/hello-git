<?php
/*
Plugin Name: Announcer
Plugin URI: http://www.aakashweb.com
Description: Display multiple Announcements, Welcome notes, Greetings, Events, News etc with attractive style, colors, themes and position it anywhere in the page. <a href="http://www.youtube.com/watch?v=WTLHuoY0mM0" title="Announcer demo video" target="_blank">Check out the demo video</a>.
Author: Aakash Chakravarthy
Version: 3.0
Author URI: http://www.aakashweb.com/
*/

define('ANCR_VERSION', '3.0');
define('ANCR_AUTHOR', 'Aakash Chakravarthy');
define('ANCR_URL', plugins_url('',__FILE__) );
define('ANCR_ADMIN', admin_url( 'options-general.php?page=announcer' ) );

$ancr_donate_link = 'http://bit.ly/ancrDonate';


// Load languages
load_plugin_textdomain('announcer', false, basename(dirname(__FILE__)) . '/languages/');


// Admin menu
function ancr_addpage() {
    $page_id = add_options_page('Announcer', 'Announcer', 'manage_options', 'announcer', 'ancr_admin_page');
	
	add_action( 'load-' . $page_id, 'ancr_admin_load' );
}
add_action('admin_menu', 'ancr_addpage');




// Load the Javascripts
function ancr_admin_js() {
	if (isset($_GET['page']) && $_GET['page'] == 'announcer'){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script( 'announcer-admin-js', ANCR_URL . '/admin/ui-js.js') ;
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}
}
add_action('admin_print_scripts', 'ancr_admin_js');



// Load the CSS
function ancr_admin_css() {
	if (isset($_GET['page']) && $_GET['page'] == 'announcer') {
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_style('announcer-admin-css', ANCR_URL . '/admin/ui-css.css');
		wp_enqueue_style('sticky_post-admin-ui-css','https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/flick/jquery-ui.css',false,"1.9.0",false);
	}
}
add_action('admin_print_styles', 'ancr_admin_css');




// Insert the required javascripts and styles to the theme
function ancr_public_scripts(){
	// jQuery
	wp_enqueue_script('jquery');
	
	// Announcer JS and CSS
	wp_register_script('announcer-js', ANCR_URL . '/public/announcer-js.js');
	wp_enqueue_script('announcer-js');
	wp_register_style('announcer-css', ANCR_URL . '/public/announcer-styles.css');
	wp_enqueue_style('announcer-css');
}
add_action( 'wp_enqueue_scripts', 'ancr_public_scripts' );



/**
 *
 * Basic functions for the plugin
 *
 **/


// Check the date
function ancr_date_check( $opts ){
  
  $s = strtotime( $opts['fromdate'] );
  $e = strtotime( $opts['todate'] );
  $c = strtotime( date('Y-n-d') );

  return (($c >= $s) && ($c <= $e));
}



function announcer( $id ){
	
	static $note = array();
	
	$options = get_option( 'announcer_data' );
	$id = intval( $id );
	
	if( $id <= 0 )
		return;
	
	$opts = $options[ $id ];
	
	if( !isset( $opts ) || empty( $opts ) )
		return;
		
	$loc = ancr_location_check( $opts[ 'location' ] );
	$date = ancr_date_check( $opts );
	$pos = $opts['position'];
	$filter = current_filter();
	$posflag = 0;
	
	if( $pos == 'above-content' && $filter == 'loop_start' && is_main_query() ){
		$posflag = 1;
	}elseif( ( $pos != 'above-content' && $pos != 'manual') && $filter == 'wp_footer' ){
		$posflag = 1;
	}elseif( $filter == '' ){
		$posflag = 1;
	}
	
	if( isset( $note[ $id ] ) && $note[ $id ] == 1 )
		$added = 1;
	else
		$added = 0;
	
	if( $opts['status'] == 'yes' && $loc && $added == 0 && $posflag && $date ){
	
		// close button
		if ($opts['closebt'] == 'yes'){
			$closebt = "\n" . '<span class="announcer-closebt">x</span>' . "\n";
		}else{
			$closebt = '';
		}
		
		// box style
		if ($opts['style'] == 'custom'){
			$class = $opts['customclass'];
		}else{
			$class = 'announcer-' . $opts['style'];
			
			$brclr = $opts['borderclr'] ? "border-color:" . $opts['borderclr'] . ';' : '';
			$bgclr = $opts['bgclr'] ? "background-color:" . $opts['bgclr'] . ';' : '';
			$txclr = $opts['textclr'] ? "color:" . $opts['textclr'] . ';' : '';
			
			$style = ' style="' . $brclr . $bgclr . $txclr . '"';
		}
		
		$class =  'announcer-' . $opts['position'] . ' ' . $class;
		
		if (strpos($opts['position'], 'top') !== false) {
			$class .= ' announcer-topbar';
		}elseif (strpos($opts['position'], 'bottom') !== false) {
			$class .= ' announcer-bottombar';
		}
		
		// Effect
		$effdur = (int) $opts['effectduration'] * 1000;
		$effect = ' data-effect="' . $opts['effect'] . '" data-effdur="' . $effdur . '"';
		
		// Position
		$position = ' data-pos="' . $opts['position'] . '"';
		
		// Main output
		echo "<!-- Start Announcement - Announcer plugin -->\n";
		echo '<div class="announcer ' . $class . '"' . $style . $effect . $position . ' data-id="' . $id . '">' . $closebt . '<div class="announcer-content">' . $opts['content'] . '</div>';
		echo "</div>\n";
		echo "<!-- End Announcement - Announcer plugin -->\n";

		// Note that it has been diplayed
		$note[ $id ] = 1;
		
	}
	
}



// Print all announcements
function ancr_hook(){
	
	$options = get_option( 'announcer_data' );
		
	if( !is_array( $options ) )
		return false;
	
	foreach( $options as $key => $val ){
		echo announcer( $key );	
	}
	
}
add_action('wp_footer', 'ancr_hook');
add_action('loop_start', 'ancr_hook');



function ancr_location_check( $loc ){
	
	if( !is_array( $loc ) ){
		return true; // no rule, so display site wide
	}
	
	$orflag = 0;
	
	foreach( $loc as $or ){
	
		$andflag = 1;
		
		foreach( $or as $rle ){
			$ispage = ancr_loc_is_page( $rle['page'], $rle['value'], $rle['operator'] );
			
			if( $ispage && $andflag )
				$andflag = 1;
			else
				$andflag = 0;
		}
		
		if( $andflag || $orflag )
			$orflag = 1; // can display;
		else
			$orflag = 0; // cannot display;
		
	}
	
	return $orflag;
	
}




function ancr_loc_is_page( $page, $val, $operator ){

	if( !empty( $val ) )
		$vale = explode( ',', $val );
	else
		$vale = '';
	
	switch( $page ){
		case 'single': $o =  is_single( $vale ); break;
		case 'page': $o = is_page( $vale ); break;
		case 'home': $o = is_home(); break;
		case 'front-page': $o = is_front_page(); break;
		case 'sticky': $o = is_sticky(); break;
		case 'post-type': $o = ( get_post_type() == $val ); break;
		case 'page-template': $o = is_page_template( $val ); break;
		case 'category': $o = is_category( $vale ); break;
		case 'tag': $o = is_tag( $vale ); break;
		case '404': $o = is_404(); break;
		case 'has-category': $o = has_category( $vale ); break;
		case 'has-tag': $o = has_tag( $vale ); break;
	}
	
	if( $operator == 'equal' ) 
		return $o;
	else
		return !$o;
		
}



// Announcer save options function
function ancr_save_options(){
	 if ( check_admin_referer( 'ancr_submit_form' ) ) {
		
		$post = array_map( 'stripslashes_deep', $_POST );
		$options = get_option( 'announcer_data' );
		
		
		if( empty( $options ) )
			$options = array('');
		
		
		// Update old announcement
		if( !empty( $post[ 'ancr_id' ] ) ){
			$ancr_id = intval( $post[ 'ancr_id' ] );
			$options[ $ancr_id ] = $post[ 'atts' ];
			$msg = 1;
		}else{
			array_push( $options, $post[ 'atts' ] );
			$ancr_id = max(array_keys($options));
			$msg = 2;
		}
		
		$options[ 'version' ] = ANCR_VERSION;
		
		update_option( 'announcer_data', $options );
		
		wp_redirect( ANCR_ADMIN . '&action=edit&message=' . $msg . '&id=' . $ancr_id ); exit;
		
		//return $ancr_id;
	}
}



// Announcer delete options
function ancr_delete_option(){
	
	if( wp_verify_nonce( $_GET['_wpnonce'], 'ancr-delete' ) ){
		$ancr_id = intval( $_GET[ 'id' ] );
		$options = get_option( 'announcer_data' );
		
		unset( $options[ $ancr_id ] );
		
		update_option( 'announcer_data', $options );
		
		wp_redirect( ANCR_ADMIN . '&message=3'); exit;
		
	}else{
		
	}
	
}



// Announcer admin on load
function ancr_admin_load(){
	
	if( $_POST && $_GET['action'] == 'edit' ){
	
		ancr_save_options();
		
	}elseif( !empty( $_GET['id'] ) && $_GET['action'] == 'delete' ){
		
		ancr_delete_option();
		
	}
}


// Announcer admin page.
function ancr_admin_page(){
	
	$params = array_merge( array(
		'id' => false,
		'action' => false,
		'message' => false
	), $_GET );
	
?>

<!-- Announcer admin page Start-->
<div class="wrap clearfix">

	<?php //ancr_share(); ?>

	<h2><img width="32" height="32" src="<?php echo ANCR_URL; ?>/admin/images/announcer.png" align="absmiddle"/>&nbsp;Announcer<sup class="smallText"> v<?php echo ANCR_VERSION; ?></sup></h2>
	
	<?php ancr_admin_message(); ?>
	
	<div class="cnt-wrap clearfix">
		<div class="cnt-left">
		
		<?php 
		
			if( $params[ 'action' ] == false ){
				include( 'admin/edit-list.php' );
			}elseif( $params[ 'action' ] == 'edit' ){
				include( 'admin/edit-single.php' );
			}else{
				include( 'admin/edit-list.php' );
			}
		?>
		
		</div>

<!--
		<div class="cnt-right">
			
			<div class="side-widget postbox">
				<h2>Donate !</h2>
				<p>If you like/found this plugin useful, consider making a small donation.</p>
				<p>This plugin covers various premium features and some donation will make me happy for the work.</p>
				
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="donations@aakashweb.com"  />
<input type="hidden" name="lc" value="US" />
<input type="hidden" name="item_name" value="Donation for Announcer plugin" />
<input type="hidden" name="button_subtype" value="services" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="rm" value="1" />
<input type="hidden" name="currency_code" value="USD" />
<input type="image" src="<?php echo ANCR_URL; ?>/admin/images/paypal.png" name="submit" alt="PayPal - The safer, easier way to pay online!" border="0" class="amtBtn" />

$&nbsp; <select id="amtSelect" name="amount" class="amtBox">
	<option value="10.00">10.00</option>
	<option value="20.00" selected="selected">20.00</option>
	<option value="30.00">30.00</option>
	<option value="40.00">40.00</option>
	<option value="50.00">50.00</option>
	<option value="60.00">60.00</option>
	<option value="">Custom amount</option>
</select>

</form>

			</div>
			
			<div class="side-widget postbox">
			<h2 class="clearfix"><img class="float-right" src="http://2.s3.envato.com/files/88315500/anpn-thumbnail-80.png" alt="Advanced Post Navigator"><a href="http://codecanyon.net/item/advanced-post-navigator/3700354?ref=vaakash" target="_blank">Advanced Post Navigator<br/><span class="smallText">for WordPress</span></a></h2>
			</div>
			
			<div class="side-widget postbox">
			<h2>Translations Needed</h2>
			<p>If you think of localizing the plugin in your language, you are welcomed ! Please get the ".po" file under "announcer/languages" folder, write, zip and send the translation to <u><a href="http://www.aakashweb.com/contact/">me</a></u></p>
			</div>
			
		</div>

	</div>
	<p class="clearfix"><em class="gray">Plugin by <a href="http://www.aakashweb.com" target="_blank">Aakash Chakravarthy</a> from <a href="http://www.aakashweb.com" target="_blank">Aakash Web</a> - <a href="http://www.aakashweb.com/forum" target="_blank">Support Forum</a> - <a href="http://www.aakashweb.com/wordpress-plugins/announcer/" target="_blank">Discuss this plugin</a></em></p>
	-->
</div><!-- wrap -->

<?php 
}


function ancr_upgrade(){
	$opts = get_option( 'announcer_data' );
	
	if( $opts['version'] == '' ){
		
		$clrs = explode( ',', $opts['announcer_stylecolors'] );
		
		$o[1] = array(
			'name' => " __( 'No name', 'announcer') ",
			'content' => $opts['announcer_content'],
			'status' => $opts['announcer_status'],
			'fromdate' => '2014-01-01',
			'todate' => $opts['announcer_expdate'],
			'closebt' => $opts['announcer_closebt'],
			'position' => 'top-float',
			'location' => '',
			'style' => $opts['announcer_style'],
			'customclass' => $opts['announcer_customclass'],
			'borderclr' => $clrs[0],
			'bgclr' => $clrs[1],
			'textclr' => $clrs[2],
			'addclass' => '',
			'effect' => $opts['announcer_effect'],
			'effectduration' => $opts['announcer_effdur'],
		);
		
		$o['version'] = ANCR_VERSION;
		
		update_option( 'announcer_data', $o );
		
	}
	
}
add_action( 'plugins_loaded', 'ancr_upgrade' );


function ancr_admin_message(){

	if( !isset( $_GET['message'] ) ) return;

	$mid = intval($_GET['message']);
	
	switch( $mid ){
		case 1:
		$msg = __( 'Announcement updated successfully !!', 'announcer' );
		break;
		
		case 2:
		$msg = __( 'Announcement created successfully', 'announcer' );
		break;
		
		case 3:
		$msg = __( 'Announcement deleted successfully', 'announcer' );
		break;
	}
	
	if( isset( $msg ) )
		echo '<div class="updated below-h2"><p>' . $msg . '</p></div>';
	
}

function ancr_default_data(){
	
	return array(
		'name' => '',
		'content' => '',
		'status' => 'yes',
		'fromdate' => '2014-01-01',
		'todate' => '2015-01-01',
		'closebt' => 'yes',
		'position' => 'top-float',
		'location' => '',
		'style' => 'style1',
		'customclass' => '',
		'borderclr' => '',
		'bgclr' => '',
		'textclr' => '',
		'addclass' => '',
		'effect' => 'fade',
		'effectduration' => 1,
	);
	
}

// function ancr_share(){
// 	echo '
// <div class="shareBtn"><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.aakashweb.com%2Fwordpress-plugins%2Fannouncer%2F&amp;width=200&amp;layout=button_count&amp;action=recommend&amp;show_faces=false&amp;share=true&amp;height=21&amp;appId=106994469342299" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe>
// 	<div class="shareExp addthis_toolbox addthis_default_style addthis_16x16_style" addthis:url="http://www.aakashweb.com/wordpress-plugins/announcer/" addthis:title="Announcer - The awesome and best Notification + Announcement plugin for #WordPress">
// 	<h3>Share</h3>
// 	<p class="clearfix"><a class="addthis_button_tweet" tw:count="vertical"></a><a class="addthis_button_google_plusone" g:plusone:size="tall"></a><a class="addthis_counter"></a></p>
// <h3>Follow</h3><p><a class="addthis_button_twitter_follow_native" tw:screen_name="vaakash"></a><br/><br/>
// <a class="addthis_button_facebook_like" fb:like:layout="button_count" addthis:url="http://www.facebook.com/aakashweb"></a></p>
// ​<script type="text/javascript">var addthis_config = addthis_config||{};addthis_config.data_track_addressbar = false;addthis_config.data_track_clickback = false;</script>
// 	</div>
// 	</div>
// 	';
// }
?>