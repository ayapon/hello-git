<?php
$dlseller_options = get_option('dlseller');
$dlseller_options = maybe_unserialize($dlseller_options);
if( !isset($dlseller_options['content_path']) || $dlseller_options['content_path'] == '' ){
	$dlseller_content_path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/';
}else{
	$dlseller_content_path = $dlseller_options['content_path'];
}
$dlseller_terms = isset($dlseller_options['dlseller_terms']) ? $dlseller_options['dlseller_terms'] : '';
$dlseller_terms2 = isset($dlseller_options['dlseller_terms2']) ? $dlseller_options['dlseller_terms2'] : '';
if( isset($dlseller_options['dlseller_rate']) ){
	$dlseller_rate = $dlseller_options['dlseller_rate'];
}else{
	$dlseller_rate = 5000;
	$dlseller_options['dlseller_rate'] = 1000;
}
$dlseller_member_reinforcement = isset($dlseller_options['dlseller_member_reinforcement']) ? $dlseller_options['dlseller_member_reinforcement'] : 'off';
$dlseller_restricting = isset($dlseller_options['dlseller_restricting']) ? $dlseller_options['dlseller_restricting'] : 'on';
$dlseller_reminder_mail = ( isset($dlseller_options['reminder_mail']) ) ? $dlseller_options['reminder_mail'] : 'off';
$dlseller_contract_renewal_mail = ( isset($dlseller_options['contract_renewal_mail']) ) ? $dlseller_options['contract_renewal_mail'] : 'off';
$dlseller_send_days_before = ( isset($dlseller_options['send_days_before']) ) ? $dlseller_options['send_days_before'] : 7;
$dlseller_scheduled_time['hour'] = ( isset($dlseller_options['scheduled_time']['hour']) ) ? $dlseller_options['scheduled_time']['hour'] : '01';
$dlseller_scheduled_time['min'] = ( isset($dlseller_options['scheduled_time']['min']) ) ? $dlseller_options['scheduled_time']['min'] : '00';
?>
<div class="wrap">
<div class="usces_admin">

<h1>WCEX <?php _e('DLSeller Setting','dlseller'); ?></h1>
<p class="version_info">Version <?php echo WCEX_DLSELLER_VERSION; ?></p>
<?php usces_admin_action_status(); ?>

<form action="" method="post" name="option_form" id="option_form">
<input name="dlseller_option_update" type="submit" class="button button-primary" value="<?php _e('change decision','usces'); ?>" />
<div id="poststuff" class="metabox-holder">

<div class="postbox">
<h3 class="hndle"><span><?php _e('DLSeller Setting','dlseller'); ?></span></h3>
<div class="inside">
<!--
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_restricting');"><?php _e('Purchaser limit', 'dlseller'); ?></a></th>
		<td><input name="dlseller_restricting" type="radio" id="dlseller_restricting_1" value="on"<?php if( $dlseller_restricting == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="dlseller_restricting_1"><?php _e('Only members', 'dlseller'); ?></label></td>
		<td><input name="dlseller_restricting" type="radio" id="dlseller_restricting_2" value="off"<?php if( $dlseller_restricting == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="dlseller_restricting_2"><?php _e('Anyone', 'dlseller'); ?></label></td>
		<td><div id="ex_dlseller_restricting" class="explanation"><?php _e("Select the \"Only members\", it will be forced to login when put into the cart. <br />The default value is \"Only members\".", 'dlseller'); ?></div></td>
	</tr>
</table>
-->
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_member_reinforcement');"><?php _e('Member check strengthen', 'dlseller'); ?></a></th>
		<td><input name="dlseller_member_reinforcement" type="radio" id="dlseller_member_reinforcement_1" value="on"<?php if( $dlseller_member_reinforcement == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="dlseller_member_reinforcement_1"><?php _e('Strengthen', 'dlseller'); ?></label></td>
		<td><input name="dlseller_member_reinforcement" type="radio" id="dlseller_member_reinforcement_2" value="off"<?php if( $dlseller_member_reinforcement == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="dlseller_member_reinforcement_2"><?php _e('Not strengthen', 'dlseller'); ?></label></td>
		<td><div id="ex_dlseller_member_reinforcement" class="explanation"><?php _e("When strengthening, address and phone number is mandatory item. Please select the \"strengthen\" if at settlement performing \"installments\" or \"auto continuation charging\".", 'dlseller'); ?></div></td>
	</tr>
</table>
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_content_path');"><?php _e('Contents directory path', 'dlseller'); ?></a></th>
		<td><input name="dlseller_content_path" type="text" id="dlseller_content_path" value="<?php echo esc_attr($dlseller_content_path); ?>" size="80" /></td>
		<td><div id="ex_dlseller_content_path" class="explanation"><?php _e('Please appoint the full path of the directory which contents file is in.', 'dlseller'); ?></div></td>
	</tr>
</table>
<!--<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_results');"><?php _e('Actual CSV', 'dlseller'); ?></a></th>
		<td><a href="<?php echo get_option('siteurl') . '/wp-admin/admin.php?page=wcex_dlseller&dlseller_transition=results'; ?>"><?php _e('Download', 'dlseller'); ?></a></td>
		<td><div id="ex_dlseller_results" class="explanation"><?php _e('Downloads the downloading and the purchase results in CSV format.', 'dlseller'); ?></div></td>
	</tr>
</table>-->
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_terms');"><?php _e('Terms of Use', 'dlseller'); ?></a></th>
		<td><textarea name="dlseller_terms" cols="90" rows="10"><?php echo esc_html($dlseller_terms); ?></textarea></td>
		<td><div id="ex_dlseller_terms" class="explanation"><?php _e('Terms of Use', 'dlseller'); ?></div></td>
	</tr>
</table>
<table class="form_table">
	<tr height="40">
	    <th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_terms2');"><?php _e('Terms of Use for the Continuation charging', 'dlseller'); ?></a></th>
		<td><textarea name="dlseller_terms2" cols="90" rows="10"><?php echo esc_html($dlseller_terms2); ?></textarea></td>
	    <td><div id="ex_dlseller_terms2" class="explanation"><?php _e('Terms of Use for the Continuation charging', 'dlseller'); ?></div></td>
	</tr>
</table>
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_rate');"><?php _e('Transfer rate', 'dlseller'); ?></a></th>
		<td><input name="dlseller_rate" type="text" id="dlseller_rate" value="<?php echo $dlseller_rate; ?>" size="30" /></td>
		<td><div id="ex_dlseller_rate" class="explanation"><?php _e('The initial value is 1000. When an error occurs, please lower it to around 500.', 'dlseller'); ?></div></td>
	</tr>
</table>
</div><!--inside-->
</div><!--postbox-->
<div class="postbox">
<h3 class="hndle"><span><?php _e('Automatic processing Setting','dlseller'); ?></span></h3>
<div class="inside">
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_reminder_mail');"><?php _e('Settlement reminder-email', 'dlseller'); ?></a></th>
		<td><input name="dlseller_reminder_mail" type="radio" id="dlseller_reminder_mail_0" value="off"<?php if( $dlseller_reminder_mail == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="dlseller_reminder_mail_0"><?php _e("Don't send",'usces'); ?></label></td>
		<td><input name="dlseller_reminder_mail" type="radio" id="dlseller_reminder_mail_1" value="on"<?php if( $dlseller_reminder_mail == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="dlseller_reminder_mail_1"><?php _e("Send",'usces'); ?></label></td>
		<td><div id="ex_dlseller_reminder_mail" class="explanation"><?php _e('Reminder-email of settlement of the auto continuation charging.', 'dlseller'); ?></div></td>
	</tr>
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_contract_renewal_mail');"><?php _e('Contract renewal email', 'dlseller'); ?></a></th>
		<td><input name="dlseller_contract_renewal_mail" type="radio" id="dlseller_contract_renewal_mail_0" value="off"<?php if( $dlseller_contract_renewal_mail == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="dlseller_contract_renewal_mail_0"><?php _e("Don't send",'usces'); ?></label></td>
		<td><input name="dlseller_contract_renewal_mail" type="radio" id="dlseller_contract_renewal_mail_1" value="on"<?php if( $dlseller_contract_renewal_mail == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="dlseller_contract_renewal_mail_1"><?php _e("Send",'usces'); ?></label></td>
		<td><div id="ex_dlseller_contract_renewal_mail" class="explanation"><?php _e('Reminder-email of contract renewal of the auto continuation charging.', 'dlseller'); ?></div></td>
	</tr>
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_send_days_before');"><?php _e('Reminder-email sent date', 'dlseller'); ?></a></th>
		<td colspan="4"><input name="dlseller_send_days_before" type="text" id="dlseller_send_days_before" value="<?php echo $dlseller_send_days_before; ?>" size="5" /><?php _e('days before', 'dlseller'); ?></td>
		<td><div id="ex_dlseller_send_days_before" class="explanation"><?php _e('Send reminder-email to the number of days before. Specified value is 7 days ago.', 'dlseller'); ?></div></td>
	</tr>
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_scheduled_time');"><?php _e('Automatic processing execution time','dlseller'); ?></a></th>
		<td colspan="4">
			<select name="scheduled_time[hour]">
		<?php for( $i = 0; $i < 24; $i++ ):
				$hour = sprintf( '%02d', $i ); ?>
				<option value="<?php echo $hour; ?>"<?php if( $dlseller_scheduled_time['hour'] == $hour ) echo ' selected'; ?>><?php echo $hour; ?></option>
		<?php endfor; ?>
			</select>:&nbsp;<select name="scheduled_time[min]">
		<?php
			$i = 0;
			while( $i < 60 ):
				$min = sprintf( '%02d', $i ); ?>
				<option value="<?php echo $min; ?>"<?php if( $dlseller_scheduled_time['min'] == $min ) echo ' selected'; ?>><?php echo $min; ?></option>
		<?php	$i += 10;
			endwhile; ?>
			</select>
		</td>
		<td><div id="ex_dlseller_scheduled_time" class="explanation"><?php _e('Reminder-email will be sent to this time.','dlseller'); ?></div></td>
	</tr>
</table>
</div><!--inside-->
</div><!--postbox-->
</div><!--poststuff-->

<input name="dlseller_option_update" type="submit" class="button button-primary" value="<?php _e('change decision','usces'); ?>" />
<input type="hidden" name="post_ID" value="<?php echo USCES_CART_NUMBER; ?>" />
<input type="hidden" name="dlseller_transition" value="dlseller_option_update" />
<input type="hidden" name="scheduled_time_before[hour]" value="<?php echo $scheduled_time['hour']; ?>" />
<input type="hidden" name="scheduled_time_before[min]" value="<?php echo $scheduled_time['min']; ?>" />
</form>
</div><!--usces_admin-->
</div><!--wrap-->