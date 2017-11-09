<?php
if( !function_exists('usces_point_coverage') ) {
	function usces_point_coverage() {
		global $usces;
		return $usces->options['point_coverage'];
	}
}

if( !function_exists('usces_get_tax_target') ) {
	function usces_get_tax_target() {
		global $usces;
		return $usces->options['tax_target'];
	}
}

if( !function_exists('usces_tax_label') ) {
	function usces_tax_label( $data = array(), $out = '' ) {
		global $usces;

		if( 'deactivate' == $usces->options['tax_display'] ) {
			$label = '';
		} else {
			if( 'exclude' == $usces->options['tax_mode'] ) {
				$label = __('consumption tax', 'usces');
			} else {
				$label = __('Internal tax', 'usces');
			}
			$label = apply_filters( 'usces_filter_tax_label', $label );
		}

		if( $out == 'return' ) {
			return $label;
		} else {
			echo $label;
		}
	}
}

if( !function_exists('usces_is_tax_display') ) {
	function usces_is_tax_display() {
		global $usces, $usces_entries;
		if( defined('USCES_VERSION') and version_compare( USCES_VERSION, '1.5', '>=' ) ) {
			if( isset($usces->options['tax_display']) and 'deactivate' == $usces->options['tax_display'] )
				return false;
			else
				return true;
		} else {
			if( empty($usces_entries['order']['tax']) )
				return false;
			else
				return true;
		}
	}
}

if( !function_exists('usces_admin_action_status') ) {
	function usces_admin_action_status( $status = '', $message = '' ) {
		global $usces;
		if( empty($status) ) {
			$status = $usces->action_status;
			$usces->action_status = 'none';
		}
		if( empty($message) ) {
			$message = $usces->action_message;
			$usces->action_message = '';
		}
		$class = '';
		if( $status == 'success' ) {
			$class = 'updated';
		} elseif( $status == 'caution' ) {
			$class = 'update-nag';
		} elseif( $status == 'error' ) {
			$class = 'error';
		}
		if( '' != $class ) {
	?>
	<div id="usces_admin_status">
		<div id="usces_action_status" class="<?php echo $class; ?> notice is-dismissible">
			<p><strong><?php echo $message; ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.' ); ?></span></button>
		</div>
	</div>
	<?php
		} else {
	?>
	<div id="usces_admin_status"></div>
	<?php
		}
	}
}

if( !function_exists('usces_change_line_break') ) {
	function usces_change_line_break( $value ){
		$cr = array("\r\n", "\r");
		$value = trim($value);
		$value = str_replace($cr, "\n", $value);
		return $value;
	}
}

function dlseller_is_orderlist_flag() {
	$orderlist_flag = 0;
	if( defined('USCES_VERSION') and version_compare( USCES_VERSION, '1.8.0', '>=' ) ) {
		$ex_options = get_option( 'usces_ex' );
		$orderlist_flag = ( isset($ex_options['system']['datalistup']['orderlist_flag']) ) ? (int)$ex_options['system']['datalistup']['orderlist_flag'] : 1;
	}
	return $orderlist_flag;
}

function dlseller_continue_member_list_hook( $hook ) {

	if( !isset( $_POST['continue_memberlist_options_apply']) ) {
		return;
	}

	$list_option = get_option( 'usces_continuelist_option' );
	foreach( $list_option['view_column'] as $key => $value ) {
		$list_option['view_column'][$key] = ( isset($_POST['hide'][$key] ) ) ? 1 : 0;
	}
	$list_option['max_row'] = (int)$_POST['continue_memberlist_per_page'];

	update_option( 'usces_continuelist_option', $list_option );
}

function dlseller_screen_settings( $screen_settings, $screen ) {

	if( 'welcart-management_page_usces_continue' != $screen->id || isset($_REQUEST['continue_action']) ) {
		return $screen_settings;
	}

	require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__))."/continueMemberList.class.php" );
	$DT = new ContinuationList();
	$arr_column = $DT->get_column();
	$list_option = get_option( 'usces_continuelist_option' );
	$init_view = apply_filters( 'dlseller_filter_continue_memberlist_column_init_view', array( 'deco_id', 'mem_id', 'name1', 'name2', 'limitofcard', 'price', 'acting', 'order_date', 'contractedday', 'chargedday', 'con_status', 'con_condition' ) );

	$screen_settings = '
	<fieldset class="metabox-prefs">
		<legend>'.__('Columns').'</legend>';
	foreach( $arr_column as $key => $value ) {
		if( 'ID' == $key ) {
			continue;
		}

		if( !isset($list_option['view_column'][$key]) && in_array( $key, $init_view ) ) {
			$list_option['view_column'][$key] = 1;
		} elseif( !isset($list_option['view_column'][$key]) ) {
			$list_option['view_column'][$key] = 0;
		}
		$checked = ( isset($list_option['view_column'][$key]) && $list_option['view_column'][$key] ) ? ' checked="checked"' : '';
		$screen_settings .= '<label><input class="hide-column-tog" name="hide['.$key.']" type="checkbox" id="'.$key.'-hide" value="'.esc_attr($value).'"'.$checked.' />'.esc_html($value).'</label>';
	}
	$screen_settings .= '</fieldset>';

	if( !isset($list_option['max_row']) ) {
		$list_option['max_row'] = 50;
	}

	$screen_settings .= '<fieldset class="screen-options">
		<legend>'.__('Pagination').'</legend>
		<label for="edit_post_per_page">'.__('Number of items per page:').'</label>
		<input type="order" step="1" min="1" max="999" class="screen-per-page" name="continue_memberlist_per_page" id="continue_memberlist_per_page" maxlength="3" value="'.(int)$list_option['max_row'].'" />
	</fieldset>
	<p class="submit"><input type="submit" name="continue_memberlist_options_apply" id="screen-options-apply" class="button button-primary" value="'.__('Apply').'"  /></p>';

	update_option( 'usces_continuelist_option', $list_option );

	return $screen_settings;
}

