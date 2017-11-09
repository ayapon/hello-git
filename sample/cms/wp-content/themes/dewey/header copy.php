<?php
/**
 * @package DEWEY
 * @subpackage Welcart_Basic
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<meta name="format-detection" content="telephone=no"/>
<link href='https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,700,800,900|Montserrat:400,700' rel='stylesheet' type='text/css'>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="logo" href="/"><img src="/images/logo.svg" alt="<?php bloginfo( 'name' ); ?>"></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="../index.php#pricing">料金プラン</a></li>
            <li><a href="../form/?business=inquiry">お問い合せ</a></li>
            <li><a href="../login.php">ログイン</a></li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

	<header id="masthead" class="site-header" role="banner">
		
		<div class="inner cf">



			<?php if(! welcart_basic_is_cart_page()): ?>
			
			<div class="snav cf">


				<?php if(usces_is_membersystem_state()): ?>
				<div class="membership">
					<i class="fa fa-user"></i>
					<ul class="cf">
						<?php if( usces_is_login() ): ?>
							<li><?php printf(__('Hello %s', 'usces'), usces_the_member_name('return')); ?></li>
							<li><?php usces_loginout(); ?></li>
							<li><a href="<?php echo USCES_MEMBER_URL; ?>"><?php _e('My page', 'welcart_basic') ?></a></li>
						<?php else: ?>
							<li><?php _e('guest', 'usces'); ?></li>
							<li><?php usces_loginout(); ?></li>
							<li><a href="<?php echo USCES_NEWMEMBER_URL; ?>"><?php _e('New Membership Registration','usces') ?></a></li>
						<?php endif; ?>
					</ul>
				</div>
				<?php endif; ?>

				<div class="incart-btn">
					<a href="<?php echo USCES_CART_URL; ?>"><i class="fa fa-shopping-cart"><span><?php _e('In the cart', 'usces') ?></span></i><?php if(! defined( 'WCEX_WIDGET_CART' ) ): ?><span class="total-quant"><?php usces_totalquantity_in_cart(); ?></span><?php endif; ?></a>
				</div>
			</div><!-- .snav -->

			<?php endif; ?>
			
		</div><!-- .inner -->

		<?php if(! welcart_basic_is_cart_page()): ?>
		
		<nav id="site-navigation" class="main-navigation" role="navigation">
			<label for="panel"><span></span></label>
			<input type="checkbox" id="panel" class="on-off" />
			<?php 
				$page_c	=	get_page_by_path('usces-cart');
				$page_m	=	get_page_by_path('usces-member');
				$pages	=	"{$page_c->ID},{$page_m->ID}";
				wp_nav_menu( array( 'theme_location' => 'header', 'container_class' => 'nav-menu-open' , 'exclude' => $pages ,  'menu_class' => 'header-nav-container cf' ) );
			?>
		</nav><!-- #site-navigation -->
		
		<?php endif; ?>

	</header><!-- #masthead -->

	<?php 
		if( is_front_page() || is_home() || welcart_basic_is_cart_page() || welcart_basic_is_member_page() ) {
			$class = 'one-column';	
		}else {
			$class = 'two-column right-set';
		};
	?>

