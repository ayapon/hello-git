<?php
require_once( PAYJP_PLUGIN_DIR . '/payjp-php/init.php' );

use Payjp\Payjp;

/**
 * Class PayjpOnWelcartSetting
 */
class PayjpOnWelcartSetting
{
	/**
	 * PayjpOnWelcartSetting constructor.
     * 管理画面コンストラクタ
	 */
	function __construct()
	{
		add_action( 'usces_action_settlement_tab_title', array( $this, 'tab_title' ), 99 );
		add_action( 'usces_action_settlement_tab_body', array( $this, 'payjp_setting_page' ) );
		add_action( 'usces_action_admin_settlement_update', array( $this, 'data_update' ) );

		// add_action('admin_menu', array($this, 'add_pages'));
		add_action( 'usces_action_admin_member_info', array( $this, 'payjp_usces_action_admin_member_info' ), 10, 3 );
		add_action( 'usces_action_order_edit_form_detail_top', array( $this, 'payjp_usces_action_order_edit_form_detail_top' ), 12, 3 );
		add_action( 'wp_ajax_payjp_recursion_delete', array( $this, 'payjp_recursion_delete' ) );
		add_action( 'wp_ajax_payjp_recursion_pause', array( $this, 'payjp_recursion_pause' ) );
		add_action( 'wp_ajax_payjp_recursion_restart', array( $this, 'payjp_recursion_restart' ) );
		add_action( 'wp_ajax_nopriv_payjp_recursion_delete', array( $this, 'payjp_recursion_delete' ) );

		$this->payjp_usces_action_admin_settlement_update();
	}

	/*** ADD SETTLEMENT UPDATE **/

	/**
	 * クレジットカード更新案内メール
	 */
	function payjp_recursion_update_mail()
	{
		global $usces;
		$usces->options = get_option( 'usces' );
		$mail_data = $usces->options[ 'mail_data' ];
		?>
        <div class="postbox">
            <h3 class="hndle"><span>クレジットカード更新のご案内メール</span><a style="cursor:pointer;"
                                                               onclick="toggleVisibility('ex_payjp_settlement_update');">
                    (<?php _e( 'explanation', 'usces' ); ?>) </a></h3>
            <div class="inside">
                <table class="form_table">
                    <tr>
                        <th width="150"><?php _e( 'Title', 'usces' ); ?></th>
                        <td><input name="title[settlement_update]" id="title[settlement_update]" type="text"
                                   class="mail_title"
                                   value="<?php esc_attr_e( $mail_data[ 'title' ][ 'settlement_update' ] ); ?>"/></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <th><?php _e( 'header', 'usces' ); ?></th>
                        <td><textarea name="header[settlement_update]" id="header[settlement_update]"
                                      class="mail_header"><?php esc_attr_e( $mail_data[ 'header' ][ 'settlement_update' ] ); ?></textarea>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <th><?php _e( 'footer', 'usces' ); ?></th>
                        <td><textarea name="footer[settlement_update]" id="footer[settlement_update]"
                                      class="mail_footer"><?php esc_attr_e( $mail_data[ 'footer' ][ 'settlement_update' ] ); ?></textarea>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <hr size="1" color="#CCCCCC"/>
                <div id="ex_payjp_settlement_update" class="explanation">カード決済が不履行になったときに、クレジットカード更新をお願いするメール。</div>
            </div>
        </div><!--postbox-->
		<?php
	}

	/**
     * payjp_admin_ordernavi
	 * @param $ordernavi
	 * @param $ordercheck
	 * @return string
	 */
	function payjp_admin_ordernavi( $ordernavi, $ordercheck )
	{
		$checked = ( isset( $ordercheck[ 'settlementupdatemail' ] ) ) ? ' checked="checked"' : '';
		$ordernavi = '
  <td><input name="check[settlementupdatemail]" type="checkbox" value="settlementupdatemail"' . $checked . ' /><a href="#" id="settlementupdateMail">クレジットカード更新のご案内メール</a></td>';
		return $ordernavi;
	}

	/**
     * payjp_action_order_list_page
	 * @param $order_action
	 */
	function payjp_action_order_list_page( $order_action )
	{
		global $usces;

		if ( 'edit' == $order_action and isset( $_GET[ 'payjp_re-settlement' ] ) and isset( $_GET[ 'order_id' ] ) ) {
			$err_code = '';
			$settltment_errmsg = '';
			$order_id = $_GET[ 'order_id' ];
			$data = $usces->get_order_data( $order_id, 'direct' );
			$member_id = $data[ 'mem_id' ];
			$member = $usces->get_member_info( $member_id );
			$pcid = $usces->get_member_meta_value( 'zeus_pcid', $member_id );
			if ( $order_id and $member and $pcid ) {
				$acting_flag = 'acting_payjp';
				$total_price = $data[ 'order_item_total_price' ] - $data[ 'order_usedpoint' ] + $data[ 'order_discount' ] + $data[ 'order_shipping_charge' ] + $data[ 'order_cod_fee' ] + $data[ 'order_tax' ];
				$acting_opts = $usces->options[ 'acting_settings' ][ 'payjp' ];
				$interface = parse_url( $acting_opts[ 'card_url' ] );
				$vars = 'send=mall';
				$vars .= '&clientip=' . $acting_opts[ 'clientip' ];
				$vars .= '&cardnumber=9999999999999999';
				$vars .= '&expyy=10';
				$vars .= '&expmm=01';
				$vars .= '&telno=' . str_replace( '-', '', $member[ 'mem_tel' ] );
				$vars .= '&email=' . $member[ 'mem_email' ];
				$vars .= '&sendid=' . $member[ 'ID' ];
				$vars .= '&username=WCEXAUTODELIVERY';
				$vars .= '&money=' . $total_price;
				$vars .= '&printord=yes';
				$vars .= '&pubsec=yes';
				$vars .= '&return_value=yes';

				$header = "POST " . $interface[ 'path' ] . " HTTP/1.1\r\n";
				$header .= "Host: " . $_SERVER[ 'HTTP_HOST' ] . "\r\n";
				$header .= "User-Agent: PHP Script\r\n";
				$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$header .= "Content-Length: " . strlen( $vars ) . "\r\n";
				$header .= "Connection: close\r\n\r\n";
				$header .= $vars;
				$fp = fsockopen( 'ssl://' . $interface[ 'host' ], 443, $errno, $errstr, 30 );

				$page = '';
				if ( $fp ) {
					fwrite( $fp, $header );
					while ( !feof( $fp ) ) {
						$scr = fgets( $fp, 1024 );
						$page .= $scr;
					}
					fclose( $fp );

					if ( false !== strpos( $page, 'Success_order' ) ) {
						//usces_log( 'zeus card : [WCEX_Auto_Delivery] Auto order data (acting_processing)', 'acting_transaction.log' );
						$ordd = usces_get_order_number( $page );
						if ( !empty( $ordd ) ) $usces->set_order_meta_value( 'order_number', $ordd, $order_id );
						$settlement = array( "settltment_status" => __( 'Re-settlement', 'autodelivery' ) );
						$usces->set_order_meta_value( $acting_flag, serialize( $settlement ), $order_id );
					} else {
						$err_code = usces_get_err_code( $page );
						$settltment_errmsg = 'ERROR : ' . __( '[Regular purchase] Settlement was not completed.', 'autodelivery' ) . ' err_code = (' . $err_code . ')';
						//usces_log( 'zeus card : [WCEX_Auto_Delivery] Certification Error : '.$err_code, 'acting_transaction.log' );
					}
				} else {
					$settltment_errmsg = 'ERROR : ' . __( '[Regular purchase] Socket connection error.', 'autodelivery' );
					//usces_log( 'zeus card : [WCEX_Auto_Delivery] Socket Error', 'acting_transaction.log' );
				}
			} else {
				$settltment_errmsg = 'ERROR : ' . __( '[Regular purchase] Member information acquisition error.', 'autodelivery' );
				//usces_log( 'zeus card : [WCEX_Auto_Delivery] Member Error : '.$member_id, 'acting_transaction.log' );
			}

			if ( '' == $settltment_errmsg ) {
				$usces->set_action_status( 'success', __( 'Re-settlement has been completed successfully.', 'autodelivery' ) );
				//} elseif( 0 === $res ) {
				//  $usces->set_action_status( 'none', '' );
			} else {
				$usces->set_action_status( 'error', $settltment_errmsg );
			}
		}
	}

	function payjp_filter_settle_info_field_value( $value, $key, $acting )
	{
		if ( 'settltment_status' == $key && __( 'Failure', 'autodelivery' ) == $value ) {
			$order_id = ( isset( $_REQUEST[ 'order_id' ] ) ) ? $_REQUEST[ 'order_id' ] : '';
			if ( '' != $order_id ) $value .= ' <input type="button" name="payjp_re-settlement" value="' . __( 'Re-settlement', 'autodelivery' ) . '" onclick="location.href=\'' . USCES_ADMIN_URL . '?page=usces_orderlist&order_action=edit&payjp_re-settlement=1&order_id=' . $order_id . '\'" />';
		}
		return $value;
	}

	function payjp_action_order_edit_page_js( $order_id, $data )
	{
		global $usces;
		$mail_data = $usces->options[ 'mail_data' ];

		$html = '
  $("#settlementupdateMail").click(function() {
    orderItem.getmailmessage("settlementupdateMail");
    $("#sendmailaddress").val($("input[name=\'customer\[mailaddress\]\']").val());
    $("#sendmailname").val($("input[name=\'customer\[name1\]\']").val()+$("input[name=\'customer\[name2\]\']").val());
    $("#sendmailsubject").val("' . $mail_data[ 'title' ][ 'settlement_update' ] . '");
    $("#mailChecked").val("settlementupdatemail");
    $("#mailSendDialog").dialog("option", "title", "クレジットカード更新のご案内メール");
    $("#mailSendDialog").dialog("open");
  });';
		echo $html;
	}

	function payjp_filter_order_item_ajax( $res )
	{
		if ( 'settlementupdateMail' == $_POST[ 'mode' ] ) {
			$res = usces_order_confirm_message( $_POST[ 'order_id' ] );
		}
		return $res;
	}

	/** /ADD_SETTLEMENT_UPDATE **/
	function add_pages()
	{
		$mincap = 'manage_options';
		if ( function_exists( 'add_submenu_page' ) ) {
			add_menu_page( 'PAY.JP設定', 'PAY.JP設定', $mincap, __FILE__, array( $this, 'payjp_setting_page' ) );
		}
	}

	/**********************************************
	 * Settlement setting page tab title
	 * @param  -
	 * @return -
	 * @echo   str
	 ***********************************************/
	public function tab_title()
	{
		echo '<li><a href="#uscestabs_payjp">PAY.JP</a></li>';
	}

	/**
	 * data_update
	 */
	public function data_update()
	{
		//$_POST['payjp_public_key'])があったら保存
		if ( isset( $_POST[ 'payjp_public_key' ] ) && isset( $_POST[ 'payjp_secret_key' ] ) ) {
			check_admin_referer( 'payjp2016' );
			$payjp_public_key = esc_attr( $_POST[ 'payjp_public_key' ] );
			update_option( 'payjp_public_key', $payjp_public_key );
			$payjp_secret_key = esc_attr( $_POST[ 'payjp_secret_key' ] );
			update_option( 'payjp_secret_key', $payjp_secret_key );

			$payjp_oauth_client_id = esc_attr( $_POST[ 'payjp_oauth_client_id' ] );
			update_option( 'payjp_oauth_client_id', $payjp_oauth_client_id );
			$payjp_oauth_client_secret = esc_attr( $_POST[ 'payjp_oauth_client_secret' ] );
			update_option( 'payjp_oauth_client_secret', $payjp_oauth_client_secret );

			$payjp_checkout_btn_text = esc_attr( $_POST[ 'payjp_checkout_btn_text' ] );
			update_option( 'payjp_checkout_btn_text', $payjp_checkout_btn_text );
			$payjp_submit_btn_text = esc_attr( $_POST[ 'payjp_submit_btn_text' ] );
			update_option( 'payjp_submit_btn_text', $payjp_submit_btn_text );

			$payjp_save_customer = esc_attr( $_POST[ 'payjp_save_customer' ] );
			update_option( 'payjp_save_customer', $payjp_save_customer );
			$payjp_use_card_info_flg = esc_attr( $_POST[ 'payjp_use_card_info_flg' ] );
			update_option( 'payjp_use_card_info_flg', $payjp_use_card_info_flg );

			// 定期課金使用するフラグ
			$payjp_use_recursion_flg = esc_attr( $_POST[ 'payjp_use_recursion_flg' ] );
			update_option( 'payjp_use_recursion_flg', $payjp_use_recursion_flg );

			// 定期課金会員限定フラグ
			$payjp_use_recursion_member_only_flg = esc_attr( $_POST[ 'payjp_use_recursion_member_only_flg' ] );
			update_option( 'payjp_use_recursion_member_only_flg', $payjp_use_recursion_member_only_flg );
			// 定期課金会員限定メッセージ
			$payjp_use_recursion_member_only_msg = esc_attr( $_POST[ 'payjp_use_recursion_member_only_msg' ] );
			update_option( 'payjp_use_recursion_member_only_msg', $payjp_use_recursion_member_only_msg );

			// 定期課金（月単位）アイテムの SKU（カンマ区切りで複数OK）
			$payjp_use_recursion_month_item_ids = esc_attr( $_POST[ 'payjp_use_recursion_month_item_ids' ] ); //カンマ区切りの商品SKU
			update_option( 'payjp_use_recursion_month_item_ids', $payjp_use_recursion_month_item_ids );
			// 定期課金（月単位）の日付（日のみ）
			$payjp_use_recursion_month_first_scheduled = esc_attr( $_POST[ 'payjp_use_recursion_month_first_scheduled' ] );
			update_option( 'payjp_use_recursion_month_first_scheduled', $payjp_use_recursion_month_first_scheduled );
			// 定期課金（月単位）を日割り課金にするか
			$payjp_use_recursion_month_prorate = esc_attr( $_POST[ 'payjp_use_recursion_month_prorate' ] );
			update_option( 'payjp_use_recursion_month_prorate', $payjp_use_recursion_month_prorate );
			// 定期課金（月単位）のトライアル日数（０または空文字で設定しない）
			$payjp_use_recursion_month_trial_days = esc_attr( $_POST[ 'payjp_use_recursion_month_trial_days' ] );
			update_option( 'payjp_use_recursion_month_trial_days', $payjp_use_recursion_month_trial_days );
			// 2017/01 年次課金開始
			// 定期課金（年単位）アイテムのSKU（カンマ区切りで複数OK）
			$payjp_use_recursion_year_item_ids = esc_attr( $_POST[ 'payjp_use_recursion_year_item_ids' ] ); //カンマ区切りの商品SKU
			update_option( 'payjp_use_recursion_year_item_ids', $payjp_use_recursion_year_item_ids );
			// 定期課金（年単位）の日付（月日） 2017/01/14現在年次課金は同日のみ
			// $payjp_use_recursion_year_first_scheduled = esc_attr( $_POST['payjp_use_recursion_year_first_scheduled'] );
			// update_option( 'payjp_use_recursion_year_first_scheduled', $payjp_use_recursion_year_first_scheduled );
			// 定期課金（年単位）を日割り課金にするか  2017/01/14現在日割りなし
			// $payjp_use_recursion_year_prorate = esc_attr( $_POST['payjp_use_recursion_year_prorate'] );
			// update_option( 'payjp_use_recursion_year_prorate', $payjp_use_recursion_year_prorate );
			// 定期課金（年単位）のトライアル日数（０または空文字で設定しない）
			$payjp_use_recursion_year_trial_days = esc_attr( $_POST[ 'payjp_use_recursion_year_trial_days' ] );
			update_option( 'payjp_use_recursion_year_trial_days', $payjp_use_recursion_year_trial_days );

			// CheckoutHelperからのsubmit時ローディング画像
			$payjp_on_welcart_loading_gif = esc_attr( $_POST[ 'payjp_on_welcart_loading_gif' ] );
			update_option( 'payjp_on_welcart_loading_gif', $payjp_on_welcart_loading_gif );

			// 会員画面購入履歴に表示
			$payjp_use_member_history_recursion_stop_flg = esc_attr( $_POST[ 'payjp_use_member_history_recursion_stop_flg' ] );
			update_option( 'payjp_use_member_history_recursion_stop_flg', $payjp_use_member_history_recursion_stop_flg );

			// 会員画面にカード変更ボタン表示
			$payjp_use_member_card_chg_flg = esc_attr( $_POST[ 'payjp_use_member_card_chg_flg' ] );
			update_option( 'payjp_use_member_card_chg_flg', $payjp_use_member_card_chg_flg );

			echo( '<div class="updated fade"><p><strong>保存しました。</strong></p></div>' );
		}

	}

	/**
	 * payjp_setting_page
	 */
	function payjp_setting_page()
	{

		// 定期課金のアイテムと通常アイテムが混在している場合のエラー
		$payjp_error_recursion_kongo = esc_attr( $_POST[ 'payjp_error_recursion_kongo' ] );
		update_option( 'payjp_error_recursion_kongo', $payjp_error_recursion_kongo );
		?>

        <div id="uscestabs_payjp">
            <div class="settlement_service"><span class="service_title">PAY.JP 設定</span></div>
            <form action="" method="post">
				<?php
				wp_nonce_field( 'payjp2016' );
				$payjp_public_key = get_option( 'payjp_public_key' );
				$payjp_secret_key = get_option( 'payjp_secret_key' );
				$payjp_oauth_client_id = get_option( 'payjp_oauth_client_id' );
				$payjp_oauth_client_secret = get_option( 'payjp_oauth_client_secret' );
				$payjp_checkout_btn_text = get_option( 'payjp_checkout_btn_text' );
				$payjp_submit_btn_text = get_option( 'payjp_submit_btn_text' );

				$payjp_save_customer = get_option( 'payjp_save_customer' );
				$payjp_save_customer_check = ( $payjp_save_customer == "1" ? " checked='checked'" : "" );
				$payjp_use_card_info_flg = get_option( 'payjp_use_card_info_flg' );
				$payjp_use_card_info_flg_mark_1 = ( $payjp_use_card_info_flg == '1' ? ' checked' : '' );
				$payjp_use_card_info_flg_mark_2 = ( $payjp_use_card_info_flg == '2' ? ' checked' : '' );
				$payjp_use_card_info_flg_mark_0 = ( $payjp_use_card_info_flg == '0' ? ' checked' : ( $payjp_use_card_info_flg_mark_1 != ' checked' && $payjp_use_card_info_flg_mark_2 != ' checked' ? ' checked' : '' ) );

				// 定期課金利用
				$payjp_use_recursion_flg = get_option( 'payjp_use_recursion_flg', 0 );  // 0:利用しない 1:利用する
				$payjp_use_recursion_flg_check = ( $payjp_use_recursion_flg == "1" ? " checked='checked'" : "" );

				// 定期課金を利用できるのは会員だけに限定するか
				$payjp_use_recursion_member_only_flg = get_option( 'payjp_use_recursion_member_only_flg', 0 );  // 0:限定しない 1:限定する
				$payjp_use_recursion_member_only_flg_check = ( $payjp_use_recursion_member_only_flg == "1" ? " checked='checked'" : "" );

				// 定期課金（月単位）を利用するアイテムの SKU（カンマ区切りで複数可能）
				$payjp_use_recursion_month_item_ids = get_option( 'payjp_use_recursion_month_item_ids', '' );
				$payjp_use_recursion_month_first_scheduled = get_option( 'payjp_use_recursion_month_first_scheduled', '' );

				// 定期課金（月単位）を日割り課金にするか
				$payjp_use_recursion_month_prorate = get_option( 'payjp_use_recursion_month_prorate', '' );
				$payjp_use_recursion_month_prorate_check = ( $payjp_use_recursion_month_prorate == "1" ? " checked='checked'" : "" );

				// 定期課金（月単位）のトライアル日数
				$payjp_use_recursion_month_trial_days = get_option( 'payjp_use_recursion_month_trial_days', '' );

				// 定期課金（年単位）を利用するアイテムの SKU（カンマ区切りで複数可能）
				$payjp_use_recursion_year_item_ids = get_option( 'payjp_use_recursion_year_item_ids', '' );
				$payjp_use_recursion_year_first_scheduled = get_option( 'payjp_use_recursion_year_first_scheduled', '' );

				// 定期課金（年単位）のトライアル日数（０または空文字で設定しない）
				$payjp_use_recursion_year_trial_days = get_option( 'payjp_use_recursion_year_trial_days', '' );

				$payjp_error_recursion_kongo = get_option( 'payjp_error_recursion_kongo', PAYJP_ERROR_RECURSION_KONGO_MSG );

				$payjp_use_member_history_recursion_stop_flg = get_option( 'payjp_use_member_history_recursion_stop_flg', '' );
				$payjp_use_member_history_recursion_stop_flg_check = ( $payjp_use_member_history_recursion_stop_flg == '1' ? " checked='checked'" : "" );

				$payjp_use_member_card_chg_flg = get_option( 'payjp_use_member_card_chg_flg', '' );
				$payjp_use_member_card_chg_flg_check = ( $payjp_use_member_card_chg_flg == '1' ? " checked='checked'" : "" );

				$payjp_use_recursion_member_only_msg = get_option( 'payjp_use_recursion_member_only_msg', PAYJP_USE_RECURSION_MEMBER_ONLY_MSG );

				$payjp_on_welcart_loading_gif = get_option( 'payjp_on_welcart_loading_gif', PAYJP_ON_WELCART_LOADING_GIF );

				$this_dir_url = plugins_url( plugin_basename( __FILE__ ) );
				?>
                <table class="settle_table">
                    <tr valign="top">
                        <th scope="row"><label for="inputsecret">秘密鍵</label></th>
                        <td><input name="payjp_secret_key" type="text" id="inputsecretkey"
                                   value="<?php echo $payjp_secret_key; ?>" class="regular-text"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="inputpublic">公開鍵</label></th>
                        <td><input name="payjp_public_key" type="text" id="inputpublickey"
                                   value="<?php echo $payjp_public_key; ?>" class="regular-text"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="input_oauth_client_id">OAuth Client ID</label></th>
                        <td><input name="payjp_oauth_client_id" type="text" id="input_oauth_client_id"
                                   value="<?php echo $payjp_oauth_client_id; ?>" class="regular-text"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="input_oauth_client_secret">OAuth Client Secret</label></th>
                        <td><input name="payjp_oauth_client_secret" type="text" id="input_oauth_client_secret"
                                   value="<?php echo $payjp_oauth_client_secret; ?>" class="regular-text"/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="inputpublic">決済開始ボタンテキスト</label></th>
                        <td><input name="payjp_submit_btn_text" type="text" id="inputsubmitbtntext"
                                   value="<?php echo $payjp_submit_btn_text; ?>" class="regular-text"/><br> %s
                            と記述した部分にご請求金額が入ります。
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="inputsecret">決済情報送信ボタンテキスト</label></th>
                        <td><input name="payjp_checkout_btn_text" type="text" id="inputcheckoutbtntext"
                                   value="<?php echo $payjp_checkout_btn_text; ?>" class="regular-text"/><br> %s
                            と記述した部分にご請求金額が入ります。
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="inputcustmer">会員にカード情報紐付ける</label></th>
                        <td><input name="payjp_save_customer" type="checkbox" id="inputcustmer"
                                   value="1" <?php echo $payjp_save_customer_check; ?> /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="inputcustmer">利用可能カード種別（契約とは連動しません）</label></th>
                        <td>
                            <label><input name="payjp_use_card_info_flg" type="radio" id="inputcardinfoflg"
                                          value="0" <?php echo $payjp_use_card_info_flg_mark_0; ?> />表示しない</label><br>
                            <label><input name="payjp_use_card_info_flg" type="radio" id="inputcardinfoflg"
                                          value="1" <?php echo $payjp_use_card_info_flg_mark_1; ?> /><img
                                        src="<?php echo $this_dir_url; ?>/../lib/payjp_2brands.png"
                                        data-pin-nopin="true"></label><br>
                            <label><input name="payjp_use_card_info_flg" type="radio" id="inputcardinfoflg"
                                          value="2" <?php echo $payjp_use_card_info_flg_mark_2; ?> /><img
                                        src="<?php echo $this_dir_url; ?>/../lib/payjp_5brands.png"
                                        data-pin-nopin="true"></label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label
                                    for="input_payjp_on_welcart_loading_gif">PAY.JPクレジットカード入力後の処理待ちローディング画像</label></th>
                        <td><input name="payjp_on_welcart_loading_gif" type="text"
                                   id="input_payjp_on_welcart_loading_gif"
                                   value="<?php echo $payjp_on_welcart_loading_gif; ?>"/></td>
                    </tr>
                </table>
                <hr>
                <table class="settle_table">
                    <tr valign="top">
                        <th scope="row"><label for="inputuserecursion">定期課金を利用する</label></th>
                        <td><input name="payjp_use_recursion_flg" type="checkbox" id="inputuserecursion"
                                   value="1" <?php echo $payjp_use_recursion_flg_check; ?> /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="inputuserecursion">定期課金を利用出来るのは会員のみに限定する</label></th>
                        <td><input name="payjp_use_recursion_member_only_flg" type="checkbox" id="inputmemberrecursion"
                                   value="1" <?php echo $payjp_use_recursion_member_only_flg_check; ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inputmemberonlymsg">定期課金 会員限定メッセージ</label></th>
                        <td><input name="payjp_use_recursion_member_only_msg" type="text" id="inputmemberonlymsg"
                                   value="<?php echo $payjp_use_recursion_member_only_msg; ?>" class="regular-text"/>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="inputuserecursionmonthitemids">月単位 定期課金アイテムのSKU値（複数カンマ区切り）</label>
                        </th>
                        <td><input name="payjp_use_recursion_month_item_ids" type="text"
                                   id="inputuserecursionmonthitemids"
                                   value="<?php echo $payjp_use_recursion_month_item_ids; ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inputuserecusionmonthfirst">定期課金初回日程（日のみ）</label></th>
                        <td><input name="payjp_use_recursion_month_first_scheduled" type="text"
                                   id="inputuserecursionmonthfirst"
                                   value="<?php echo $payjp_use_recursion_month_first_scheduled; ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="input_payjp_use_recursion_month_prorate">定期課金（月単位）を日割り課金にするか<br>※日割課金にしない場合は初回の支払が上記「定期課金初回日程」まで行われません。ご注意ください。</label>
                        </th>
                        <td><input name="payjp_use_recursion_month_prorate" id="input_payjp_use_recursion_month_prorate"
                                   type="checkbox" value="1" <?php echo $payjp_use_recursion_month_prorate_check; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="input_payjp_use_recursion_month_trial_days">定期課金（月単位）のトライアル日数（０または空文字で設定しない）</label>
                        </th>
                        <td><input type="text" name="payjp_use_recursion_month_trial_days"
                                   id="input_payjp_use_recursion_month_trial_days"
                                   value="<?php echo $payjp_use_recursion_month_trial_days; ?>"/><br>
                            <a href="http://blog.pay.jp/entry/2016/01/12/164741">トライアル課金についてはこちらをご参考に設定してください</a></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="inputuserecursionyearitemids">年単位 定期課金アイテムのSKU値（複数カンマ区切り）</label>
                        </th>
                        <td><input name="payjp_use_recursion_year_item_ids" type="text"
                                   id="inputuserecursionyearitemids"
                                   value="<?php echo $payjp_use_recursion_year_item_ids; ?>"/></td>
                    </tr>
					<?php /** 2017/01/14 現在 年次定期課金の指定日なし
					 * <tr>
					 * <th scope="row"><label for="inputuserecusionyearfirst">定期課金初回日程（月日）<br />※ 12-01、01-01 形式で入力してください</label></th>
					 * <td><input name="payjp_use_recursion_year_first_scheduled" type="text" id="inputuserecursionyearfirst" value="<?php echo $payjp_use_recursion_year_first_scheduled; ?>" /></td>
					 * </tr>
					 */ ?>
                    <tr>
                        <th scope="row"><label for="input_payjp_use_recursion_year_trial_days">定期課金（年単位）のトライアル日数（０または空文字で設定しない）</label>
                        </th>
                        <td><input type="text" name="payjp_use_recursion_year_trial_days"
                                   id="input_payjp_use_recursion_year_trial_days"
                                   value="<?php echo $payjp_use_recursion_year_trial_days; ?>"/><br>
                            <a href="http://blog.pay.jp/entry/2016/01/12/164741">トライアル課金についてはこちらをご参考に設定してください</a></td>
                    </tr>
					<?php /* 2017/01/14 現在年次課金の日割り課金はなし
  <tr>
    <th scope="row"><label for="input_payjp_use_recursion_year_prorate">定期課金（年単位）を日割り課金にするか</label></th>
    <td><input name="payjp_use_recursion_year_prorate" id="input_payjp_use_recursion_year_prorate" type="checkbox" value="1" <?php echo $payjp_use_recursion_year_prorate_check; ?>></td>
  </tr>
  */ ?>

                    <tr>
                        <th scope="row"><label for="inputerrormsg">定期課金アイテム混合時エラーメッセージ</label></th>
                        <td><input name="payjp_error_recursion_kongo" type="text" id="inputerrormsg"
                                   value="<?php echo $payjp_error_recursion_kongo; ?>" class="regular-text"/></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="payjp_use_member_history_recursion_stop_flg">会員購入履歴に「定期購入」の停止ボタン表示するか</label>
                        </th>
                        <td><input name="payjp_use_member_history_recursion_stop_flg"
                                   id="payjp_use_member_history_recursion_stop_flg" type="checkbox"
                                   value="1" <?php echo $payjp_use_member_history_recursion_stop_flg_check; ?> /></td>
                    </tr>

                    <tr>
                        <th scope="row"><label
                                    for="payjp_use_member_history_recursion_stop_flg">会員購入履歴に「カード変更」ボタン表示するか</label>
                        </th>
                        <td><input name="payjp_use_member_card_chg_flg" id="payjp_use_member_card_chg_flg"
                                   type="checkbox" value="1" <?php echo $payjp_use_member_card_chg_flg_check; ?> /></td>
                    </tr>

                </table>
                <input name="usces_option_update" type="submit" class="button button-primary" value="PAY.JPの設定を更新する"/>
				<?php wp_nonce_field( 'admin_settlement', 'wc_nonce' ); ?>
            </form>
            <div class="settle_exp">
                <p><strong>PAY.JP</strong></p>
                <a href="https://pay.jp/" target="_blank">PAY.JP サービスの詳細はこちら 》</a>
            </div>
        </div><!-- /uscestabs_payjp -->
		<?php
	}


	/**
	 * 決済種別追加
	 */
	function payjp_usces_action_admin_settlement_update()
	{
		$payjp_use_recursion_flg = get_option( 'payjp_use_recursion_flg' );
		$payjp_save_customer = get_option( 'payjp_save_customer' );

		$options = get_option( 'usces_payment_structure' );
		$options[ 'acting_payjp' ] = NULL;
		// 支払い方法追加
		$options[ 'acting_payjp' ] = 'PAY.JP';
		if ( $payjp_save_customer == '1' ) {
			$options[ 'acting_payjp_customer' ] = 'PAY.JP（会員紐付け）';
		}
		if ( $payjp_use_recursion_flg == '1' ) {
			$options[ 'acting_payjp_recursion_month' ] = 'PAY.JP定期課金（月単位）';
			if ( $payjp_save_customer == '1' ) {
				$options[ 'acting_payjp_recursion_month_customer' ] = 'PAY.JP定期課金（月単位・会員紐付け）';
			}
			$options[ 'acting_payjp_recursion_year' ] = 'PAY.JP定期課金（年単位）';
			if ( $payjp_save_customer == '1' ) {
				$options[ 'acting_payjp_recursion_year_customer' ] = 'PAY.JP定期課金（年単位・会員紐付け）';
			}

		}
		ksort( $options );
		update_option( 'usces_payment_structure', $options );
	}

	/**
	 * PAY.JP保存ID
	 */
	function payjp_usces_action_admin_member_info( $data, $member_metas, $usces_member_history )
	{
		// 161017 新規会員ページでは表示しないように分岐
		$page = esc_attr( $_REQUEST[ 'page' ] );
		if ( strpos( $page, "membernew" ) === FALSE ) {
			$payjp_cus_key = usces_get_custom_field_value( 'member', 'payjp_cus_key', $data[ 'ID' ], 'return' );
			echo "<hr>";
			$payjp_admin_customers = "https://pay.jp/dashboard/customers/";
			$secret_key = get_option( 'payjp_secret_key' );

			$payjp_admin_customers .= $payjp_cus_key;
			?>
            <script>
                function goto_payjp_customer_info() {
                    var newWindow = window.open('payjp_WIN');
                    newWindow.location.href = '<?php echo $payjp_admin_customers; ?>';
                    return false;
                }
            </script>
            <tr>
                <td class="label">PAY.JP<br/>Customer KEY</td>
                <td><?php echo $payjp_cus_key; ?>
                    <button id="payjp_customer_info" onclick="return goto_payjp_customer_info();">顧客の詳細表示</button>
                </td>
            </tr>
			<?php
		}
	}

	/**
	 * 定期課金ID保存
	 */
	function payjp_usces_action_order_edit_form_detail_top( $data, $csod_meta, $filter_args )
	{
		$payjp_charge_id = usces_get_custom_field_value( 'order', 'payjp_charge_id', $data[ 'ID' ], 'return' );
		$payjp_teiki_id = usces_get_custom_field_value( 'order', 'payjp_teiki_id', $data[ 'ID' ], 'return' );

		// init
		$payjp_cus_key = NULL;
		$payjp_recursion_canceld_at = NULL;
		$payjp_recursion_trial_days = NULL;
		$payjp_recursion_last_executed = NULL;
		$payjp_recursion_next_scheduled = NULL;
		$payjp_recursion_paused_at = NULL;

		echo "<hr>";
		$payjp_admin_charges = "https://pay.jp/dashboard/charges/";
		$secret_key = get_option( 'payjp_secret_key' );
		if ( strpos( trim( $secret_key ), "test" ) === 0 ) {
			$payjp_admin_charges = "https://pay.jp/dashboard/charges/";
		}
		$payjp_admin_charges .= $payjp_charge_id;
		?>
        <script>
            function goto_payjp_charges_info() {
                var newWindow = window.open('payjp_WIN');
                newWindow.location.href = '<?php echo $payjp_admin_charges; ?>';
                return false;
            }
        </script>
		<?php
		if ( $payjp_charge_id != '' && $payjp_teiki_id == NULL ) {
			?>
            <tr>
                <td class="label">課金ID</td>
                <td><?php echo $payjp_charge_id; ?></td>
                <td>
                    <button id="payjp_charges_info" onclick="return goto_payjp_charges_info();">課金情報表示</button>
                </td>
            </tr>
			<?php
		} elseif ( $payjp_teiki_id != NULL ) {
			$payjp_recursion_obj = NULL;
			\Payjp\Payjp::setApiKey( $secret_key );
			try {
				$payjp_recursion_obj = \Payjp\Subscription::retrieve( $payjp_teiki_id );
			} catch ( Exception $ex ) {
				// N/A
			}
			$payjp_recursion_status = '';
			if ( !is_null( $payjp_recursion_obj ) ) {
//				$payjp_recursion_deleted = $payjp_recursion_obj->deleted;
//				$payjp_recursion_amount = $payjp_recursion_obj->plan[ 'amount' ];
//				$payjp_recursion_period = ( $payjp_recursion_obj->plan[ 'interval' ] == 'month' ? '月単位' : $payjp_recursion_obj->plan[ interval ] == 'year' ? '年単位' : '' );
				$payjp_recursion_trial_days = $payjp_recursion_obj->plan[ 'trial_days' ];
				$payjp_recursion_last_executed = date( 'Y-m-d', $payjp_recursion_obj->current_period_start );
				$payjp_recursion_next_scheduled = date( 'Y-m-d', $payjp_recursion_obj->current_period_end );
				$payjp_recursion_canceld_at = date( 'Y-m-d', $payjp_recursion_obj->canceled_at );
				$payjp_recursion_status = $payjp_recursion_obj->status;
				$payjp_cus_key = $payjp_recursion_obj->customer;
				$payjp_recursion_paused_at = date( 'Y-m-d', $payjp_recursion_obj->paused_at );
			} else {
				$payjp_recursion_obj = (object) array( "deleted" => TRUE );
			}
			echo "<hr>";
			$payjp_admin_recursions = "https://pay.jp/dashboard/subscriptions/";

			$payjp_admin_recursions .= $payjp_teiki_id;
			?>
            <script>
                function goto_payjp_recursion_info() {
                    var newWindow = window.open('payjp_WIN');
                    newWindow.location.href = '<?php echo $payjp_admin_recursions; ?>';
                    return false;
                }
            </script>
			<?php
			if ( $payjp_cus_key != '' ) {
				$payjp_admin_customers = "https://pay.jp/dashboard/customers/";

				$payjp_admin_customers .= $payjp_cus_key;
				?>
                <script>
                    function goto_payjp_customer_info() {
                        var newWindow = window.open('payjp_WIN');
                        newWindow.location.href = '<?php echo $payjp_admin_customers; ?>';
                        return false;
                    }
                </script>
				<?php
			}
			?>
            <script>
                function this_payjp_recursion_delete() {
                    if (confirm('定期課金を停止し定期課金オブジェクトを削除します。一度削除すると新規作成しないと復活できません。よろしいですか？')) {
                        if (confirm('もう一度、削除対象の受注かどうかお確かめください。本当によろしいですか？')) {
                            jQuery.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: {
                                    'action': 'payjp_recursion_delete',
                                    'payjp_teiki_id': '<?php echo $payjp_teiki_id; ?>'
                                },
                                success: function (response) {
                                    response = response.substr(0, response.length - 1);
                                    alert(response);
                                    return true;
                                }
                            });
                            return false;
                        }
                    }
                    return false;
                }

                function this_payjp_recursion_pause() {
                    if (confirm('該当の定期課金を停止状態にします。よろしいですか？')) {
                        jQuery.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: {
                                'action': 'payjp_recursion_pause',
                                'payjp_teiki_id': '<?php echo $payjp_teiki_id; ?>'
                            },
                            success: function (response) {
                                response = response.substr(0, response.length - 1);
                                alert(response);
                                return true;
                            }
                        });
                        return false;
                    }
                    return false;
                }

                function this_payjp_recursion_restart() {
                    if (confirm('該当の定期課金を再開します。よろしいですか？')) {
                        jQuery.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: {
                                'action': 'payjp_recursion_restart',
                                'payjp_teiki_id': '<?php echo $payjp_teiki_id; ?>'
                            },
                            success: function (response) {
                                response = response.substr(0, response.length - 1);
                                alert(response);
                                return true;
                            }
                        });
                        return false;
                    }
                    return false;
                }
            </script>
            <tr>
                <td class="label">PAY.JP<br/>定期課金ID</td>
                <td><?php echo $payjp_teiki_id; ?></td>
				<?php
				if ( $payjp_recursion_status == 'active' || $payjp_recursion_status == 'trial' ) {
					?>
                    <td>
                        status:<?php echo $payjp_recursion_status; ?><?php if ( $payjp_recursion_status == 'trial' ) { ?>
                            <br>トライアル日数:<?php echo $payjp_recursion_trial_days;
						} ?></td>
                    <td><?php if ( $payjp_recursion_status == 'trial' ) { ?>トライアル開始<?php } else { ?>購読開始<?php } ?>
                        ：<?php echo $payjp_recursion_last_executed; ?>
                        <br><?php if ( $payjp_recursion_status == 'trial' ) { ?>トライアル終了<?php } else { ?>購読終了<?php } ?>
                        ：<?php echo $payjp_recursion_next_scheduled; ?></td>
                    <td>
                        <button id="payjp_recursion_info" onclick="return goto_payjp_recursion_info();">定期課金情報表示
                        </button>
                    </td>
                    <td>
                        <button id="payjp_customer_info" onclick="return goto_payjp_customer_info();">顧客の詳細情報表示</button>
                        <br>
                        <button id="payjp_recursion_delete" onclick="this_payjp_recursion_pause();">定期課金 一時停止処理</button>&nbsp;&nbsp;
                        <button id="payjp_recursion_delete" onclick="this_payjp_recursion_delete();">定期課金 キャンセル処理
                        </button>
                    </td>
					<?php
				} elseif ( $payjp_recursion_status == 'paused' ) {
					?>
                    <td>status:<?php echo $payjp_recursion_status; ?></td>
                    <td>定期課金 停止中<br>停止日：<?php echo $payjp_recursion_paused_at; ?></td>
                    <td>
                        <button id="payjp_recursion_info" onclick="return goto_payjp_recursion_info();">定期課金情報表示
                        </button>
                    </td>
                    <td>
                        <button id="payjp_customer_info" onclick="return goto_payjp_customer_info();">顧客の詳細情報表示</button>
                        <br>
                        <button id="payjp_recursion_restart" onclick="this_payjp_recursion_restart();">定期課金 再開</button>&nbsp;&nbsp;
                        <button id="payjp_recursion_delete" onclick="this_payjp_recursion_delete();">定期課金 キャンセル</button>
                    </td>
					<?php
				} else {
					echo "<td>定期課金 削除済<br>削除日:" . $payjp_recursion_canceld_at . "</td>";
				}
				?>
            </tr>
			<?php
		}
	}

	/**
	 * 管理画面から定期課金キャンセル処理リクエストがあった場合の挙動
	 */
	function payjp_recursion_delete()
	{
		try {
			$payjp_teiki_id = esc_attr( $_POST[ 'payjp_teiki_id' ] );

			$payjp_result = $this->payjp_recursion_delete_by_id( $payjp_teiki_id );
			echo '削除完了しました。ステータスの変更を確認するにはこの受注データを再表示してください。';
		} catch ( Exception $e ) {
			echo '削除失敗しました。MSG:' . $e->getMessage();
		}
	}

	/**
	 * 管理画面から定期課金停止処理リクエストがあった場合の挙動
	 */
	function payjp_recursion_pause()
	{
		try {
			$payjp_teiki_id = esc_attr( $_POST[ 'payjp_teiki_id' ] );

			$payjp_result = $this->payjp_recursion_pause_by_id( $payjp_teiki_id );
			echo '該当の定期課金を停止状態にしました。ステータスの変更を確認するにはこの受注データを再表示してください。';
		} catch ( Exception $e ) {
			echo '停止処理が失敗しました。MSG:' . $e->getMessage();
		}
	}

	/**
	 * 管理画面から定期課金再開処理リクエストがあった場合の挙動
	 */
	function payjp_recursion_restart()
	{
		try {
			$payjp_teiki_id = esc_attr( $_POST[ 'payjp_teiki_id' ] );

			$payjp_result = $this->payjp_recursion_restart_by_id( $payjp_teiki_id );
			echo '該当の定期課金を再開しました。ステータスの変更を確認するにはこの受注データを再表示してください。';
		} catch ( Exception $e ) {
			echo '停止処理が失敗しました。MSG:' . $e->getMessage();
		}
	}

	/**
	 * 定期課金キャンセル処理
	 */
	function payjp_recursion_delete_by_id( $teiki_id )
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
	 * 定期課金停止処理
	 */
	function payjp_recursion_pause_by_id( $teiki_id )
	{
		try {
			$secret_key = get_option( 'payjp_secret_key' );
			\Payjp\Payjp::setApiKey( $secret_key );
			$su = \Payjp\Subscription::retrieve( $teiki_id );
			$result = $su->pause();
			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * 定期課金再開処理
	 */
	function payjp_recursion_restart_by_id( $teiki_id )
	{
		try {
			$secret_key = get_option( 'payjp_secret_key' );
			\Payjp\Payjp::setApiKey( $secret_key );
			$su = \Payjp\Subscription::retrieve( $teiki_id );
			$result = $su->resume();
			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}
	}
}

if ( is_admin() ) {
	$showtext = new PayjpOnWelcartSetting;

	function payjp_admin_add_my_ajaxurl()
	{
		?>
        <script>
            var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
        </script>
		<?php
	}

	add_action( 'wp_head', 'payjp_admin_add_my_ajaxurl', 1 );
}