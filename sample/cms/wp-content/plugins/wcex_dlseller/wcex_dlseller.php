<?php
/*
Plugin Name: WCEX DL Seller
Plugin URI: http://www.welcart.com/
Description: This plug-in is an extension plug-ins for sale download dedicated Welcart. Please use in conjunction with the Welcart e-Commerce.
Version: 3.0.0.1706161
Author: Collne Inc.
Author URI: http://www.collne.com/
Text Domain: dlseller
Domain Path: /languages/
*/

if ( !defined('USCES_EX_PLUGIN') )
	define('USCES_EX_PLUGIN', 1);

define('WCEX_DLSELLER', true);
define('WCEX_DLSELLER_VERSION', "3.0.0.1706161");

define('USCES_DB_CONTINUATION', '1.0');
define('USCES_DB_CONTINUATION_META', '1.0');
define('WCEX_DLSELLER_UP300', 1);

if ( defined('USCES_VERSION') ):
	load_plugin_textdomain('dlseller', USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/languages', plugin_basename(dirname(__FILE__)).'/languages');
	if(version_compare(USCES_VERSION, '1.1-beta', '>=')) {
		require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/define_function11.php');
	} else {
		require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/define_function.php');
	}
	require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/template_func.php');
	require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/utility.php');

	dlseller_upgrade_300();

	add_action('init', 'wcex_dlseller_init', 9);
	add_action('usces_action_shop_admin_menue', 'dlseller_add_shop_admin_menue');
	add_action('usces_action_management_admin_menue', 'dlseller_add_management_admin_menue');
	add_action('wp_ajax_dlseller_make_mail_ajax', 'dlseller_make_mail_ajax' );
	add_action('wp_ajax_dlseller_send_mail_ajax', 'dlseller_send_mail_ajax' );
	add_action('usces_main', 'dlseller_define_functions', 1);

	add_action( "load-welcart-management_page_usces_continue", 'dlseller_continue_member_list_hook' );
	add_filter( 'screen_settings', 'dlseller_screen_settings', 10, 2 );

	$dlseller_options = get_option('dlseller');
	if( !isset($dlseller_options['dlseller_restricting']) || empty($dlseller_options['dlseller_restricting']) )
		$dlseller_options['dlseller_restricting'] = 'on';
	if( !isset($dlseller_options['scheduled_time']['hour']) ) $dlseller_options['scheduled_time']['hour'] = 1;
	if( !isset($dlseller_options['scheduled_time']['min']) ) $dlseller_options['scheduled_time']['min'] = 0;
	update_option('dlseller', $dlseller_options);

	add_action( 'plugins_loaded', 'dlseller_setup' );
	register_deactivation_hook( __FILE__, 'dlseller_event_clear' );
endif;

function dlseller_add_shop_admin_menue(){
	add_submenu_page(USCES_PLUGIN_BASENAME, __('DLSeller Setting','dlseller'), __('DLSeller Setting','dlseller'), 'level_6', 'wcex_dlseller', 'dlseller_shop_admin_page');
}

function dlseller_add_management_admin_menue(){
	add_submenu_page('usces_orderlist', __('Continue Members','dlseller'), __('Continue Members','dlseller'), 'level_6', 'usces_continue', 'continue_member_list_page');
}

function wcex_dlseller_init(){
	global $usces;
	usces_register_action('09dlseller_card_update', 'request', 'dlseller_card_update', NULL, 'dlseller_card_update');
	usces_register_action('10dlseller_transition', 'request', 'dlseller_transition', NULL, 'wcex_dlseller_main');
	add_action( 'admin_print_footer_scripts', 'dlseller_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', 'dlseller_admin_enqueue_scripts' );
	add_filter('usces_template_path_single_item', 'usces_dlseller_path_single_item');
	add_filter('usces_template_path_customer', 'usces_dlseller_path_customer');
	add_filter('usces_template_path_delivery', 'usces_dlseller_path_delivery');
	add_filter('usces_template_path_ordercompletion', 'usces_dlseller_path_ordercompletion');
	add_filter('usces_filter_inCart_quant', 'usces_filter_dlseller_incart_quant');
	add_filter('usces_filter_single_item_inform', 'dlseller_filter_single_item_inform');
	add_filter('usces_filter_get_item', 'dlseller_get_item', 10, 2);
	add_filter('usces_filter_member_check', 'dlseller_member_check', 11);
	add_filter('usces_filter_customer_check', 'dlseller_customer_check', 11);
	add_filter('usces_filter_delivery_check', 'dlseller_delivery_check', 11);
	if( version_compare( USCES_VERSION, '1.3.10.2', '<' ) ) {
		add_filter('usces_filter_order_confirm_mail_first', 'dlseller_order_mail_first', 10, 2);
		add_filter('usces_filter_send_order_mail_first', 'dlseller_order_mail_first', 10, 2);
	}
	add_filter('usces_filter_order_confirm_mail_shipping', 'dlseller_order_mail_shipping', 10, 2);
	add_filter('usces_filter_send_order_mail_shipping', 'dlseller_order_mail_shipping', 10, 2);
	add_filter('usces_filter_order_confirm_mail_meisai', 'dlseller_filter_order_mail_meisai', 10, 2);
	add_filter('usces_filter_send_order_mail_meisai', 'dlseller_filter_order_mail_meisai', 10, 2);
	add_filter('usces_filter_js_intoCart', 'dlseller_filter_js_intoCart', 10, 2);
	add_filter('usces_item_master_second_section', 'dlseller_item_master_second_section', 10, 2);
	add_filter('usces_filter_admin_modified_label', 'dlseller_filter_admin_modified_label', 10);
	add_filter('usces_filter_confirm_prebutton_value', 'dlseller_filter_confirm_prebutton_value', 10);
	add_filter('usces_filter_states_form_js', 'dlseller_filter_states_form_js', 10);
	add_filter('usces_filter_history_item_name', 'dlseller_filter_history_item_name', 10, 4);
	add_filter('usces_filter_member_history_header', 'dlseller_filter_member_history_header', 10, 2 );
	//add_filter('usces_filter_memberinfo_page_header', 'dlseller_filter_memberinfo_page_header', 10, 2 );
	//add_action('usces_action_memberinfo_page_header', 'dlseller_action_memberinfo_page_header', 10, 2 );
	add_filter('usces_filter_confirm_shipping_info', 'dlseller_filter_confirm_shipping_info', 10 );
	add_filter('usces_filter_shipping_address_info', 'dlseller_filter_confirm_shipping_info', 10 );
	add_filter('usces_filter_payment_detail', 'dlseller_filter_payment_detail', 10, 2 );
	add_filter('usces_filter_remise_card_job', 'dlseller_filter_remise_card_job', 10 );
	add_filter('usces_filter_remise_card_item', 'dlseller_filter_remise_card_item', 10 );
	add_filter('usces_fiter_the_payment_method', 'dlseller_fiter_the_payment_method', 10, 2 );
	add_filter('usces_filter_the_payment_method_choices', 'dlseller_filter_the_payment_method_choices', 10, 2 );

	add_action('save_post', 'dlseller_item_save_metadata');
	add_action('wp_print_styles', 'add_dlseller_stylesheet');
	add_action('wp_head', 'dlseller_shop_head');
	add_action('usces_action_member_logout', 'dlseller_action_member_logout');
	add_action('usces_action_reg_orderdata', 'dlseller_action_reg_orderdata', 10);
	add_action('usces_action_del_orderdata', 'dlseller_action_del_orderdata', 10);
	add_action('usces_action_update_orderdata', 'dlseller_action_update_orderdata', 10, 5);
	add_filter('usces_filter_template_redirect', 'dlseller_filter_template_redirect', 2);
	add_action('usces_action_single_item_inform', 'dlseller_action_single_item_inform');
	add_action('usces_action_essential_mark', 'dlseller_action_essential_mark', 10, 2);
	add_action('usces_action_item_dupricate', 'dlseller_action_item_dupricate', 10, 2);
	add_action('usces_pre_reg_orderdata', 'dlseller_pre_reg_orderdata');
	add_action('usces_action_pre_delete_memberdata', 'dlseller_action_pre_delete_memberdata', 10);
	add_filter('usces_filter_send_delmembermail_notice', 'dlseller_filter_send_delmembermail_notice', 10, 2);
	add_action('usces_action_order_print_cart_row', 'dlseller_action_order_print_cart_row', 10, 3);
	add_action('usces_action_confirm_after_form', 'dlseller_action_confirm_after_form' );
	
	if( is_admin() && ( isset($_REQUEST['page']) && 'usces_continue' == $_REQUEST['page'] ) ){
		wp_enqueue_script('jquery-ui-dialog');
	}

	$dlseller_options = get_option('dlseller');
	if( isset($dlseller_options['reminder_mail']) && 'on' == $dlseller_options['reminder_mail'] ) {
		add_action( 'usces_action_admin_mailform', 'dlseller_action_admin_reminder_mailform' );
	}
	if( isset($dlseller_options['contract_renewal_mail']) && 'on' == $dlseller_options['contract_renewal_mail'] ) {
		add_action( 'usces_action_admin_mailform', 'dlseller_action_admin_contract_renewal_mailform' );
	}
}

function dlseller_setup() {
	add_action( 'wcdl_event', 'dlseller_do_event' );
	add_action( 'wp', 'dlseller_schedule' );
}

function dlseller_schedule() {
	if( wp_next_scheduled('wcdl_event') )
		return;

	dlseller_schedule_event();
}

function dlseller_schedule_event( $add = 1 ) {
	$dlseller_options = get_option( 'dlseller' );

	$gmt_offset = get_option( 'gmt_offset' );
	$now = current_time( 'timestamp', 0 );
	$year = (int)date('Y',$now);
	$month = (int)date('n',$now);
	$day = (int)date('j',$now) + 1;
	$timestamp = mktime( (int)$dlseller_options['scheduled_time']['hour'], (int)$dlseller_options['scheduled_time']['min'], 0, $month, $day, $year ) - ( $gmt_offset * 3600 );
	wp_schedule_event( $timestamp, 'daily', 'wcdl_event' );
}

function dlseller_do_event() {
	global $usces, $wpdb;

	if( !dlseller_event_mark() )
		return;

	$dlseller_options = get_option( 'dlseller' );
	if( isset($dlseller_options['reminder_mail']) && 'on' == $dlseller_options['reminder_mail'] ) {
		$send_days_before = ( isset($dlseller_options['send_days_before']) ) ? (int)$dlseller_options['send_days_before'] : 7;
		$reminder_mail_date = date_i18n( 'Y-m-d', strtotime('+'.$send_days_before.' days') );
	} else {
		$reminder_mail_date = '';
	}
	if( isset($dlseller_options['contract_renewal_mail']) && 'on' == $dlseller_options['contract_renewal_mail'] ) {
		$send_days_before = ( isset($dlseller_options['send_days_before']) ) ? (int)$dlseller_options['send_days_before'] : 7;
		$contract_renewal_mail_date = date_i18n( 'Y-m-d', strtotime('+'.$send_days_before.' days') );
	} else {
		$contract_renewal_mail_date = '';
	}

	$todays_charging = array();
	$today = date( 'Y-m-d', current_time('timestamp') );
	//$metatable = $wpdb->prefix.'usces_member_meta';
	//$query = $wpdb->prepare( "SELECT * FROM $metatable WHERE meta_key LIKE %s", 'continuepay_%' );
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$sel_query = "SELECT `con_id`, 
		`con_order_id` AS `order_id`, 
		`con_member_id` AS `member_id`, 
		`con_acting` AS `acting`, 
		`con_price` AS `price`, 
		`con_frequency` AS `frequency`, 
		`con_chargingday` AS `chargingday`, 
		`con_interval` AS `interval`, 
		`con_next_charging` AS `chargedday`, 
		`con_next_contracting` AS `contractedday`, 
		`con_startdate` AS `startdate`, 
		`con_status` AS `status` 
		FROM {$continuation_table} 
		WHERE `con_status` = 'continuation'";
	$res = $wpdb->get_results( $sel_query, ARRAY_A );
	//foreach( $res as $row ) {
	foreach( $res as $continue_data ) {
		//$continue_data = unserialize( $row['meta_value'] );
		//if( 'continuation' == $continue_data['status'] ) {
			//list( $pre, $order_id ) = explode( "_", $row['meta_key'] );
			$order_id = $continue_data['order_id'];
			$member_id = $continue_data['member_id'];
			$update = false;

			$continue_data  = apply_filters( 'dlseller_filter_pre_do_continuation', $continue_data, $today );

			//次回契約更新日
			if( !empty($continue_data['interval']) ) {//契約期間(月数)あり
				if( empty($continue_data['contractedday']) ) {
					$contractedday = dlseller_next_contracting( $order_id, 'time' );
					if( !empty($contractedday) ) {
						$continue_data['contractedday'] = date( 'Y-m-d', $contractedday );
						$update = true;
					}
				} else {
					if( $today == $continue_data['contractedday'] ) {
						$year = (int)substr($today, 0, 4);
						$month = (int)substr($today, 5, 2);
						$day = (int)substr($today, 8, 2);
						$time = mktime(0, 0, 0, ($month + $continue_data['interval']), $day, $year);
						$continue_data['contractedday'] = date( 'Y-m-d', $time );
						do_action( 'dlseller_action_do_continuation_contracting', $today, $member_id, $order_id, $continue_data );
						$update = true;
					}
				}
			} else {
				$continue_data['contractedday'] = NULL;
			}

			//次回課金日
			if( empty($continue_data['chargedday']) ) {
				$continue_data['chargedday'] = date( 'Y-m-d', dlseller_next_charging( $order_id, 'time' ) );
				$update = true;
			} else {
				if( $today == $continue_data['chargedday'] ) {
					$year = (int)substr($today, 0, 4);
					$month = (int)substr($today, 5, 2);
					//if( 99 == $continue_data['chargingday'] ) {
					//	$chargingday = (int)substr($today, 8, 2);
					//} else {
					//	$chargingday = $continue_data['chargingday'];
					//}
					$chargingday = $continue_data['chargingday'];
					$time = mktime(0, 0, 0, ($month + $continue_data['frequency']), $chargingday, $year);
					$continue_data['chargedday'] = date( 'Y-m-d', $time );
					//$todays_charging[] = array( 'member_id'=>$row['member_id'], 'order_id'=>$order_id, 'data'=>$continue_data );
					$todays_charging[] = array( 'member_id'=>$member_id, 'order_id'=>$order_id, 'data'=>$continue_data );
					//do_action( 'dlseller_action_do_continuation_charging', $today, $row['member_id'], $order_id, $continue_data );
					do_action( 'dlseller_action_do_continuation_charging', $today, $member_id, $order_id, $continue_data );
					$update = true;
				}
			}

			//$usces->set_member_meta_value( $row['meta_key'], serialize($continue_data), $row['member_id'] );
			if( $update ) {
				$upd_query = $wpdb->prepare( "UPDATE {$continuation_table} SET `con_next_charging` = %s, `con_next_contracting` = %s WHERE `con_id` = %d", 
					$continue_data['chargedday'],
					$continue_data['contractedday'],
					$continue_data['con_id']
				);
				$wpdb->query( $upd_query );
			}

			if( !empty($reminder_mail_date) && !empty($continue_data['chargedday']) && $reminder_mail_date == $continue_data['chargedday'] ) {
				dlseller_send_reminder_mail( $order_id, $continue_data );
			}
			if( !empty($contract_renewal_mail_date) && !empty($continue_data['contractedday']) && $contract_renewal_mail_date == $continue_data['contractedday'] ) {
				dlseller_send_contract_renewal_mail( $order_id, $continue_data );
			}
		//}
	}
	do_action( 'dlseller_action_do_continuation', $today, $todays_charging );
}

function dlseller_event_mark() {
	global $wpdb;

	$today = date( 'Y-m-d', current_time('timestamp') );
usces_log("dlseller_event_mark:".$today,"dlseller.log");
	$table_name = $wpdb->prefix."usces_access";
	$query = $wpdb->prepare( "SELECT acc_date FROM {$table_name} WHERE acc_key = %s LIMIT 1", 'wcdl_event' );
	$acc_date = $wpdb->get_var( $query );
	if( $acc_date == $today )
		return false;

	sleep(rand(1,10));

	if( $acc_date ) {
		$query = $wpdb->prepare( "UPDATE {$table_name} SET acc_date = %s WHERE acc_key = %s LIMIT 1", $today, 'wcdl_event' );
	} else {
		$query = $wpdb->prepare( "INSERT INTO {$table_name} (acc_date, acc_key) VALUES (%s, %s)", $today, 'wcdl_event' );
	}
	$res = $wpdb->query( $query );
	if( !$res ) {
usces_log("dlseller_event_mark:*** Stopped the automatic processing. ***","dlseller.log");
		return false;
	}
	return true;
}

function dlseller_event_clear() {
	wp_clear_scheduled_hook( 'wcdl_event' );
}

function dlseller_action_item_dupricate($post_id, $newpost_id){

	if( $item_division = get_post_meta($post_id, '_item_division', true) ){
		update_post_meta($newpost_id, '_item_division', $item_division);
	}
	if( $item_charging_type = get_post_meta($post_id, '_item_charging_type', true) ){
		update_post_meta($newpost_id, '_item_charging_type', $item_charging_type);
	}
	if( $item_frequency = get_post_meta($post_id, '_item_frequency', true) ){
		update_post_meta($newpost_id, '_item_frequency', $item_frequency);
	}
	if( $item_chargingday = get_post_meta($post_id, '_item_chargingday', true) ){
		update_post_meta($newpost_id, '_item_chargingday', $item_chargingday);
	}
	if( $dlseller_interval = get_post_meta($post_id, '_dlseller_interval', true) ){
		update_post_meta($newpost_id, '_dlseller_interval', $dlseller_interval);
	}
	if( $dlseller_validity = get_post_meta($post_id, '_dlseller_validity', true) ){
		update_post_meta($newpost_id, '_dlseller_validity', $dlseller_validity);
	}
	if( $dlseller_file = get_post_meta($post_id, '_dlseller_file', true) ){
		update_post_meta($newpost_id, '_dlseller_file', $dlseller_file);
	}
	if( $dlseller_date = get_post_meta($post_id, '_dlseller_date', true) ){
		update_post_meta($newpost_id, '_dlseller_date', $dlseller_date);
	}
	if( $dlseller_version = get_post_meta($post_id, '_dlseller_version', true) ){
		update_post_meta($newpost_id, '_dlseller_version', $dlseller_version);
	}
	if( $dlseller_author = get_post_meta($post_id, '_dlseller_author', true) ){
		update_post_meta($newpost_id, '_dlseller_author', $dlseller_author);
	}
	if( $dlseller_purchases = get_post_meta($post_id, '_dlseller_purchases', true) ){
		update_post_meta($newpost_id, '_dlseller_purchases', $dlseller_purchases);
	}
	if( $dlseller_downloads = get_post_meta($post_id, '_dlseller_downloads', true) ){
		update_post_meta($newpost_id, '_dlseller_downloads', $dlseller_downloads);
	}
}

function dlseller_action_essential_mark( $data=NULL, $field=NULL ) {
	global $usces_essential_mark;
	$type = ( is_array($data) && isset($data['type'])) ? $data['type'] : '';
	$dlseller_options = get_option('dlseller');
	if ( (isset($dlseller_options['dlseller_member_reinforcement']) && 'on' == $dlseller_options['dlseller_member_reinforcement']) || 'customer' == $type || 'delivery' == $type )
		return;

	$usces_essential_mark = array(
		'name1' => '<em>' . __('*', 'usces') . '</em>',
		'name2' => '',
		'name3' => '',
		'name4' => '',
		'zipcode' => '',
		'country' => '',
		'states' => '',
		'address1' => '',
		'address2' => '',
		'address3' => '',
		'tel' => '',
		'fax' => ''
		);
}

function dlseller_filter_remise_card_item( $item ) {
	if( dlseller_have_continue_charge() )
		$item = '0000990';
	return $item;
}

function dlseller_filter_remise_card_job( $job ) {
	if( dlseller_have_continue_charge() )
		$job = 'AUTH';
	return $job;
}

function dlseller_filter_payment_detail() {
	$args = func_get_args();
	$str = $args[0];
	$usces_entries = $args[1];

	if( dlseller_have_continue_charge() ) {
		$str = ' '.__('Continuation Charging', 'dlseller');
	}
	return $str;
}

function dlseller_filter_confirm_shipping_info( $html ) {
	if( ! dlseller_have_shipped() ) {
		$html = '';
	}
	return $html;
}

function dlseller_filter_memberinfo_page_header($html) {
	global $usces;
	$html .= '
<script type="text/javascript">
	function pdfWindow( type, id ) {
		var wx = 800;
		var wy = 900;
		var x = (screen.width- wx) / 2;
		var y = (screen.height - wy) / 2;
		x = 0;
		y = 0;
		printWin = window.open("' . USCES_SSL_URL_ADMIN.'/wp-admin/admin.php?page=usces_orderlist&order_action=pdfout&order_id="+id+"&type="+type+"&uscesid='.$usces->get_uscesid().'","sub","left="+x+",top="+y+",width="+wx+",height="+wy+",scrollbars=yes");
	}
</script>'."\n";
	return $html;
}
function dlseller_action_memberinfo_page_header($html) {
	global $usces;
	$html .= '
<script type="text/javascript">
	function pdfWindow( type, id ) {
		var wx = 800;
		var wy = 900;
		var x = (screen.width- wx) / 2;
		var y = (screen.height - wy) / 2;
		x = 0;
		y = 0;
		printWin = window.open("' . USCES_SSL_URL_ADMIN.'/wp-admin/admin.php?page=usces_orderlist&order_action=pdfout&order_id="+id+"&type="+type+"&uscesid='.$usces->get_uscesid().'","sub","left="+x+",top="+y+",width="+wx+",height="+wy+",scrollbars=yes");
	}
</script>'."\n";
	echo $html;
}

function dlseller_filter_member_history_header() {
	global $usces;
	$args = func_get_args();
	$data = $args[1];

	$colspan = usces_is_membersystem_point() ? 8 : 6;
	$html = '<tr><td class="retail" colspan="'.$colspan.'">'."\n";
	$html .= '&nbsp;&nbsp;' . '<a href="javascript:void(0)" class="bill_pdf_button" onclick="pdfWindow( \'bill\', \'' . $data['ID'] . '\' )">' . __('Invoice', 'usces') . ' PDF</a>';
	if( !preg_match('/noreceipt/', $data['order_status']) ){
		$html .= '&nbsp;&nbsp;' . '<a href="javascript:void(0)" class="receipt_pdf_button" onclick="pdfWindow( \'receipt\', \'' . $data['ID'] . '\' )">' . __('Receipt', 'usces') . ' PDF</a>' . "\n";
	}else{
		$html .= '&nbsp;&nbsp;<span class="noreceipt">' . __('unpaid', 'usces') . '</span>';
	}
	$html .= '</td>' . "\n";
	$member = $usces->get_member();
	$status = dlseller_get_continue_status( $member['ID'], $data['ID'] );
	if( 'continuation' == $status ) {
		$html .= '<td class="right green">'.__('continuation', 'dlseller').'</td></tr>'."\n";
	//} else if( 'cancellation' == $status ) {
	//	$html .= '<td class="right red">'.__('cancellation', 'dlseller').'</td></tr>'."\n";
	} else {
		$html .= '<td class="retail"></td></tr>'."\n";
	}
	return $html;
}

function dlseller_filter_history_item_name(){
	global $usces;
	$args = func_get_args();
	$data = $args[1];//umh
	$rows = $args[2];//cart_row
	//$index = $args[3];//i
	$division = dlseller_get_division( $rows['post_id'] );
	$html = '';
	if( 'data' == $division ) {
		$member = $usces->get_member();
		$mid = (int)$member['ID'];
		$period = dlseller_get_validityperiod($mid, $rows['post_id'], $data['ID']);
		if( preg_match('/noreceipt/', $data['order_status']) ){
			$html .= '';
		}elseif(  empty($period['lastdates']) || $period['lastdates'] >= date('Y/m/d', current_time('timestamp')) ){
			$index = $rows['row_index'];
			$dlitem = $usces->get_item( $rows['post_id'] );
			$html .= '<div class="redownload_link"><a class="redownload_button" href="' . USCES_CART_URL . $usces->delim . 'dlseller_transition=download&rid=' . $index . '&oid=' . $data['ID'] . apply_filters('dlseller_filter_download_para', '', $rows['post_id'], $rows['sku']) . '">' . __('Download the latest version', 'dlseller') . (!empty($dlitem['dlseller_version']) ? '(v' . $dlitem['dlseller_version'] . ')' : '') . '</a></div>';
		}else{
			$html .= '<div class="limitover">' . __('Expired', 'dlseller') . '</div>';
		}
	}
	return apply_filters( 'dlseller_filter_history_item_name', $html, $args );
}

function dlseller_filter_states_form_js( $js ) {
	global $usces;
	if( !dlseller_have_shipped() && ( (is_page(USCES_CART_NUMBER) || $usces->is_cart_page($_SERVER['REQUEST_URI'])) && ('customer' == $usces->page || 'delivery' == $usces->page) ) ) {
		$js = '';
	} elseif( (true === $usces->is_member_logged_in() && WCUtils::is_blank($usces->page)) || (true === $usces->is_member_logged_in() && 'member' == $usces->page) || 'editmemberform' == $usces->page || 'newmemberform' == $usces->page ){
		$js .= '
<script type="text/javascript">
	function pdfWindow( type, id ) {
		window.open ( "'.USCES_ADMIN_URL.'?page=usces_orderlist&order_action=pdfout&noheader=true&order_id="+id+"&type="+type+"&uscesid='.$usces->get_uscesid().'", "_blank");
	}
</script>';
	}
	return $js;
}

function dlseller_filter_template_redirect() {
	global $usces, $post, $usces_entries, $usces_carts, $usces_members, $usces_item, $usces_gp, $member_regmode, $wp_version;
	if( version_compare($wp_version, '4.4-beta', '>') && is_embed() )
		return;
	
	$parent_path = get_template_directory() . '/wc_templates';
	$child_path = get_stylesheet_directory() . '/wc_templates';

	if( is_single() && 'item' == $post->post_mime_type ) {
		$division = dlseller_get_division( $post->ID );
		$usces_item = $usces->get_item( $post->ID );
		if( 'data' == $division ){
		
			if( file_exists($child_path . '/wc_item_single_data.php') ){
				if( !post_password_required($post) ){
					include($child_path . '/wc_item_single_data.php');
					exit;
				}
			}elseif( file_exists($parent_path . '/wc_item_single_data.php') && !defined('USCES_PARENT_LOAD') ){
				if( !post_password_required($post) ){
					include($parent_path . '/wc_item_single_data.php');
					exit;
				}
			}
			
		}elseif( 'service' == $division ){
		
			if( file_exists($child_path . '/wc_item_single_service.php') ){
				if( !post_password_required($post) ){
					include($child_path . '/wc_item_single_service.php');
					exit;
				}
			}elseif( file_exists($parent_path . '/wc_item_single_service.php') && !defined('USCES_PARENT_LOAD') ){
				if( !post_password_required($post) ){
					include($parent_path . '/wc_item_single_service.php');
					exit;
				}
			}
			
		}else{
		
			if( file_exists($child_path . '/wc_item_single.php') ){
				if( !post_password_required($post) ){
					include($child_path . '/wc_item_single.php');
					exit;
				}
			}elseif( file_exists($parent_path . '/wc_item_single.php') && !defined('USCES_PARENT_LOAD') ){
				if( !post_password_required($post) ){
					include($parent_path . '/wc_item_single.php');
					exit;
				}
			}
			
		}
		return true;

	}elseif( isset($_REQUEST['page']) && ('search_item' == $_REQUEST['page'] || 'usces_search' == $_REQUEST['page']) && $usces->is_cart_page($_SERVER['REQUEST_URI']) ){

		if( file_exists($child_path . '/wc_search_page.php') ){
			include($child_path . '/wc_search_page.php');
			exit;
		}elseif( file_exists($parent_path . '/wc_search_page.php') && !defined('USCES_PARENT_LOAD') ){
			include($parent_path . '/wc_search_page.php');
			exit;
		}

	}else if( $usces->is_cart_page($_SERVER['REQUEST_URI']) ){
	
		switch( $usces->page ){
		
			case 'customer':
				if( file_exists($child_path . '/cart/wc_customer_page.php') ){
					usces_get_entries();
					usces_get_member_regmode();
					include($child_path . '/cart/wc_customer_page.php');
					exit;
				}elseif( file_exists($parent_path . '/cart/wc_customer_page.php') && !defined('USCES_PARENT_LOAD') ){
					usces_get_entries();
					usces_get_member_regmode();
					include($parent_path . '/cart/wc_customer_page.php');
					exit;
				}
				
			case 'delivery':
				if( file_exists($child_path . '/cart/wc_delivery_page.php') ){
					usces_get_entries();
					usces_get_carts(); 
					include($child_path . '/cart/wc_delivery_page.php');
					exit;
				}elseif( file_exists($parent_path . '/cart/wc_delivery_page.php') && !defined('USCES_PARENT_LOAD') ){
					usces_get_entries();
					usces_get_carts(); 
					include($parent_path . '/cart/wc_delivery_page.php');
					exit;
				}
				
			case 'confirm':
				if( file_exists($child_path . '/cart/wc_confirm_page.php') ){
					usces_get_entries();
					usces_get_carts();
					usces_get_members();
					include($child_path . '/cart/wc_confirm_page.php');
					exit;
				}elseif( file_exists($parent_path . '/cart/wc_confirm_page.php') && !defined('USCES_PARENT_LOAD') ){
					usces_get_entries();
					usces_get_carts();
					usces_get_members();
					include($parent_path . '/cart/wc_confirm_page.php');
					exit;
				}
				
			case 'ordercompletion':
				if( file_exists($child_path . '/cart/wc_completion_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include($child_path . '/cart/wc_completion_page.php');
					exit;
				}elseif( file_exists($parent_path . '/cart/wc_completion_page.php') && !defined('USCES_PARENT_LOAD') ){
					usces_get_entries();
					usces_get_carts();
					include($parent_path . '/cart/wc_completion_page.php');
					exit;
				}
				
			case 'error':
				if( file_exists($child_path . '/cart/wc_cart_error_page.php') ){
					include($child_path . '/cart/wc_cart_error_page.php');
					exit;
				}elseif( file_exists($parent_path . '/cart/wc_cart_error_page.php') && !defined('USCES_PARENT_LOAD') ){
					include($parent_path . '/cart/wc_cart_error_page.php');
					exit;
				}
				
			case 'newmemberform':
				if( file_exists($child_path . '/member/wc_new_member_page.php') ){
					$member_regmode = 'newmemberform';
					include($child_path . '/member/wc_new_member_page.php');
					exit;
				}elseif( file_exists($parent_path . '/member/wc_new_member_page.php') && !defined('USCES_PARENT_LOAD') ){
					$member_regmode = 'newmemberform';
					include($parent_path . '/member/wc_new_member_page.php');
					exit;
				}
				
			case 'cart':
			default:
				if( file_exists($child_path . '/cart/wc_cart_page.php') ){
					include($child_path . '/cart/wc_cart_page.php');
					exit;
				}elseif( file_exists($parent_path . '/cart/wc_cart_page.php') && !defined('USCES_PARENT_LOAD') ){
					include($parent_path . '/cart/wc_cart_page.php');
					exit;
				}
				
		}
		return true;

	}else if($usces->is_inquiry_page($_SERVER['REQUEST_URI']) ){

	}else if( $usces->is_member_page($_SERVER['REQUEST_URI']) ){

		if($usces->options['membersystem_state'] != 'activate') return;

		if( $usces->is_member_logged_in() ) {
		
			$member_regmode = 'editmemberform';
			if( file_exists($child_path . '/member/wc_member_page.php') ){
				include($child_path . '/member/wc_member_page.php');
				exit;
			}elseif( file_exists($parent_path . '/member/wc_member_page.php') && !defined('USCES_PARENT_LOAD') ){
				include($parent_path . '/member/wc_member_page.php');
				exit;
			}

		} else {
		
			switch( $usces->page ){
				case 'login':
					if( file_exists($child_path . '/member/wc_login_page.php') ){
						include($child_path . '/member/wc_login_page.php');
						exit;
					}elseif( file_exists($parent_path . '/member/wc_login_page.php') && !defined('USCES_PARENT_LOAD') ){
						include($parent_path . '/member/wc_login_page.php');
						exit;
					}
					
				case 'newmemberform':
					if( file_exists($child_path . '/member/wc_new_member_page.php') ){
						$member_regmode = 'newmemberform';
						include($child_path . '/member/wc_new_member_page.php');
						exit;
					}elseif( file_exists($parent_path . '/member/wc_new_member_page.php') && !defined('USCES_PARENT_LOAD') ){
						$member_regmode = 'newmemberform';
						include($parent_path . '/member/wc_new_member_page.php');
						exit;
					}
					
				case 'lostmemberpassword':
					if( file_exists($child_path . '/member/wc_lostpassword_page.php') ){
						include($child_path . '/member/wc_lostpassword_page.php');
						exit;
					}elseif( file_exists($parent_path . '/member/wc_lostpassword_page.php') && !defined('USCES_PARENT_LOAD') ){
						include($parent_path . '/member/wc_lostpassword_page.php');
						exit;
					}
					
				case 'changepassword':
					if( file_exists($child_path . '/member/wc_changepassword_page.php') ){
						include($child_path . '/member/wc_changepassword_page.php');
						exit;
					}elseif( file_exists($parent_path . '/member/wc_changepassword_page.php') && !defined('USCES_PARENT_LOAD') ){
						include($parent_path . '/member/wc_changepassword_page.php');
						exit;
					}
					
				case 'newcompletion':
				case 'editcompletion':
				case 'lostcompletion':
				case 'changepasscompletion':
					if( file_exists($child_path . '/member/wc_member_completion_page.php') ){
						include($child_path . '/member/wc_member_completion_page.php');
						exit;
					}elseif( file_exists($parent_path . '/member/wc_member_completion_page.php') && !defined('USCES_PARENT_LOAD') ){
						include($parent_path . '/member/wc_member_completion_page.php');
						exit;
					}
					
				default:
					if( file_exists($child_path . '/member/wc_login_page.php') ){
						include($child_path . '/member/wc_login_page.php');
						exit;
					}elseif( file_exists($parent_path . '/member/wc_login_page.php') && !defined('USCES_PARENT_LOAD') ){
						include($parent_path . '/member/wc_login_page.php');
						exit;
					}
			}
		}
		return true;
	}
}

function dlseller_action_usces_main() {
	global $usces;
	if( !isset($_REQUEST['dlseller_transition']) && $usces->is_cart_page($_SERVER['REQUEST_URI'])){
		usces_dlseller_login_check();
	}
}

function dlseller_action_member_logout() {
	unset($_SESSION['usces_cart']);
}

function dlseller_shop_head() {
	if( file_exists(get_stylesheet_directory() . '/dlseller.css') ){
?>
		<link href="<?php echo get_stylesheet_directory_uri(); ?>/dlseller.css" rel="stylesheet" type="text/css" />
<?php
	}
}

function usces_dlseller_login_check() {
	global $usces;

	if( !$usces->is_member_logged_in() ){
		header('location: '.USCES_MEMBER_URL);
		exit;
	}
}

function dlseller_card_update() {
	global $usces;
	$dls_opts = get_option('dlseller');
	$action = $_REQUEST['dlseller_card_update'];
	switch($action){
		case 'login':

			if( $usces->is_member_logged_in() ){
				add_filter('usces_template_path_member', 'usces_dlseller_path_upaction');
				add_filter('usces_filter_title_member', 'usces_dlseller_title_upaction');
				$usces->page = 'member';
				add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_member');
				add_action('the_post', array($usces, 'action_memberFilter'));
			}else{
				$res = $usces->member_login();
				if( $res != 'login' ){
					add_filter('usces_template_path_member', 'usces_dlseller_path_upaction');
					add_filter('usces_filter_title_member', 'usces_dlseller_title_upaction');
					$usces->page = 'member';
					add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_member');
					add_action('the_post', array($usces, 'action_memberFilter'));
				}else{
					add_filter('usces_filter_login_page_header', 'usces_dlseller_path_uplogin_header');
					add_filter('usces_template_path_login', 'usces_dlseller_path_uplogin');
					$usces->page = 'login';
					add_filter('usces_filter_login_form_action', 'usces_dlseller_login_form_action');
					add_filter('usces_filter_login_button', 'usces_dlseller_login_button');
					add_filter('usces_filter_login_inform', 'usces_dlseller_login_inform_cardup');
	//				add_action('usces_action_login_page_inform', 'usces_dlseller_login_wc_inform_cardup');
					add_filter('usces_filter_memberTitle', 'usces_dlseller_title_login',1);
					add_action('the_post', array($usces, 'action_memberFilter'));
					add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_login');
				}
			}
			break;
	}
	return false;
}

function wcex_dlseller_main() {
	global $usces, $wp_query, $usces_item, $post;
	$dls_opts = get_option('dlseller');
	$action = $_REQUEST['dlseller_transition'];

	switch($action){
		case 'single_item':
			$ids = array_keys($_POST['inCart']);
			$post_id = $ids[0];
			$division = dlseller_get_division( $post_id );
			$charging_type = $usces->getItemChargingType( $post_id );
			if( 'continue' == $charging_type ){
				if( $usces->cart->num_row() !== false ) {
					$usces->cart->crear_cart();
				}
			}else{
				if( $usces->cart->num_row() !== false && dlseller_have_continue_charge() ) {
					$usces->cart->crear_cart();
				}
			}
			return true;
			break;
		case 'login':
			$res = $usces->member_login();
			if( $res != 'login' ){
				if( $usces->cart->num_row() !== false ){
					$member = $usces->get_member();
					$mid = (int)$member['ID'];
					$cart = $usces->cart->get_cart();
					$dlseller_cart = $cart;
					$cart_row = $cart[0];
					$post_id = $cart_row['post_id'];
					$sku = $cart_row['sku'];
					$usces_item = $usces->get_item( $post_id );
					$usces_item['sku'] = $sku;
					$period = dlseller_get_validityperiod($mid, $post_id);
					if( $usces->is_purchased_item($mid, $post_id) === true && ( empty($period['lastdate']) || $period['lastdate'] >= mysql2date(__('Y/m/d'),current_time('mysql', 0)) ) ){
						$usces->cart->crear_cart();
						add_filter('usces_template_path_cart', 'usces_dlseller_path_redownload');
						add_filter('usces_filter_title_cart', 'usces_dlseller_title_redownload',1);
					}
					$usces->page = 'cart';
					add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
					add_action('the_post', array($usces, 'action_cartFilter'));
				}else{
					$usces->page = 'member';
					add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_member');
					add_action('the_post', array($usces, 'action_memberFilter'));
				}
			}else{
				$usces->page = 'login';
				add_filter('usces_filter_login_form_action', 'usces_dlseller_login_form_action');
				add_filter('usces_filter_login_button', 'usces_dlseller_login_button');
				add_filter('usces_filter_login_inform', 'usces_dlseller_login_inform');
				add_action('usces_action_login_page_inform', 'usces_dlseller_login_wc_inform');
				add_filter('usces_filter_newmember_urlquery', 'usces_dlseller_newmember_urlquery');
				add_filter('usces_filter_memberTitle', 'usces_dlseller_title_login',1);
				add_action('the_post', array($usces, 'action_memberFilter'));
				add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_login');
			}
			break;
		case 'newmember':
			$usces->page = 'newmemberform';
		//	add_filter('usces_filter_newmember_form_action', 'usces_dlseller_newmember_form_action');
			add_filter('usces_filter_newmember_button', 'usces_dlseller_newmember_button');
			add_filter('usces_filter_newmember_inform', 'usces_dlseller_newmember_inform');
			add_action('usces_action_newmember_page_inform', 'usces_dlseller_newmember_wc_inform');
			add_action('the_post', array($usces, 'action_memberFilter'));
			add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_editmemberform');
			add_action('template_redirect', array($usces, 'template_redirect'));
			break;
		case 'regmember':
			$_POST['member_regmode'] = 'newmemberform';
			$res = $usces->regist_member();
			if( 'newcompletion' == $res ){
				$email = stripslashes(trim($_POST['member']['mailaddress1']));
				$pass = stripslashes(trim($_POST['member']['password1']));
				$lires = $usces->member_just_login($email, $pass);
				if( $lires == 'login' ){
					wp_redirect(get_option('home'));
					exit;
				}
				wp_redirect(USCES_CART_URL.$usces->delim . 'customerinfo=1');
				exit;
//				$usces->page = 'delivery';
//				add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
//				add_action('the_post', array($usces, 'action_cartFilter'));
			}else{
				$usces->page = 'newmemberform';
			//	add_filter('usces_filter_newmember_form_action', 'usces_dlseller_newmember_form_action');
				add_filter('usces_filter_newmember_button', 'usces_dlseller_newmember_button');
				add_filter('usces_filter_newmember_inform', 'usces_dlseller_newmember_inform');
				add_action('usces_action_newmember_page_inform', 'usces_dlseller_newmember_wc_inform');
				add_action('the_post', array($usces, 'action_memberFilter'));
				add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_editmemberform');
				add_action('template_redirect', array($usces, 'template_redirect'));
			}
			break;
		case 'cart':
			return true;
			break;
		case 'confirm':
			return true;
			break;
		case 'download':
			dlseller_download();
			break;
		case 'results':
			dlseller_results_csv();
			break;
		case 'member_reference':
			if( $usces->is_member_logged_in() ){
				$usces->page = 'cart';
				$member = $usces->get_member();
				$mid = (int)$member['ID'];
				$post_id = (int)$_GET['post_id'];
				$sku = urldecode($_GET['sku']);
				$usces_item = $usces->get_item( $post_id );
				$usces_item['sku'] = $sku;
				$period = dlseller_get_validityperiod($mid, $post_id);
				if( $usces->is_purchased_item($mid, $post_id) === true && ( empty($period['lastdates']) || $period['lastdates'] >= date('Y/m/d', current_time('timestamp')) ) ){
					add_filter('usces_template_path_cart', 'usces_dlseller_path_redownload');
					if( 'service' == $usces_item['item_division'] ){
						add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_ordercompletion');
						add_filter('usces_filter_title_cart', 'usces_dlseller_title_information',1);
					}else{
						add_filter('yoast-ga-push-after-pageview', 'dlseller_trackPageview_redownload');
						add_filter('usces_filter_title_cart', 'usces_dlseller_title_redownload',1);
					}
				}
				add_action('the_post', array($usces, 'action_cartFilter'));
			}else{
				$usces->page = 'login';
				add_filter('usces_filter_login_form_action', 'usces_dlseller_login_form_action');
				add_filter('usces_filter_login_button', 'usces_dlseller_login_button');
				add_filter('usces_filter_login_inform', 'usces_dlseller_login_inform');
				add_action('usces_action_login_page_inform', 'usces_dlseller_login_wc_inform');
				add_filter('usces_filter_newmember_urlquery', 'usces_dlseller_newmember_urlquery');
				add_filter('usces_filter_memberTitle', 'usces_dlseller_title_login',1);
				add_action('the_post', array($usces, 'action_memberFilter'));
			}
			break;
	}
}

function usces_dlseller_path_uplogin_header(){
	$mes = '<h2>' . __('Credit card update processing', 'dlseller') . '</h2>
	<p>' . __('Login is necessary to update Credit card. Please work to update it according to the guidance of the next page if You can log in.', 'dlseller') . '</p>';
	return $mes;
}

function usces_dlseller_login_form_action( $url ){
	$url = USCES_CART_URL;
	return $url;
}

function usces_dlseller_newmember_form_action( $url ){
	$url = USCES_CART_URL;
	return $url;
}

function usces_dlseller_path_single_item( $path ){
	global $post;
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/single_item.php';
	return $path;
}

function usces_dlseller_path_customer( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/customer_info.php';
	return $path;
}

function usces_dlseller_path_delivery( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/delivery_info.php';
	return $path;
}

function usces_dlseller_path_confirm( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/confirm.php';
	return $path;
}

function usces_dlseller_path_ordercompletion( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/completion.php';
	return $path;
}

function usces_dlseller_path_redownload( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/redownload.php';
	return $path;
}

function usces_dlseller_path_member_form( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/member_form.php';
	return $path;
}

function usces_dlseller_path_member( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/member.php';
	return $path;
}

function usces_dlseller_path_uplogin( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/uplogin.php';
	return $path;
}

function usces_dlseller_path_upaction( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/upaction.php';
	return $path;
}

function usces_dlseller_newmember_urlquery( $query ){
	return '&dlseller_transition=newmember';
}

function usces_dlseller_title_cart( $title ){
	return __('Checkout', 'dlseller');
}

function usces_dlseller_title_redownload( $title ){
	return __('Redownload', 'dlseller');
}

function usces_dlseller_title_information( $title ){
	return __('Information', 'dlseller');
}

function usces_dlseller_title_ordercompletion( $title ){
	return __('Download', 'dlseller');
}

function usces_dlseller_title_login( $title ){
	return __('Log-in for members', 'usces');
}

function usces_dlseller_title_upaction( $title ){
	return __('Credit card update processing', 'dlseller');
}

function dlseller_order_mail_first(){
	$args = func_get_args();
	$data = $args[1];
	$str = __('membership number', 'usces') . " : " . $data['mem_id'] . "\r\n";
	return $str;
}

function dlseller_order_mail_shipping($msg_shipping){
	if( dlseller_have_shipped() )
		return $msg_shipping;
	else
		return "";
}

function usces_filter_dlseller_incart_quant($quant){
	if( dlseller_have_continue_charge() )
		return 1;
	else
		return $quant;
}

function usces_filter_cart_upbutton($button){
	$addtag .= '';
	return $addtag;
}

function dlseller_filter_single_item_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="single_item" />';
	return $html;
}

function dlseller_action_single_item_inform(){
	echo '<input name="dlseller_transition" type="hidden" value="single_item" />';
}

function usces_dlseller_login_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="login" />';
	return $html;
}

function usces_dlseller_login_inform_cardup($html){
	$html .= '<input name="dlseller_card_update" type="hidden" value="login" />';
	$html .= '<input name="dlseller_order_id" type="hidden" value="' . (int)$_REQUEST['dlseller_order_id'] . '" />';
	$html .= '<input name="dlseller_up_mode" type="hidden" value="2" />';
	return $html;
}

function usces_dlseller_newmember_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="regmember" />';
	return $html;
}

function usces_dlseller_newmember_wc_inform(){
	$html = '<input name="dlseller_transition" type="hidden" value="regmember" />';
	echo $html;
}

function usces_dlseller_cart_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="cart" />';
	return $html;
}

function dlseller_customer_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="customer" />';
	return $html;
}

function usces_dlseller_confirm_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="confirm" />';
	return $html;
}

function usces_dlseller_login_wc_inform_cardup(){
	echo '<input name="dlseller_card_update" type="hidden" value="login" />';
}

function usces_dlseller_login_button($button){
	$button = '<input type="submit" name="dlSeller" id="member_login" value="' . __('Log-in', 'usces') . '" tabindex="100" />';
	return $button;
}

function usces_dlseller_newmember_button($button){
	$button = '<input name="regmemberdl" type="submit" value="' . __('transmit a message', 'usces') . '" />';
	return $button;
}

function dlseller_download() {
	global $usces;
	$member = $usces->get_member();
	$mid = (int)$member['ID'];
	if( !isset($_GET['pid']) ){
		$rid = (int)$_GET['rid'];
		$oid = (int)$_GET['oid'];
		$order = $usces->get_order_data($oid);
		if( $order === false )
			exit;
		if(version_compare(USCES_VERSION, '1.4', '>=')) {
			$cart = usces_get_ordercartdata($oid);
			foreach($cart as $cart_row){
				if( $rid == $cart_row['row_index'] ){
					$terget_row = $cart_row;
					break;
				}
			}
			$post_id = $terget_row['post_id'];
			$sku = $terget_row['sku_code'];
		}else{
			$cart = $order['cart'];
			$post_id = $cart[$rid]['post_id'];
			$sku = $cart[$rid]['sku'];
		}
	}else{
		$post_id = (int)$_GET['pid'];
		$sku = isset($_GET['sku']) ? $_GET['sku'] : NULL;
	}

	if( $usces->is_purchased_item($mid, $post_id, urldecode($sku)) === false ){
		header( "Content-Type: text/html; charset=UTF-8" );
		echo __('An error occurred. Please contact the manager.', 'dlseller') . '(error:nomember or nopurchase)<br /><br /><br />';
		echo '<a href="' . get_option('home') . '">' . __('Back to the top page.', 'usces') . '</a>';
		exit;
	}

//	$files = get_post_meta($post_id, '_dlseller_file', true);
	$filename = dlseller_get_filename( $post_id, $sku );
	$downloads = get_post_meta($post_id, '_dlseller_downloads', true);
	$dl = (int)$downloads;
	$dlseller_options = get_option('dlseller');
	$delseller_path = $dlseller_options['content_path'] . $filename;
	if( !file_exists($delseller_path) || !is_file($delseller_path) ){
		header( "Content-Type: text/html; charset=UTF-8" );
		echo __('An error occurred. Please contact the manager.', 'dlseller') . '(error:nofile)<br /><br /><br />';
		echo '<a href="' . get_option('home') . '">' . __('Back to the top page.', 'usces') . '</a>';
		exit;
	}

	$content_length = filesize($delseller_path);
	session_write_close();
	mb_http_output("pass");
	header( "Cache-Control: public" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=" . usces_dlseller_filename( $post_id, $sku ) );
	header( "Content-Type: application/octet-stream" );
	header( "Content-Length: " . $content_length ) ;

	set_time_limit(0);
	while (ob_get_level() > 0){
		ob_end_clean();
	}
	ob_start();
	
	$fp = fopen($delseller_path, "r");
	while(!feof($fp)){
		print fread($fp, round($dlseller_options['dlseller_rate'] * 1024));
		ob_flush();
	}
	ob_flush();
	fclose($fp);
	ob_end_clean();

	$dl++;
	usces_dlseller_update_dlcount($post_id, 0, 1 );
	exit;
}

function dlseller_enqueue_scripts() {
	if( (( array_key_exists( 'page', $_GET ) and 'usces_itemedit' == $_GET['page'] ) and ( array_key_exists( 'action', $_GET ) and 'edit' == $_GET['action'] ))
		|| ( array_key_exists( 'page', $_GET ) and 'usces_itemnew' == $_GET['page'] ) ) :
?>
<script type="text/javascript">
jQuery(function($) {
	$("#division_shipped").change(function() {
		$("tr.shipped").css("display","");
		$("tr.dl_service").css("display","none");
		$("tr.dl_content").css("display","none");
		$("tr.dl_data").css("display","none");
	});
	$("#division_data").change(function() {
		$("tr.shipped").css("display","none");
		$("tr.dl_service").css("display","none");
		$("tr.dl_content").css("display","");
		$("tr.dl_data").css("display","");
	});
	$("#division_service").change(function() {
		$("tr.shipped").css("display","none");
		$("tr.dl_service").css("display","");
		$("tr.dl_content").css("display","");
		$("tr.dl_data").css("display","none");
	});
	var dld = $("input[name=\'item_division\']:checked").val();
	if( "shipped" == dld ){
		$("tr.shipped").css("display","");
		$("tr.dl_service").css("display","none");
		$("tr.dl_content").css("display","none");
		$("tr.dl_data").css("display","none");
	}else if( "data" == dld ){
		$("tr.shipped").css("display","none");
		$("tr.dl_service").css("display","none");
		$("tr.dl_content").css("display","");
		$("tr.dl_data").css("display","");
	}else if( "service" == dld ){
		$("tr.shipped").css("display","none");
		$("tr.dl_service").css("display","");
		$("tr.dl_content").css("display","");
		$("tr.dl_data").css("display","none");
	}
	var dct = $("#item_charging_type").val();
	if( "0" == dct ){
		$("tr.dl_frequency").css("display","none");
		$("tr.dl_chargingday").css("display","none");
		$("tr.dl_interval").css("display","none");
	}else{
		$("tr.dl_frequency").css("display","");
		$("tr.dl_chargingday").css("display","");
		$("tr.dl_interval").css("display","");
	}
	$("#item_charging_type").change(function() {
		dct = $("#item_charging_type").val();
		if( "0" == dct ){
			$("tr.dl_frequency").css("display","none");
			$("tr.dl_chargingday").css("display","none");
			$("tr.dl_interval").css("display","none");
		}else{
			$("tr.dl_frequency").css("display","");
			$("tr.dl_chargingday").css("display","");
			$("tr.dl_interval").css("display","");
		}
	});
});
</script>
<?php elseif( ( array_key_exists( 'page', $_GET ) and 'usces_continue' == $_GET['page'] ) and ( array_key_exists( 'action', $_GET ) and 'edit' == $_GET['action'] ) ): ?>
<script type="text/javascript" src="<?php echo USCES_WP_PLUGIN_URL . '/usc-e-shop/js/jquery/jquery-ui-1.7.1.custom.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo USCES_WP_PLUGIN_URL . '/usc-e-shop/js/jquery/bgiframe/jquery.bgiframe.min.js'; ?>"></script>
<?php
	endif;
}

function dlseller_admin_enqueue_scripts( $hook_suffix ) {
	if( 'welcart-management_page_usces_continue' == $hook_suffix ) {
		$path = USCES_FRONT_PLUGIN_URL.'/js/jquery/jquery.cookie.js';
		wp_enqueue_script( 'usces_member_cookie', $path, array('jquery'), USCES_VERSION, true );
	}
}

function dlseller_item_master_second_section() {
	global $usces;
	$args = func_get_args();
	$html = $args[0];
	$post_ID = $args[1];

	$division = dlseller_get_division( $post_ID );
	switch( $division ){
		case 'shipped':
			$shipped_select = ' checked="checked"';
			$data_select = '';
			$serv_select = '';
			break;
		case 'data':
			$shipped_select = '';
			$data_select = ' checked="checked"';
			$serv_select = '';
			break;
		case 'service':
			$shipped_select = '';
			$data_select = '';
			$serv_select = ' checked="checked"';
			break;
		default:
			$shipped_select = '';
			$data_select = '';
			$serv_select = '';
	}
	$item_charging_type = get_post_meta($post_ID, '_item_charging_type', true);
	$item_frequency = get_post_meta($post_ID, '_item_frequency', true);
	$item_chargingday = get_post_meta($post_ID, '_item_chargingday', true);
	$dlseller_interval = get_post_meta($post_ID, '_dlseller_interval', true);
	$dlseller_validity = get_post_meta($post_ID, '_dlseller_validity', true);
	$dlseller_file = get_post_meta($post_ID, '_dlseller_file', true);
	$dlseller_date = get_post_meta($post_ID, '_dlseller_date', true);
	$dlseller_version = get_post_meta($post_ID, '_dlseller_version', true);
	$dlseller_author = get_post_meta($post_ID, '_dlseller_author', true);
	$dlseller_purchases = get_post_meta($post_ID, '_dlseller_purchases', true);
	$dlseller_downloads = get_post_meta($post_ID, '_dlseller_downloads', true);
	$dls_mon = usces_dlseller_get_dlcount($post_ID, 'month');
	$dls_tol = usces_dlseller_get_dlcount($post_ID, 'total');

	ob_start();
?>
	<tr>
	<th><?php _e('Division', 'dlseller'); ?></th>
	<td><label for="division_shipped"><input name="item_division" id="division_shipped" type="radio" value="shipped"<?php echo $shipped_select; ?> />&nbsp;<?php _e('Shipped', 'dlseller'); ?></label>&nbsp;&nbsp;
		<label for="division_data"><input name="item_division" id="division_data" type="radio" value="data"<?php echo $data_select; ?> />&nbsp;<?php _e('Data file', 'dlseller'); ?></label>&nbsp;&nbsp;
		<label for="division_service"><input name="item_division" id="division_service" type="radio" value="service"<?php echo $serv_select; ?> />&nbsp;<?php _e('Service', 'dlseller'); ?></label></td>
	</tr>
	<th><?php _e('Charging type','usces'); ?></th>
	<td>
		<select id="item_charging_type" name="item_charging_type">
			<option value="0"<?php if( 0 === (int)$item_charging_type ) echo ' selected="selected"'; ?>><?php _e('Normal Charging','dlseller'); ?></option>
			<option value="1"<?php if( 1 === (int)$item_charging_type ) echo ' selected="selected"'; ?>><?php _e('Continuation Charging','dlseller'); ?></option>
		</select>
	</td>
	</tr>
	<tr class="dl_frequency">
	<th><?php _e('Charging Interval', 'dlseller'); ?></th>
	<td>
		<select id="item_frequency" name="item_frequency">
<?php
		$frequency_options = '
			<option value="1"' . ( 1 === (int)$item_frequency ? ' selected="selected"' : '' ) . '>' . __('Monthly','dlseller') . '</option>
			<option value="6"' . ( 6 === (int)$item_frequency ? ' selected="selected"' : '' ) . '>' . __('Every six months','dlseller') . '</option>
			<option value="12"' . ( 12 === (int)$item_frequency ? ' selected="selected"' : '' ) . '>' . __('Every year','dlseller') . '</option>';
		echo apply_filters( 'dlseller_filter_item_frequency_options', $frequency_options, $item_frequency, $post_ID );
?>
		</select>
	</td>
	</tr>
	<tr class="dl_chargingday">
	<th><?php _e('Charging Date', 'dlseller'); ?></th>
	<td>
		<select id="item_chargingday" name="item_chargingday">
			<option value="99"<?php if( 99 === (int)$item_chargingday ) echo ' selected="selected"'; ?>><?php _e('Order date','dlseller'); ?></option>
		<?php for($i=1; $i<29; $i++) : ?>
			<option value="<?php echo $i; ?>"<?php if( $i === (int)$item_chargingday ) echo ' selected="selected"'; ?>><?php echo str_pad($i, 2, " ", STR_PAD_LEFT); ?></option>
		<?php endfor; ?>
		</select>
	</td>
	<tr class="dl_interval">
	<th><?php _e('Contract Period(Months)', 'dlseller'); ?></th>
	<td><input type="text" name="dlseller_interval" id="dlseller_interval" class="itemCode" value="<?php echo esc_attr($dlseller_interval); ?>" /></td>
	</tr>
	</tr>
	<tr class="dl_data">
	<th><?php _e('Validity(days)', 'dlseller'); ?></th>
	<td><input type="text" name="dlseller_validity" id="dlseller_validity" class="itemCode" value="<?php echo esc_attr($dlseller_validity); ?>" /></td>
	</tr>
	<tr class="dl_data">
	<th><?php _e('File Name', 'dlseller'); ?></th>
	<td><input type="text" name="dlseller_file" id="dlseller_file" class="itemCode" value="<?php echo esc_attr($dlseller_file); ?>" /></td>
	</tr>
	<tr class="dl_data">
	<th><?php _e('Release Date', 'dlseller'); ?></th>
	<td><input type="text" name="dlseller_date" id="dlseller_date" class="itemCode" value="<?php echo esc_attr($dlseller_date); ?>" /></td>
	</tr>
	<tr class="dl_data">
	<th><?php _e('Version', 'dlseller'); ?></th>
	<td><input type="text" name="dlseller_version" id="dlseller_version" class="itemCode" value="<?php echo esc_attr($dlseller_version); ?>" /></td>
	</tr>
	<tr class="dl_data">
	<th><?php _e('Author', 'dlseller'); ?></th>
	<td><input type="text" name="dlseller_author" id="dlseller_author" class="itemCode" value="<?php echo esc_attr($dlseller_author); ?>" /></td>
	</tr>
	<tr class="dl_data">
	<th><?php _e('Purchases', 'dlseller'); ?></th>
	<td><?php echo $dls_mon['par']; ?>(<?php echo $dls_tol['par']; ?>)</td>
	</tr>
	<tr class="dl_data">
	<th><?php _e('Downloads', 'dlseller'); ?></th>
	<td><?php echo $dls_mon['dl']; ?>(<?php echo $dls_tol['dl']; ?>)</td>
	</tr>
<?php
	$addtag = ob_get_contents();
	ob_end_clean();
	echo apply_filters( 'dlseller_filter_item_master_second_section', $addtag, $post_ID ) . $html;
}

function dlseller_item_save_metadata() {
	global $usces;

	$post_ID = isset($_POST['post_ID']) ? $_POST['post_ID'] : -1;
	if( $post_ID < 0 ) return $post_ID;

	if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
		return $post_ID;
	} else {
	if ( !current_user_can( 'edit_post', $post_ID ))
		return $post_ID;
	}

	if(isset($_POST['item_division'])){
		$item_division = $_POST['item_division'];
		update_post_meta($post_ID, '_item_division', $item_division);
	}
	if(isset($_POST['item_charging_type'])){
		$item_charging_type = (int)$_POST['item_charging_type'];
		update_post_meta($post_ID, '_item_charging_type', $item_charging_type);
	}
	if(isset($_POST['item_frequency'])){
		$item_frequency = (int)$_POST['item_frequency'];
		update_post_meta($post_ID, '_item_frequency', $item_frequency);
	}
	if(isset($_POST['item_chargingday'])){
		$item_chargingday = (int)$_POST['item_chargingday'];
		update_post_meta($post_ID, '_item_chargingday', $item_chargingday);
	}
	if(isset($_POST['dlseller_interval'])){
		$dlseller_interval = trim($_POST['dlseller_interval']);
		update_post_meta($post_ID, '_dlseller_interval', $dlseller_interval);
	}
	if(isset($_POST['dlseller_validity'])){
		$dlseller_validity = trim($_POST['dlseller_validity']);
		update_post_meta($post_ID, '_dlseller_validity', $dlseller_validity);
	}
	if(isset($_POST['dlseller_file'])){
		$dlseller_file = trim($_POST['dlseller_file']);
		update_post_meta($post_ID, '_dlseller_file', $dlseller_file);
	}
	if(isset($_POST['dlseller_date'])){
		$dlseller_date = trim($_POST['dlseller_date']);
		update_post_meta($post_ID, '_dlseller_date', $dlseller_date);
	}
	if(isset($_POST['dlseller_version'])){
		$dlseller_version = trim($_POST['dlseller_version']);
		update_post_meta($post_ID, '_dlseller_version', $dlseller_version);
	}
	if(isset($_POST['dlseller_author'])){
		$dlseller_author = trim($_POST['dlseller_author']);
		update_post_meta($post_ID, '_dlseller_author', $dlseller_author);
	}
	if(isset($_POST['dlseller_purchases'])){
		$dlseller_purchases = trim($_POST['dlseller_purchases']);
		update_post_meta($post_ID, '_dlseller_purchases', $dlseller_purchases);
	}
	if(isset($_POST['dlseller_downloads'])){
		$dlseller_downloads = trim($_POST['dlseller_downloads']);
		update_post_meta($post_ID, '_dlseller_downloads', $dlseller_downloads);
	}
}

function dlseller_shop_admin_page() {
	global $usces, $wpdb, $wp_locale, $current_user;
	global $wp_query;

	if( isset($_POST['dlseller_transition']) && 'dlseller_option_update' == $_POST['dlseller_transition']){
		$_POST = $usces->stripslashes_deep_post($_POST);
		$dlseller_options = array();
		$dlseller_options['content_path'] = trim($_POST['dlseller_content_path']);
		$dlseller_options['dlseller_terms'] = trim($_POST['dlseller_terms']);
		$dlseller_options['dlseller_terms2'] = trim($_POST['dlseller_terms2']);
		$dlseller_options['dlseller_rate'] = (int)trim($_POST['dlseller_rate']);
		$dlseller_options['dlseller_member_reinforcement'] = isset($_POST['dlseller_member_reinforcement']) ? $_POST['dlseller_member_reinforcement'] : 'off';
		$dlseller_options['dlseller_restricting'] = isset($_POST['dlseller_restricting']) ? $_POST['dlseller_restricting'] : 'on';
		$dlseller_options['reminder_mail'] = ( isset($_POST['dlseller_reminder_mail']) ) ? $_POST['dlseller_reminder_mail'] : 'off';
		$dlseller_options['contract_renewal_mail'] = ( isset($_POST['dlseller_contract_renewal_mail']) ) ? $_POST['dlseller_contract_renewal_mail'] : 'off';
		$dlseller_options['send_days_before'] = ( isset($_POST['send_days_before']) ) ? (int)$_POST['send_days_before'] : 7;
		$dlseller_options['scheduled_time'] = $_POST['scheduled_time'];
		update_option('dlseller', $dlseller_options);
		$usces->action_status = 'success';
		$usces->action_message = __('options are updated', 'usces');

		if( ($_POST['scheduled_time']['hour'] != $_POST['scheduled_time_before']['hour']) || 
			($_POST['scheduled_time']['min'] != $_POST['scheduled_time_before']['min']) ) {
			$add = 1;
			$today = date( 'Y-m-d', current_time('timestamp') );
			$table_name = $wpdb->prefix."usces_access";
			$query = $wpdb->prepare( "SELECT acc_date FROM {$table_name} WHERE acc_key = %s LIMIT 1", 'wcdl_event' );
			$acc_date = $wpdb->get_var( $query );
			if( $acc_date && $acc_date != $today ) {
				$now = date( 'Hi', current_time('timestamp') );
				if( $now < $_POST['scheduled_time']['hour'].$_POST['scheduled_time']['min'] ) {
					$add = 0;
				}
			}
			dlseller_event_clear();
			dlseller_schedule_event( $add );
		}
	}

	if(empty($usces->action_message) || $usces->action_message == '') {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}

	require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_dlseller_page.php');
}

/* continue member list page */
function continue_member_list_page() {
	global $usces;
	if(empty($usces->action_message) || $usces->action_message == '') {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}

	$continue_action = isset($_REQUEST['continue_action']) ? $_REQUEST['continue_action'] : '';
	do_action( 'dlseller_action_continue_member_list_page', $continue_action );
	switch ($continue_action) {
		case 'dlcontinuememberlist':
			dlseller_download_continue_member_list();
			break;
		case 'editpost':
			$res = usces_update_continue_memberdata();
			if ( 1 === $res ) {
				$usces->set_action_status('success', __('Continuation charging information is updated','dlseller'));
			} elseif ( 0 === $res ) {
				$usces->set_action_status('none', '');
			} else {
				$usces->set_action_status('error', 'ERROR : '.__('failure in update','usces'));
			}
			require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_continue_member_edit_form.php');
			break;
		case 'edit':
			require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_continue_member_edit_form.php');
			break;
		default:
			require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_continue_member_list_page.php');
	}

}

function dlseller_get_item() {
	global $usces;
	$args = func_get_args();
	$usces_item = isset($args[0]) ? $args[0] : array();
	if(isset($args[1])) {
		$post_id = $args[1];
		$item_division = get_post_meta($post_id, '_item_division', true);
		$item_charging_type = get_post_meta($post_id, '_item_charging_type', true);
		$item_frequency = get_post_meta($post_id, '_item_frequency', true);
		$dlseller_interval = get_post_meta($post_id, '_dlseller_interval', true);
		$dlseller_validity = get_post_meta($post_id, '_dlseller_validity', true);
		$dlseller_file = get_post_meta($post_id, '_dlseller_file', true);
		$dlseller_date = get_post_meta($post_id, '_dlseller_date', true);
		$dlseller_version = get_post_meta($post_id, '_dlseller_version', true);
		$dlseller_author = get_post_meta($post_id, '_dlseller_author', true);
		$dlseller_purchases = get_post_meta($post_id, '_dlseller_purchases', true);
		$dlseller_downloads = get_post_meta($post_id, '_dlseller_downloads', true);
		$usces_item['item_division'] = !empty($item_division) ? $item_division : 'shipped';
		$usces_item['item_charging_type'] = !empty($item_charging_type) ? $item_charging_type : 0;
		$usces_item['item_frequency'] = !empty($item_frequency) ? $item_frequency : 1;
		$usces_item['item_chargingday'] = $usces->getItemChargingDay( $post_id );
		$usces_item['dlseller_interval'] = !empty($dlseller_interval) ? $dlseller_interval : '';
		$usces_item['dlseller_validity'] = !empty($dlseller_validity) ? $dlseller_validity : '';
		$usces_item['dlseller_file'] = !empty($dlseller_file) ? $dlseller_file : '';
		$usces_item['dlseller_date'] = !empty($dlseller_date) ? $dlseller_date : '';
		$usces_item['dlseller_version'] = !empty($dlseller_version) ? $dlseller_version : '';
		$usces_item['dlseller_author'] = !empty($dlseller_author) ? $dlseller_author : '';
		$usces_item['dlseller_purchases'] = (int)$dlseller_purchases;
		$usces_item['dlseller_downloads'] = (int)$dlseller_downloads;
	}
	return $usces_item;
}

function dlseller_get_validityperiod( $mid, $post_id, $order_id = '', $order_date = '' ) {
	global $usces;

	$firstdate = '';
	if( !empty($order_id) ) {
		$receipted_date = $usces->get_order_meta_value( 'receipted_date', $order_id );
		if( $receipted_date ) {
			$firstdate = $receipted_date;
		} else {
			$firstdate = $order_date;
		}
	} else {
		$history = $usces->get_member_history($mid);
		foreach ( $history as $row ) {
			$receipted_date = '';
			if(strpos($row['order_status'], 'cancel') !== false || strpos($row['order_status'], 'estimate') !== false){
				continue;
			}else{
				$receipted_date = $usces->get_order_meta_value('receipted_date', $row['ID']);
				$carts = $row['cart'];
				foreach($carts as $cart){
					if( $post_id == $cart['post_id'] ){
						if( $receipted_date ){
							$firstdate = $receipted_date;
						}else{
							$firstdate = $row['order_date'];
						}
						break 2;
					}
				}
			}
		}
	}

	if( !$firstdate ){
		$res = array('firstdate'=>NULL, 'lastdate'=>NULL);
	}else{
		$item = $usces->get_item( $post_id );
		if( empty($item['dlseller_validity']) ){
			//$res = array( 'firstdate'=>(mysql2date(__('Y/m/d'), $firstdate)), 'lastdate'=>__('No limit', 'dlseller'),  'firstdates'=>date('Y/m/d', strtotime($firstdate)), 'lastdates'=>'' );
			$res = array( 'firstdate'=>(mysql2date(__('Y/m/d'), $firstdate)), 'lastdate'=>NULL,  'firstdates'=>date('Y/m/d', strtotime($firstdate)), 'lastdates'=>'' );
		}else{
			$t = getdate(strtotime($firstdate));
			$hour = empty($t['hour']) ? 0 : $t['hour'];
			$min = empty($t['minutes']) ? 0 : $t['minutes'];
			$sec = empty($t['seconds']) ? 0 : $t['seconds'];
			$month = empty($t['mon']) ? 0 : $t['mon'];
			$day = empty($t['mday']) ? 0 : $t['mday'];
			$year = empty($t['year']) ? 0 : $t['year'];
			$lastdate = date('Y-m-d H:i:s',  mktime($hour, $min, $sec, $month, $day+$item['dlseller_validity'], $year) );
			$res = array( 'firstdate'=>(mysql2date(__('Y/m/d'), $firstdate)), 'lastdate'=>(mysql2date(__('Y/m/d'), $lastdate)), 'firstdates'=>date('Y/m/d', strtotime($firstdate)), 'lastdates'=>(date('Y/m/d', strtotime($lastdate))) );
		}
	}
	return $res;
}

function dlseller_member_check() {
	global $usces;
	$mes = '';
	foreach ( $_POST['member'] as $key => $vlue ) {
			if( 'password1' !== $key && 'password2' !== $key ){
				$_SESSION['usces_member'][$key] = trim($vlue);
			}
	}
	$member_pass_rule_min = $usces->options['system']['member_pass_rule_min'];
	$member_pass_rule_max = $usces->options['system']['member_pass_rule_max'];

	if ( !WCUtils::is_blank($_POST['member']['password1']) || !WCUtils::is_blank($_POST['member']['password2']) ){
		if( !empty( $member_pass_rule_max ) ){
			if( $member_pass_rule_min > strlen( trim($_POST['member']['password1']) ) || strlen( trim($_POST['member']['password1']) ) > $member_pass_rule_max ){
				$mes .= sprintf(__('Please enter %2$s characters a minimum of %1$s characters and a maximum password.', 'usces'), $member_pass_rule_min, $member_pass_rule_max ) . "<br />";
			}
		}else{
			if( $member_pass_rule_min > strlen( trim($_POST['member']['password1']) ) ){
				$mes .= sprintf(__('Please enter at least %s characters password.', 'usces'), $member_pass_rule_min) . "<br />";
			}
		}
	}
	if ( $_POST['member_regmode'] == 'editmemberform' ) {
		if ( (!WCUtils::is_blank($_POST['member']['password1']) || !WCUtils::is_blank($_POST['member']['password2']) ) && trim($_POST['member']['password1']) != trim($_POST['member']['password2']) )
			$mes .= __('Password is not correct.', 'usces') . "<br />";

		if ( !is_email($_POST['member']['mailaddress1']) || WCUtils::is_blank($_POST['member']['mailaddress1']) )
			$mes .= __('e-mail address is not correct', 'usces') . "<br />";
			
	} else {
		if ( WCUtils::is_blank($_POST['member']['password1']) || WCUtils::is_blank($_POST['member']['password2']) || trim($_POST['member']['password1']) != trim($_POST['member']['password2']) )
			$mes .= __('Password is not correct.', 'usces') . "<br />";
		if ( !is_email($_POST['member']['mailaddress1']) || WCUtils::is_blank($_POST['member']['mailaddress1']) || WCUtils::is_blank($_POST['member']['mailaddress2']) || trim($_POST['member']['mailaddress1']) != trim($_POST['member']['mailaddress2']) )
			$mes .= __('e-mail address is not correct', 'usces') . "<br />";
		
	}
	if ( WCUtils::is_blank($_POST["member"]["name1"]) )
		$mes .= __('Name is not correct', 'usces') . "<br />";

	$dlseller_opts = get_option('dlseller');
	if( 'on' == $dlseller_opts['dlseller_member_reinforcement'] ){
//		if ( trim($_POST["member"]["name3"]) == "" && USCES_JP )
//			$mes .= __('Invalid CANNAT pretend.', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["member"]["zipcode"]) )
			$mes .= __('postal code is not correct', 'usces') . "<br />";
		if ( $_POST["member"]["pref"] == __('-- Select --', 'usces') || $_POST["member"]["pref"] == '-- Select --' )
			$mes .= __('enter the prefecture', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["member"]["address1"]) )
			$mes .= __('enter the city name', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["member"]["address2"]) )
			$mes .= __('enter house numbers', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["member"]["tel"]) )
			$mes .= __('enter phone numbers', 'usces') . "<br />";
		if( !WCUtils::is_blank($_POST['member']["tel"]) && preg_match("/[^\d-]/", trim($_POST["member"]["tel"])) )
			$mes .= __('Please input a phone number with a half size number.', 'usces') . "<br />";
	}

	if( $_POST['member_regmode'] !== 'editmemberform' && isset( $usces->options['agree_member']) && 'activate' === $usces->options['agree_member'] ){
		if( !isset($_POST['agree_member_check']) ){
			$mes .= __('Please accept the membership agreement.', 'usces') . "<br />";
		}
	}

	$mes = usces_filter_member_check_custom_member( $mes );
	$mes = apply_filters( 'dlseller_filter_member_check', $mes );

	return $mes;
}

function dlseller_customer_check($mes) {
	global $usces;
	$mes = '';

	if( dlseller_have_shipped() ){

		if ( !is_email($_POST['customer']['mailaddress1']) || WCUtils::is_blank($_POST['customer']['mailaddress1']) || WCUtils::is_blank($_POST['customer']['mailaddress2']) || trim($_POST['customer']['mailaddress1']) != trim($_POST['customer']['mailaddress2']) )
			$mes .= __('e-mail address is not correct', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["customer"]["name1"]) )
			$mes .= __('Name is not correct', 'usces') . "<br />";
//		if ( trim($_POST["customer"]["name3"]) == "" && USCES_JP )
//			$mes .= __('Invalid CANNAT pretend.', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["customer"]["zipcode"]) )
			$mes .= __('postal code is not correct', 'usces') . "<br />";
		if ( $_POST["customer"]["pref"] == __('-- Select --', 'usces') || $_POST["customer"]["pref"] == '-- Select --' )
			$mes .= __('enter the prefecture', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["customer"]["address1"]) )
			$mes .= __('enter the city name', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["customer"]["address2"]) )
			$mes .= __('enter house numbers', 'usces') . "<br />";
		if ( WCUtils::is_blank($_POST["customer"]["tel"]) )
			$mes .= __('enter phone numbers', 'usces') . "<br />";
		if( !WCUtils::is_blank($_POST['customer']["tel"]) && preg_match("/[^\d-]/", trim($_POST["customer"]["tel"])) )
			$mes .= __('Please input a phone number with a half size number.', 'usces') . "<br />";
	}

	$mes = usces_filter_customer_check_custom_customer( $mes );
	$mes = apply_filters( 'dlseller_filter_customer_check', $mes );

	return $mes;
}

function dlseller_delivery_check($mes) {
	global $usces;
	$mes = '';
	$ses = '';
	$entries = $usces->cart->get_entry();

	if( dlseller_have_shipped() ){

		if ( isset($_POST['delivery']['delivery_flag']) && $_POST['delivery']['delivery_flag'] == 1 ) {
			if ( WCUtils::is_blank($_POST["delivery"]["name1"]) )
				$mes .= __('Name is not correct', 'usces');
//			if ( trim($_POST["delivery"]["name3"]) == "" && USCES_JP )
//				$mes .= __('Invalid CANNAT pretend.', 'usces') . "<br />";
			if ( WCUtils::is_blank($_POST["delivery"]["zipcode"]) )
				$mes .= __('postal code is not correct', 'usces') . "<br />";
			if ( $_POST["delivery"]["pref"] == __('-- Select --', 'usces') || $_POST["delivery"]["pref"] == '-- Select --' )
				$mes .= __('enter the prefecture', 'usces') . "<br />";
			if ( WCUtils::is_blank($_POST["delivery"]["address1"]) )
				$mes .= __('enter the city name', 'usces') . "<br />";
			if ( WCUtils::is_blank($_POST["delivery"]["address2"]) )
				$mes .= __('enter house numbers', 'usces') . "<br />";
			if ( WCUtils::is_blank($_POST["delivery"]["tel"]) )
				$mes .= __('enter phone numbers', 'usces') . "<br />";
		}else{
			if( empty($entries['customer']['name1']) )
				$ses .= __('Name is not correct', 'usces');
			if( empty($entries['customer']['zipcode']) )
				$ses .= __('postal code is not correct', 'usces') . "<br />";
			if( $entries['customer']['pref'] == __('-- Select --', 'usces') )
				$ses .= __('enter the prefecture', 'usces') . "<br />";
			if( empty($entries['customer']['address1']) )
				$ses .= __('enter the city name', 'usces') . "<br />";
			if( empty($entries['customer']['address2']) )
				$ses .= __('enter house numbers', 'usces') . "<br />";
			if( empty($entries['customer']['tel']) )
				$ses .= __('enter phone numbers', 'usces') . "<br />";

			if( !empty($ses) ){
				$_SESSION['usces_entry']['delivery']['delivery_flag'] = 1;
				$mes .= $ses;
			}

		}
		if ( !isset($_POST['offer']['delivery_method']) || (empty($_POST['offer']['delivery_method']) && !WCUtils::is_zero($_POST['offer']['delivery_method'])) ) {
			$mes .= __('chose one from delivery method.', 'usces') . "<br />";
		} else {
			$d_method_index = $usces->get_delivery_method_index((int)$_POST['offer']['delivery_method']);
			$country = $_SESSION['usces_entry']["delivery"]["country"];
			$local_country = usces_get_base_country();
			if($country == $local_country) {
				if($usces->options['delivery_method'][$d_method_index]['intl'] == 1) {
					$mes .= __('Delivery method is incorrect. Can not specify an international flight.', 'usces') . "<br />";
				}
			} else {
				if( WCUtils::is_zero($usces->options['delivery_method'][$d_method_index]['intl']) ) {
					$mes .= __('Delivery method is incorrect. Specify the international flights.', 'usces') . "<br />";
				}
			}
		}
	}
	if ( !isset($_POST['offer']['payment_name']) ){
		$mes .= __('chose one from payment options.', 'usces') . "<br />";
	}else{
		if( dlseller_have_shipped() ){
			$payments = $usces->getPayments($_POST['offer']['payment_name']);
			if('COD' == $payments['settlement']){
				$total_items_price = $usces->get_total_price();
				$usces_entries = $usces->cart->get_entry();
				$materials = array(
					'total_items_price' => $usces_entries['order']['total_items_price'],
					'discount' => $usces_entries['order']['discount'],
					'shipping_charge' => $usces_entries['order']['shipping_charge'],
					'cod_fee' => $usces_entries['order']['cod_fee'],
					'use_point' => ( isset($usces_entries['order']['use_point']) ) ? $usces_entries['order']['use_point'] : 0,
				);
				$tax = $usces->getTax( $total_items_price, $materials );
				$total_items_price = $total_items_price + $tax;
				$cod_limit_amount = ( isset($usces->options['cod_limit_amount']) && 0 < (int)$usces->options['cod_limit_amount'] ) ? $usces->options['cod_limit_amount'] : 0;
				if( 0 < $cod_limit_amount && $total_items_price > $cod_limit_amount )
					$mes .= sprintf(__('A total products amount of money surpasses the upper limit(%s) that I can purchase in C.O.D.', 'usces'), usces_crform($usces->options['cod_limit_amount'], true, false, 'return')) . "<br />";
			}
		}
	}
	if( dlseller_have_shipped() ){
		if( isset($d_method_index) && isset($payments) ) {
			if($usces->options['delivery_method'][$d_method_index]['nocod'] == '1') {
				if('COD' == $payments['settlement'])
					$mes .= __('COD is not available.', 'usces') . "<br />";
			}
		}
	}
	$mes = usces_filter_delivery_check_custom_order( $mes );
	$mes = apply_filters( 'dlseller_filter_delivery_check', $mes );

	if ( !isset($_POST['offer']['terms']) && dlseller_have_dlseller_content() )
		$mes .= __('Not agree', 'dlseller') . "<br />";

	return $mes;
}

function dlseller_pre_reg_orderdata() {
	if( !dlseller_have_shipped() ) {
		$_SESSION['usces_entry']['order']['delivery_method'] = -1;
	}
}

function add_dlseller_stylesheet() {
	$dlsellerStyleUrl = USCES_PLUGIN_URL . '/wcex_dlseller/dlseller.css';
	$dlsellerStyleFile = USCES_PLUGIN_DIR . '/wcex_dlseller/dlseller.css';
	if ( file_exists($dlsellerStyleFile) ) {
		wp_register_style('dlsellerStyleSheets', $dlsellerStyleUrl);
		wp_enqueue_style( 'dlsellerStyleSheets');
	}
}

function usces_dlseller_validity($post) {
	$validity = usces_get_itemMeta('_dlseller_validity', $post->ID, 'return');
	if ( empty($validity) )
		$res = __('No limit', 'dlseller');
	else
		$res = $validity;

	return $res;
}

function usces_dlseller_get_dlcount($post_id, $piriod = 'total', $mon = 0){
	global $usces;
	//$today = substr(get_date_from_gmt(gmdate('Y-m-d H:i:s', time())), 0, 10);
	$today = current_time('Y-m-d');
	switch ($piriod){
		case 'today':
			$startday = $today;
			$endday = $today;
			break;
		case 'month':
			if($mon){
				$startday = $mon . '01';
				$endday = date('Y-m-d', mktime(0,0,0,(substr($mon, 5, 2)+1), 0, substr($mon, 0, 4)));
			}else{
				$startday = substr($today, 0, 8) . '01';
				$endday = date('Y-m-d', mktime(0,0,0,(substr($today, 5, 2)+1), 0, substr($today, 0, 4)));
			}
			break;
		case 'total':
			$startday = '2000-01-01';
			$endday = $today;
			break;
	}
	$data = $usces->get_access_piriod('dlseller', 'count', $startday, $endday);
	if( $data === false ){
		$res = NULL;
	}else{
		$res = array('par' => 0, 'dl' => 0);
		$par = 0;
		$dl = 0;
		foreach( $data as $values ){
			$vals = unserialize($values['acc_value']);
			foreach( (array)$vals as $key => $dls ){
				if($key == $post_id){
					$par += $dls['par'];
					$dl += $dls['dl'];
				}
			}
		}
		$res = array('par' => $par, 'dl' => $dl);
	}
	return $res;
}

function usces_dlseller_update_dlcount($post_id, $par=0, $dl=0 ){
	global $usces;
	//$today = substr(get_date_from_gmt(gmdate('Y-m-d H:i:s', time())), 0, 10);
	$today = current_time('Y-m-d');

	$values = $usces->get_access('dlseller', 'count', $today);
	if( $values === NULL || !isset($values[$post_id]) ){
		$values[$post_id]['par'] = $par;
		$values[$post_id]['dl'] = $dl;
	}else{
		$values[$post_id]['par'] += $par;
		$values[$post_id]['dl'] += $dl;
	}

	$array = array();
	$array['acc_key'] = 'dlseller';
	$array['acc_type'] = 'count';
	$array['acc_value'] = $values;
	$array['acc_date'] = $today;

	$usces->update_access($array);
}

function usces_dlseller_filename($post, $sku = '') {
	if( is_object($post) ){
		$post_id = $post->ID;
	}else{
		$post_id = $post;
	}
	$path = dlseller_get_filename( $post_id, $sku );
	if ( empty($path) )
		$res = '';
	else
		$res = basename($path);

	return $res;
}

function dlseller_results_csv(){

	$content = 'AAA';
	$filename = 'dlseller_result.csv';

	header( "Content-Disposition: attachment; filename=" . $filename);
	header( "Content-Type: application/octet-stream" );
	//header( "Content-Length: " . $content_length ) ;
	print $content;

	exit;
}

function dlseller_action_reg_orderdata( $args ){
	global $usces, $wpdb;
	if( !isset($args['charging_type'] ) || 'continue' != $args['charging_type'] ) return;

	//$args = array('cart'=>$cart, 'entry'=>$entry, 'order_id'=>$order_id, 'member_id'=>$member['ID'], 'payments'=>$set, 'charging_type'=>$charging_type, 'results'=>$results);
	extract( $args );

	//$key = 'continuepay_' . $order_id;
	//$order_date = substr($args['order_date'], 0, 10);
	$order_data = $usces->get_order_data($order_id);
	//$order_date = $order_data['date'];
	//$carts = $order_data['cart'];
	$startdate = date( 'Y-m-d', dlseller_first_charging( $cart[0]['post_id'], 'time' ) );
	$usces_item = $usces->get_item( $cart[0]['post_id'] );
	$frequency = ( !empty($usces_item['item_frequency']) ) ? (int)$usces_item['item_frequency'] : 0;
	if( !empty($usces_item['item_chargingday']) ) {
		if( 99 == (int)$usces_item['item_chargingday'] ) {
			$chargingday = intval(substr($startdate, 8, 2));
		} else {
			$chargingday = (int)$usces_item['item_chargingday'];
		}
	} else {
		$chargingday = intval(substr($startdate, 8, 2));
	}
	$interval = ( !empty($usces_item['dlseller_interval']) ) ? (int)$usces_item['dlseller_interval'] : NULL;

/*	$value = array(
			'price' => $order_data['end_price'],
			'acting' => $payments['payment_name'],
			'startdate' => $startdate,
			'interval' => $usces_item['dlseller_interval'],
			'chargingday' => $usces_item['item_chargingday'],
			'frequency' => $usces_item['item_frequency'],
			'condition' => '',
			'status' => 'continuation',
			'chargedday' => NULL,
			'contractedday' => NULL
			);

	//$usces->set_member_meta_value($key, serialize($value));
	$usces->set_member_meta_value( $key, serialize($value), $member_id );
*/
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = $wpdb->prepare( "INSERT INTO {$continuation_table} ( `con_order_id`, `con_member_id`, `con_division`, `con_post_id`, `con_sku`, `con_acting`, `con_order_price`, `con_price`, `con_frequency`, `con_chargingday`, `con_interval`, `con_next_charging`, `con_next_contracting`, `con_startdate`, `con_condition`, `con_status`) VALUES
		( %d, %d, %s, %d, %s, %s, %f, %f, %d, %d, %d, %s, %s, %s, %s, %s )",
		$order_id, 
		$member_id, 
		$usces_item['item_division'], 
		$cart[0]['post_id'], 
		$cart[0]['sku'], 
		$payments['settlement'], 
		$order_data['end_price'], 
		$order_data['end_price'], 
		$frequency, 
		$chargingday, 
		$interval, 
		'', 
		'', 
		$startdate, 
		'', 
		'continuation' 
	);
	$res = $wpdb->query( $query );
}

function dlseller_action_del_orderdata( $obj ){
	global $usces, $wpdb;
	if( !$obj ) return;

	//$member_id = $obj->mem_id;
	//$key = 'continuepay_' . $obj->ID;
	//$usces->del_member_meta($key, $member_id);
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$continuation_meta_table = $wpdb->prefix.'usces_continuation_meta';
	$query = $wpdb->prepare( "SELECT con_id FROM {$continuation_table} WHERE con_order_id = %d", $obj->ID );
	$res = $wpdb->get_results( $query, ARRAY_A );
	foreach( $res as $con_id ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$continuation_meta_table} WHERE `con_id` = %d", $con_id ) );
	}
	$query = $wpdb->prepare( "DELETE FROM {$continuation_table} WHERE `con_order_id` = %d", $obj->ID );
	$res = $wpdb->query( $query );
}

//function dlseller_action_update_orderdata( $obj ){
//function dlseller_action_update_orderdata( $new_orderdata, $old_status, $old_orderdata ){
function dlseller_action_update_orderdata( $new_orderdata, $old_status, $old_orderdata, $new_cart, $old_cart ){
	global $usces, $wpdb;
	//if( !$obj ) return;
	//$order_data = $usces->get_order_data($obj->ID);
	//$carts = $order_data['cart'];
	if( !$new_orderdata ) return;
	//$carts = unserialize( $new_orderdata->order_cart );
	if( !dlseller_have_continue_charge( $new_cart ) ) return;

	//$member_id = $obj->mem_id;
	$member_id = $new_orderdata->mem_id;
	//$key = 'continuepay_' . $obj->ID;
/*	$key = 'continuepay_' . $new_orderdata->ID;
	$continues = $usces->get_member_meta_value($key, $member_id);
	if( empty($continues) ) 
		$continues = serialize(array());

	$usces_item = $usces->get_item( $new_cart[0]['post_id'] );

	$value = unserialize($continues);
	$condition = isset( $value['condition'] ) ? $value['condition'] : '';
	$chargedday = isset( $value['chargedday'] ) ? $value['chargedday'] : NULL;
	$contractedday = isset( $value['contractedday'] ) ? $value['contractedday'] : NULL;
	//$status = ( false !== strpos($obj->order_status, 'cancel') ) ? 'cancellation' : 'continuation';
	$status = ( false !== strpos($new_orderdata->order_status, 'cancel') ) ? 'cancellation' : 'continuation';
	if( false !== strpos($new_orderdata->order_status, 'completion') || false !== strpos($new_orderdata->order_status, 'continuation') ) {
		if( 'update' == $_POST['up_modified'] ) {
			$startdate = $new_orderdata->order_modified;
		} else {
			$startdate = $value['startdate'];
		}
	} else {
		$startdate = ( empty($value['startdate']) ) ? $new_orderdata->order_date : $value['startdate'];
	}
	

	$value = array(
			'price' => $usces->get_total_price( $new_cart ),
			'acting' => $new_orderdata->order_payment_name,
			'startdate' => $startdate,
			'interval' => $usces_item['dlseller_interval'],
			'chargingday' => $usces_item['item_chargingday'],
			'frequency' => $usces_item['item_frequency'],
			'condition' => $condition,
			'status' => $status,
			'chargedday' => $chargedday,
			'contractedday' => $contractedday
			);

	$usces->set_member_meta_value($key, serialize($value), $member_id);
*/
	$total_full_price = $usces->get_total_price($new_cart) - $new_orderdata->order_usedpoint + $new_orderdata->order_discount + $new_orderdata->order_shipping_charge + $new_orderdata->order_tax;
	if( $total_full_price < 0 ) $total_full_price = 0;

	$continuation_table = $wpdb->prefix.'usces_continuation';
	$sel_query = $wpdb->prepare( "SELECT * FROM {$continuation_table} WHERE `con_order_id` = %d", $new_orderdata->ID );
	$continue_data = $wpdb->get_row( $sel_query, ARRAY_A );
	$usces_item = $usces->get_item( $new_cart[0]['post_id'] );

	$con_id = $continue_data['con_id'];
	$status = ( false !== strpos($new_orderdata->order_status, 'cancel') ) ? 'cancellation' : 'continuation';
	if( false !== strpos($new_orderdata->order_status, 'completion') || false !== strpos($new_orderdata->order_status, 'continuation') ) {
		if( 'update' == $_POST['up_modified'] ) {
			$startdate = $new_orderdata->order_modified;
		} else {
			$startdate = $continue_data['con_startdate'];
		}
	} else {
		$startdate = ( empty($continue_data['con_startdate']) ) ? $new_orderdata->order_date : $continue_data['con_startdate'];
	}
	$payments = $usces->getPayments( $new_orderdata->order_payment_name );
	$frequency = ( !empty($usces_item['item_frequency']) ) ? (int)$usces_item['item_frequency'] : 0;
	if( !empty($usces_item['item_chargingday']) ) {
		if( 99 == (int)$usces_item['item_chargingday'] ) {
			$chargingday = intval(substr($startdate, 8, 2));
		} else {
			$chargingday = (int)$usces_item['item_chargingday'];
		}
	} else {
		$chargingday = intval(substr($startdate, 8, 2));
	}
	$interval = ( !empty($usces_item['dlseller_interval']) ) ? (int)$usces_item['dlseller_interval'] : NULL;

	$upd_query = $wpdb->prepare( "UPDATE {$continuation_table} SET 
		`con_post_id` = %d, 
		`con_sku` = %s, 
		`con_acting` = %s, 
		`con_price` = %f, 
		`con_frequency` = %d, 
		`con_chargingday` = %d, 
		`con_interval` = %d, 
		`con_startdate` = %s, 
		`con_status` = %s 
		WHERE `con_id` = %d", 
		$new_cart[0]['post_id'], 
		$new_cart[0]['sku_code'], 
		$payments['settlement'], 
		$total_full_price, 
		$frequency, 
		$chargingday, 
		$interval, 
		$startdate, 
		$status, 
		$con_id
	);
	$res = $wpdb->query( $upd_query );

	$limit = $usces->get_member_meta_value('limitofcard', $member_id);
	if( $limit ) {
		$lmitparts = explode('/', $limit);
		if( 4 == strlen($lmitparts[0]) ){
			$limit = $lmitparts[1] . '/' . $lmitparts[0];
			$usces->set_member_meta_value('limitofcard', $limit, $member_id);
		}
	}
}

function dlseller_trackPageview_redownload($push){
	$push[] = "'_trackPageview','/wc_redownload'";
	return $push;
}

function usces_ssl_attachment_uli_incontent($content){
	global $usces;
	if( $usces->is_cart_or_member_page($_SERVER['REQUEST_URI']) || $usces->is_inquiry_page($_SERVER['REQUEST_URI']) ){
		$content = str_replace(('src="'.get_option('siteurl')), ('src="'.USCES_SSL_URL_ADMIN), $content);
	}
	return $content;
}

function dlseller_filter_js_intoCart(){
	global $usces;
	$args = func_get_args();
	$str = $args[0];
	$post_id = $args[1];
	$js = '';

	$division = dlseller_get_division( $post_id );
	$charging_type = $usces->getItemChargingType( $post_id );

	if( false !== $usces->cart->num_row() && 'continue' == $charging_type ){
		if( dlseller_have_continue_charge() ) {
			$js = "if(confirm('" . __('You can add only one continuation charging item in your shopping cart.', 'dlseller') . "')){\n";
			$js .= "return true;\n";
			$js .= "}else{\n";
			$js .= "return false;\n";
			$js .= "}\n";
		}else{
			$js = "if(confirm('" . __('You have the continuation charging item in your shopping cart. If you want to add this item, you have to clear the item in your cart. Is it ok to clear your cart?', 'dlseller') . "')){\n";
			$js .= "return true;\n";
			$js .= "}else{\n";
			$js .= "return false;\n";
			$js .= "}\n";
		}
	}else if( false !== $usces->cart->num_row() ){
		if( dlseller_have_continue_charge() ) {
			$js = "if(confirm('" . __('This is the continuation charging item. If you want to add this item, you have to clear your cart. Is it ok to clear your cart?', 'dlseller') . "')){\n";
			$js .= "return true;\n";
			$js .= "}else{\n";
			$js .= "return false;\n";
			$js .= "}\n";
		}
	}

	return $js;
}

function dlseller_filter_admin_modified_label(){
	return __('Modified', 'dlseller');
}

function dlseller_filter_confirm_prebutton_value(){
	return __('Back', 'usces');
}
//loop
function dlseller_filter_order_mail_meisai(){
	global $usces, $wpdb;
	$args = func_get_args();
	$meisai = $args[0];
	$data = $args[1];
	$order_id = $data['ID'];
	//$cart = unserialize($data['order_cart']);
	$cart = usces_get_ordercartdata( $order_id );
	$post_id = $cart[0]['post_id'];
	$charging_type = $usces->getItemChargingType( $post_id );

	if( 'continue' != $charging_type ) return $meisai;

	$cart_row = $cart[0];
	//$sku = $cart_row['sku'];
	//$sku_code = urldecode($cart_row['sku']);
	$sku_code = $cart_row['sku_code'];
	$quantity = $cart_row['quantity'];
	$options = ( empty($cart_row['options']) ) ? array() : $cart_row['options'];
	$cartItemName = $usces->getCartItemName($post_id, $sku_code);
	$skuPrice = $cart_row['price'];
	$item_custom = usces_get_item_custom( $post_id, 'notag', 'return' );
	$usces_item = $usces->get_item( $post_id );
	//$continue_data = unserialize($usces->get_member_meta_value('continuepay_' . $order_id, $data['mem_id']));

	$meisai = "\r\n".__('Items','usces')." : \r\n";
	$meisai .= usces_mail_line( 2, $data['order_email'] );//--------------------
	$meisai .= apply_filters('usces_filter_cart_item_name_nl', $cartItemName, $args). "\r\n\r\n";
	if( is_array($options) && count($options) > 0 ){
		foreach($options as $key => $value){
			if( !empty($key) ) {
				$key = urldecode($key);
				if(is_array($value)) {
					$c = '';
					$meisai .= $key . ' : ';
					foreach($value as $v) {
						$meisai .= $c.urldecode($v);
						$c = ', ';
					}
					$meisai .= "\r\n"; 
				} else {
					$meisai .= $key . ' : ' . urldecode($value) . "\r\n"; 
				}
			}
		}
	}
	$meisai .= __('Unit price','usces') . " ".usces_crform( $skuPrice, true, false, 'return' ) . __(' * ','usces') . $cart_row['quantity'] . "\r\n";
	$meisai .= usces_mail_line( 3, $data['order_email'] );//====================
	$meisai .= __('total items','usces') . " : " . usces_crform( $data['order_item_total_price'], true, false, 'return' ) . "\r\n";

	if( defined('USCES_VERSION') and version_compare( USCES_VERSION, '1.5', '>=' ) ) {
		if( $data['order_discount'] != 0 )
			$meisai .= apply_filters('usces_confirm_discount_label', __('Campaign disnount', 'usces'), $order_id) . " : " . usces_crform( $data['order_discount'], true, false, 'return' ) . "\r\n";
		if( usces_is_tax_display() ) {
			$meisai .= usces_tax_label( $data, 'return' );
			if( 'exclude' == usces_get_tax_mode() ) $meisai .= " : " . usces_tax( $data, 'return' );
			$meisai .= "\r\n";
		}
		if( $data['order_usedpoint'] != 0 )
			$meisai .= __('use of points','usces') . " : " . number_format($data['order_usedpoint']) . __('Points','usces') . "\r\n";
	} elseif( defined('USCES_VERSION') and version_compare( USCES_VERSION, '1.4-beta', '>=' ) ) {
		if( $data['order_discount'] != 0 )
			$meisai .= apply_filters('usces_confirm_discount_label', __('Campaign disnount', 'usces'), $order_id) . " : " . usces_crform( $data['order_discount'], true, false, 'return' ) . "\r\n";
		if ( 0.00 < (float)$data['order_tax'] )
			$meisai .= usces_tax_label($data, 'return') . " : " . usces_crform( $data['order_tax'], true, false, 'return' ) . "\r\n";
		if( $data['order_usedpoint'] != 0 )
			$meisai .= __('use of points','usces')." : ".number_format($data['order_usedpoint']).__('Points','usces')."\r\n";
	} else {
		if ( $data['order_usedpoint'] != 0 )
			$meisai .= __('use of points','usces') . " : " . number_format($data['order_usedpoint']) . __('Points','usces') . "\r\n";
		if ( $data['order_discount'] != 0 )
			$meisai .= apply_filters('usces_confirm_discount_label', __('Campaign disnount', 'usces'), $order_id) . " : " . usces_crform( $data['order_discount'], true, false, 'return' ) . "\r\n";
		if ( 'shipped' == dlseller_get_division( $post_id ) ) {
			$meisai .= __('Shipping','usces') . " : " . usces_crform( $data['order_shipping_charge'], true, false, 'return' ) . "\r\n";
		}
		if ( !empty($usces->options['tax_rate']) )
			$meisai .= __('consumption tax','usces') . " : " . usces_crform( $data['order_tax'], true, false, 'return' ) . "\r\n";
	}
	$total_full_price = $data['order_item_total_price'] - $data['order_usedpoint'] + $data['order_discount'] + $data['order_shipping_charge'] + $data['order_tax'];
	if( $total_full_price < 0 ) $total_full_price = 0;
	$meisai .= usces_mail_line( 2, $data['order_email'] );//--------------------
	$meisai .= __('Amount', 'usces') . '(' . dlseller_frequency_name( $post_id, 'amount', 'return' ) . ') : ' . usces_crform( $total_full_price, true, false, 'return' ) . "\r\n";
	$meisai .= usces_mail_line( 2, $data['order_email'] );//--------------------
	$meisai .= "(" . __('Currency', 'usces') . ' : ' . __(usces_crcode( 'return' ), 'usces') . ")\r\n\r\n";

	//$meisai .= __('Application Date', 'dlseller') . ' : ' . date(__('Y/m/d'), strtotime( $data['order_date'] )) . "\r\n";
	//$meisai .= __('Next Withdrawal Date', 'dlseller') . ' : ' . date(__('Y/m/d'), strtotime( dlseller_next_charging( $order_id ) )) . "\r\n";
	//if( 0 < (int)$usces_item['dlseller_interval'] )
	//	$meisai .= __('Renewal Date', 'dlseller') . ' : ' . date(__('Y/m/d'), strtotime( dlseller_next_contracting( $order_id ) )) . "\r\n";
	//$meisai .= "\r\n";
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = $wpdb->prepare( "SELECT 
		`con_interval` AS `interval`, 
		`con_next_charging` AS `next_charging`, 
		`con_next_contracting` AS `next_contracting`, 
		`con_startdate` AS `startdate` 
		FROM {$continuation_table} 
		WHERE `con_order_id` = %d", $order_id
	);
	$continue_data = $wpdb->get_row( $query, ARRAY_A );
	if( $continue_data ) {
		$meisai .= __('Application Date', 'dlseller').' : '.date(__('Y/m/d'), strtotime($continue_data['startdate']))."\r\n";
		$next_charging = ( empty($continue_data['next_charging']) ) ? dlseller_next_charging( $order_id ) : $continue_data['next_charging'];
		$meisai .= __('Next Withdrawal Date', 'dlseller').' : '.date(__('Y/m/d'), strtotime($next_charging))."\r\n";
		if( 0 < (int)$usces_item['dlseller_interval'] ) {
			$next_contracting = ( empty($continue_data['next_contracting']) ) ? dlseller_next_contracting( $order_id ) : $continue_data['next_contracting'];
			$meisai .= __('Renewal Date', 'dlseller').' : '.date(__('Y/m/d'), strtotime($next_contracting))."\r\n";
		}
		$meisai .= "\r\n";
	}

	if( $item_custom )
		$meisai .= $item_custom;

	return $meisai;
}

function dlseller_get_charging_type( $post_id ){
	global $usces;
	$charging_type = $usces->getItemChargingType( $post_id );
	return $charging_type;
}

function dlseller_get_division( $post_id ){
	$item_division = get_post_meta($post_id, '_item_division', true);
	$division = empty($item_division) ? 'shipped' : $item_division;
	return $division;
}

function dlseller_have_shipped(){
	global $usces;
	$carts = $usces->cart->get_cart();

	$division = '';
	if(!empty($carts)) {
		foreach( $carts as $index => $cart ){
			extract($cart);
			if( 'shipped' == dlseller_get_division( $post_id ) ){
				$division = 'shipped';
				break;
			}
		}
	}
	if( 'shipped' == $division )
		return true;
	else
		return false;
}

function dlseller_require_payment() {
	global $usces;
	$cart = $usces->cart->get_cart();

	$amount = 0;
	if( !empty($cart) ) {
		foreach( $cart as $cart_row ) {
			if( 'shipped' != dlseller_get_division( $cart_row['post_id'] ) ) {
				$amount += $cart_row['price'];
			}
		}
	}
	if( 0 < $amount )
		return true;
	else
		return false;
}

function dlseller_have_continue_charge( $carts = NULL ){
	global $usces;
	if( NULL == $carts )
		$carts = $usces->cart->get_cart();
	$charging_type = 'once';
	foreach( $carts as $cart ){
		if( 'continue' == $usces->getItemChargingType( $cart['post_id'] ) ){
			$charging_type = 'continue';
			break;
		}
	}
	if( 'continue' == $charging_type )
		return true;
	else
		return false;
}

function dlseller_have_dlseller_content(){
	global $usces;
	$carts = $usces->cart->get_cart();

	$division = '';
	foreach( $carts as $index => $cart ){
		extract($cart);
		$content = dlseller_get_division( $post_id );
		if( 'data' == $content || 'service' == $content ){
			$division = 'dlseller';
			break;
		}
	}
	if( 'dlseller' == $division )
		return true;
	else
		return false;
}

function dlseller_terms(){
	$dlseller_options = get_option('dlseller');
	if( dlseller_have_continue_charge() ){
		$dlseller_terms = nl2br(esc_html($dlseller_options['dlseller_terms2']));
	}else{
		$dlseller_terms = nl2br(esc_html($dlseller_options['dlseller_terms']));
	}
	echo $dlseller_terms;
}

function dlseller_completion_info($usces_carts , $out=''){
	global $usces, $usces_entries;
	$member = $usces->get_member();
	$oid = isset($usces_entries['order']['ID']) ? $usces_entries['order']['ID'] : '';
	$html = '<ul class="dllist">';

	for($i=0; $i<count($usces_carts); $i++) { 
		$cart_row = $usces_carts[$i];
		$post_id = $cart_row['post_id'];
		$sku = $cart_row['sku'];
		$sku_code = esc_attr(urldecode($cart_row['sku']));
		$item_post = get_post( $post_id );
		$usces_item = $usces->get_item( $post_id );
		$periods = dlseller_get_validityperiod($member['ID'], $post_id);
		$cartItemName = $usces->getCartItemName($post_id, $sku_code );
		$purchased = $usces->is_purchased_item($member['ID'], $post_id);
		$charging_type = $usces->getItemChargingType( $post_id );
		$item_custom = usces_get_item_custom( $post_id, 'table', 'return' );
		$options = ( empty($cart_row['options']) ) ? array() : $cart_row['options'];
		$payments = usces_get_payments_by_name($usces_entries['order']['payment_name']);
		$args = compact('usces_carts', 'i', 'cart_row', 'post_id', 'sku', 'sku_code', 'item_post', 'cartItemName' );

		$list = '<li>';
		$list .= '<div class="thumb">'."\n";
		$itemImage = usces_the_itemImage(0, 200, 250, $item_post, 'return');
		$list .= apply_filters('dlseller_filter_the_itemImage', $itemImage, $item_post);
		$list .= '</div>'."\n";

		if( 'service' == $usces_item['item_division'] ){

			if( 'continue' == $charging_type ){

				//$nextdate = get_date_from_gmt(gmdate('Y-m-d H:i:s', time()));
				$nextdate = current_time('Y/m/d');
				$chargingday = $usces->getItemChargingDay( $post_id );

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>'. apply_filters('usces_filter_cart_item_name', esc_html($cartItemName), $args ) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				//$list .= '<tr><th>' . __('Application Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), mktime(0,0,0,(int)substr($nextdate, 5, 2),(int)substr($nextdate, 8, 4),(int)substr($nextdate, 0, 4))) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Application Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), strtotime($nextdate)) . '</td></tr>'."\n";
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), strtotime( dlseller_first_charging( $post_id ) )) . '</td></tr>'."\n";
				if( 0 < (int)$usces_item['dlseller_interval'] ){
					$list .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month(Automatic Renewal)', 'dlseller') . '</td></tr>'."\n";
				}
				$list .= '</table>'."\n";
				$list .= '</div>'."\n";

			}else{

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Code', 'dlseller') . '</th><td>' . esc_html($usces_item['itemCode']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>'. apply_filters('usces_filter_cart_item_name', esc_html($cartItemName), $args ) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				$purchase_mes = '';
				if( $purchased !== true ){
					$purchase_mes = '<p>' . __('I contact you by an email if I can confirm the receipt of money.', 'dlseller') . '</p>'."\n";
				}
				$list .= apply_filters('dlseller_filter_service_purchase_message', $purchase_mes, $purchased);
				$list .= '</div>'."\n";
				$list .= '<div class="clear"></div>'."\n";
			}

		}elseif( 'data' == $usces_item['item_division'] ){

			usces_dlseller_update_dlcount($post_id, 1, 0 );
			$files = dlseller_get_filename( $post_id, $sku );
			$filename = basename($files);

			if( 'continue' == $charging_type ){

				//$nextdate = get_date_from_gmt(gmdate('Y-m-d H:i:s', time()));
				$chargingday = $usces->getItemChargingDay( $post_id );

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Code', 'dlseller') . '</th><td>' . esc_html($usces_item['itemCode']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>'. apply_filters('usces_filter_cart_item_name', esc_html($cartItemName), $args ) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '<tr><th>' . __('Version', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_version']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Release Date', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_date']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Author', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_author']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Re-download validity(days)', 'dlseller') . '</th><td>'."\n";
				if( empty($periods['lastdate']) ){
					$list .= __('No limit', 'dlseller');
				}else{
					//$list .= esc_html($periods['firstdate']) . '～' . esc_html($periods['lastdate']);
					$list .= sprintf(__("From %1$s to %2$s", 'dlseller'), esc_html($periods['firstdate']), esc_html($periods['lastdate']) );
				}
				$list .= '</td></tr>'."\n";
				$list .= '<tr><th>' . __('File Name', 'dlseller') . '</th><td>' . $filename . '</td></tr>'."\n";
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), strtotime( dlseller_first_charging( $post_id ) )) . '</td></tr>'."\n";
				if( 0 < (int)$usces_item['dlseller_interval'] ){
					$list .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month(Automatic Renewal)', 'dlseller') . '</td></tr>'."\n";
				}
				$list .= '</table>'."\n";
				$list .= '<a class="redownload_button" href="' . USCES_CART_URL . $usces->delim . 'dlseller_transition=download&rid=' . $i . '&oid=' . $oid . apply_filters('dlseller_filter_download_para', '', $post_id, $sku) . '">' . __('Download', 'dlseller') . '</a>'."\n";
				$list .= '<p>' . __('You can download it again during your subscription period.', 'dlseller') . '</p>'."\n";
				$list .= '</div>'."\n";
				$list .= '<div class="clear"></div>'."\n";

			}else{

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Code', 'dlseller') . '</th><td>' . esc_html($usces_item['itemCode']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>'. apply_filters('usces_filter_cart_item_name', esc_html($cartItemName), $args ) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '<tr><th>' . __('Version', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_version']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Release Date', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_date']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Author', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_author']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Re-download validity(days)', 'dlseller') . '</th><td>'."\n";
				
				if( empty($periods['lastdate']) ){
					$list .= __('No limit', 'dlseller');
				}elseif( 'transferAdvance' == $payments['settlement'] || 'transferDeferred' == $payments['settlement'] ){
					$list .= sprintf(__('From payment day for %s days', 'dlseller'), esc_html($usces_item['dlseller_validity']) );
				}else{
					$list .= sprintf(__('From %1$s to %2$s', 'dlseller'), esc_html($periods['firstdate']), esc_html($periods['lastdate']) );
				}
				$list .= '</td></tr>'."\n";
				$list .= '<tr><th>' . __('File Name', 'dlseller') . '</th><td>' . $filename . '</td></tr>'."\n";
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				if( $purchased !== true ){
					$purchase_mes = '<p>' . __('After the receipt of money, you can download it from your member page.', 'dlseller') . '</p>'."\n";
					$purchase_mes .= '<p>' . __('I contact you by an email if I can confirm the receipt of money.', 'dlseller') . '</p>'."\n";
				}else{
					$purchase_mes = '<a class="redownload_button" href="' . USCES_CART_URL . $usces->delim . 'dlseller_transition=download&rid=' . $i . '&oid=' . $oid . apply_filters('dlseller_filter_download_para', '', $post_id, $sku) . '">' . __('Download', 'dlseller') . '</a>'."\n";
					$purchase_mes .= '<p>' . __('You can download it again during your subscription period.', 'dlseller') . '</p>'."\n";
				}
				$list .= apply_filters('dlseller_filter_data_purchase_message', $purchase_mes, $purchased);
				$list .= '</div>'."\n";
				$list .= '<div class="clear"></div>'."\n";
			}

		}else{//'shipped' == $usces_item['item_division']

			if( 'continue' == $charging_type ){

				//$nextdate = get_date_from_gmt(gmdate('Y-m-d H:i:s', time()));
				$nextdate = current_time('Y/m/d');
				$chargingday = $usces->getItemChargingDay( $post_id );

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>'. apply_filters('usces_filter_cart_item_name', esc_html($cartItemName), $args ) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				//$list .= '<tr><th>' . __('Application Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), mktime(0,0,0,(int)substr($nextdate, 5, 2),(int)substr($nextdate, 8, 4),(int)substr($nextdate, 0, 4))) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Application Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), strtotime( $nextdate )) . '</td></tr>'."\n";
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), strtotime( dlseller_first_charging( $post_id ) )) . '</td></tr>'."\n";
				if( 0 < (int)$usces_item['dlseller_interval'] ){
					$list .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month(Automatic Renewal)', 'dlseller') . '</td></tr>'."\n";
				}
				$list .= '</table>'."\n";
				$list .= '</div>'."\n";

			}else{

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Code', 'dlseller') . '</th><td>' . esc_html($usces_item['itemCode']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>'. apply_filters('usces_filter_cart_item_name', esc_html($cartItemName), $args ) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				if( $purchased !== true ){
					$purchase_mes = '<p>' . __('I am in the dispatch of the product if I can confirm the receipt of money.', 'dlseller') . '</p>'."\n";
				}else{
					$purchase_mes = '';
				}
				$list .= apply_filters('dlseller_filter_shipped_purchase_message', $purchase_mes, $purchased);
				$list .= '</div>'."\n";
				$list .= '<div class="clear"></div>'."\n";
			}
		}

		$list .= '</li>'."\n";
		$html .= apply_filters('dlseller_filter_completion_list', $list, $cart_row, $i);
	}
	if( isset($_GET['dlseller_update']) ){
		$html .= '<li>';
		$html .= '<div class="update_info">'."\n";
		$html .= '<p>' . __('Card information update processing was completed. Thank you.', 'dlseller') . '</p>'."\n";
		$html .= '</div>'."\n";
		$html .= '</li>';
	}
	$html .= '</ul>'."\n";
	$html = apply_filters('dlseller_filter_completion_html', $html, $usces_carts);

	if( 'return' == $out )
		return $html;
	else
		echo $html;
}

function dlseller_upcard_url( $member_id, $order_id, $cardlimit, $out = '' ){
	global $usces;
	if( !$member_id || !$order_id || !$cardlimit ) return;

	$html = '';
	$limits = explode('/', $cardlimit);
	$limit = substr(current_time('mysql', 0), 0, 2) . $limits[1] . $limits[0];
	$now = date('Ym', current_time('timestamp', 0));

	if( $limit <= $now ){
		$html = '<a href="javascript:void(0)" onClick="uscesMail.getMailData(\'' . $member_id . '\', \'' . $order_id . '\')">' . __('Update Request Email', 'dlseller') . '</a>';
	}

	if( 'return' == $out )
		return '<br />' . $html;
	else
		echo '<br />' . $html;
}

function dlseller_make_mail_ajax(){
	global $usces;

	$order_id = $_POST['order_id'];
	$member_id = $_POST['member_id'];
	$now = date('Ym', current_time('timestamp', 0));
	$infos = $usces->get_member_info( $member_id );
	$mail_data = $usces->options['mail_data'];

	$nonsessionurl = usces_url('cartnonsession', 'return');
	$parts = parse_url($nonsessionurl);
	if( isset($parts['query']) ) {
		parse_str( $parts['query'], $query );
	}
	if( false !== strpos($nonsessionurl, '/usces-cart') ) {
		$nonsessionurl = str_replace( '/usces-cart', '/usces-member', $nonsessionurl );
	} elseif( isset($query['page_id']) && $query['page_id'] == USCES_CART_NUMBER ) {
		$nonsessionurl = str_replace( 'page_id='.USCES_CART_NUMBER, 'page_id='.USCES_MEMBER_NUMBER, $nonsessionurl );
	}
	$delim = ( false === strpos($nonsessionurl, '?') ) ? '?' : '&';

	$limits = explode('/', $infos['limitofcard']);
	$regd = date('Ym', strtotime(substr(current_time('mysql', 0), 0, 2) . $limits[1] . '-' . $limits[0] . '-01'));
	if( $regd == $now ){
		$flag = 'NOW';
	}else if( $regd < $now ){
		$flag = 'PASSED';
	}else{
		die();
	}

	$limit = date(__('F, Y'), strtotime(substr(current_time('mysql', 0), 0, 2) . $limits[1] . '-' . $limits[0] . '-01'));
	$mail = $infos['mem_email'];
	$name = usces_localized_name( $infos['mem_name1'], $infos['mem_name2'], 'return' );
	$subject = apply_filters( 'dlseller_filter_card_update_mail_subject', __('Credit Card Information Update Request', 'dlseller'), $member_id, $order_id );
	$message  = __('Member ID','dlseller').' : ' . $infos['ID'] . "\r\n";
	$message .= __('Contractor name','dlseller').' : '.sprintf( _x('%s', 'honorific', 'usces'), $name )."\r\n\r\n\r\n";
	$message .= __("Thank you very much for using our service.",'dlseller')."\r\n\r\n";
	$message .= __("Please be sure to check this notification because it is an important contact for continued use of the service under contract.",'dlseller')."\r\n\r\n";
	$message .= __("---------------------------------------------------------",'dlseller')."\r\n";
	$message .= sprintf( __("Currently registered credit card expiration date is %s, ",'dlseller'), $limit )."\r\n";
	if( 'NOW' == $flag ){
		$message .= __("So you keep on this you will not be able to pay next month.",'dlseller')."\r\n";
	}else{
		$message .= __("So your payment of this month is outstanding payment.",'dlseller')."\r\n";
	}
	$message .= __("---------------------------------------------------------",'dlseller')."\r\n\r\n";
	$message .= __("If you have received a new credit card, ",'dlseller')."\r\n";
	$message .= __("Please click the URL below and update the card information during this month.",'dlseller')."\r\n";
	$message .= __("Sorry for troubling you, please process it.",'dlseller')."\r\n\r\n\r\n";
	$message .= $nonsessionurl . $delim . 'dlseller_card_update=login&dlseller_up_mode=1&dlseller_order_id=' . $order_id . "\r\n\r\n";
	$message .= __("If the card information update procedure failed, please contact us at the following email address.",'dlseller')."\r\n\r\n";
	$message .= __("Thank you.",'dlseller')."\r\n\r\n\r\n";
	$message .= $mail_data['footer']['ordermail'];
	$message = apply_filters( 'dlseller_filter_card_update_mail', $message, $member_id, $order_id );

	$ret = $mail . '#usces#' . $name . '#usces#' . $subject . '#usces#' . $message;
	die($ret);
}

function dlseller_send_mail_ajax() {
	global $wpdb, $usces;
	$_POST = $usces->stripslashes_deep_post($_POST);

	$order_id = $_POST['oid'];
	$member_id = $_POST['mid'];
	$name = trim(urldecode($_POST['name']));

	$para = array(
			'to_name' => sprintf(_x('%s', 'honorific', 'usces'), $name),
			'to_address' => trim($_POST['mailaddress']), 
			'from_name' => get_option('blogname'), 
			'from_address' => $usces->options['sender_mail'], 
			'return_path' => $usces->options['sender_mail'],
			'subject' => trim(urldecode($_POST['subject'])),
			'message' => trim(urldecode($_POST['message']))
			);

	$res = usces_send_mail( $para );
	if($res){

		//$key = 'continuepay_' . $order_id;
		//$continuepay = unserialize($usces->get_member_meta_value($key, $member_id));
		//$continuepay['condition'] = __('Credit card update request', 'dlseller') . '<br />(' . date(__('F j, Y'), current_time('timestamp', 0)) . ')';
		//$usces->set_member_meta_value($key, serialize($continuepay), $member_id);
		$continuation_table = $wpdb->prefix.'usces_continuation';
		$sel_query = $wpdb->prepare( "SELECT `con_condition` FROM {$continuation_table} WHERE `con_order_id` = %d", $order_id );
		$condition = $wpdb->get_var( $sel_query );
		$condition .= __('Credit card update request', 'dlseller') . '<br />(' . date(__('F j, Y'), current_time('timestamp', 0)) . ')';
		$upd_query = $wpdb->prepare( "UPDATE {$continuation_table} SET `con_condition` = %s WHERE `con_order_id` = %d", 
			$condition,
			$order_id
		);
		$res = $wpdb->query( $upd_query );

		$bcc_para = array(
				'to_name' => 'Shop Admin',
				'to_address' => $usces->options['sender_mail'], 
				'from_name' => 'Welcart Auto BCC', 
				'from_address' => 'Welcart', 
				'return_path' => $usces->options['sender_mail'],
				'subject' => trim(urldecode($_POST['subject'])) . ' to ' . sprintf(_x('%s', 'honorific', 'usces'), $name),
				'message' => trim(urldecode($_POST['message']))
				);

		usces_send_mail( $bcc_para );

		die('success');
	}else{
		die('error');
	}
}

function dlseller_frequency_name( $post_id, $type = '', $out = '' ){
	global $usces;
	$frequency = (int)$usces->getItemFrequency( $post_id );
	if( 'amount' == $type ){
		switch( $frequency ){
			case 1:
				$name = __('Monthly Fee', 'dlseller');
				break;
			case 6:
				$name = __('Semiannual Fee', 'dlseller');
				break;
			case 12:
				$name = __('Annual Fee', 'dlseller');
				break;
		}
	}else{
		switch( $frequency ){
			case 1:
				$name = __('Monthly Fee', 'dlseller');
				break;
			case 6:
				$name = __('Semiannual Fee', 'dlseller');
				break;
			case 12:
				$name = __('Annual Fee', 'dlseller');
				break;
		}
	}
	$name = apply_filters( 'dlseller_filter_frequency_name', $name, $post_id, $type );

	if( 'return' == $out )
		return $name;
	else
		echo $name;
}

//function dlseller_first_charging( $post_id, $type = '' ){
function dlseller_first_charging( $post_id, $type = '', $order_id = '' ){
	global $usces, $wpdb;
	$now = current_time('mysql');
	$thisyear = (int)substr($now, 0, 4);
	$thismonth = (int)substr($now, 5, 2);
	$today = (int)substr($now, 8, 2);
	$usces_item = $usces->get_item( $post_id );
	$continue_data = array();
	if( 99 == $usces_item['item_chargingday'] ) {
		if( !empty($order_id) ) {
/*
			$data = $usces->get_order_data($order_id);
			$key = 'continuepay_'.$order_id;
			$continue_data = unserialize($usces->get_member_meta_value($key, $data['mem_id']));
			if( empty($continue_data['chargedday']) ) {
				$chargedday = str_replace("年", "-", str_replace("月", "-", str_replace("日", "-", $continue_data['startdate'])));
			} else {
				$chargedday = $continue_data['chargedday'];
			}
*/
			$continuation_table = $wpdb->prefix.'usces_continuation';
			$query = $wpdb->prepare( "SELECT `con_next_charging` AS `chargedday`, `con_startdate` AS `startdate` FROM {$continuation_table} WHERE `con_order_id` = %d", $order_id );
			$continue_data = $wpdb->get_row( $query, ARRAY_A );
			if( empty($continue_data['chargedday']) ) {
				$chargedday = $continue_data['startdate'];
			} else {
				$chargedday = $continue_data['chargedday'];
			}
			list( $year, $month, $day ) = explode( "-", $chargedday );
			if( empty($continue_data['chargedday']) ) $month = (int)$month + $usces_item['item_frequency'];
			//$time = mktime(0, 0, 0, $month + $usces_item['item_frequency'], $day, $thisyear);
			$time = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year);

		} else {
			//$nextday = date('Y-m-d H:i:s', strtotime("+1 day"));
			$nextday = current_time('Y-m-d');
			list( $year, $month, $day ) = explode( "-", $nextday );
			$time = mktime(0, 0, 0, (int)$month, (int)$day+1, (int)$year);
		}

	} else {
		$chargingday = $usces_item['item_chargingday'];
		if( $today < $chargingday ) {
			$month = $thismonth;
		} else {
			$month = $thismonth + 1;
		}
		$time = mktime(0, 0, 0, $month, $chargingday, $thisyear);
	}

	$time = apply_filters( 'dlseller_filter_first_charging', $time, $post_id, $usces_item, $order_id, $continue_data );

	if( 'time' == $type ){
		return $time;
	}else{
		//$date = date(__('Y/m/d'), $time);
		$date = date('Y-m-d', $time);
		return $date;
	}
}

function dlseller_next_charging( $order_id, $type = '' ){
	global $usces, $wpdb;
	//$key = 'continuepay_' . $order_id;
	//$order_data = $usces->get_order_data($order_id);
	//$carts = $order_data['cart'];
	//$post_id = $carts[0]['post_id'];
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = $wpdb->prepare( "SELECT `con_post_id`, `con_next_charging` AS `chargedday` FROM {$continuation_table} WHERE `con_order_id` = %d", $order_id );
	$continue_data = $wpdb->get_row( $query, ARRAY_A );
	//$usces_item = $usces->get_item( $post_id );

	//$continue_data = unserialize($usces->get_member_meta_value($key, $order_data['mem_id']));
	if( empty($continue_data) ) return;

	if( empty($continue_data['chargedday']) ){
		$post_id = $continue_data['con_post_id'];
		//$time = dlseller_first_charging( $post_id, 'time' );
		$time = dlseller_first_charging( $post_id, 'time', $order_id );

	}else{
		$chargedday = $continue_data['chargedday'];
		list( $year, $month, $day ) = explode( "-", $chargedday );
		//$time = mktime(0, 0, 0, $month + $usces_item['item_frequency'], $usces_item['item_chargingday'], $year);
		$time = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year);
	}

	$time = apply_filters( 'dlseller_filter_next_charging', $time, $order_id, $continue_data );

	if( 'time' == $type ){
		return $time;
	}else{
		//$date = date(__('Y/m/d'), $time);
		$date = date('Y-m-d', $time);
		return $date;
	}
}
/*
function dlseller_first_contracting( $post_id, $type = '' ){
	global $usces;
	$now = current_time('mysql');
	$thisyear = (int)substr($now, 0, 4);
	$thismonth = (int)substr($now, 5, 2);
	$today = (int)substr($now, 8, 2);
	$usces_item = $usces->get_item( $post_id );

	if( $today < $usces_item['item_chargingday'] ){
		$month = $thismonth;
	}else{
		$month = $thismonth + 1;
	}

	if( empty($usces_item['dlseller_interval']) ){

		$time = NULL;
		$date = NULL;

	}else{

		$time = mktime(0, 0, 0, $month + $usces_item['dlseller_interval'], $usces_item['item_chargingday'], $thisyear);
		$date = date(__('Y/m/d'), $time);
	}

	if( 'time' == $type ){
		return $time;
	}else{
		return $date;
	}
}
*/
function dlseller_next_contracting( $order_id, $type = '' ){
	global $usces, $wpdb;
	//$key = 'continuepay_' . $order_id;
	//$order_data = $usces->get_order_data($order_id);
	//$carts = $order_data['cart'];
	//$post_id = $carts[0]['post_id'];
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = $wpdb->prepare( "SELECT `con_post_id`, 
		`con_chargingday` AS `chargingday`, 
		`con_interval` AS `interval`, 
		`con_next_contracting` AS `contractedday`, 
		`con_startdate` AS `startdate` 
		FROM {$continuation_table} 
		WHERE `con_order_id` = %d", 
		$order_id
	);
	$continue_data = $wpdb->get_row( $query, ARRAY_A );
	//$post_id = $continue_data['con_post_id'];
	//$usces_item = $usces->get_item( $post_id );

	//$continue_data = unserialize($usces->get_member_meta_value($key, $order_data['mem_id']));
	if( empty($continue_data) ) return NULL;

	//if( empty($usces_item['dlseller_interval']) ){
	if( empty($continue_data['interval']) ){
		$time = NULL;
		$date = NULL;

	}elseif( empty($continue_data['contractedday']) ){

		if( empty($continue_data['startdate']) ) {
			$time = NULL;
			$date = NULL;

		} else {
			//$contractedday = str_replace("年", "-", str_replace("月", "-", str_replace("日", "-", $continue_data['startdate'])));
			$contractedday = $continue_data['startdate'];
			list( $year, $month, $day ) = explode( "-", $contractedday );
			//$time = mktime(0, 0, 0, (int)$month + $usces_item['dlseller_interval'], (int)$day, (int)$year);
			$time = mktime(0, 0, 0, (int)$month + $continue_data['interval'], (int)$day, (int)$year);
			//$date = date(__('Y/m/d'), $time);
			$date = date('Y-m-d', $time);
		}

	}else{
		$contractedday = $continue_data['contractedday'];
		list( $year, $month, $day ) = explode( "-", $contractedday );
		//$time = mktime(0, 0, 0, $month + $usces_item['dlseller_interval'], $usces_item['item_chargingday'], $year);
		$time = mktime(0, 0, 0, $month + $continue_data['interval'], $continue_data['chargingday'], $year);
		//$time = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year);
		//$date = date(__('Y/m/d'), $time);
		$date = date('Y-m-d', $time);
	}

	$time = apply_filters( 'dlseller_filter_next_contracting', $time, $order_id, $continue_data );

	if( 'time' == $type ){
		return $time;
	}else{
		return $date;
	}
}

function dlseller_get_filename( $post_id, $sku = '' ){
	$file = get_post_meta($post_id, '_dlseller_file', true);
	return apply_filters('dlseller_filter_filename', $file, $post_id, $sku);
}

function dlseller_fiter_the_payment_method( $payments, $value ) {
	$have_continue_charge = dlseller_have_continue_charge();
	$dl_payments = array();
	$continue_payment_method = apply_filters( 'usces_filter_the_continue_payment_method', array( 'acting_remise_card', 'acting_paypal_ec' ) );
	$have_shipped = dlseller_have_shipped();

/*	if( !dlseller_have_shipped() ) {
		foreach( $payments as $payment ){
			if( $payment['settlement'] == 'COD' ) 
				continue;

			if( $have_continue_charge ) {
				if( !in_array( $payment['settlement'], $continue_payment_method ) ) {
					continue;
				}
				if( isset($usces->options['acting_settings']['remise']['continuation']) && 'on' !== $usces->options['acting_settings']['remise']['continuation'] && 'acting_remise_card' == $payment['settlement']) {
					continue;
				} elseif( isset($usces->options['acting_settings']['paypal']['continuation']) && 'on' !== $usces->options['acting_settings']['paypal']['continuation'] && 'acting_paypal_ec' == $payment['settlement']) {
					continue;
				}
			}
			$dl_payments[] = $payment;
		}
		ksort($dl_payments);
	} else {
		$dl_payments = $payments;
	}*/
	if( $have_continue_charge ) {
		foreach( $payments as $payment ){
			if( !in_array( $payment['settlement'], $continue_payment_method ) ) {
				continue;
			}
			if( isset($usces->options['acting_settings']['remise']['continuation']) && 'on' !== $usces->options['acting_settings']['remise']['continuation'] && 'acting_remise_card' == $payment['settlement']) {
				continue;
			} elseif( isset($usces->options['acting_settings']['paypal']['continuation']) && 'on' !== $usces->options['acting_settings']['paypal']['continuation'] && 'acting_paypal_ec' == $payment['settlement']) {
				continue;
			}
			$dl_payments[] = $payment;
		}
		ksort($dl_payments);
	} else {
		if( !dlseller_have_shipped() ) {
			foreach( $payments as $payment ){
				if( $payment['settlement'] == 'COD' ) 
					continue;

				$dl_payments[] = $payment;
			}
			ksort($dl_payments);
		} else {
			$dl_payments = $payments;
		}
	}
	$dl_payments = apply_filters( 'dlseller_filter_the_payment_method_restriction', $dl_payments, $value );
	return $dl_payments;
}

function dlseller_filter_the_payment_method_choices( $html, $payments ) {

	if( !dlseller_have_shipped() && !dlseller_require_payment() ) {
		$nopayment_message = apply_filters( 'dlseller_filter_nopayment_message', __('no payment','dlseller') );
		$nopayment = apply_filters( 'dlseller_filter_nopayment_name', __('no payment','dlseller') );
		$html= '<div>'.$nopayment_message.'</div><input name="offer[payment_name]" type="hidden" value="'.$nopayment.'" />';
	}
	return $html;
}

function dlseller_action_pre_delete_memberdata( $member_id ) {
	global $wpdb;

	$continue_message = '';
/*
	$metatable = $wpdb->prefix.'usces_member_meta';
	$query = $wpdb->prepare( "SELECT * FROM $metatable WHERE meta_key LIKE %s AND member_id = %d", 'continuepay_%', $member_id );
	$res = $wpdb->get_results( $query, ARRAY_A );
	foreach( $res as $row ) {
		$continue_data = unserialize( $row['meta_value'] );
		if( 'continuation' == $continue_data['status'] ) {
			list( $pre, $order_id ) = explode( "_", $row['meta_key'] );
			$continue_message .= __('Order ID', 'dlseller').' : '.$order_id.' '.__('Next Withdrawal Date', 'dlseller').' : '.date(__('Y/m/d'), strtotime( dlseller_next_charging( $order_id ) ))."\r\n";
		}
	}
*/
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = $wpdb->prepare( "SELECT `con_order_id`, `con_next_charging` FROM {$continuation_table} WHERE `con_member_id` = %d AND `con_status` = 'continuation'", $member_id );
	$res = $wpdb->get_results( $query, ARRAY_A );
	foreach( $res as $row ) {
		$order_id = $row['con_order_id'];
		$next_charging = ( empty($row['con_next_charging']) ) ? dlseller_next_charging( $order_id ) : $row['con_next_charging'];
		$continue_message .= __('Order ID', 'dlseller').' : '.$order_id.' '.__('Next Withdrawal Date', 'dlseller').' : '.date(__('Y/m/d'), strtotime($next_charging))."\r\n";
	}
	if( '' != $continue_message ) {
		$_SESSION['usces_member_'.$member_id]['continue_message'] = $continue_message;
	}
}

function dlseller_filter_send_delmembermail_notice( $message, $member ) {
	$continue_message = ( empty($_SESSION['usces_member_'.$member['ID']]['continue_message']) ) ? '' : $_SESSION['usces_member_'.$member['ID']]['continue_message'];
	if( '' != $continue_message ) {
		$message .= "\r\n".__('*** There is a contract of auto continuation billing.', 'dlseller')."\r\n".$continue_message."\r\n";
		unset( $_SESSION['usces_member_'.$member['ID']]['continue_message'] );
	}
	return $message;
}

function dlseller_status_name( $status, $out = '' ) {
	if( $status == 'continuation' ) {
		$status_name = __('continuation', 'dlseller');
	} else {
		$status_name = __('cancellation', 'dlseller');
	}

	if( 'return' == $out )
		return $status_name;
	else
		echo $status_name;
}

function dlseller_get_continue_status( $member_id, $order_id ) {
	global $wpdb;

	$status = '';
/*
	$metatable = $wpdb->prefix.'usces_member_meta';
	$meta_key = 'continuepay_'.$order_id;
	$query = $wpdb->prepare( "SELECT meta_value FROM $metatable WHERE meta_key = %s AND member_id = %d", $meta_key, $member_id );
	$meta_value = $wpdb->get_var( $query );
	if( $meta_value ) {
		$continue_data = unserialize( $meta_value );
		if( !empty($continue_data['status']) ) $status = $continue_data['status'];
	}
*/
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = $wpdb->prepare( "SELECT `con_status` FROM {$continuation_table} WHERE `con_member_id` = %d AND `con_order_id` = %d", $member_id, $order_id );
	$con_status = $wpdb->get_var( $query );
	return $status;
}

function dlseller_action_order_print_cart_row( $pdf, $data, $pdf_args ) {
	global $usces, $wpdb;

	extract( $pdf_args );
	$order_id = $data->order['ID'];
	$member_id = $data->customer['mem_id'];
/*
	$key = 'continuepay_'.$order_id;
	$meta_value = $usces->get_member_meta_value( $key, $member_id );
	if( $meta_value ) {
		$font = '';
		$continue_data = ( empty($meta_value) ) ? array() : unserialize( $meta_value );
*/
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = $wpdb->prepare( "SELECT `con_interval` AS `interval`, `con_next_charging` AS `next_charging`, `con_next_contracting` AS `next_contracting` FROM {$continuation_table} WHERE `con_order_id` = %d", $order_id );
	$continue_data = $wpdb->get_row( $query, ARRAY_A );
	if( $continue_data ) {
		$font = '';
		list( $fontsize, $lineheight, $linetop ) = usces_set_font_size(8);
		$next_charging = ( empty($continue_data['next_charging']) ) ? dlseller_next_charging( $order_id ) : $continue_data['next_charging'];
		$line = __('Next Withdrawal Date', 'dlseller').' : '.date(__('Y/m/d'), strtotime($next_charging));
		$pdf->SetFont( $font, '', $fontsize );
		$pdf->SetXY( $x+6.0, $pdf->GetY()+$linetop );
		$pdf->MultiCell( 81.6, $lineheight-0.2, usces_conv_euc($line), $border, 'L' );
		$interval = $continue_data['interval'];
		if( 0 < (int)$interval ) {
			$next_contracting = ( empty($continue_data['next_contracting']) ) ? dlseller_next_contracting( $order_id ) : $continue_data['next_contracting'];
			$line = __('Renewal Date', 'dlseller').' : '.date(__('Y/m/d'), strtotime($next_contracting));
			$pdf->SetFont( $font, '', $fontsize );
			$pdf->SetXY( $x+6.0, $pdf->GetY()+$linetop );
			$pdf->MultiCell( 81.6, $lineheight-0.2, usces_conv_euc($line), $border, 'L' );
		}
	}
}

function dlseller_action_confirm_after_form(){
	if( usces_is_member_system() && usces_is_member_system_point() && usces_is_login() && !usces_is_available_point() ){
		$mes = '<p class="available_point_message">'.__('Sorry, can not use the points to the continuation accounting items.', 'dlseller').'</p>' . "\n";
	}else{
		$mes = '';
	}
	echo $mes;
}

function dlseller_have_member_continue_order( $member_id ) {
	global $wpdb;
	$continue = false;

	//$metatable = $wpdb->prefix.'usces_member_meta';
	//$query = $wpdb->prepare( "SELECT * FROM $metatable WHERE meta_key LIKE %s AND member_id = %d", 'continuepay_%', $member_id );
	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = $wpdb->prepare( "SELECT * FROM {$continuation_table} WHERE `con_member_id` = %d AND `con_status` = 'continuation'", $member_id );
	$continue_order = $wpdb->get_results( $query, ARRAY_A );
	if( 0 < count($continue_order) ) $continue = true;
	return $continue;
}

function dlseller_action_admin_reminder_mailform() {
	global $usces;
	$usces->options = get_option( 'usces' );
	$mail_data = $usces->options['mail_data'];

	if( empty($mail_data['title']['dlseller_reminder']) ) $mail_data['title']['dlseller_reminder'] = __('Guidance of the settlement date', 'dlseller');
	if( empty($mail_data['header']['dlseller_reminder']) ) $mail_data['header']['dlseller_reminder'] = __('This e-mail is a guide for the next settlement date of the auto continuation charging.', 'dlseller')."\n\n\n";
	if( empty($mail_data['footer']['dlseller_reminder']) ) $mail_data['footer']['dlseller_reminder'] = $mail_data['footer']['othermail'];
?>
		<div class="postbox">
		<h3 class="hndle"><span><?php _e('Settlement reminder-email', 'dlseller'); ?><?php _e('(automatic transmission)', 'dlseller'); ?></span><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_reminder');"> (<?php _e('explanation', 'usces'); ?>) </a></h3>
		<div class="inside">
		<table class="form_table">
			<tr>
				<th width="150"><?php _e('Title', 'usces'); ?></th>
				<td><input name="title[dlseller_reminder]" id="title[dlseller_reminder]" type="text" class="mail_title" value="<?php esc_attr_e($mail_data['title']['dlseller_reminder']); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php _e('header', 'usces'); ?></th>
				<td><textarea name="header[dlseller_reminder]" id="header[dlseller_reminder]" class="mail_header"><?php esc_attr_e($mail_data['header']['dlseller_reminder']); ?></textarea></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php _e('footer', 'usces'); ?></th>
				<td><textarea name="footer[dlseller_reminder]" id="footer[dlseller_reminder]" class="mail_footer"><?php esc_attr_e($mail_data['footer']['dlseller_reminder']); ?></textarea></td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<hr size="1" color="#CCCCCC" />
		<div id="ex_dlseller_reminder" class="explanation"><?php _e('Reminder-email of settlement of the auto continuation charging.', 'dlseller'); ?><?php _e('Send automatically on reminder-email sent date.', 'dlseller'); ?></div>
		</div>
		</div><!--postbox-->
<?php
}

function dlseller_action_admin_contract_renewal_mailform() {
	global $usces;
	$usces->options = get_option( 'usces' );
	$mail_data = $usces->options['mail_data'];

	if( empty($mail_data['title']['dlseller_contract_renewal']) ) $mail_data['title']['dlseller_contract_renewal'] = __('Guidance of the contract renewal date', 'dlseller');
	if( empty($mail_data['header']['dlseller_contract_renewal']) ) $mail_data['header']['dlseller_contract_renewal'] = __('This e-mail is a guide for the next contract renewal date of the auto continuation charging.', 'dlseller')."\n\n\n";
	if( empty($mail_data['footer']['dlseller_contract_renewal']) ) $mail_data['footer']['dlseller_contract_renewal'] = $mail_data['footer']['othermail'];
?>
		<div class="postbox">
		<h3 class="hndle"><span><?php _e('Contract renewal email', 'dlseller'); ?><?php _e('(automatic transmission)', 'dlseller'); ?></span><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_contract_renewal');"> (<?php _e('explanation', 'usces'); ?>) </a></h3>
		<div class="inside">
		<table class="form_table">
			<tr>
				<th width="150"><?php _e('Title', 'usces'); ?></th>
				<td><input name="title[dlseller_contract_renewal]" id="title[dlseller_contract_renewal]" type="text" class="mail_title" value="<?php esc_attr_e($mail_data['title']['dlseller_contract_renewal']); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php _e('header', 'usces'); ?></th>
				<td><textarea name="header[dlseller_contract_renewal]" id="header[dlseller_contract_renewal]" class="mail_header"><?php esc_attr_e($mail_data['header']['dlseller_contract_renewal']); ?></textarea></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php _e('footer', 'usces'); ?></th>
				<td><textarea name="footer[dlseller_contract_renewal]" id="footer[dlseller_contract_renewal]" class="mail_footer"><?php esc_attr_e($mail_data['footer']['dlseller_contract_renewal']); ?></textarea></td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<hr size="1" color="#CCCCCC" />
		<div id="ex_dlseller_contract_renewal" class="explanation"><?php _e('Reminder-email of contract renewal of the auto continuation charging.', 'dlseller'); ?><?php _e('Send automatically on reminder-email sent date.', 'dlseller'); ?></div>
		</div>
		</div><!--postbox-->
<?php
}

function dlseller_send_reminder_mail( $order_id, $continue_data ) {
	global $usces;

	$order_data = $usces->get_order_data( $order_id, 'direct' );
	$mail_data = $usces->options['mail_data'];

	$name = usces_localized_name( trim($order_data['order_name1']), trim($order_data['order_name1']), 'return' );
	$subject = $mail_data['title']['dlseller_reminder'];
	$mail_header = $mail_data['header']['dlseller_reminder'];
	$mail_footer = $mail_data['footer']['dlseller_reminder'];
	$message = $mail_header.usces_order_confirm_message( $order_id ).$mail_footer;
	if( $usces->options['put_customer_name'] == 1 ) {
		$dear_name = sprintf( __('Dear %s', 'usces'), $name );
		$message = $dear_name."\r\n\r\n".$message;
	}

	$send_para = array(
		'to_name' => sprintf( _x('%s', 'honorific', 'usces'), $name ),
		'to_address' => $order_data['order_email'],
		'from_name' => get_option( 'blogname' ),
		'from_address' => $usces->options['sender_mail'],
		'return_path' => $usces->options['sender_mail'],
		'subject' => $subject,
		'message' => $message
	);
	$send_para = apply_filters( 'dlseller_filter_send_reminder_mail_para_to_customer', $send_para, $order_data, $continue_data );
	usces_send_mail( $send_para );

	$admin_para = array(
		'to_name' => 'Shop Admin',
		'to_address' => $usces->options['order_mail'],
		'from_name' => 'Welcart Auto BCC',
		'from_address' => $usces->options['sender_mail'],
		'return_path' => $usces->options['sender_mail'],
		'subject' => $subject,
		'message' => $message
	);
	usces_send_mail( $admin_para );
}

function dlseller_send_contract_renewal_mail( $order_id, $continue_data ) {
	global $usces;

	$order_data = $usces->get_order_data( $order_id, 'direct' );
	$mail_data = $usces->options['mail_data'];

	$name = usces_localized_name( trim($order_data['order_name1']), trim($order_data['order_name1']), 'return' );
	$subject = $mail_data['title']['dlseller_contract_renewal'];
	$mail_header = $mail_data['header']['dlseller_contract_renewal'];
	$mail_footer = $mail_data['footer']['dlseller_contract_renewal'];
	$message = $mail_header.usces_order_confirm_message( $order_id ).$mail_footer;
	if( $usces->options['put_customer_name'] == 1 ) {
		$dear_name = sprintf( __('Dear %s', 'usces'), $name );
		$message = $dear_name."\r\n\r\n".$message;
	}

	$send_para = array(
		'to_name' => sprintf( _x('%s', 'honorific', 'usces'), $name ),
		'to_address' => $order_data['order_email'],
		'from_name' => get_option( 'blogname' ),
		'from_address' => $usces->options['sender_mail'],
		'return_path' => $usces->options['sender_mail'],
		'subject' => $subject,
		'message' => $message
	);
	$send_para = apply_filters( 'dlseller_filter_send_contract_renewal_mail_para_to_customer', $send_para, $order_data, $continue_data );
	usces_send_mail( $send_para );

	$admin_para = array(
		'to_name' => 'Shop Admin',
		'to_address' => $usces->options['order_mail'],
		'from_name' => 'Welcart Auto BCC',
		'from_address' => $usces->options['sender_mail'],
		'return_path' => $usces->options['sender_mail'],
		'subject' => $subject,
		'message' => $message
	);
	usces_send_mail( $admin_para );
}

function dlseller_get_con_id( $order_id ) {
	global $wpdb;

	$continuation_table = $wpdb->prefix.'usces_continuation';
	$query = "SELECT con_id FROM {$continuation_table} WHERE con_order_id = ".$order_id;
	$con_id = $wpdb->get_var( $query );
	return $con_id;
}

function dlseller_get_continuation_meta_value( $key, $con_id ) {
	global $wpdb;

	$continuation_meta_table = $wpdb->prefix.'usces_continuation_meta';
	$query = $wpdb->prepare( "SELECT meta_value FROM {$continuation_meta_table} WHERE con_id = %d AND meta_key = %s", 
		$con_id, $key
	);
	$res = $wpdb->get_var( $query );
	return $res;
}

function dlseller_set_continuation_meta_value( $key, $meta_value, $con_id ) {
	global $wpdb;

	if( empty($key) || empty($con_id) ) return;

	$continuation_meta_table = $wpdb->prefix.'usces_continuation_meta';
	$query = $wpdb->prepare( "SELECT count(*) FROM {$continuation_meta_table} WHERE con_id = %d AND meta_key = %s", 
		$con_id, $key
	);
	$var = $wpdb->get_var( $query );
	if( 0 < $var ) {
		$query = $wpdb->prepare( "UPDATE {$continuation_meta_table} SET meta_value = %s WHERE con_id = %d AND meta_key = %s", 
			$meta_value, $con_id, $key
		);
	} else {
		$query = $wpdb->prepare( "INSERT INTO  {$continuation_meta_table} ( con_id, meta_key, meta_value ) VALUES( %d, %s, %s )", 
			$con_id, $key, $meta_value
		);
	}
	$res = $wpdb->query( $query );
	return $res;
}

function dlseller_del_continuation_meta( $key = '', $con_id ) {
	global $wpdb;

	if( empty($con_id) ) return;

	$continuation_meta_table = $wpdb->prefix.'usces_continuation_meta';
	if( empty($key) ) {
		$query = $wpdb->prepare( "DELETE FROM {$continuation_meta_table} WHERE con_id = %d", $con_id );
	} else {
		$query = $wpdb->prepare( "DELETE FROM {$continuation_meta_table} WHERE con_id = %d AND meta_key = %s", $con_id, $key );
	}
	$res = $wpdb->query( $query );
	return $res;
}

