<?php
/**
 * @package Welcart
 * @subpackage Welcart_Basic
 */

//---------------------------------------------------------------------------
// WordpressのJavascriptやCSSのハンドル名をHTMLソースに表示する
//---------------------------------------------------------------------------
// function my_get_dependency( $dependency ) {
//     $dep = "";
//     if ( is_a( $dependency, "_WP_Dependency" ) ) {
//         $dep .= "$dependency->handle";
//         $dep .= " [" . implode( " ", $dependency->deps ) . "]";
//         $dep .= " '$dependency->src'";
//         $dep .= " '$dependency->ver'";
//         $dep .= " '$dependency->args'";
//         $dep .= " (" . implode( " ", $dependency->extra ) . ")";
//     }
//     return "$dep\n";
// }
//  
// function my_style_queues() {
//     global $wp_styles;
//     echo "<!-- WP_Dependencies for styles\n";
//     foreach ( $wp_styles->queue as $val ) {
//         echo my_get_dependency( $wp_styles->registered[ $val ] );
//     }
//     echo "-->\n";
// }
// add_action( 'wp_print_styles', 'my_style_queues', 9999 );
//  
// function my_script_queues() {
//     global $wp_scripts;
//     echo "<!-- WP_Dependencies for scripts\n";
//     foreach ( $wp_scripts->queue as $val ) {
//         echo my_get_dependency( $wp_scripts->registered[ $val ] );
//     }
//     echo "-->\n";
// }
// add_action( 'wp_print_scripts', 'my_script_queues', 9999 );

/* 不要なCSSを停止 */

function my_scripts_method() {
wp_dequeue_style('siteorigin-panels-front');
wp_dequeue_style('vkExUnit_common_style');
wp_dequeue_style('wc-basic-style');
wp_dequeue_style('usces_default_css');
wp_dequeue_style('theme_cart_css');
}
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

//　改行の時に自動的にPタグが挿入されるのを防ぐ
remove_filter('the_content', 'wpautop');
remove_filter( 'the_excerpt', 'wpautop' );

/* WordpressのJqueryを停止 */
function stop_wp_jq() {
if ( !is_admin() ) {
wp_deregister_script('jquery');
//wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', array(), '1.11.3');
}
}
add_action('init', 'stop_wp_jq');

/* .css,.jsファイルを読み込ませないためのコード */
// function my_scripts_method() {
// wp_dequeue_script('toc-front');
// wp_dequeue_style('toc-screen');
// wp_dequeue_style('wp-pagenavi');
// wp_dequeue_script( 'comment-reply' );
// wp_dequeue_script( 'twentytwelve-navigation' );
// }
// add_action( 'wp_enqueue_scripts', 'my_scripts_method' );


/***********************************************************
* check the execution of welcart
***********************************************************/
// if( !welcart_basic_is_active( 'usc-e-shop1.9.0/usc-e-shop.php' ) ) {
// 	add_action( 'admin_notices', 'welcart_basic_echo_message' );
// }
// 
// function welcart_basic_echo_message() {
// 	echo '<div class="message error"><p>'.__("Welcart Basic theme requires <strong>Welcart e-Commerce</strong>. Please <a href=\"plugins.php\">enable Welcart e-Commerce</a>.", 'welcart_basic' ).'</p></div>';
// }

function welcart_basic_is_active( $plugin ) {
	if( function_exists('is_plugin_active') ) {
		return is_plugin_active( $plugin );
	} else {
		return in_array(
			$plugin,
			get_option('active_plugins')
		);
	}
}

/***********************************************************
* welcart_setup
***********************************************************/
if ( ! function_exists( 'welcart_basic_setup' ) ):
function welcart_basic_setup() {

	load_theme_textdomain( 'welcart_basic', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );

	register_nav_menus( array(
		'header' => __( 'Header Navigation', 'usces' ),
		'footer' => __( 'Footer Navigation', 'usces' ),
	) );

	add_theme_support( 'custom-header', apply_filters( 'welcart_basic_custom_header_args', array(
		'default-image'	=> get_template_directory_uri() . '/images/image-top.jpg',
		'width'			=> '1000',
		'height'		=> '400',
		'header-text'	=> false,
	) ) );
	register_default_headers( array(
		'basic-default'	=> array(
			'url'			=> '%s/images/image-top.jpg',
			'thumbnail_url'	=> '%s/images/image-top.jpg',
		)
	) );
}
endif;
add_action( 'after_setup_theme', 'welcart_basic_setup' );

if ( !defined('USCES_VERSION') ) return;

/***********************************************************
* includes
***********************************************************/
require( get_template_directory() . '/inc/template-functions.php' );
require( get_template_directory() . '/inc/widget-customized.php' );
require( get_template_directory() . '/inc/front-customized.php' );
/*	Theme customizer	*/
require( dirname( __FILE__ ) . '/inc/theme-customizer.php' );

/*-------------------------------------------*/
/*	Admin page _ Add style
/*-------------------------------------------*/
function basic_admin_enqueue( $hook ){
	if( 'welcart-shop_page_usces_itemedit' == $hook || 'widgets.php' == $hook ){
		wp_enqueue_style( 'basic_admin_style', get_template_directory_uri() . '/css/admin.css', array() );
	}
}
add_action( 'admin_enqueue_scripts', 'basic_admin_enqueue' );

function welcart_theme_version(){
	$themename = 'welcart_basic';
	$theme = wp_get_theme( $themename );
	$theme_ver = !empty($theme) ? $theme->get('Version') : '0';
	echo "<!-- Type Basic : v".$theme_ver." -->\n";
}
add_action( 'wp_footer', 'welcart_theme_version' );

/***********************************************************
* welcart_page_menu_args
***********************************************************/
function welcart_basic_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'welcart_basic_page_menu_args' );

/***********************************************************
* sidebar
***********************************************************/
function welcart_basic_widgets_init() {

	register_sidebar(array(
		'name' => __( 'Home Left Widget', 'welcart_basic' ),
		'id' => 'left-widget-area',
		'description' => __( 'Widget area left of the top page footer top', 'welcart_basic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget_title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'Home Center Widget', 'welcart_basic' ),
		'id' => 'center-widget-area',
		'description' => __( 'Widget area center of the top page footer top', 'welcart_basic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget_title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' =>  __( 'Home Right Widget', 'welcart_basic' ),
		'id' => 'right-widget-area',
		'description' => __( 'Widget area right of the top page footer top', 'welcart_basic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget_title">',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name' => __( 'Sidebar Widget 1', 'welcart_basic' ),
		'id' => 'side-widget-area1',
		'description' => __( 'Widget area Product Details page or category page or search page', 'welcart_basic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget_title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'Sidebar Widget 2', 'welcart_basic' ),
		'id' => 'side-widget-area2',
		'description' => __( 'Widget area of posts and pages', 'welcart_basic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget_title">',
		'after_title' => '</h3>',
	));
}
add_action( 'widgets_init', 'welcart_basic_widgets_init' );

/***********************************************************
* wp_enqueue_scripts
***********************************************************/
if ( ! function_exists( 'welcart_basic_scripts_styles' ) ) {
	function welcart_basic_scripts_styles() {
		global $usces, $is_IE;

		$template_dir = get_template_directory_uri();

		wp_enqueue_style( 'wc-basic-style', get_stylesheet_uri(), array(), '1.0' );
// 		wp_enqueue_style( 'font-awesome', $template_dir . '/font-awesome/font-awesome.min.css', array(), '1.0' );
		
// 		if(defined('WCEX_POPLINK') ) {
// 			if( welcart_basic_is_poplink_page() ) {
// 				wp_enqueue_style( 'wc_basic_poplink', $template_dir . '/css/poplink.css', array(), '1.0' );
// 			}
// 		}
		
		if( $is_IE ) {
			wp_enqueue_style( 'wc-basic-ie', $template_dir . '/css/ie.css', array(), '1.0' );
			wp_enqueue_script( 'wc_basic_css3', $template_dir . '/js/css3-mediaqueries.js', array(), '1.0');
			wp_enqueue_script( 'wc-basic_html5', $template_dir . '/js/html5shiv.js', array(), '1.0');
		}
		
// 		wp_enqueue_script( 'wc-basic-js', $template_dir . '/js/front-customized.js', array(), '1.0' );
	}
}
add_action( 'wp_enqueue_scripts', 'welcart_basic_scripts_styles' );

// function welcart_basic_swipebox_scripts() {
// 	if( is_singular() ) {
// 		$template_dir = get_template_directory_uri();
// 		wp_enqueue_style( 'swipebox-style', $template_dir .'/css/swipebox.min.css' );
// 		wp_enqueue_script( 'swipebox', $template_dir . '/js/jquery.swipebox.min.js', array(), '1.4.1');
// 		wp_enqueue_script( 'wc-basic_swipebox', $template_dir . '/js/wb-swipebox.js', array('swipebox'), '1.0');
// 	}
// }
// add_action( 'wp_enqueue_scripts', 'welcart_basic_swipebox_scripts' );

/***********************************************************
* excerpt
***********************************************************/
if ( ! function_exists( 'welcart_assistance_excerpt_length' ) ) {
	function welcart_assistance_excerpt_length( $length ) {
		return 10;
	}
}
if ( ! function_exists( 'welcart_assistance_excerpt_mblength' ) ) {
	function welcart_assistance_excerpt_mblength( $length ) {
		return 40;
	}
}
if ( ! function_exists( 'welcart_excerpt_length' ) ) {
	function welcart_excerpt_length( $length ) {
		return 40;
	}
}
add_filter( 'excerpt_length', 'welcart_excerpt_length' );

if ( ! function_exists( 'welcart_excerpt_mblength' ) ) {
	function welcart_excerpt_mblength( $length ) {
		return 110;
	}
}
add_filter( 'excerpt_mblength', 'welcart_excerpt_mblength' );

if ( ! function_exists( 'welcart_continue_reading_link' ) ) {
	function welcart_continue_reading_link() {
		return ' <a href="'. get_permalink() . '">' . __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'welcart_basic' ) . '</a>';
	}
}
if ( ! function_exists( 'welcart_auto_excerpt_more' ) ) {
	function welcart_auto_excerpt_more( $more ) {
		return ' &hellip;' . welcart_continue_reading_link();
	}
}
//add_filter( 'excerpt_more', 'welcart_auto_excerpt_more' );

if ( ! function_exists( 'welcart_custom_excerpt_more' ) ) {
	function welcart_custom_excerpt_more( $output ) {
		if ( has_excerpt() && ! is_attachment() ) {
			$output .= welcart_continue_reading_link();
		}
		return $output;
	}
}
//add_filter( 'get_the_excerpt', 'welcart_custom_excerpt_more' );

/***********************************************************
* pre_get_posts
***********************************************************/
function welcart_basic_query( $query ) {
	$item_cat = get_category_by_slug('item');
	$item_cat_id = $item_cat->cat_ID;
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_search && !isset($_GET['searchitem']) ) {
		$query->set('category_name','item');
	}
}
add_action( 'pre_get_posts', 'welcart_basic_query' );

/***********************************************************
 * search_form
************************************************************/
function welcart_basic_search_form( $form ) {
    $form = '<form role="search" method="get" id="searchform" action="'.home_url( '/' ).'" >
		<div class="s-box">
			<input type="text" value="' . get_search_query() . '" name="s" id="s" />
			<input type="submit" id="searchsubmit" value="&#xf002" />
		</div>
    </form>';
    return $form;
}
add_filter( 'get_search_form', 'welcart_basic_search_form' );

/***********************************************************
* Plugin Hook Remove
***********************************************************/
//WCEX Mobile
remove_filter( 'usces_filter_cart_row', 'wcmb_cart_row_of_smartphone_wct', 10, 3);
remove_filter( 'usces_filter_confirm_row', 'wcmb_confirm_row_of_smartphone_wct', 10, 3);

function welcart_basic_the_post(){
	global $post;

	if ( 'item' === $post->post_mime_type ){
		$select_sku_switch = get_post_meta($post->ID, '_select_sku_switch', true);
		if(  !defined("WCEX_SKU_SELECT") || 1 != $select_sku_switch ){
			remove_action( 'usces_action_single_item_outform', 'wcad_action_single_item_outform' );
			add_action( 'usces_action_single_item_outform', 'welcart_basic_action_single_item_outform' );
		}
	}
}
add_action( 'the_post', 'welcart_basic_the_post', 9 );

function welcart_basic_init(){
	add_filter('wcex_sku_select_filter_single_item_autodelivery', 'welcart_basic_single_item_autodelivery_sku_select');
}
add_action( 'init', 'welcart_basic_init', 9 );

//特定プラグインの更新通知を非表示に
add_filter('site_transient_update_plugins', 'custom_site_transient_update_plugins');
function custom_site_transient_update_plugins($value) {
    $ignore_plugins = array(
        'usc-e-shop/usc-e-shop.php'
    );
    foreach ($ignore_plugins as $ignore_plugin) {
        if (isset($value->response[$ignore_plugin])) {
            unset($value->response[$ignore_plugin]);
        }
    }
    return $value;
}

/* アイキャッチ画像の有効化 */
add_theme_support( 'post-thumbnails', array( 'post' ) );

/* ダッシュボードのウェルカムスクリーン（ようこそ）をデフォルトで非表示 */
remove_action( 'welcome_panel', 'wp_welcome_panel' );


/* 管理画面下部のwp表記を変更 */
function remove_footer_admin () {
	echo 'Supported by <a href="http://www.dewey.co.jp" target="_blank">DEWEY Inc.</a>';
	}
add_filter('admin_footer_text', 'remove_footer_admin');

//ヘッダのgeneratorを非表示に
remove_action('wp_head', 'wp_generator');

//ログインロゴのリンク先をホームに変更
function custom_login_logo_link(){
    return get_bloginfo('home');
}
add_filter('login_headerurl','custom_login_logo_link');

//「ダッシュボード」の不要なコンテンツを非表示
function tidy_dashboard(){
  global $wp_meta_boxes, $current_user;

  // remove incoming links info for authors or editors
  if(in_array('author', $current_user->roles) || in_array('editor', $current_user->roles))
  {
    unset($wp_meta_boxes['dashboard']['normal ']['core']['dashboard_incoming_links']);
  }

  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']); //プラグイン
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']); //WordPress開発ブログ
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); //WordPressフォーラム
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']); //概要
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']); //最近のコメント
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']); //被リンク
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']); //クイック投稿
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']); //最近の下書き
}
//add our function to the dashboard setup hook
add_action('wp_dashboard_setup', 'tidy_dashboard');

//ログインロゴをオリジナルに変更するためのCSS配置
function login_css() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo("template_directory").'/login.css">';
}
add_action('login_head', 'login_css');

// 管理画面 wordpressロゴ、リンク削除
function remove_admin_bar_menu( $wp_admin_bar ) {
$wp_admin_bar->remove_menu( 'wp-logo' );
}
add_action( 'admin_bar_menu', 'remove_admin_bar_menu', 70 );

//管理画面のタイトルからwordpressを削除する
add_filter('admin_title', 'my_admin_title', 10, 2);
function my_admin_title($admin_title, $title)
{
    return $title .' &lsaquo; ' . get_bloginfo('name');
}

//ダッシュボードのヘルプを削除
function disable_help_link() {
    echo '<style type="text/css">
#contextual-help-link-wrap {display: none !important;}
</style>';
}
add_action('admin_head', 'disable_help_link');

//WPからのメールの送信者をwordpress@から変更
add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');

function new_mail_from($old) {
	return 'no-reply@nightworks.jp';//変更したいメールアドレスを入力
}
function new_mail_from_name($old) {
	return 'NightWorks（顧客・ボトル管理アプリ）';//サイト名を入力
}

//ユーザー名でもメールアドレスでもログイン出来るようにする
function login_with_email_address($username) {
        $user = get_user_by('email',$username);
        if(!empty($user->user_login))
                $username = $user->user_login;
        return $username;
}
add_action('wp_authenticate','login_with_email_address');
function change_username_text($text){
       if(in_array($GLOBALS['pagenow'], array('wp-login.php'))){
         if ($text == 'ユーザー名'){$text = 'ユーザー名 又は メールアドレス';}
            }
                return $text;
         }
add_filter( 'gettext', 'change_username_text' );

//Welcart
/** 会員登録必須 */
function my_usces_filter_customer_button() {
	// 次へ・・・文言撤去
	return "";
}
add_filter( 'usces_filter_customer_button', 'my_usces_filter_customer_button' );


//Welcartに並び替え機能追加
 
//投稿一覧から商品を外すフィルターを削除
add_action( 'pre_get_posts', 'display_items',5); function display_items( $query ){ if (is_admin() && $_GET['display'] == 'welcart-item'){ global $usces; remove_filter( 'pre_get_posts', array(&$usces, 'filter_divide_item') ); $query->set('posts_per_page',999);
}
}
//アドミンバーにメニューを追加
add_action('admin_bar_menu', 'display_items_menu', 9999);
function display_items_menu($wp_admin_bar){
$wp_admin_bar->add_menu(array(
'id' => 'wp-item-list',
'meta' => array(),
'title' => '商品並替',
'href' => site_url('/wp-admin/edit.php?display=welcart-item')
));
}


//wp-adminのheadに読み込み
function my_admin_meta() {
  echo '
	<meta name="apple-mobile-web-app-title" content="ダッシュボード" />
	<link href="/images/apple-touch-icon.png" rel="apple-touch-icon">
  '.PHP_EOL;
}
add_action('admin_print_styles', 'my_admin_meta');


//アイキャッチCSS
function inc_welcart_css_list() {
    echo '<style>.column-thumbnail{width:60px;}</style>';
}
add_action('admin_print_styles', 'inc_welcart_css_list', 21);

//JetpackのOGPタグの出力を無効化
add_filter( 'jetpack_enable_open_graph', '__return_false' );

//Welcart特定の商品の場合に支払い方法を限定
add_filter('usces_fiter_the_payment_method', 'my_the_payment_method', 10, 2);
function my_the_payment_method($payments, $value){
    global $usces;
    $carts = $usces->cart->get_cart();
    $mysku = array('P001','P021'); //特定の商品のSKU
    foreach($carts as $cart){
        $sku = $cart['sku'];
        if(in_array($sku, $mysku)){
            $payments = array(
               array(
                   'id' => 3,
                   'name' => '無料', //支払方法名
                   'explanation' => 'このプランの料金は無料です。', //説明
                   'settlement' => 'COD', //決済種別
                   'module' => '', //決済モジュール
                   'sort' => 0, //表示順序
                   'use' => 'activate', //activeで「使用」
               ),
           );
        }
    }
    return $payments;
}

//Welcart特定の商品の場合に支払い方法を限定2
add_filter('usces_fiter_the_payment_method', 'my_the_payment_method2', 10, 2);
function my_the_payment_method2($payments, $value){
    global $usces;
    $carts = $usces->cart->get_cart();
    $mysku = array('P042'); //特定の商品のSKU
    foreach($carts as $cart){
        $sku = $cart['sku'];
        if(in_array($sku, $mysku)){
            $payments = array(
               array(
                   'id' => 4,
                   'name' => 'クレジットカード(再開専用)', //支払方法名
                   'explanation' => 'ご利用再開専用のご決済になります。(以前ご利用のカードとは異なるカードをご利用ください)', //説明
                   'settlement' => 'acting_webpay', //決済種別
                   'module' => '', //決済モジュール
                   'sort' => 0, //表示順序
                   'use' => 'activate', //activeで「使用」
               ),
           );
        }
    }
    return $payments;
}

//Welcartお客様情報のFAX入力フィールドを非表示
add_filter('usces_filter_apply_addressform', 'my_apply_addressform', 10, 3);
function my_apply_addressform($formtag, $type, $data){
    global $usces, $usces_settings;
    $options = get_option('usces');
    $form = $options['system']['addressform'];
    $nameform = $usces_settings['nameform'][$form];
    $values = $data[$type];
    $applyform = usces_get_apply_addressform($form);
    $formtag = usces_custom_field_input($data, $type, 'name_pre', 'return');
    $formtag .= '<tr class="inp1">
    <th width="127" scope="row">' . usces_get_essential_mark('name1', $data) . __('Full name', 'usces').'</th>';
    if( $nameform ){
        $formtag .= '<td class="name_td">'.__('Given name', 'usces').'<input name="' . $type . '[name2]" id="name2" type="text" value="' . esc_attr($values['name2']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>';
        $formtag .= '<td class="name_td">'.__('Familly name', 'usces').'<input name="' . $type . '[name1]" id="name1" type="text" value="' . esc_attr($values['name1']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>';
    }else{
        $formtag .= '<td class="name_td">'.__('Familly name', 'usces').'<input name="' . $type . '[name1]" id="name1" type="text" value="' . esc_attr($values['name1']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>';
        $formtag .= '<td class="name_td">'.__('Given name', 'usces').'<input name="' . $type . '[name2]" id="name2" type="text" value="' . esc_attr($values['name2']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>';
    }
    $formtag .= '</tr>';
    $furigana = '<tr class="inp1">
    <th scope="row">' . usces_get_essential_mark('name3', $data).__('furigana', 'usces').'</th>';
    if( $nameform ){
        $furigana .= '<td>'.__('Given name', 'usces').'<input name="' . $type . '[name4]" id="name4" type="text" value="' . esc_attr($values['name4']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>';
        $furigana .= '<td>'.__('Familly name', 'usces').'<input name="' . $type . '[name3]" id="name3" type="text" value="' . esc_attr($values['name3']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>';
    }else{
        $furigana .= '<td>'.__('Familly name', 'usces').'<input name="' . $type . '[name3]" id="name3" type="text" value="' . esc_attr($values['name3']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>';
        $furigana .= '<td>'.__('Given name', 'usces').'<input name="' . $type . '[name4]" id="name4" type="text" value="' . esc_attr($values['name4']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>';
    }
    $furigana .= '</tr>';
    $formtag .= apply_filters( 'usces_filter_furigana_form', $furigana, $type, $values );
    $formtag .= usces_custom_field_input($data, $type, 'name_after', 'return');
    $formtag .= '<tr>
    <th scope="row">' . usces_get_essential_mark('zipcode', $data).__('Zip/Postal Code', 'usces').'</th>
    <td colspan="2"><input name="' . $type . '[zipcode]" id="zipcode" type="text" value="' . esc_attr($values['zipcode']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: inactive" />'.apply_filters('usces_filter_addressform_zipcode', NULL, $type) . apply_filters( 'usces_filter_after_zipcode', '100-1000', $applyform ) . '</td>
    </tr>';
    if( count( $options['system']['target_market'] ) == 1 ){
        $formtag .= '<input type="hidden" name="' .$type. '[country]" id="' .$type. '_country" value="' .$options['system']['target_market'][0]. '">';
    }else{
        $formtag .= '<tr>
        <th scope="row">' . usces_get_essential_mark('country', $data) . __('Country', 'usces') . '</th>
        <td colspan="2">' . uesces_get_target_market_form( $type, $values['country'] ) . apply_filters( 'usces_filter_after_country', NULL, $applyform ) . '</td>
        </tr>';
    }
    $formtag .= '<tr>
    <th scope="row">' . usces_get_essential_mark('states', $data).__('Province', 'usces').'</th>
    <td colspan="2">' . usces_pref_select( $type, $values ) . apply_filters( 'usces_filter_after_states', NULL, $applyform ) . '</td>
    </tr>
    <tr class="inp2">
    <th scope="row">' . usces_get_essential_mark('address1', $data).__('city', 'usces').'</th>
    <td colspan="2"><input name="' . $type . '[address1]" id="address1" type="text" value="' . esc_attr($values['address1']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" />' . apply_filters( 'usces_filter_after_address1', __('Kitakami Yokohama', 'usces'), $applyform ) . '</td>
    </tr>
    <tr>
    <th scope="row">' . usces_get_essential_mark('address2', $data).__('numbers', 'usces').'</th>
    <td colspan="2"><input name="' . $type . '[address2]" id="address2" type="text" value="' . esc_attr($values['address2']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" />' . apply_filters( 'usces_filter_after_address2', '3-24-555', $applyform ) . '</td>
    </tr>
    <tr>
    <th scope="row">' . usces_get_essential_mark('address3', $data).__('building name', 'usces').'</th>
    <td colspan="2"><input name="' . $type . '[address3]" id="address3" type="text" value="' . esc_attr($values['address3']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" />' . apply_filters( 'usces_filter_after_address3', __('tuhanbuild 4F', 'usces'), $applyform ) . '</td>
    </tr>
    <tr>
    <th scope="row">' . usces_get_essential_mark('tel', $data).__('Phone number', 'usces').'</th>
    <td colspan="2"><input name="' . $type . '[tel]" id="tel" type="text" value="' . esc_attr($values['tel']) . '" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: inactive" />' . apply_filters( 'usces_filter_after_tel', '1000-10-1000', $applyform ) . '</td>
    </tr>';
    return $formtag;
}

//Welcart既納客専用ページ
// add_action('get_header', 'member_page');
// function member_page(){
//  if(is_category('8')&& usces_the_member_status('return') != 'ベーシックプラン'||in_category('8')&& usces_the_member_status('return') != 'ベーシックプラン'){
//  wp_redirect(get_permalink('54'));
//  exit;
//  }
// }
