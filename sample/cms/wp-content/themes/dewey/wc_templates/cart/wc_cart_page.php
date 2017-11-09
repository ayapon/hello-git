<?php
/**
 * @package Welcart
 * @subpackage Welcart_Basic
 */

get_header();
?>
<section class="container spacer">
<div id="primary" class="site-content">
	<div id="content" class="cart-page" role="main">

	<?php if( have_posts() ) : usces_remove_filter(); ?>

		<article class="post" id="wc_<?php usces_page_name(); ?>">

			<h1 class="cart_page_title"><?php _e('In the cart', 'usces'); ?></h1>

			<div class="cart_navi">
				<ul>
					<li class="current"><?php _e('1.Cart','usces'); ?></li>
					<li><?php _e('2.Customer Info','usces'); ?></li>
					<li>お支払方法</li>
					<li><?php _e('4.Confirm','usces'); ?></li>
				</ul>
			</div>

			<div class="header_explanation">
				<?php do_action( 'usces_action_cart_page_header' ); ?>
			</div><!-- .header_explanation -->

			<div class="error_message"><?php usces_error_message(); ?></div>

			<form action="<?php usces_url('cart'); ?>" method="post" onKeyDown="if(event.keyCode == 13){return false;}">
			<?php if( usces_is_cart() ) : ?>
				<div id="cart">
<!--					<div class="upbutton"><?php _e('Press the `update` button when you change the amount of items.','usces'); ?><input name="upButton" type="submit" value="<?php _e('Quantity renewal','usces'); ?>" onclick="return uscesCart.upCart()" /></div>-->

					<table cellspacing="0" id="cart_table">
						<thead>
						<tr>

							<th class="thumbnail"> </th>
							<th class="productname"><?php _e('item name','usces'); ?></th>
							<th class="subtotal"><?php _e('Amount','usces'); ?><?php usces_guid_tax(); ?></th>
							<th class="action"></th>
						</tr>
						</thead>
						<tbody>
							<?php usces_get_cart_rows(); ?>
<!--						<tr>
							<th class="thumbnail"></th>
							<th scope="row" class="aright"><?php _e('total items','usces'); ?><?php usces_guid_tax(); ?></th>
							<th class="aright amount"><?php usces_crform(usces_total_price('return'), true, false); ?></th>
							<th class="action"></th>
						</tr>-->
						</tbody>
						<tfoot>

<?php if ( 'exclude' == $this->options['tax_mode'] ): ?>
<?php
    $total_price = usces_total_price('return') - usces_order_discount('return');
    $tax = $this -> getTax( $total_price );
?>
    <tr>
        <td class="thumbnail"> </td>
        <td class="aright"><?php _e('consumption tax', 'usces'); ?></td>
        <td class="aright"><?php echo usces_crform($tax, true, false); ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <th class="thumbnail"> </th>
        <th class="aright"><?php _e('total items','usces'); ?>月額<em class="tax">（税込）</em></th>
        <th class="aright"><?php echo usces_crform(($total_price + $tax), true, false); ?></th>
        <th>&nbsp;</th>
    </tr>
<?php endif; ?>

						</tfoot>
					</table>

<div class="attention cartatention">※プランは<strong>必ず1つだけ選択</strong>いただくようにお願いします。複数表示されている場合は、<strong>1つになるように不要なものを削除</strong>してください。<br />
※有料プランも当月内は無料にてご利用いただけます。翌月1日より上記金額が毎月課金されます。</div>

					<?php if( $usces_gp ) : ?>
					<div class="gp"><img src="<?php bloginfo('template_directory'); ?>/images/gp.gif" alt="<?php _e('Business package discount','usces'); ?>" /><span><?php _e('The price with this mark applys to Business pack discount.','usces'); ?></span></div>
					<?php endif; ?>
				</div><!-- #cart -->
			<?php else : ?>
				<div class="no_cart"><?php _e('There are no items in your cart.','usces'); ?></div>
			<?php endif; ?>

				<div class="send"><?php usces_get_cart_button(); ?></div>
				<?php do_action( 'usces_action_cart_page_inform' ); ?>
			</form>

			<div class="footer_explanation">
				<?php do_action( 'usces_action_cart_page_footer' ); ?>
			</div><!-- .footer_explanation -->

		</article><!-- .post -->

	<?php else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>

	</div><!-- #content -->
</div><!-- #primary -->
</section>

<?php get_footer(); ?>
