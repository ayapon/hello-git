<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<?php usces_delivery_info_script(); ?>
<section class="container spacer">
<div id="primary" class="site-content">
	<div id="content" class="cart-page" role="main">

<?php if (have_posts()) : usces_remove_filter(); ?>
	
<div class="post" id="wc_<?php usces_page_name(); ?>">

<h1 class="cart_page_title"><?php _e('Shipping / Payment options', 'usces'); ?></h1>
<div class="entry">
		
<div id="delivery-info">
	
				<div class="cart_navi">
					<ul>
						<li><?php _e('1.Cart','usces'); ?></li>
						<li><?php _e('2.Customer Info','usces'); ?></li>
						<li class="current">支払い方法</li>
						<li><?php _e('4.Confirm','usces'); ?></li>
					</ul>
				</div>

	<div class="header_explanation">
<?php do_action('usces_action_delivery_page_header'); ?>
	</div>
	
	<div class="error_message"><?php usces_error_message(); ?></div>
	<form action="<?php usces_url('cart'); ?>" method="post">
<?php if( dlseller_have_shipped() ) : ?>

<?php endif; ?>
	<table class="customer_form" id="time">
<?php if( dlseller_have_shipped() ) : ?>
<input type="hidden" name="delivery[delivery_flag]" value='0' />
<input type="hidden" name="offer[delivery_method]" value='0'>
<input type="hidden" name="offer[delivery_date]" value=''>
<input type="hidden" name="offer[delivery_time]" value=''>
<?php endif; ?>
		<tr>
			<th scope="row"><em><?php _e('*', 'usces'); ?></em><?php _e('payment method', 'usces'); ?></th>
			<td colspan="2"><?php usces_the_payment_method( $usces_entries['order']['payment_name']); ?></td>
		</tr>
	</table>

<?php usces_delivery_secure_form(); ?>

<?php $meta = usces_has_custom_field_meta('order'); ?>
<?php if(!empty($meta) and is_array($meta)) : ?>
	<table class="customer_form" id="custom_order">
	<?php usces_custom_field_input($usces_entries, 'order', ''); ?>
	</table>
<?php endif; ?>

<?php if( dlseller_have_dlseller_content() ) : ?>


<h4><strong>新規店舗(追加含)お申込みのお客様へ</strong></h4>
お申込み種別で「新規店舗お申込み(追加含)」をお選びの上、<strong>ご希望のショップID（半角英数4字以上のみ）をご入力ください</strong>
ご希望のショップIDが未入力の場合は当社で指定させていただきます。また、第2希望までがすでに他のお客様で使用済みの場合は、弊社にご一任いただきますことをご了承ください。<br />
正式なショップIDは、アカウントのご利用準備が整い次第、あらためてご案内させていただきます。<br />
<br />
<h4><strong>ご利用プランのご変更のお客様へ</strong></h4>
すでにご利用いただいているプランのご変更のお申込みの場合、お申込み種別で「ご利用プラン変更」をお選びの上、<strong>ご希望のショップID欄は空白のまま「次へ」</strong>お進みください。こちらにご入力いただいても<strong>無効となります</strong>のでご了承ください。<br />
<br />
	<table class="customer_form">
		<tr>
		<th rowspan="2" scope="row"><em><?php _e('*', 'usces'); ?></em><?php _e('Terms of Use', 'dlseller'); ?></th>
		<td colspan="2"><div class="dlseller_terms"><?php dlseller_terms(); ?></div></td>
		</tr>
		<tr>
		<td colspan="2"><label for="terms"><input type="checkbox" name="offer[terms]" id="terms" /><?php _e('Agree', 'dlseller'); ?></label></td>
		</tr>
		</table>
<?php endif; ?>
<?php $entry_order_note = empty($usces_entries['order']['note']) ? apply_filters('usces_filter_default_order_note', NULL) : $usces_entries['order']['note']; ?>
	<table class="customer_form" id="notes_table">
		<tr>
			<th scope="row"><?php _e('Notes', 'usces'); ?></th>
			<td colspan="2"><textarea name="offer[note]" id="note" class="notes"><?php echo esc_html($entry_order_note); ?></textarea></td>
		</tr>
	</table>

	<div class="send"><input name="offer[cus_id]" type="hidden" value="" />		
	<input name="backCustomer" type="submit" class="back_to_customer_button" value="<?php _e('Back', 'usces'); ?>"<?php echo apply_filters('usces_filter_deliveryinfo_prebutton', NULL); ?> />&nbsp;&nbsp;
	<input name="confirm" type="submit" class="to_confirm_button" value="<?php _e(' Next ', 'usces'); ?>"<?php echo apply_filters('usces_filter_deliveryinfo_nextbutton', NULL); ?> /></div>
	<?php do_action('usces_action_delivery_page_inform'); ?>
	</form>

	<div class="footer_explanation">
<?php do_action('usces_action_delivery_page_footer'); ?>
	</div>
</div>

		</div><!-- end of entry -->
	</div><!-- end of post -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_sidebar( 'cartmember' ); ?>

	</div><!-- #content -->
</div><!-- #primary -->
</section>
<?php get_footer(); ?>
