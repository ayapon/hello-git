<?php
/*
Plugin Name: PAY.JP On Welcart By GTI
Plugin URI: https://gti.co.jp/
Description: PAY.JP On Welcart.
Version: 2.2
Author: Takeshi Satoh@ GTI Inc.
Author URI: https://gti.co.jp/
Text Domain: payjp-on-welcart
Domain Path: /languages/
 */
/**
 * History
 * 2017/08/28
 * ・active_card → default_card
 *
 * 2017/04/21
 * ・定期課金日設定の不具合修正
 *
 * 2017/02/20
 * ・顧客カード情報の変更対応
 * ・定期課金の年単位（年次の定期課金）対応への対応
 *
 * 2016/12/26
 * ・「定期課金（月単位）を日割り課金にするか」のパラメータがどのような状態でも
 * 　受注メールの内容に
 * 　「定期課金の今回支払日割計算額は・・・円となっています。」
 * 　の表記が表示されておりました。
 * 　こちらを「日割課金」しない場合では表示しないように修正いたしました。
 * 　この「日割課金」金額についてはPAY.JPに記載してある計算方法で独自に計算し
 * 　出力した結果となりますので実際には「日割課金」しない場合は日割課金がされておりません。
 * 　課金されてもされていなくても表示のみの金額となります。
 *
 * 2016/12/20
 * ・管理画面 定期課金（月単位）を日割り課金にするか のチェック表示修正
 * 　payjp-admin.php
 *
 * 2016/11/19
 * ・前回のカードで決済について不具合修正
 * 2016/08/11
 * ・定期課金の一時停止と再開機能追加
 * 2016/09/26
 * ・メッセージの識別子が小文字だったのを修正
 */
define( 'PAYJP_WP_CONTENT_DIR', ABSPATH . 'wp-content' );
define( 'PAYJP_WP_PLUGIN_DIR', PAYJP_WP_CONTENT_DIR . '/plugins' );
define( 'PAYJP_PLUGIN_DIR', PAYJP_WP_PLUGIN_DIR . '/' . plugin_basename( dirname( __FILE__ ) ) );
define( 'PAYJP_ERROR_RECURSION_KONGO_MSG', 'カート内に定期課金できない商品も入っています。定期課金の場合は「定期課金可能商品」のみで決済してください。この画面から「戻る」ボタンで商品の調整を行ってください。' );
define( 'PAYJP_USE_RECURSION_MEMBER_ONLY_MSG', '定期課金アイテムのご購入は会員限定となっております。ログインまたは会員登録を行ってください。' );
define( 'PAYJP_ON_WELCART_LOADING_GIF', plugin_dir_url( __FILE__ ) . 'img/gif-load.gif' );

require_once( PAYJP_PLUGIN_DIR . '/payjp-php/init.php' );

if ( is_admin() ) {
	require_once( PAYJP_PLUGIN_DIR . "/payjp-admin.php" );
}

class PayjpOnWelcart
{

	function __construct()
	{

		register_activation_hook( __FILE__, array( $this, 'payjp_set_initial' ) );
		register_deactivation_hook( __FILE__, array( $this, 'payjp_deactivate' ) );
		// アンインストール時処理
		register_uninstall_hook( __FILE__, array( $this, 'payjp_uninstall' ) );

		// テーマセットアップ時処理
		add_action( 'after_setup_theme', array( $this, 'payjp_usces_action_acting_construct' ), 8 );
		// エラー時対応
		add_filter( 'usces_filter_get_error_settlement', array( $this, 'payjp_usces_filter_get_error_settlement' ), 10, 1 );
		// 確認画面処理 決済ボタン表示
		add_filter( 'usces_filter_confirm_inform', array( $this, 'payjp_usces_filter_confirm_inform' ), 10, 5 );
		// 支払いリスト生成
		add_filter( 'usces_fiter_the_payment_method', array( $this, 'payjp_usces_fiter_the_payment_method' ), 10, 2 );
		// 決済情報表示
		add_filter( 'usces_fiter_the_payment_method_explanation', array( $this, 'payjp_usces_fiter_the_payment_method_explanation' ), 10, 3 );
		// 前回の決済カードで決済する方法を 支払方法一覧に入れる処理
		add_filter( 'usces_filter_the_continue_payment_method', array( $this, 'payjp_usces_filter_the_continue_payment_method' ), 10, 1 );
		// 受注時処理
		add_action( 'usces_action_reg_orderdata', array( $this, 'payjp_usces_order' ), 10, 1 );
		// 遷移
		add_filter( 'usces_filter_check_acting_return_results', array( $this, 'payjp_acting_return' ) );
		// 受注メールに文言追加
		add_filter( 'usces_filter_send_order_mail_payment', array( $this, 'payjp_order_mail_payment' ), 10, 6 );

		// メンバー情報
		add_filter( 'usces_filter_memberinfo_page_header', array( $this, 'payjp_memberinfo_page_header' ), 10, 1 );
		// 会員購入履歴画面
		add_filter( 'usces_filter_history_cart_row', array( $this, 'payjp_usces_filter_history_cart_row' ), 10, 4 );

		// クレジットカード更新
		add_action( 'usces_action_memberinfo_page_header', array( $this, 'payjp_card_update' ), 8 );

		add_action( 'usces_action_memberinfo_page_header', array( $this, 'payjp_member_update_settlement_page_header' ), 10 );

		add_action( 'wp_ajax_payjp_teiki_delete', array( $this, 'payjp_teiki_delete' ) );
		add_action( 'wp_ajax_nopriv_payjp_teiki_delete', array( $this, 'payjp_teiki_delete' ) );

		add_filter( 'usces_filter_member_history', array( $this, 'payjp_usces_filter_member_history' ), 10, 2 );

	}


	/**
	 * テーマセットアップ時
	 */
	function payjp_usces_action_acting_construct()
	{
		global $usces_gp;
		global $usces_entries;
		global $usces;

	}

	/**********************************************
	 * usces_filter_check_acting_return_results
	 * 決済完了ページ制御
	 * @param  $results
	 * @return array $results
	 ***********************************************/
	public function payjp_acting_return( $results )
	{
		if ( strpos( $payment[ 'settlement' ], 'acting_payjp' ) !== 0 ) return $results;

		$results[ 'reg_order' ] = false;

		usces_log( 'PAY.JP results : ' . print_r( $results, true ), 'acting_transaction.log' );
		if ( !isset( $_REQUEST[ 'nonce' ] ) ) {
			wp_redirect( home_url() );
			exit;
		}

		return $results;
	}

	/**
	 * 受注時処理
	 */
	function payjp_usces_order( $args )
	{

		global $usces;


		$cart = $args[ 'cart' ];
		$usces_entries = $args[ 'entry' ];
		$order_id = $args[ 'order_id' ];
		$payments = $args[ 'payments' ];
		$acting_flag = $payments[ 'settlement' ];

		if ( !$usces_entries || !$cart )
			wp_redirect( USCES_CART_URL );

		if ( strpos( $acting_flag, 'acting_payjp' ) !== 0 ) return true;

		if ( !$usces_entries[ 'order' ][ 'total_full_price' ] )
			return true;

		if ( !wp_verify_nonce( $_REQUEST[ 'nonce' ], $acting_flag ) )
			wp_redirect( USCES_CART_URL );

		$token = esc_attr( $_POST[ 'payjp-token' ] );
		$rand = esc_attr( $_POST[ 'sub' ] );

		if ( $token != '' && $rand != '' ) {    //PAY.JP

			$purchase = esc_attr( $_POST[ 'purchase' ] );

			$datas = usces_get_order_acting_data( $rand );
			$order_data = maybe_unserialize( $datas[ 'order_data' ] );


			$total_full_price = $order_data[ 'entry' ][ 'order' ][ 'total_full_price' ];

			$_GET[ 'uscesid' ] = $datas[ 'sesid' ];
			if ( empty( $datas[ 'sesid' ] ) ) {
				$log = array( 'acting' => $purchase, 'key' => $rand, 'result' => 'SESSION ERROR', 'data' => $_POST );
				usces_save_order_acting_error( $log );
				usces_log( 'PAY.JP construct : error1', 'acting_transaction.log' );
			} else {
				usces_auth_order_acting_data( $rand );
				usces_log( 'PAY.JP construct : ' . $rand, 'acting_transaction.log' );
				usces_set_acting_notification_time( $rand );
			}

			// 注文処理
			if ( $token ) {
				try {
					// TOKEN ゲット。
					$secret_key = get_option( 'payjp_secret_key' );
					$this->payjp_error_log( "================= PAY.JP PART 1 =========" );
					$this->payjp_error_log( "SECRET KEY : " . $secret_key );
					\Payjp\Payjp::setApiKey( $secret_key );

					// 決済
					// token の最後が - の場合は「前回の決済」を利用
					$cus_flg = FALSE;
					if ( preg_match( "/-$/", $token ) ) {
						$cus_flg = TRUE;
						$this->payjp_error_log( " 前回決済 ------ - ");
					}

					// 顧客情報を保持していたらそれを使用
					$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', usces_memberinfo( 'ID', 'return' ), 'return' );
					$payjp_customer_info = "";
					$payjp_card_last4 = "";

					$this->payjp_error_log( "ACTING_FLG: ".$acting_flag );
					// 顧客情報が取得できたら
					if ( $payjp_cus_key != NULL ) {
						try {
							$this->payjp_error_log( 'CUS_KEY : ' . $payjp_cus_key );
							$payjp_customer_info = NULL;
							// PAY.JPにて新たなカードを利用する場合は更新情報を送信
							if ( $cus_flg == FALSE ) {
								$this->payjp_error_log( "通常決済" );
								$payjp_charge_info = \Payjp\Charge::retrieve( $token );
								// awxew
								$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_key );
								$email = usces_memberinfo( 'mailaddress1', 'return' );
								$save_result = $payjp_customer_info->save(
									array(
										"card" => $token,
										"email" => $email
									)
								);
								$this->payjp_error_log( ' EMAIL UPDATE: ' . $email );
								$payjp_customer_card = $payjp_charge_info->card;
							} else {
								$this->payjp_error_log( "前回のカードで決済" );
								$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_key );
								$payjp_customer_card = $payjp_customer_info->cards->retrieve( $payjp_customer_info->default_card );
							}
							$payjp_card_last4 = $payjp_customer_card->last4;

						} catch ( Exception $err ) {
							$this->payjp_error_log( "================= PAY.JP ERROR =========" );
							$this->payjp_error_log( $err->getMessage() );
							$payjp_card_last4 = '';
							$payjp_cus_key = '';
						}
					}

					if ( $payjp_cus_key != "" && $payjp_card_last4 != "" ) {
						$this->payjp_error_log( '----------PAY.JP CUS KEY: ' . $payjp_cus_key );
						$this->payjp_error_log( '----------PAY.JP CARD LAST4: ' . $payjp_card_last4 );

					} else {
						// なければ作成し保存
						// 顧客生成
						$customer_info = array( "card" => $token );
						// ログインしていればEMAILを格納
						if ( usces_is_login() ) {
							$email = usces_memberinfo( 'mailaddress1', 'return' );
							$customer_info[ 'email' ] = $email;
						}
						$cus_object = \Payjp\Customer::create( $customer_info );

						$payjp_cus_key = $cus_object->id;
						$usces->set_member_meta_value( 'csmb_payjp_cus_key', $payjp_cus_key, usces_memberinfo( 'ID', 'return' ) );
						$this->payjp_error_log( '----------PAY.JP CUS KEY: ' . $payjp_cus_key );
					}
					$this->payjp_error_log( '----------ACTING FLAG: ' . $acting_flag );
					// 定期課金かどうかで課金方法分岐
					if ( $acting_flag == "acting_payjp_recursion_month" || $acting_flag == 'acting_payjp_recursion_month_customer' ||
						$acting_flag == "acting_payjp_recursion_year" || $acting_flag == 'acting_payjp_recursion_year_customer'
					) {
						$cart = $usces->cart->get_cart();

						if ( $acting_flag == 'acting_payjp_recursion_month' || $acting_flag == 'acting_payjp_recursion_month_customer' ) {
							// 定期課金タイミング（日付）
							$today_date = date( 'd' );
							$payjp_use_recursion_month_first_scheduled = intval( get_option( 'payjp_use_recursion_month_first_scheduled', $today_date ) );
							$payjp_use_recursion_month_prorate = intval( get_option( 'payjp_use_recursion_month_prorate' ) );
							$payjp_use_recursion_month_trial_days = intval( get_option( 'payjp_use_recursion_month_trial_days' ) );

							$this->payjp_error_log( '=============== PAY.JP RECURSION MONTH ========' );

							// 定期課金（月単位）
							// \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");
							$plan_args = array(
								"amount" => $total_full_price,
								"currency" => "jpy",
								"interval" => "month",
								"name" => $order_id
								// "trial_days" => 30,	//トライアルDAYS
							);
							// 定期日設定
							if ( $payjp_use_recursion_month_first_scheduled > 0 ) {
								$plan_args[ 'billing_day' ] = $payjp_use_recursion_month_first_scheduled;
							}
							// トライアル設定
							if ( $payjp_use_recursion_month_trial_days > 0 ) {
								$plan_args[ 'trial_days' ] = $payjp_use_recursion_month_trial_days;
							}

						} else {
							// 定期課金タイミング（当日の月日のみ）
							// トライアル日数(trial_days)を設定することは可能ですが、課金日(billing_day)を設定することはできません。

							$payjp_use_recursion_year_trial_days = intval( get_option( 'payjp_use_recursion_year_trial_days' ) );

							$this->payjp_error_log( '=============== PAY.JP RECURSION YEAR ========' );

							// 定期課金（年単位）
							// \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");
							$plan_args = array(
								"amount" => $total_full_price,
								"currency" => "jpy",
								"interval" => "year",
								"name" => $order_id
							);
							// billing_day = NULL;
							if ( $payjp_use_recursion_year_trial_days > 0 ) {
								$plan_args[ 'trial_days' ] = $payjp_use_recursion_year_trial_days;
							}

						}

						$plan = \Payjp\Plan::create( $plan_args );
						// 定期課金作成
						$subscription_args = array(
							"customer" => $payjp_cus_key,
							"plan" => $plan->id
						);
						if ( $payjp_use_recursion_month_prorate == 1 ) {
							$subscription_args[ 'prorate' ] = TRUE;
						}
						$payjp_result = \Payjp\Subscription::create( $subscription_args );

						$payjp_teiki_id = $payjp_result->id;
						$this->payjp_error_log( ' PAY.JP_result:  ID = ' . $payjp_teiki_id );
						$this->payjp_error_log( ' PAY.JP_result:  STATUS = ' . $payjp_result->status );
						// 定期課金IDをステータスにかかわらず保存
						$usces->set_order_meta_value( 'csod_payjp_teiki_id', $payjp_teiki_id, $order_id );
					} else {
						// TOKEN ゲット。
						$secret_key = get_option( 'payjp_secret_key' );

						$this->payjp_error_log( "SECRET KEY : " . $secret_key );

						\Payjp\Payjp::setApiKey( $secret_key );
						$payjp_result = \Payjp\Charge::create( array(
							"customer" => $payjp_cus_key,
							"amount" => $total_full_price,
							"currency" => "jpy"
						) );
					}
					$this->payjp_error_log( 'PAY.JP RESULT' );

					$paid = FALSE;
					$status = '';

					if (
						$acting_flag == "acting_payjp_recursion_month" || $acting_flag == "acting_payjp_recursion_year" ||
						$acting_flag == "acting_payjp_recursion_month_customer" || $acting_flag == "acting_payjp_recursion_year_customer"
					) {
						$status = $payjp_result->status;    // 定期課金の場合
						// payjp_card_last4 は上で作ったものを使用
					} else {
						$paid = $payjp_result->paid;    // 通常の場合
						$payjp_card_last4 = $payjp_result->card->last4;
						// $payjp_customer_card = $payjp_customer_info->cards->retrieve($payjp_customer_info->default_card);
						// $payjp_card_last4 = $payjp_customer_card->last4;
					}

					$payjp_order_id = $payjp_result->id;

					$payjp_amount = $payjp_result->amount;
					$payjp_currency = $payjp_result->currency;

					if ( $paid === TRUE || $status == 'active' || $status == 'trial' ) {
						// 成功したpayjpセッションを受注に登録する
						if ( $paid === TRUE ) {
							$usces->set_order_meta_value( 'csod_payjp_charge_id', $payjp_result->id, $order_id );
						}

						usces_log( 'PAY.JP : Payment confirmation: order_id{' . $order_id . '} payjp_id{' . $payjp_result->id . '}', 'acting_transaction.log' );

						// サンクスページ（独自）遷移
						$usces->page = 'ordercompletion';

						global $usces;

						$response_data[ 'acting' ] = $acting_flag;
						$response_data[ 'acting_return' ] = 1;
						$response_data[ 'result' ] = 1;
						$response_data[ 'nonce' ] = wp_create_nonce( $acting_flag );

						return true;
					} else {
						// $this->payjp_error_log('----------failed: PAYMENT ERROR----------');
						$log = array( 'acting' => 'payjp', 'key' => $rand, 'result' => 'PAY.JP ERROR', 'data' => $payjp_result );
						usces_save_order_acting_error( $log );
						$usces->page = 'error';
					}
					// return $payjp_result;

				} catch ( Exception $e ) {
					// echo '捕捉した例外: ',  $e->getMessage(), "\n";
					$log = array( 'acting' => 'payjp', 'key' => $rand, 'result' => 'PAY.JP ERROR:' . $e->getMessage(), 'data' => $payjp_result );
					usces_save_order_acting_error( $log );

					$this->payjp_error_log( $e->getMessage() );
					$usces->page = 'error';
				}

				if ( $usces->page == 'error' ) {
					$response_data[ 'acting_return' ] = 0;
					$response_data[ 'result' ] = 0;
					$response_data[ 'nonce' ] = wp_create_nonce( $acting_flag );
					wp_redirect( add_query_arg( $response_data, USCES_CART_URL ) );

					exit;
					return false;
				}
			}
		}
		exit;
		return false;
	}

	/**
	 * エラー時メッセージ
	 */
	function payjp_usces_filter_get_error_settlement( $res )
	{
		$res .= "予期せぬエラーが発生しました。決済をやり直してください。<br>";
		$res .= "<a href='" . USCES_CART_URL . "'>カートへ戻る</a>";
		return $res;
	}

	/**
	 * 決済ボタン
	 */
	function payjp_usces_filter_confirm_inform( $html, $payments, $acting_flag, $rand, $purchase_disabled )
	{
		global $usces;

		$this->payjp_error_log( ' ============================== ACTING_KEY : ' . $acting_flag );

		if (
			$acting_flag == "acting_payjp" || $acting_flag == "acting_payjp_customer" ||
			$acting_flag == "acting_payjp_recursion_month" || $acting_flag == "acting_payjp_recursion_month_customer" ||
			$acting_flag == "acting_payjp_recursion_year" || $acting_flag == "acting_payjp_recursion_year_customer"
		) {
			global $usces_gp;
			global $usces_entries;
			global $usces;

			$purchase_disabled = ' style="display:none;" ';

			// actingデータ保存
			$usces->save_order_acting_data( $rand );
			usces_save_order_acting_data( $rand );

			$usces_entries = $usces->cart->get_entry();
			$cart = $usces->cart->get_cart();

			$total_full_price = $usces_entries[ 'order' ][ 'total_full_price' ];

			// payjp_acting($total_full_price, $rand, $acting_flag, $purchase_disabled );
			$public_key = get_option( 'payjp_public_key' );
			$checkout_btn_text = sprintf( get_option( 'payjp_checkout_btn_text' ), number_format( $total_full_price ) );
			$submit_btn_text = sprintf( get_option( 'payjp_submit_btn_text' ), number_format( $total_full_price ) );
			$payjp_oauth_client_id = get_option( 'payjp_oauth_client_id', '' );
			$this->payjp_error_log( "purchase: PAY.JP " );

			$subhtml = "<hr /><script>function loading() {
  jQuery('#loading').css('display', 'block');
  jQuery('body').css('opacity', '0.5');
}

function loading_over() {
  jQuery('#loading').css('display', 'none');
  jQuery('body').css('opacity', '1');
}

function payjp_purchase() {
  loading();
  jQuery('#purchase_form').submit();
}
//--></script>";
			$subhtml .= apply_filters( 'payjp_on_welcart_loading_purchase_css', "
<style>
#loading {
  display: none;
  position:           fixed;
  z-index:            1;
  top:                0;
  left:               0;
  width:              100%;
  height:             100%;
  background-color:   rgba(0,0,0,0.15);
}
#loading img {
  width: 96px; /* gif画像の幅 */
  height: 96px; /* gif画像の高さ */
  margin: -68px 0 0 -68px; /* gif画像を画面中央に */
  padding: 20px; /* gif画像を大きく */
  background: #BABABA; /* gif画像の背景色 */
  opacity: 0.5; /* 透過させる */
  border-radius: 15px; /* 丸角 */
  position: fixed; /* gif画像をスクロールさせない */
  left: 50%; /* gif画像を画面横中央へ */
  top: 50%; /* gif画像を画面縦中央へ */
}
</style>" );
			$loading_gif = get_option( 'payjp_on_welcart_loading_gif', PAYJP_ON_WELCART_LOADING_GIF );
			$subhtml .= "<div style='text-align: center;'><form id='purchase_form' action='" . USCES_CART_URL . "' method=POST onKeyDown='if (event.keyCode == 13) {return false;}' >    <div id='loading'>
  <img src='{$loading_gif}' >
</div>";

			// 通常決済の場合
			if ( $acting_flag == "acting_payjp" ) {
				$subhtml .= "<script
  type='text/javascript'
  src='https://checkout.pay.jp/'
  class='payjp-button'
  data-key='{$public_key}'
  data-text='{$submit_btn_text}'
  data-submit-text='{$checkout_btn_text}'";
				if ( $payjp_oauth_client_id != "" ) {
					$subhtml .= " data-payjp='{$payjp_oauth_client_id}' ";
				}
				$subhtml .= "data-partial='true' data-on-created='payjp_purchase'>
</script>
								<input type='hidden' name='acting' value='payjp' />";
			} else {
				$customer_id = usces_memberinfo( 'ID', 'return' );
				$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', $customer_id, 'return' );
				$secret_key = get_option( 'payjp_secret_key' );

				$this->payjp_error_log( "SECRET KEY : " . $secret_key );
				\Payjp\Payjp::setApiKey( $secret_key );
				try {
					$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', $customer_id, 'return' );
					$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_key );

					$payjp_customer_card = $payjp_customer_info->cards->retrieve( $payjp_customer_info->default_card );
					$payjp_card_last4 = $payjp_customer_card->last4;

				} catch ( Exception $e ) {
					$this->payjp_error_log( '709 - Exception ::: ' . $e->getMessage() . ' CUS_KEY:' . $payjp_cus_key );
					$payjp_cus_key = '';
					$payjp_customer_info = '';
					$payjp_card_last4 = '';
				}

				$flg = 0;
				if (
					$acting_flag == "acting_payjp_recursion_month" ||
					$acting_flag == "acting_payjp_recursion_month_customer"
				) {
					// 定期課金会員限定機能チェック
					$member_only_flg = get_option( 'payjp_use_recursion_member_only_flg', '0' );    // 1:会員限定

					if ( ( $member_only_flg == '1' && usces_is_login() ) || $member_only_flg != '1' ) {
						// 商品が２つ以上の場合はチェック
						if ( $usces->get_total_quantity() >= 2 ) {
							$carts = $usces->cart->get_cart();
							$monthitem_ids = get_option( 'payjp_use_recursion_month_item_ids', '' );
							$this->payjp_error_log( 'month_item_ids : ' . $monthitem_ids );
							$monthitem_list = array();
							if ( $monthitem_ids != '' ) {
								$monthitem_list = explode( ",", $monthitem_ids );
							}
							foreach ( $carts as $cart ) {
								$sku = $cart[ 'sku' ];
								if ( !in_array( $sku, $monthitem_list ) ) {
									if ( strpos( $subhtml, get_option( 'payjp_error_recursion_kongo', PAYJP_ERROR_RECURSION_KONGO_MSG ) ) > 0 ) {
										// $subhtml そのまま
										$flg = 1;
									} else {
										$subhtml .= get_option( 'payjp_error_recursion_kongo', PAYJP_ERROR_RECURSION_KONGO_MSG );
										$flg = 1;
									}
								}
							}
						}
					} else {
						$subhtml .= get_option( 'payjp_use_recursion_member_only_msg', PAYJP_USE_RECURSION_MEMBER_ONLY_MSG );
						$flg = 1;
					}
					if ( $flg != 1 ) {
						if ( $payjp_cus_key != "" && $payjp_card_last4 != "" && strpos( $acting_flag, '_customer' ) >= 3 ) {
							$time_value = time() . "-";
							$subhtml .= "<input name='purchase' type='submit' id='purchase_button' class='checkout_button' value='上記内容で注文する'>
								<input type='hidden' name='acting' value='acting_payjp_recursion_month' />
								<input type='hidden' name='payjp-token' value='{$time_value}' />";
						} else {
							$subhtml .= "<script
  type='text/javascript'
  src='https://checkout.pay.jp/'
  class='payjp-button'
  data-key='{$public_key}'
  data-text='{$submit_btn_text}'
  data-submit-text='{$checkout_btn_text}' 
  data-partial='true'
  data-on-created='payjp_purchase'
  >
</script>
									<input type='hidden' name='acting' value='acting_payjp_recursion_month' />";
						}
					}
				} elseif (
					$acting_flag == "acting_payjp_recursion_year" ||
					$acting_flag == "acting_payjp_recursion_year_customer"
				) {
					// 定期課金会員限定機能チェック
					$member_only_flg = get_option( 'payjp_use_recursion_member_only_flg', '0' );    // 1:会員限定
					$this->payjp_error_log( 'member_only_flg: ' . $member_only_flg );
					$this->payjp_error_log( 'member_login: ' . ( usces_is_login() ? 'LOGIN' : 'LOGOUT' ) );
					if ( ( $member_only_flg == '1' && usces_is_login() ) || $member_only_flg != '1' ) {
						// 商品が２つ以上の場合はチェック
						if ( $usces->get_total_quantity() >= 2 ) {
							$carts = $usces->cart->get_cart();
							$yearitem_ids = get_option( 'payjp_use_recursion_year_item_ids', '' );
							$this->payjp_error_log( 'year_item_ids : ' . $yearitem_ids );
							$yearitem_list = array();
							if ( $yearitem_ids != '' ) {
								$yearitem_list = explode( ",", $yearitem_ids );
							}
							foreach ( $carts as $cart ) {
								$sku = $cart[ 'sku' ];
								if ( !in_array( $sku, $yearitem_list ) ) {
									if ( strpos( $subhtml, get_option( 'payjp_error_recursion_kongo', PAYJP_ERROR_RECURSION_KONGO_MSG ) ) > 0 ) {
										// $subhtml そのまま
										$flg = 1;
									} else {
										$subhtml .= get_option( 'payjp_error_recursion_kongo', PAYJP_ERROR_RECURSION_KONGO_MSG );
										$flg = 1;
									}
								}
							}
						}
					} else {
						$subhtml .= get_option( 'payjp_use_recursion_member_only_msg', PAYJP_USE_RECURSION_MEMBER_ONLY_MSG );
						$flg = 1;
					}
					if ( $flg != 1 ) {
						if ( $payjp_cus_key != '' && $payjp_card_last4 != "" && strpos( $acting_flag, '_customer' ) >= 3 ) {
							$time_value = time() . "-";
							$subhtml .= "<input name='purchase' type='submit' id='purchase_button' class='checkout_button' value='上記内容で注文する'>
								<input type='hidden' name='acting' value='acting_payjp_recursion_year' />
								<input type='hidden' name='payjp-token' value='{$time_value}' />";
						} else {
							$subhtml .= "<script
  type='text/javascript'
  src='https://checkout.pay.jp/'
  class='payjp-button'
  data-key='{$public_key}'
  data-on-created='payjp_purchase'
  data-text='{$submit_btn_text}'
  data-submit-text='{$checkout_btn_text}' 
  data-partial='true'>
</script>
									<input type='hidden' name='acting' value='acting_payjp_recursion_year' />";
						}
					}
				} else {
					if ( $payjp_cus_key != "" && $payjp_card_last4 != "" ) {
						$time_value = time() . "-";
						$subhtml .= "<input name='purchase' type='submit' id='purchase_button' class='checkout_button' value='上記内容で注文する'>
							<input type='hidden' name='acting' value='payjp_customer' />
							<input type='hidden' name='payjp-token' value='{$time_value}' />";
					} else {
						$subhtml .= "PAY.JPに保存したデータが利用できなくなっています。この画面から「戻る」ボタンを押し、その他の決済方法をお選びください。";
					}
				}
			}
			$subhtml .= "
				<input type='hidden' name='acting_return' value='1' />
				<input type='hidden' name='result' value='1' />
				<input type='hidden' name='amount' value='" . $total_full_price . "' />
				<input type='hidden' name='sub' value='" . $rand . "' />
				<input type='hidden' name='nonce' value='" . wp_create_nonce( $acting_flag ) . "'>
				</form></div>";
			$html = '<form id="purchase_back_form" action="' . USCES_CART_URL . '" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
				<div class="send"><input name="backDelivery" type="submit" id="back_button" value="' . __( 'Back', 'usces' ) . '"' . apply_filters( 'usces_filter_confirm_prebutton', NULL ) . ' />
				<input name="purchase" type="submit" id="purchase_button" class="checkout_button" value="' . __( 'Checkout', 'usces' ) . '"' . $purchase_disabled . ' /></div>
				<input type="hidden" name="rand" value="' . $rand . '">
				<input type="hidden" name="nonce" value="' . wp_create_nonce( $acting_flag ) . '">' . "\n";
			$html = $subhtml . $html;
			$payments[ 'settlement' ] = $acting_flag;
			$payments[ 'use' ] = "active";
		}
		return $html;
	}

	/**
	 * 支払いリスト生成
	 */
	function payjp_usces_fiter_the_payment_method( $payments, $value )
	{
		$customer_id = usces_memberinfo( 'ID', 'return' );
		global $usces;

		$secret_key = get_option( 'payjp_secret_key' );
		\Payjp\Payjp::setApiKey( $secret_key );
		try {
			$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', $customer_id, 'return' );
			$this->payjp_error_log( " = = == CUSTOMER KEY : " . $payjp_cus_key );
			$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_key );
			$payjp_customer_card = $payjp_customer_info->cards->retrieve( $payjp_customer_info->default_card );

			$payjp_card_last4 = $payjp_customer_card->last4;

			$this->payjp_error_log( ' ====== PAY.JP CARD LAST4: ' . $payjp_card_last4 . " TYPE: " . $payjp_customer_card->brand );
		} catch ( Exception $e ) {
			$this->payjp_error_log( 'Exception ::: ' . $e->getMessage() . ' CUS_KEY:' . $payjp_cus_key );
			$payjp_cus_key = '';
			$payjp_customer_info = '';
			$payjp_card_last4 = '';
		}

		// カート内に「月額課金商品」があれば支払いを月額課金のみとする
		// ※基本設定で支払い方法に「月額課金」を追加していない場合は支払い方法が出ない
		$carts = $usces->cart->get_cart();
		$monthitem_ids = get_option( 'payjp_use_recursion_month_item_ids', '' );

		// 定期課金会員限定機能チェック
		$member_only_flg = get_option( 'payjp_use_recursion_member_only_flg', '0' );    // 1:会員限定

		$mysku = explode( ',', $monthitem_ids );
		foreach ( $carts as $cart ) {
			$sku = $cart[ 'sku' ];
			if ( in_array( $sku, $mysku ) ) {
				$new_payments = array();

				foreach ( $payments as $payment ) {

					if (
						strpos( $payment[ 'settlement' ], '_recursion_month' ) <= 0 &&
						strpos( $payment[ 'settlement' ], '_recursion_month_customer' ) <= 0
					) {
						// リストから外す
					} else {
						if ( strpos( $payment[ 'settlement' ], 'acting_payjp_recursion_month_customer' ) === 0 && $payjp_card_last4 == "" ) {
							// 会員紐付けられないためリストから外す
						} else {
							array_push( $new_payments, $payment );
						}
					}
				}
				return $new_payments;
			}
		}
		// カート内に「年額課金商品」があれば支払いを月額課金のみとする
		// ※基本設定で支払い方法に「年額課金」を追加していない場合は支払い方法が出ない
		$yearitem_ids = get_option( 'payjp_use_recursion_year_item_ids', '' );
		$mysku = explode( ',', $yearitem_ids );
		foreach ( $carts as $cart ) {
			$sku = $cart[ 'sku' ];
			if ( in_array( $sku, $mysku ) ) {
				$new_payments = array();

				foreach ( $payments as $payment ) {
					if (
						strpos( $payment[ 'settlement' ], '_recursion_year' ) <= 0 &&
						strpos( $payment[ 'settlement' ], '_recursion_year_customer' ) <= 0
					) {
						// リストから外す
					} else {
						if ( strpos( $payment[ 'settlement' ], 'acting_payjp_recursion_year_customer' ) === 0 && $payjp_card_last4 == "" ) {
							// 会員紐付けられないためリストから外す
						} else {
							array_push( $new_payments, $payment );
						}
					}
				}
				return $new_payments;
			}
		}

		$new_payments = array();
		if ( trim( $payjp_cus_key ) != '' && $payjp_customer_info != NULL && $payjp_card_last4 != "" ) {
			// そのまま返却
			foreach ( $payments as $payment ) {
				// 定期課金のものが入っていないので外す
				if ( $payment[ 'settlement' ] == 'acting_payjp_recursion_month' ) {
					// リストから外す
				} elseif ( $payment[ 'settlement' ] == 'acting_payjp_recursion_month_customer' ) {
					// リストから外す
				} elseif ( $payment[ 'settlement' ] == 'acting_payjp_recursion_year' ) {
					// リストから外す
				} elseif ( $payment[ 'settlement' ] == 'acting_payjp_recursion_year_customer' ) {

				} else {
					array_push( $new_payments, $payment );
				}
			}
			return $new_payments;
		} else {

			foreach ( $payments as $payment ) {

				if ( $payment[ 'settlement' ] == 'acting_payjp_customer' ) {
					// リストから外す
				} elseif ( $payment[ 'settlement' ] == 'acting_payjp_recursion_month' ) {
					// リストから外す
				} elseif ( $payment[ 'settlement' ] == 'acting_payjp_recursion_month_customer' ) {
					// リストから外す
				} elseif ( $payment[ 'settlement' ] == 'acting_payjp_recursion_year' ) {
					// リストから外す
				} elseif ( $payment[ 'settlement' ] == 'acting_payjp_recursion_year_customer' ) {
					// リストから外す
				} else {
					array_push( $new_payments, $payment );
				}
			}
			return $new_payments;
		}
		return $payments;
	}

	/**
	 * 決済情報表示
	 */
	function payjp_usces_fiter_the_payment_method_explanation( $explanation, $payment, $value )
	{

		if (
			$payment[ 'settlement' ] == "acting_payjp" ||
			$payment[ 'settlement' ] == "acting_payjp_recursion_month" ||
			$payment[ 'settlement' ] == "acting_payjp_recursion_year"
		) {
			$use_card_info_flg = get_option( 'payjp_use_card_info_flg', NULL );
			if ( $use_card_info_flg != NULL ) {
				$this_dir_url = plugins_url( plugin_basename( __FILE__ ) );
				if ( $use_card_info_flg == 1 ) {
					$explanation_add = <<< EOF
				<img src="{$this_dir_url}/../lib/payjp_2brands.png" data-pin-nopin="true">
EOF;
				} elseif ( $use_card_info_flg == 2 ) {
					$explanation_add = <<< EOF
				<img src="{$this_dir_url}/../lib/payjp_5brands.png" data-pin-nopin="true">
EOF;
				}
				$explanation = $explanation_add . $explanation;
			}
		} elseif (
			$payment[ 'settlement' ] == "acting_payjp_customer" ||
			$payment[ 'settlement' ] == "acting_payjp_recursion_year_customer" ||
			$payment[ 'settlement' ] == "acting_payjp_recursion_month_customer"
		) {
			$customer_id = usces_memberinfo( 'ID', 'return' );
			$this->payjp_error_log( ' payjp_usces_filter_delivery_secure_form_loop :::::' . $customer_id );
			$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', $customer_id, 'return' );

			$this->payjp_error_log( ' cus_key : ' . $payjp_cus_key );
			if ( $payjp_cus_key != '' ) {
				$secret_key = get_option( 'payjp_secret_key' );
				\Payjp\Payjp::setApiKey( $secret_key );
				try {
					$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_key );
					$payjp_customer_card = $payjp_customer_info->cards->retrieve( $payjp_customer_info->default_card );
					$payjp_card_last4 = $payjp_customer_card->last4;
					$this->payjp_error_log( ' ==== = = = CARD LAST4 : ' . $payjp_card_last4 );
					if ( $payjp_card_last4 != "" ) {
						$explanation .= "保存していたカードで決済　";
						$payjp_card_type = $payjp_customer_info->default_card->brand;
						$explanation .= "（カード番号: " . $payjp_card_type . " **** **** **** " . $payjp_card_last4 . "）";
						// 更に改変出来るようにフィルター設置
						$explanation = apply_filters( 'payjp_usces_fiter_the_payment_method_explanation', $explanation, $payjp_customer_info );
					}
				} catch ( Exception $e ) {
					$this->payjp_error_log( '785 - Exception ::: ' . $e->getMessage() );
					$this->payjp_error_log( $e->getMessage() );
				}
			}
		}
		return $explanation;
	}

	/**
	 * 「前回の決済を利用する」支払い方法を　支払方法一覧に入れる
	 */
	function payjp_usces_filter_the_continue_payment_method( $array )
	{

		$customer_id = usces_memberinfo( 'ID', 'return' );
		$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', $customer_id, 'return' );

		if ( trim( $payjp_cus_key ) != '' ) {
			// そのまま返却
		} else {
			array_push( $array, 'acting_payjp_customer' );
		}
		return $array;
	}

	/**
	 * メンバー画面 ヘッダ
	 */
	function payjp_usces_filter_member_history_header()
	{
		$acting_flag = esc_attr( $_POST[ 'acting' ] );
		if ( !empty( $acting_flag ) ) $acting_flag = "acting_" . $acting_flag;

		if ( strpos( $acting_flag, 'acting_payjp' ) !== FALSE ) {

			if ( !wp_verify_nonce( $_REQUEST[ 'nonce' ], $acting_flag ) ) wp_redirect( USCES_MEMBER_URL );

			$token = esc_attr( $_POST[ 'payjp-token' ] );
			$rand = esc_attr( $_POST[ 'sub' ] );

			if ( $token != '' && $rand != '' ) { //

				// 注文処理
				if ( isset( $token ) ) {
					try {
						// TOKEN ゲット。
						$secret_key = get_option( 'payjp_secret_key' );
						$payjp_token = $token;
						// 顧客情報を保持していたらそれを使用
						$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', usces_memberinfo( 'ID', 'return' ), 'return' );
						$payjp_customer_info = "";
						$payjp_card_last4 = "";
						// 顧客情報が取得できたら
						if ( $payjp_cus_key != NULL ) {
							try {
								$this->payjp_error_log( 'CUS_KEY : ' . $payjp_cus_key );
								$payjp_customer_info = NULL;
								// PAY.JPにて新たなカードを利用する場合は更新情報を送信
								$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_key );
								$payjp_card_last4 = $payjp_customer_info->default_card->last4;
							} catch ( Exception $err ) {
								$this->payjp_error_log( "================= PAY.JP ERROR =========" );
								$this->payjp_error_log( $err->getMessage() );
								$payjp_card_last4 = '';
								$payjp_cus_key = '';
							}
						}

						if ( $payjp_cus_key != "" || $payjp_card_last4 != "" ) {
							$this->payjp_error_log( '----------PAY.JP CUS KEY: ' . $payjp_cus_key );
							$this->payjp_error_log( '----------PAY.JP CARD LAST4: ' . $payjp_card_last4 );
						}
					} catch ( Exception $e ) {

					}
				}
			}
		}
		return NULL;
	}

	/**
	 * メンバー画面
	 */
	function payjp_usces_filter_member_history( $html, $args )
	{

		$use_member_history_recursion_stop_flg = get_option( 'payjp_use_member_history_recursion_stop_flg', '' );
		if ( $use_member_history_recursion_stop_flg == '1' ) {
			$ajax_url = admin_url( 'admin-ajax.php' );
			$nonce_str = wp_create_nonce( '__delete' );
			$html .= "
<script>
var ajaxurl = '{$ajax_url}';
function payjp_this_recursion_delete(teiki_id) {
    if ( confirm('定期課金を停止し定期課金オブジェクトを削除します。一度削除すると復活できません。よろしいですか？') ) {
      if ( confirm('もう一度お聞きします。削除しても本当によろしいですか？') ) {
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action' : 'payjp_teiki_delete',
                'payjp_teiki_id' : teiki_id,
                'nonce' : '{$nonce_str}'
            },
            success: function( response ){
              response = response.substr(0, response.length - 1);
                alert( response );
                location.reload();
                return true;
            }
        });
        return false;
      }
    }
    return false;
}
</script>
        ";
		}
		return $html;
	}

	/**
	 * メンバー購入履歴
	 */
	function payjp_usces_filter_history_cart_row( $optstr, $umhs, $cart_row, $i )
	{

		$use_member_history_recursion_stop_flg = get_option( 'payjp_use_member_history_recursion_stop_flg', '' );
		if ( $use_member_history_recursion_stop_flg == '1' ) {

			$payjp_teiki_id = usces_get_custom_field_value( 'order', 'payjp_teiki_id', $umhs[ 'ID' ], 'return' );

			// $this->payjp_error_log( $payjp_teiki_id );

			$secret_key = get_option( 'payjp_secret_key' );

			\Payjp\Payjp::setApiKey( $secret_key );
			$payjp_recursion_obj = NULL;
			try {
				$payjp_recursion_obj = \Payjp\Subscription::retrieve( $payjp_teiki_id );
			} catch ( Exception $e ) {
				// なければそのまま終了
				// N/A
			}
			$payjp_recursion_status = '';
			if ( !is_null( $payjp_recursion_obj ) ) {
				$payjp_recursion_status = $payjp_recursion_obj->status;
				$payjp_recursion_amount = $payjp_recursion_obj->plan[ amount ];
				$payjp_recursion_period = ( $payjp_recursion_obj->plan[ interval ] == 'month' ? '月単位' : ( $payjp_recursion_obj->plan[ interval ] == 'year' ? '年単位' : '' ) );
				$payjp_recursion_trial_days = $payjp_recursion_obj->plan[ trial_days ];
				$payjp_recursion_current_period_start = date( 'Y-m-d', $payjp_recursion_obj->current_period_start );
				$payjp_recursion_current_period_end = date( 'Y-m-d', $payjp_recursion_obj->current_period_end );
				try {
					$payjp_cus_id = $payjp_recursion_obj->customer;
					$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_id );
					$payjp_customer_card = $payjp_customer_info->cards->retrieve( $payjp_customer_info->default_card );
					$payjp_card_last4 = $payjp_customer_card->last4;
					$payjp_card_type = $payjp_customer_card->brand;

				} catch ( Exception $err ) {
					$this->payjp_error_log( $err->getMessage() );
					$payjp_card_last4 = '';
					$payjp_card_type = '';
				}
			}

			if ( $payjp_recursion_status == 'active' || $payjp_recursion_status == 'trial' ) {

				if ( $payjp_teiki_id != NULL ) {
					$recursion_html = "";
					$recursion_html .= "<tr><td colspan='2'><input type='button' value='定期課金解約' onclick='return payjp_this_recursion_delete(\"" . $payjp_teiki_id . "\");' /></td>";
					// "前回課金：{$payjp_recursion_current_period_start}<br>次回課金：{$payjp_recursion_current_period_end}</td>";
					$recursion_html .= "<td colspan='2'>" . ( $payjp_recursion_status == 'trial' ? "トライアル開始" : "前回課金" ) . "：" . $payjp_recursion_current_period_start . "<br>" . ( $payjp_recursion_status == 'trial' ? "トライアル終了" : "次回課金" ) . "：" . $payjp_recursion_current_period_end . "</td>";

					$recursion_html = apply_filters( 'payjp_filter_history_cart_row', $recursion_html, $umhs[ 'ID' ], $payjp_teiki_id, $payjp_recursion_current_period_start, $payjp_recursion_current_period_end, $payjp_card_type, $payjp_card_last4 );

					$optstr .= $recursion_html;
					// カード情報変更
				}

			} elseif ( $payjp_recursion_status == 'canceled' || $payjp_recursion_status == '404' ) {
				$recursion_html = "<tr><td colspan='6'>定期課金 削除済</td></tr>";
				$recursion_html = apply_filters( 'payjp_filter_history_cart_row_teiki_deleted', $recursion_html );
				$optstr .= $recursion_html;
			}
		}
		return $optstr;
	}

	/**
	 * デフォルトクレカ変更
	 */
	function payjp_member_update_settlement_page_header()
	{
		// コンフィグにて操作
		$cus_flg = get_option( 'payjp_use_member_card_chg_flg', '' );

		$this->payjp_error_log( '======= payjp_memberinfo_page_header =========' );
		$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', usces_memberinfo( 'ID', 'return' ), 'return' );
		$payjp_customer_info = "";
		$payjp_card_last4 = "";
		// 顧客情報が取得できたら
		if ( $payjp_cus_key != NULL ) {
			try {
				$this->payjp_error_log( 'CUS_KEY : ' . $payjp_cus_key );
				$payjp_customer_info = NULL;
				// PAY.JPにて新たなカードを利用する場合は更新情報を送信
				if ( $cus_flg == FALSE ) {
					return "";
				} else {
					$secret_key = get_option( 'payjp_secret_key' );
					\Payjp\Payjp::setApiKey( $secret_key );
					$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_key );
				}

				$payjp_customer_card = $payjp_customer_info->cards->retrieve( $payjp_customer_info->default_card );
				$payjp_card_last4 = $payjp_customer_card->last4;
				$payjp_card_type = $payjp_customer_card->brand;

				// ob_start();
				// var_dump( $payjp_customer_info->cards );
				// $var = ob_get_contents();
				// ob_end_clean();
				$this->payjp_error_log( $var );

				if ( !$payjp_customer_card ) {
					$payjp_card_last4 = "";
				}

			} catch ( Exception $err ) {
				$this->payjp_error_log( "================= PAY.JP ERROR =========" );
				$this->payjp_error_log( $err->getMessage() );
			}
		}
		$card_info = "";
		if ( $payjp_card_last4 != "" && $payjp_cus_key != "" ) {
			$card_info = "<table><tr><th>登録済み カードタイプ</th><td>{$payjp_card_type} </td></tr><tr><th>登録済み カード番号</th><td> **** **** **** {$payjp_card_last4} </td</tr></table>";
		} else {
			$card_info = "<table><tr><td>登録されているカードはありません。</td></tr></table>";
		}
		$recursion_html .= apply_filters( 'payjp_customer_card_info', $card_info, $payjp_card_type, $payjp_card_last4 );

		$recursion_html .= "<form action='" . USCES_MEMBER_URL . "' method=POST>";
		// 決済情報変更
		$public_key = get_option( 'payjp_public_key' );
		$payjp_oauth_client_id = get_option( 'payjp_oauth_client_id', '' );
		$this->payjp_error_log( "purchase: PAY.JP " );

		$recursion_html .= "<script
	type='text/javascript'
	src='https://checkout.pay.jp/'
	class='payjp-button'
	data-key='{$public_key}'
	data-text='カード情報変更'
	data-submit-text='カード情報変更'";
		// if ( $payjp_oauth_client_id != "" ) {
		//   $recursion_html .= " data-payjp='{$payjp_oauth_client_id}' ";
		// }
		$recursion_html .= " data-partial='false' data-on-created='payjp_purchase'>
	</script>";
		// $acting_flag = "acting_payjp_recursion_month";
		// if ( $payjp_recursion_period == 'year' ) {
		// 	$acting_flag = "acting_payjp_recursion_year";
		// $recursion_html .= "<input type='hidden' name='acting' value='payjp_recursion_year' />";
		// } else {
		// $recursion_html .= "<input type='hidden' name='acting' value='payjp_recursion_month' />";
		// }
		$rand = rand();
		// <input type='hidden' name='payjp_teiki_id' value='".$payjp_teiki_id."' />
		$recursion_html .= "
	<input type='hidden' name='acting' value='__card' />
	<!--input type='hidden' name='acting_return' value='1' /-->
	<input type='hidden' name='result' value='1' />
	<input type='hidden' name='sub' value='" . $rand . "' />
	<input type='hidden' name='nonce' value='" . wp_create_nonce( '__card' ) . "'>
	</form></td>";

		$header .= $recursion_html;
		echo $header;
	}


	/**
	 * 管理画面から定期課金停止処理リクエストがあった場合の挙動
	 */
	function payjp_teiki_delete()
	{

		$this->payjp_error_log( __FUNCTION__ . " : -------- payjp_teiki_delete ---" );


		if ( !wp_verify_nonce( $_REQUEST[ 'nonce' ], '__delete' ) ) echo '削除失敗しました。ブラウザをリロードしてからお試しください。';

		try {
			$payjp_teiki_id = esc_attr( $_POST[ 'payjp_teiki_id' ] );

			$payjp_result = $this->payjp_teiki_delete_by_id( $payjp_teiki_id );
			$this->payjp_error_log( " DELETE TEIKI_ID: " . $payjp_teiki_id );
			echo '削除完了しました。ステータスの変更を確認するにはこの受注データを再表示してください。';
		} catch ( Exception $e ) {
			$this->payjp_error_log( " DELETE FAILED ::::  TEIKI_ID: " . $payjp_teiki_id );
			echo '削除失敗しました。MSG:' . $e->getMessage();
		}
	}

	/**
	 * クレジットカード変更処理
	 */
	/**
	 * クレジットカード変更処理
	 */
	function payjp_card_update()
	{
		$secret_key = get_option( 'payjp_secret_key' );

		$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', usces_memberinfo( 'ID', 'return' ), 'return' );

		// カード変更フラグ
		$acting_flag = esc_attr( $_POST[ 'acting' ] );
		if ( $acting_flag == '__card' ) {
			$this->payjp_error_log( __FUNCTION__ . ' : ==== payjp_card_update ==== **********************************' );
			if ( !wp_verify_nonce( $_POST[ 'nonce' ], $acting_flag ) ) {
				wp_redirect( USCES_CART_URL );
				exit;
			}
			if ( $payjp_cus_key ) {
				try {
					\Payjp\Payjp::setApiKey( $secret_key );
					$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_key );

					$payjp_result = NULL;
					if ( $payjp_customer_info ) {
						$payjp_token = esc_attr( $_POST[ 'payjp-token' ] );
						// 現在のカード削除
						if ( $payjp_customer_info->default_card ) {
							$payjp_customer_card = $payjp_customer_info->cards->retrieve( $payjp_customer_info->default_card );
							$payjp_customer_card->delete();
						}
						// カード作成
						$card_response = $payjp_customer_info->cards->create( array(
							"card" => $payjp_token
						) );
						$card = $payjp_customer_info->cards->retrieve( $card_response->id );
						$card->exp_year = $card_response->exp_year;
						$card->exp_month = $card_response->exp_month;
						$result = $card->save();
						$card->default_card = $card_response->id;
						$result = $card->save();

						$this->payjp_error_log( "======= * * * * * = CARD SAVE OK = * * * * * * =====" );
					}
				} catch ( Exception $e ) {
					// N/A
				}
			}
			$response_data = array();

			$this->payjp_error_log( __FUNCTION__ . ": === card change finished == " );
			$response_data[ 'acting' ] = $acting_flag;
			$response_data[ 'acting_return' ] = 1;
			$response_data[ 'result' ] = 1;
			$response_data[ 'nonce' ] = wp_create_nonce( $acting_flag );

			return true;
		}
		// 終了
	}

	/**
	 * 定期課金停止処理
	 */
	function payjp_teiki_delete_by_id( $teiki_id )
	{
		try {
			$secret_key = get_option( 'payjp_secret_key' );
			\Payjp\Payjp::setApiKey( $secret_key );
			$su = \Payjp\Subscription::retrieve( $teiki_id );
			$result = $su->cancel();
			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}
	}


	/**
	 * メールに文言追加
	 */
	function payjp_order_mail_payment( $msg_payment, $order_id, $payment, $cart, $entry, $data )
	{
		if ( strpos( $payment[ 'settlement' ], 'acting_payjp' ) !== 0 ) return $msg_payment;
		$this->payjp_error_log( ' payjp_order_mail_payment :::::: ' );
		$this->payjp_error_log( ' ORDER_ID: ' . $order_id );

		global $usces;

		$secret_key = get_option( 'payjp_secret_key' );
		$this->payjp_error_log( "SECRET KEY : " . $secret_key );
		\Payjp\Payjp::setApiKey( $secret_key );
		try {
			$payjp_charge_id = usces_get_custom_field_value( 'order', 'payjp_charge_id', $order_id, 'return' );
			$payjp_teiki_id = usces_get_custom_field_value( 'order', 'payjp_teiki_id', $order_id, 'return' );
			$payjp_charge_info = NULL;
			if ( $payjp_teiki_id != NULL ) {
				$payjp_recursion_info = \Payjp\Subscription::retrieve( $payjp_teiki_id );
// 日割の場合、今回の課金額計算
				/**
				 * 例えばプラン金額が1,000円、課金日が1日、課金作成日が11月17日だとします。この場合、作成日(11/17)から次の課金日(12/1)までの日数は14日です。また直前の課金日は11月1日なので次の課金日(12/1)までの日数は30日です。30日のうち14日分を課金するので、日割りによる課金額は1,000円 x 14 / 30 = 466円となります。
				 */
				$subsc_created = $payjp_recursion_info->created;    // time値
				$plan_trial_days = intval( $payjp_recursion_info->plan->trial_days );
				$subsc_created_date = date( 'Y-m-d', $subsc_created );
				if ( $plan_trial_days > 0 ) {
					$subsc_created_date = date( 'Y-m-d', strtotime( "+" . $plan_trial_days . " day", $subsc_created ) );
				}
				$subsc_current_period_end = $payjp_recursion_info->current_period_end;    // time値
				$subsc_next_date = date( 'Y-m-d', $subsc_current_period_end );

				$diff_days = $this->day_diff( $subsc_created_date, $subsc_next_date );

				$subsc_prev_date = date( "Y-m-d", strtotime( "-1 month", $subsc_current_period_end ) );

				$interval_days = $this->day_diff( $subsc_prev_date, $subsc_next_date );

				$plan_amount = $payjp_recursion_info->plan->amount;

				$hiwari = floor( $plan_amount * $diff_days / $interval_days );    // 小数点以下切り捨て
				if ( $hiwari < 0 ) $hiwari = 0;    // マイナスにはならない

				$this->payjp_error_log( ' created: ' . $subsc_created_date . '  next: ' . $subsc_next_date . '  amount: ' . $plan_amount . ' hiwari: ' . $hiwari );

				$payjp_cus_id = $payjp_recursion_info->customer;
				$payjp_customer_info = \Payjp\Customer::retrieve( $payjp_cus_id );
//$this->payjp_error_log( "charge_id: ".$payjp_charge_id );
//$payjp_charge_info = \Payjp\Charge::retrieve( $payjp_charge_id );

				$payjp_customer_card = $payjp_customer_info->cards->retrieve( $payjp_customer_info->default_card );
				$payjp_card_last4 = $payjp_customer_card->last4;
				$payjp_card_type = $payjp_customer_card->brand;
			} else {
				$payjp_charge_info = \Payjp\Charge::retrieve( $payjp_charge_id );
				$payjp_card_last4 = $payjp_charge_info->card->last4;
				$payjp_card_type = $payjp_charge_info->card->brand;
			}
		} catch ( Exception $e ) {
			$this->payjp_error_log( $e->getMessage() );
			$payjp_charge_id = '';
			$payjp_charge_info = NULL;
			$payjp_card_last4 = '';
			$payjp_card_type = '';
		}
		$card_info = '';
		if ( $payjp_card_type != '' && $payjp_card_last4 != '' ) {
			$card_info = "（カード番号: " . $payjp_card_type . " **** **** **** " . $payjp_card_last4 . "）";
		}

		$mes = "-------------------------------------------\r\n";
		$mes .= "下記のカードを利用しています。\r\n";
		$mes .= $card_info . "\r\n";
		// 日割課金フラグ
		$payjp_use_recursion_month_prorate = intval( get_option( 'payjp_use_recursion_month_prorate' ) );
		if ( isset( $hiwari ) && strpos( $payment[ 'settlement' ], 'month' ) !== FALSE ) {
			// 日割課金ONの場合 で 年次定期課金ではないこと（月次定期課金のみ）
			$this->payjp_error_log( 'PAYMENT : Settlement :: ' . $payment[ 'settlement' ] );
			if ( $payjp_use_recursion_month_prorate == 1 && strpos( $payment[ 'settlement' ], 'year' ) === FALSE ) {
				$mes .= " 定期課金の今回支払日割計算額は ￥" . number_format( $hiwari ) . " となっています。\r\n";
			}
			$mes .= " 次回課金日は " . $subsc_next_date . " となっております。\r\n";
		}
		$mes .= "-------------------------------------------\r\n";
		$mes = apply_filters( 'payjp_order_mail_payment', $mes, $payjp_card_type, $payjp_card_last4, $card_info, $hiwari, $payjp_use_recursion_month_prorate );

		return $msg_payment . $mes;
	}

	function day_diff( $date1, $date2 )
	{

		// 日付をUNIXタイムスタンプに変換
		$timestamp1 = strtotime( $date1 );
		$timestamp2 = strtotime( $date2 );

		// 何秒離れているかを計算
		$seconddiff = abs( $timestamp2 - $timestamp1 );

		// 日数に変換
		$daydiff = $seconddiff / ( 60 * 60 * 24 );

		// 戻り値
		return $daydiff;

	}

	/**
	 * エラーログ出力
	 */
	function payjp_error_log( $msg )
	{
		// エラーログ出力は wp_config.php 等で　define('PAYJP_ERROR_LOG_ON', '1'); とすればOK
		if ( defined( 'PAYJP_ERROR_LOG_ON' ) ) {
			error_log( $msg );
		}
	}

	/**
	 * プラグイン初期化時処理
	 */
	function payjp_set_initial()
	{
		// オプション初期値設定

		if (
			get_option( 'payjp_public_key', '' ) == '' &&
			get_option( 'payjp_secret_key', '' ) == ''
		) {
			// PAY.JP公開キー
			update_option( 'payjp_public_key', '' );
			// PAY.JP秘密キー
			update_option( 'payjp_secret_key', '' );
			// PAY.JP OAuth Client ID
			update_option( 'payjp_oauth_client_id', '' );
			// PAY.JP OAuth Client Secret
			update_option( 'payjp_oauth_client_secret', '' );
			// 会員にカード情報紐付け 1:する
			update_option( 'payjp_save_customer', 0 );
			// 利用可能カード種別 0:表示しない 1:VISA/MASTER 2:VISA/MASTER/JCB/DINERS/AMEX
			update_option( 'payjp_use_card_info_flg', 0 );
			// 定期課金を利用する 1:利用する
			update_option( 'payjp_use_recursion_flg', 0 );
			// 定期課金を利用できるのは会員のみに限定する 1:限定
			update_option( 'payjp_use_recursion_member_only_flg', 0 );
			// 定期課金 会員限定時のエラーメッセージ
			update_option( 'payjp_use_recursion_member_only_msg', PAYJP_USE_RECURSION_MEMBER_ONLY_MSG );
			// 月単位 定期課金アイテムの SKU値
			update_option( 'payjp_use_recursion_month_item_ids', '' );
			// 月単位 定期課金 初回日程（日のみ）
			update_option( 'payjp_use_recursion_month_first_scheduled', '' );
			// 月単位 定期課金 日割課金フラグ
			update_option( 'payjp_use_recursion_month_prorate', '' );
			// 月単位 定期課金 トライアル日数
			update_option( 'payjp_use_recursion_month_trial_days', '' );
			// 年単位 定期課金アイテムの SKU値
			update_option( 'payjp_use_recursion_year_item_ids', '' );
			// 年単位 定期課金 初回日程（月日）
			update_option( 'payjp_use_recursion_year_first_scheduled', '' );
			// 年単位 定期課金 日割課金フラグ
			update_option( 'payjp_use_recursion_year_prorate', '' );
			// 年単位 定期課金 トライアル日数
			update_option( 'payjp_use_recursion_year_trial_days', '' );

			// 定期課金アイテム（月・年も別）が混在している場合のエラーメッセージ
			update_option( 'payjp_error_recursion_kongo', PAYJP_ERROR_RECURSION_KONGO_MSG );
			// 会員購入履歴に定期課金削除ボタン表示するか
			update_option( 'payjp_use_member_history_recursion_stop_flg', '' );  // 1: する
			// CheckoutHelperからsubmit時のloading画像
			update_option( 'payjp_on_welcart_loading_gif', PAYJP_ON_WELCART_LOADING_GIF );
		}
	}

	/**
	 * プラグイン無効化時処理
	 */
	function payjp_deactivate()
	{

		$options = get_option( 'usces_payment_structure' );

		$new_options = array();
		// 支払い方法からPAY.JP関連を削除
		foreach ( $options as $name => $value ) {
			if ( !in_array( $name, array(
				'acting_payjp',
				'acting_payjp_customer',
				'acting_payjp_recursion_month',
				'acting_payjp_recursion_month_customer',
				'acting_payjp_recursion_year',
				'acting_payjp_recursion_year_customer'
			) ) ) {
				$new_options[ $name ] = $value;
			}
		}
		ksort( $new_options );
		update_option( 'usces_payment_structure', $new_options );
	}

	function payjp_uninstall()
	{
		$options = get_option( 'usces_payment_structure' );

		$new_options = array();
		// 支払い方法からPAY.JP関連を削除
		foreach ( $options as $name => $value ) {
			if ( !in_array( $name, array(
				'acting_payjp',
				'acting_payjp_customer',
				'acting_payjp_recursion_month',
				'acting_payjp_recursion_month_customer',
				'acting_payjp_recursion_year',
				'acting_payjp_recursion_year_customer'
			) ) ) {
				$new_options[ $name ] = $value;
			}
		}
		ksort( $new_options );
		update_option( 'usces_payment_structure', $new_options );

		// 独自に設定したオプションを削除する
		delete_option( 'payjp_public_key' );
		delete_option( 'payjp_secret_key' );
		delete_option( 'payjp_oauth_client_id' );
		delete_option( 'payjp_oauth_client_secret' );
		delete_option( 'payjp_save_customer' );
		delete_option( 'payjp_use_card_info_flg' );
		delete_option( 'payjp_use_recursion_flg' );
		delete_option( 'payjp_use_recursion_member_only_flg' );
		delete_option( 'payjp_use_recursion_member_only_msg' );
		delete_option( 'payjp_use_recursion_month_item_ids' );
		delete_option( 'payjp_use_recursion_month_first_scheduled' );
		delete_option( 'payjp_use_recursion_month_past_future_flg' );
		delete_option( 'payjp_use_recursion_month_prorate' );
		delete_option( 'payjp_use_recursion_month_trial_days' );
		delete_option( 'payjp_use_recursion_year_item_ids' );
		delete_option( 'payjp_use_recursion_year_first_scheduled' );
		delete_option( 'payjp_use_recursion_year_past_future_flg' );
		delete_option( 'payjp_use_recursion_year_prorate' );
		delete_option( 'payjp_use_recursion_year_trial_days' );
		delete_option( 'payjp_error_recursion_kongo' );
		delete_option( 'payjp_use_member_history_recursion_stop_flg' ); // 1: する
		delete_option( 'payjp_on_welcart_loading_gif' );
	}
}

$payjpOnWelcart = new PayjpOnWelcart();

