<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>
<section class="container spacer">
<div id="primary" class="site-content">
	<div id="content" class="cart-page" role="main">

<?php if (have_posts()) : usces_remove_filter(); ?>

<div class="post" id="wc_<?php usces_page_name(); ?>">

<h1 class="cart_page_title"><?php _e('Customer Information', 'usces'); ?></h1>
<div class="entry">
		
<div id="customer-info">

				<div class="cart_navi">
					<ul>
						<li><?php _e('1.Cart','usces'); ?></li>
						<li class="current"><?php _e('2.Customer Info','usces'); ?></li>
						<li>支払い方法</li>
						<li><?php _e('4.Confirm','usces'); ?></li>
					</ul>
				</div>
	
	<div class="header_explanation">
	<?php do_action('usces_action_customer_page_header'); ?>
	</div><!-- end of header_explanation -->
	
	<div class="error_message"><?php usces_error_message(); ?></div>

<?php if( ! dlseller_have_dlseller_content() && ! dlseller_have_continue_charge() ) : ?>
	<h5><?php _e('The nonmember please enter at here.','usces'); ?></h5>
	<form action="<?php echo USCES_CART_URL; ?>" method="post" name="customer_form" onKeyDown="if (event.keyCode == 13) {return false;}">
	<table border="0" cellpadding="0" cellspacing="0" class="customer_form">
		<tr>
			<th scope="row"><em><?php _e('*', 'usces'); ?></em><?php _e('e-mail adress', 'usces'); ?></th>
			<td colspan="2"><input name="customer[mailaddress1]" id="mailaddress1" type="text" value="<?php echo esc_attr($usces_entries['customer']['mailaddress1']); ?>" placeholder="PCメールが受信可能なメールアドレスをご利用ください。" /></td>
		</tr>
		<tr>
			<th scope="row"><em><?php _e('*', 'usces'); ?></em><?php _e('e-mail adress', 'usces'); ?>(<?php _e('Re-input', 'usces'); ?>)</th>
			<td colspan="2"><input name="customer[mailaddress2]" id="mailaddress2" type="text" value="<?php echo esc_attr($usces_entries['customer']['mailaddress2']); ?>" placeholder="確認のため、再入力ください。" /></td>
		</tr>
	<?php if( usces_is_membersystem_state() ) : ?>
		<tr>
			<th scope="row"><?php if( $member_regmode == 'editmemberfromcart' ) : ?><em><?php _e('*', 'usces'); ?></em><?php endif; ?><?php _e('password', 'usces'); ?></th>
			<td colspan="2"><input name="customer[password1]" style="width:100px" type="password" value="<?php echo esc_attr($usces_entries['customer']['password1']); ?>" />お好きなパスワード。(半角英数6文字以上)</td>
		</tr>
		<tr>
			<th scope="row"><?php if( $member_regmode == 'editmemberfromcart' ) : ?><em><?php _e('*', 'usces'); ?></em><?php endif; ?><?php _e('Password (confirm)', 'usces'); ?></th>
			<td colspan="2"><input name="customer[password2]" style="width:100px" type="password" value="<?php echo esc_attr($usces_entries['customer']['password2']); ?>" />確認のため、再入力ください。</td>
		</tr>
	<?php endif; ?>


	<?php uesces_addressform( 'customer', $usces_entries, 'echo' ); ?>
	</table>
					<?php usces_agree_member_field(); ?>

	<input name="member_regmode" type="hidden" value="<?php echo $member_regmode; ?>" />
	<input name="member_id" type="hidden" value="<?php echo usces_memberinfo('ID'); ?>" />
	<div class="send">
	<?php usces_get_customer_button(); ?>
	</div>

	<?php do_action('usces_action_customer_page_inform'); ?>
	</form>
<?php endif; ?>


<br />
<hr />
<?php if( usces_is_membersystem_state() ) : ?>
	<h5 id="cart_member_login"><?php _e('The member please enter at here.','usces'); ?></h5>
	<form action="<?php usces_url('cart'); ?>" method="post" name="customer_loginform" onKeyDown="if (event.keyCode == 13) {return false;}">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="customer_form">
		<tr>
			<th scope="row"><?php _e('e-mail adress', 'usces'); ?></th>
			<td><input name="loginmail" id="mailaddress1" type="text" value="<?php echo esc_attr($usces_entries['customer']['mailaddress1']); ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('password', 'usces'); ?></th>
			<td><input name="loginpass" id="mailaddress1" type="password" value="" /></td>
		</tr>
	</table>
	<p id="nav">
	<a class="lostpassword" href="<?php usces_url('lostmemberpassword'); ?>"><?php _e('Did you forget your password?', 'usces'); ?></a>
	</p>
<!--	<p id="nav">
	<a class="newmember" href="<?php usces_url('newmember'); ?>&dlseller_transition=newmember"><?php _e('New enrollment for membership.', 'usces'); ?></a>
	</p>-->
	<div class="send">
	<input name="backCart" type="submit" class="back_cart_button" value="<?php _e('Back', 'usces'); ?>" />&nbsp;&nbsp;
	<input name="customerlogin" type="submit" value="<?php _e(' Next ', 'usces'); ?>" /></div>
	<?php do_action('usces_action_customer_page_member_inform'); ?>
	</form>
<?php endif; ?>

	<div class="footer_explanation">
	<?php do_action('usces_action_customer_page_footer'); ?>
	</div><!-- end of footer_explanation -->
</div><!-- end of customer-info -->

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
