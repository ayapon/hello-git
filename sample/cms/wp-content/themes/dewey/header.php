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
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?>" />
	<link href="/images/apple-touch-icon.png" rel="apple-touch-icon">
	<link rel="shortcut icon" href="/favicon.ico" />
	<?php wp_head(); ?>

<?php if(is_page( array('usces-cart', 'usces-member' ))): ?>
<link rel="stylesheet" href="/store/wp-content/themes/dewey/css/validationEngine.jquery.css" type="text/css"/>
<script type="text/javascript">
jQuery(function(){
jQuery("#name1, #name2, #zipcode, #customer_pref, #address1, #address2").addClass("validate[required]");
jQuery("input[name*='mailaddress1'],input[name*='mailaddress2']").addClass("validate[required,custom[email]]");
jQuery("#tel").addClass("validate[required,custom[tel]]");
jQuery("#customer_pref option:first-child").val("");
    jQuery("form").validationEngine();
    jQuery(".back_cart_button, .back_to_customer_button").click(function(){
        jQuery("form").validationEngine('hideAll');
        jQuery("form").validationEngine('detach');
        return true;
     });
});
</script>
<?php endif; ?>

</head>

<body <?php body_class(); ?> id="<?php echo esc_attr( $post->post_name ); ?>">

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="logo" href="/" title="<?php bloginfo('description'); ?> | <?php bloginfo( 'name' ); ?>"><img src="/images/logo.svg" alt="<?php bloginfo( 'name' ); ?>"></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
<?php if( is_front_page() || is_home() ): ?>
            <li><a href="/index.php#pricing" class="scroll">料金プラン</a></li>
<?php else: ?>
            <li><a href="/index.php#pricing">料金プラン</a></li>
<?php endif; ?>
            <li><a href="/faq">よくある質問</a></li>
            <li><a href="/form">お問い合せ</a></li>
            <li><a href="/usces-member">ログイン</a></li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

	<?php if( is_front_page() || is_home() ): ?>
    <header>
      <div class="container">
        <div class="row header-info">
          <div class="col-sm-10 col-sm-offset-1 text-center">
            <h1 class="wow fadeIn">クラブ ラウンジ スナック<br />
 居酒屋など飲食店の<br />
運営補助アプリケーション</h1>
            <br />
            <p class="lead wow fadeIn" data-wow-delay="0.5s">例えばボトルキープの管理、紙の台帳から、そろそろ卒業しませんか？<br>
            あるいは顧客管理、案内状の送付先などお客様との繋がりをサポート。</p>
              
            <div class="row">
              <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                <div class="row">
                  <div class="col-xs-6 text-right wow fadeInUp" data-wow-delay="1s">
                    <a href="#be-the-first" class="btn btn-secondary btn-lg scroll">もっと詳しく！</a>
                  </div>
                  <div class="col-xs-6 text-left wow fadeInUp" data-wow-delay="1.4s">
                    <a href="#invite" class="btn btn-primary btn-lg scroll">サンプルを見る</a>
                  </div>
                </div><!--End Button Row-->  
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </header>
	<?php else: ?>
    <header>
    </header>
	<?php endif; ?>

	<?php if( is_front_page() || is_home() ) { ?>
    <div class="mouse-icon hidden-xs">
				<div class="scroll"></div>
			</div>
	<?php }; ?>