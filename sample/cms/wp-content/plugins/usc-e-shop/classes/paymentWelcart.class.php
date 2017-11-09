<?php
/*
WelcartPay based on e-SCOTT
Version: 1.0.0
Author: Collne Inc.
*/

class WELCARTPAY_SETTLEMENT extends ESCOTT_MAIN
{
	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	protected $key_aes;
	protected $key_iv;
	protected $continuation_charging_mail;

	public function __construct() {

		$this->acting_name = 'WelcartPay';
		$this->acting_formal_name = 'WelcartPay based on e-SCOTT';

		$this->acting_card = 'welcart_card';
		$this->acting_conv = 'welcart_conv';
		$this->acting_atodene = 'welcart_atodene';

		$this->acting_flg_card = 'acting_welcart_card';
		$this->acting_flg_conv = 'acting_welcart_conv';
		$this->acting_flg_atodene = 'acting_welcart_atodene';

		$this->pay_method = array(
			'acting_welcart_card',
			'acting_welcart_conv',
		);
		$this->unavailable_method = array(
			'acting_zeus_card',
			'acting_zeus_conv',
			'acting_escott_card',
			'acting_escott_conv'
		);
		$this->merchantfree3 = 'wc2collne';
		$this->quick_key_pre = 'wcpay';

		$this->key_aes = 'HgmhZ94rN799CD3F';
		$this->key_iv = 'gNqc4zwhNLCSC5cv';

		parent::__construct( 'welcart' );

		if( is_admin() ) {
			if( defined('WCEX_AUTO_DELIVERY') ) {
				add_filter( 'wcad_filter_admin_notices', array( $this, 'admin_notices_autodelivery' ) );
			}
		}

		if( $this->is_activate_card() || $this->is_activate_conv() ) {
			if( is_admin() ) {
				add_action( 'usces_action_admin_ajax', array( $this, 'admin_ajax' ) );
				add_filter( 'usces_filter_orderlist_detail_value', array( $this, 'orderlist_settlement_status' ), 10, 4 );
				add_action( 'usces_action_order_edit_form_status_block_middle', array( $this, 'settlement_status' ), 10, 3 );
				add_action( 'usces_action_order_edit_form_settle_info', array( $this, 'settlement_information' ), 10, 2 );
				add_action( 'usces_action_endof_order_edit_form', array( $this, 'settlement_dialog' ), 10, 2 );
			}
		}

		if( $this->is_validity_acting('card') ) {
			add_filter( 'usces_fiter_the_payment_method_explanation', array( $this, 'set_payment_method_explanation' ), 10, 3 );
			add_filter( 'usces_filter_available_payment_method', array( $this, 'set_available_payment_method' ) );
			add_filter( 'usces_filter_delivery_secure_form_howpay', array( $this, 'delivery_secure_form_howpay' ) );
			add_filter( 'usces_filter_template_redirect', array( $this, 'member_update_settlement' ), 1 );
			add_action( 'usces_action_member_submenu_list', array( $this, 'e_update_settlement' ) );
			add_filter( 'usces_filter_member_submenu_list', array( $this, 'update_settlement' ), 10, 2 );

			//*** WCEX DL Seller ***
			if( defined('WCEX_DLSELLER') ) {
				if( defined('WCEX_DLSELLER_VERSION') and version_compare( WCEX_DLSELLER_VERSION, '2.2-beta', '<=' ) ) {
					add_filter( 'usces_filter_the_continue_payment_method', array( $this, 'continuation_payment_method' ) );
				}
				add_filter( 'dlseller_filter_first_charging', array( $this, 'first_charging_date' ), 9, 5 );
				add_filter( 'dlseller_filter_the_payment_method_restriction', array( $this, 'payment_method_restriction' ), 10, 2 );
				add_filter( 'dlseller_filter_continue_member_list_limitofcard', array( $this, 'continue_member_list_limitofcard' ), 10, 4 );
				//add_filter( 'dlseller_filter_continue_member_list_continue_status', array( $this, 'continue_member_list_continue_status' ), 10, 4 );
				add_filter( 'dlseller_filter_continue_member_list_condition', array( $this, 'continue_member_list_condition' ), 10, 4 );
				add_action( 'dlseller_action_continue_member_list_page', array( $this, 'continue_member_list_page' ) );
				add_filter( 'dlseller_filter_card_update_mail', array( $this, 'continue_member_card_update_mail' ), 10, 3 );
				add_action( 'dlseller_action_do_continuation_charging', array( $this, 'auto_continuation_charging' ), 10, 4 );
				add_action( 'dlseller_action_do_continuation', array( $this, 'do_auto_continuation' ), 10, 2 );
			}

			//*** WCEX Auto Delivery ***
			if( defined('WCEX_AUTO_DELIVERY') ) {
				add_filter( 'wcad_filter_shippinglist_acting', array( $this, 'set_shippinglist_acting' ) );
				add_filter( 'wcad_filter_available_regular_payment_method', array( $this, 'available_regular_payment_method' ) );
				add_filter( 'wcad_filter_the_payment_method_restriction', array( $this, 'payment_method_restriction' ), 10, 2 );
				add_action( 'wcad_action_reg_auto_orderdata', array( $this, 'register_auto_orderdata' ) );
			}
		}

		if( $this->is_validity_acting('atodene') ) {
			if( is_admin() ) {
				add_action( 'usces_after_cart_instant', array( $this, 'atodene_upload' ), 9 );
				add_action( 'usces_action_order_list_page', array( $this, 'output_atodene_csv' ) );
				add_action( 'usces_action_order_list_searchbox_bottom', array( $this, 'action_atodene_button' ) );
				add_action( 'usces_action_order_list_footer', array( $this, 'order_list_footer' ) );
				add_filter( 'usces_filter_order_list_page_js', array( $this, 'order_list_page_js' ) );
				add_filter( 'usces_order_list_action_status', array( $this, 'order_list_action_status' ) );
				add_filter( 'usces_order_list_action_message', array( $this, 'order_list_action_message' ) );

				$acting_opts = $this->get_acting_settings();
				if( isset($acting_opts['atodene_byitem']) && 'on' == $acting_opts['atodene_byitem'] ) {
					add_filter( 'usces_item_master_second_section', array( $this, 'edit_item_atodene_byitem' ), 10, 2 );
					add_action( 'usces_action_save_product', array( $this, 'save_item_atodene_byitem' ), 10, 2 );
				}
			} else {
				add_filter( 'usces_filter_nonacting_settlements', array( $this, 'nonacting_settlements' ) );
			}

			//*** WCEX Auto Delivery ***
			if( defined('WCEX_AUTO_DELIVERY') ) {
				add_filter( 'wcad_filter_the_payment_method_restriction', array( $this, 'payment_method_restriction_atodene' ), 11, 2 );
			}
		}

		$this->initialize_data();
	}

	/**
	 * Return an instance of this class.
	 */
	public static function get_instance() {
		if( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**********************************************
	* Initialize
	***********************************************/
	public function initialize_data() {

		$options = get_option( 'usces' );
		if( !in_array( 'welcart', $options['acting_settings'] ) ) {
			$options['acting_settings']['welcart']['merchant_id'] = ( isset($options['acting_settings']['welcart']['merchant_id']) ) ? $options['acting_settings']['welcart']['merchant_id'] : '';
			$options['acting_settings']['welcart']['merchant_pass'] = ( isset($options['acting_settings']['welcart']['merchant_pass']) ) ? $options['acting_settings']['welcart']['merchant_pass'] : '';
			$options['acting_settings']['welcart']['tenant_id'] = ( isset($options['acting_settings']['welcart']['tenant_id']) ) ? $options['acting_settings']['welcart']['tenant_id'] : '0001';
			$options['acting_settings']['welcart']['auth_key'] = ( isset($options['acting_settings']['welcart']['auth_key']) ) ? $options['acting_settings']['welcart']['auth_key'] : '';
			$options['acting_settings']['welcart']['ope'] = ( isset($options['acting_settings']['welcart']['ope']) ) ? $options['acting_settings']['welcart']['ope'] : 'test';
			$options['acting_settings']['welcart']['card_activate'] = ( isset($options['acting_settings']['welcart']['card_activate']) ) ? $options['acting_settings']['welcart']['card_activate'] : 'off';
			$options['acting_settings']['welcart']['foreign_activate'] = ( isset($options['acting_settings']['welcart']['foreign_activate']) ) ? $options['acting_settings']['welcart']['foreign_activate'] : 'off';
			$options['acting_settings']['welcart']['seccd'] = ( isset($options['acting_settings']['welcart']['seccd']) ) ? $options['acting_settings']['welcart']['seccd'] : 'on';
			$options['acting_settings']['welcart']['token_code'] = ( isset($options['acting_settings']['welcart']['token_code']) ) ? $options['acting_settings']['welcart']['token_code'] : '';
			$options['acting_settings']['welcart']['quickpay'] = ( isset($options['acting_settings']['welcart']['quickpay']) ) ? $options['acting_settings']['welcart']['quickpay'] : 'off';
			$options['acting_settings']['welcart']['operateid'] = ( isset($options['acting_settings']['welcart']['operateid']) ) ? $options['acting_settings']['welcart']['operateid'] : '1Auth';
			$options['acting_settings']['welcart']['operateid_dlseller'] = ( isset($options['acting_settings']['welcart']['operateid_dlseller']) ) ? $options['acting_settings']['welcart']['operateid_dlseller'] : '1Gathering';
			$options['acting_settings']['welcart']['auto_settlement_mail'] = ( isset($options['acting_settings']['welcart']['auto_settlement_mail']) ) ? $options['acting_settings']['welcart']['auto_settlement_mail'] : 'on';
			$options['acting_settings']['welcart']['howtopay'] = ( isset($options['acting_settings']['welcart']['howtopay']) ) ? $options['acting_settings']['welcart']['howtopay'] : '1';
			$options['acting_settings']['welcart']['conv_activate'] = ( isset($options['acting_settings']['welcart']['conv_activate']) ) ? $options['acting_settings']['welcart']['conv_activate'] : 'off';
			$options['acting_settings']['welcart']['conv_limit'] = ( !empty($options['acting_settings']['welcart']['conv_limit']) ) ? $options['acting_settings']['welcart']['conv_limit'] : '7';
			$options['acting_settings']['welcart']['conv_fee_type'] = ( isset($options['acting_settings']['welcart']['conv_fee_type']) ) ? $options['acting_settings']['welcart']['conv_fee_type'] : '';
			$options['acting_settings']['welcart']['conv_fee'] = ( isset($options['acting_settings']['welcart']['conv_fee']) ) ? $options['acting_settings']['welcart']['conv_fee'] : '';
			$options['acting_settings']['welcart']['conv_fee_limit_amount'] = ( isset($options['acting_settings']['welcart']['conv_fee_limit_amount']) ) ? $options['acting_settings']['welcart']['conv_fee_limit_amount'] : '';
			$options['acting_settings']['welcart']['conv_fee_first_amount'] = ( isset($options['acting_settings']['welcart']['conv_fee_first_amount']) ) ? $options['acting_settings']['welcart']['conv_fee_first_amount'] : '';
			$options['acting_settings']['welcart']['conv_fee_first_fee'] = ( isset($options['acting_settings']['welcart']['conv_fee_first_fee']) ) ? $options['acting_settings']['welcart']['conv_fee_first_fee'] : '';
			$options['acting_settings']['welcart']['conv_fee_amounts'] = ( isset($options['acting_settings']['welcart']['conv_fee_amounts']) ) ? $options['acting_settings']['welcart']['conv_fee_amounts'] : array();
			$options['acting_settings']['welcart']['conv_fee_fees'] = ( isset($options['acting_settings']['welcart']['conv_fee_fees']) ) ? $options['acting_settings']['welcart']['conv_fee_fees'] : array();
			$options['acting_settings']['welcart']['conv_fee_end_fee'] = ( isset($options['acting_settings']['welcart']['conv_fee_end_fee']) ) ? $options['acting_settings']['welcart']['conv_fee_end_fee'] : '';
			$options['acting_settings']['welcart']['atodene_activate'] = ( isset($options['acting_settings']['welcart']['atodene_activate']) ) ? $options['acting_settings']['welcart']['atodene_activate'] : 'off';
			$options['acting_settings']['welcart']['atodene_byitem'] = ( isset($options['acting_settings']['welcart']['atodene_byitem']) ) ? $options['acting_settings']['welcart']['atodene_byitem'] : 'off';
			$options['acting_settings']['welcart']['atodene_billing_method'] = ( isset($options['acting_settings']['welcart']['atodene_billing_method']) ) ? $options['acting_settings']['welcart']['atodene_billing_method'] : '2';
			$options['acting_settings']['welcart']['atodene_fee_type'] = ( isset($options['acting_settings']['welcart']['atodene_fee_type']) ) ? $options['acting_settings']['welcart']['atodene_fee_type'] : '';
			$options['acting_settings']['welcart']['atodene_fee'] = ( isset($options['acting_settings']['welcart']['atodene_fee']) ) ? $options['acting_settings']['welcart']['atodene_fee'] : '';
			$options['acting_settings']['welcart']['atodene_fee_limit_amount'] = ( isset($options['acting_settings']['welcart']['atodene_fee_limit_amount']) ) ? $options['acting_settings']['welcart']['atodene_fee_limit_amount'] : '';
			$options['acting_settings']['welcart']['atodene_fee_first_amount'] = ( isset($options['acting_settings']['welcart']['atodene_fee_first_amount']) ) ? $options['acting_settings']['welcart']['atodene_fee_first_amount'] : '';
			$options['acting_settings']['welcart']['atodene_fee_first_fee'] = ( isset($options['acting_settings']['welcart']['atodene_fee_first_fee']) ) ? $options['acting_settings']['welcart']['atodene_fee_first_fee'] : '';
			$options['acting_settings']['welcart']['atodene_fee_amounts'] = ( isset($options['acting_settings']['welcart']['atodene_fee_amounts']) ) ? $options['acting_settings']['welcart']['atodene_fee_amounts'] : array();
			$options['acting_settings']['welcart']['atodene_fee_fees'] = ( isset($options['acting_settings']['welcart']['atodene_fee_fees']) ) ? $options['acting_settings']['welcart']['atodene_fee_fees'] : array();
			$options['acting_settings']['welcart']['atodene_fee_end_fee'] = ( isset($options['acting_settings']['welcart']['atodene_fee_end_fee']) ) ? $options['acting_settings']['welcart']['atodene_fee_end_fee'] : '';
			$options['acting_settings']['welcart']['activate'] = ( isset($options['acting_settings']['welcart']['activate']) ) ? $options['acting_settings']['welcart']['activate'] : 'off';
			update_option( 'usces', $options );
		}

		$welcartpay_keys = get_option( 'usces_welcartpay_keys' );
		if( empty( $welcartpay_keys ) ) {
			$welcartpay_keys = array(
				'c0778c9aefe850d5ac8efed5d62ed281',
				'd0771e4b42ef683223df03f9558c23fd',
				'dfef8e46f7231e7e8271f906582a4e1d',
				'ad6dbb5e26cc9db1fe5d876a75764559',
				'4fc1738fffa5aa33792ddf8e5c183f72',
				'd255b1cb2c4d20959e3c80e457e5274c',
				'479ffcfe47db920e972a8c7932e581d9',
				'43c7f4782379b05cf69bbbfb547e3312',
				'524047b0e0ad64d4f7b42c14c77758e2',
				'b848aed9c05cbf2c85d2889b274c18ec'
			);
			update_option( 'usces_welcartpay_keys', $welcartpay_keys );
		}

		$available_settlement = get_option( 'usces_available_settlement' );
		if( !in_array( 'welcart', $available_settlement ) ) {
			$settlement = array( 'welcart'=>__('WelcartPay','usces') );
			$available_settlement = array_merge( $settlement, $available_settlement );
			update_option( 'usces_available_settlement', $available_settlement );
		}

		$noreceipt_status = get_option( 'usces_noreceipt_status' );
		if( !in_array( 'acting_welcart_conv', $noreceipt_status ) || !in_array( 'acting_welcart_atodene', $noreceipt_status ) ) {
			$noreceipt_status[] = 'acting_welcart_conv';
			$noreceipt_status[] = 'acting_welcart_atodene';
			update_option( 'usces_noreceipt_status', $noreceipt_status );
		}
	}

	/**********************************************
	* admin_print_footer_scripts
	* JavaScript
	* @param  -
	* @return -
	* @echo   js
	***********************************************/
	public function admin_scripts() {
		global $usces;

		$admin_page = ( isset($_GET['page']) ) ? $_GET['page'] : '';
		switch( $admin_page ):
		case 'usces_settlement':
			$settlement_selected = get_option( 'usces_settlement_selected' );
			if( in_array( 'welcart', (array)$settlement_selected ) ):
				$acting_opts = $this->get_acting_settings();
?>
<script type="text/javascript">
jQuery(document).ready( function($) {
	var card_activate = "<?php echo $acting_opts['card_activate']; ?>";
	var conv_activate = "<?php echo $acting_opts['conv_activate']; ?>";
	var atodene_activate = "<?php echo $acting_opts['atodene_activate']; ?>";
	if( "on" == card_activate || "token" == card_activate ) {
		$(".card_welcart").css("display", "");
		$(".card_token_code_welcart").css("display", "");
		$(".card_howtopay_welcart").css("display", "");
	} else if( "link" == card_activate ) {
		$(".card_welcart").css("display", "");
		$(".card_token_code_welcart").css("display", "none");
		$(".card_howtopay_welcart").css("display", "none");
	} else {
		$(".card_welcart").css("display", "none");
		$(".card_token_code_welcart").css("display", "none");
		$(".card_howtopay_welcart").css("display", "none");
	}
	if( "on" == conv_activate ) {
		$(".conv_welcart").css("display", "");
	} else {
		$(".conv_welcart").css("display", "none");
	}
	if( "on" == atodene_activate ) {
		$(".atodene_welcart").css("display", "");
	} else {
		$(".atodene_welcart").css("display", "none");
	}
	$(document).on( "change", ".card_activate_welcart", function() {
		if( "on" == $( this ).val() || "token" == $( this ).val() ) {
			$(".card_welcart").css("display", "");
			$(".card_token_code_welcart").css("display", "");
			$(".card_howtopay_welcart").css("display", "");
		} else if( "link" == $( this ).val() ) {
			$(".card_welcart").css("display", "");
			$(".card_token_code_welcart").css("display", "none");
			$(".card_howtopay_welcart").css("display", "none");
		} else {
			$(".card_welcart").css("display", "none");
			$(".card_token_code_welcart").css("display", "none");
			$(".card_howtopay_welcart").css("display", "none");
		}
	});
	$(document).on( "change", ".conv_activate_welcart", function() {
		if( "on" == $( this ).val() ) {
			$(".conv_welcart").css("display", "");
		} else {
			$(".conv_welcart").css("display", "none");
		}
	});
	$(document).on( "change", ".atodene_activate_welcart", function() {
		if( "on" == $( this ).val() ) {
			$(".atodene_welcart").css("display", "");
		} else {
			$(".atodene_welcart").css("display", "none");
		}
	});

	adminSettlementWelcartPay = {
		openFee : function( mode ) {
			$("#fee_change_field").html("");
			$("#fee_fix").val( $("#"+mode+"_fee").val() );
			$("#fee_limit_amount_fix").val( $("#"+mode+"_fee_limit_amount_fix").val() );
			$("#fee_first_amount").val( $("#"+mode+"_fee_first_amount").val() );
			$("#fee_first_fee").val( $("#"+mode+"_fee_first_fee").val() );
			$("#fee_limit_amount_change").val( $("#"+mode+"_fee_limit_amount_change").val() );
			var fee_amounts = new Array();
			var fee_fees = new Array();
			if( 0 < $("#"+mode+"_fee_amounts").val().length ) {
				fee_amounts = $("#"+mode+"_fee_amounts").val().split("|");
			}
			if( 0 < $("#"+mode+"_fee_fees").val().length ) {
				fee_fees = $("#"+mode+"_fee_fees").val().split("|");
			}
			if( 0 < fee_amounts.length ) {
				var amount = parseInt($("#fee_first_amount").val()) + 1;
				for( var i = 0; i < fee_amounts.length; i++ ) {
					html = '<tr id="row_'+i+'"><td class="cod_f"><span id="amount_'+i+'">'+amount+'</span></td><td class="cod_m"><?php _e(' - ','usces'); ?></td><td class="cod_e"><input name="fee_amounts['+i+']" type="text" class="short_str num" value="'+fee_amounts[i]+'" /></td><td class="cod_cod"><input name="fee_fees['+i+']" type="text" class="short_str num" value="'+fee_fees[i]+'" /></td></tr>';
					$("#fee_change_field").append(html);
					amount = parseInt(fee_amounts[i]) + 1;
				}
				$("#end_amount").html( amount );
			} else {
				$("#end_amount").html( parseInt($("#"+mode+"_fee_first_amount").val()) + 1 );
			}
			$("#fee_end_fee").val( $("#"+mode+"_fee_end_fee").val() );

			var fee_type = $("#"+mode+"_fee_type").val();
			if( "change" == fee_type ) {
				$("#fee_type_change").prop("checked", true);
				$("#welcartpay_fee_fix_table").css("display","none");
				$("#welcartpay_fee_change_table").css("display","");
			} else {
				$("#fee_type_fix").prop("checked", true);
				$("#welcartpay_fee_fix_table").css("display","");
				$("#welcartpay_fee_change_table").css("display","none");
			}
		},

		updateFee : function( mode ) {
			var fee_type = $("input[name='fee_type']:checked").val();
			$("#"+mode+"_fee_type").val( fee_type );
			$("#"+mode+"_fee").val( $("#fee_fix").val() );
			$("#"+mode+"_fee_limit_amount").val( $("#fee_limit_amount_"+fee_type).val() );
			$("#"+mode+"_fee_first_amount").val( $("#fee_first_amount").val() );
			$("#"+mode+"_fee_first_fee").val( $("#fee_first_fee").val() );
			var fee_amounts = "";
			var fee_fees = "";
			var sp = "";
			var fee_amounts_length = $("input[name^='fee_amounts']").length;
			for( var i = 0; i < fee_amounts_length; i++ ) {
				fee_amounts += sp + $("input[name='fee_amounts\["+i+"\]']").val();
				fee_fees += sp + $("input[name='fee_fees\["+i+"\]']").val();
				sp = "|";
			}
			$("#"+mode+"_fee_amounts").val( fee_amounts );
			$("#"+mode+"_fee_fees").val( fee_fees );
			$("#"+mode+"_fee_end_fee").val( $("#fee_end_fee").val() );
		},

		setFeeType : function( mode, closed ) {
			var fee_type = $("input[name='fee_type']:checked").val();
			if( "change" == fee_type ) {
				$("#"+mode+"_fee_type_field").html("<?php _e('Variable','usces'); ?>");
				if( !closed ) {
					$("#welcartpay_fee_fix_table").css("display","none");
					$("#welcartpay_fee_change_table").css("display","");
				}
			} else if( "fix" == fee_type ) {
				$("#"+mode+"_fee_type_field").html("<?php _e('Fixation','usces'); ?>");
				if( !closed ) {
					$("#welcartpay_fee_fix_table").css("display","");
					$("#welcartpay_fee_change_table").css("display","none");
				}
			}
		}
	};

	$("#welcartpay_fee_dialog").dialog({
		autoOpen: false,
		height: 500,
		width: 450,
		modal: true,
		open: function() {
			adminSettlementWelcartPay.openFee( $("#welcartpay_fee_mode").val() );
		},
		buttons: {
			"<?php _e('Settings'); ?>": function() {
				adminSettlementWelcartPay.updateFee( $("#welcartpay_fee_mode").val() );
			},
			"<?php _e('Close'); ?>": function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			adminSettlementWelcartPay.setFeeType( $("#welcartpay_fee_mode").val(), true );
		}
	});

	$(document).on("click", "#conv_fee_setting", function() {
		$("#welcartpay_fee_mode").val( "conv" );
		$("#welcartpay_fee_dialog").dialog( "option", "title", "<?php _e('Online storage agency settlement fee setting','usces'); ?>" );
		$("#welcartpay_fee_dialog").dialog( "open" );
	});

	$(document).on("click", "#atodene_fee_setting", function() {
		$("#welcartpay_fee_mode").val( "atodene" );
		$("#welcartpay_fee_dialog").dialog( "option", "title", "<?php _e('Postpay settlement fee setting','usces'); ?>" );
		$("#welcartpay_fee_dialog").dialog( "open" );
	});

	$(document).on("click", ".fee_type", function() {
		if( "change" == $(this).val() ) {
			$("#welcartpay_fee_fix_table").css("display","none");
			$("#welcartpay_fee_change_table").css("display","");
		} else {
			$("#welcartpay_fee_fix_table").css("display","");
			$("#welcartpay_fee_change_table").css("display","none");
		}
	});

	$(document).on("change", "input[name='fee_first_amount']", function() {
		var rows = $("input[name^='fee_amounts']");
		var first_amount = $("input[name='fee_first_amount']");
		if( 0 == rows.length && $(first_amount).val() != '' ) {
			$("#end_amount").html( parseInt($(first_amount).val()) + 1 );
		} else if( 0 < rows.length && $(first_amount).val() != '' ) {
			$('#amount_0').html( parseInt($(first_amount).val()) + 1 );
		}
	});

	$(document).on("change", "#fee_limit_amount_change", function() {
		if( "change" == $("input[name='fee_type']:checked").val() ) {
			var amount = parseInt($("#end_amount").html());
			var limit = parseInt($("#fee_limit_amount_change").val());
			if( amount >= limit ) {
				alert("<?php _e('A value of the amount of upper limit is dirty.', 'usces'); ?>"+amount+' : '+limit );
			}
		}
	});

	$(document).on("change", "input[name^='fee_amounts']", function() {
		var rows = $("input[name^='fee_amounts']");
		var cnt = $(rows).length;
		var end_amount = $("#end_amount");
		var id = $(rows).index(this);
		if( id >= cnt - 1 ) {
			$(end_amount).html( parseInt($(rows).eq(id).val()) + 1 );
		} else if( id < cnt - 1 ) {
			$("#amount_"+(id+1)).html( parseInt($(rows).eq(id).val()) + 1 );
		}
	});

	$(document).on("click", "#fee_add_row", function() {
		var rows = $("input[name^='fee_amounts']");
		$(rows).unbind("change");
		var first_amount = $("input[name='fee_first_amount']");
		var first_fee = $("input[name='fee_first_fee']");
		var end_amount = $("#end_amount");
		var enf_fee = $("input[name='fee_end_fee']");
		if( 0 == rows.length ) {
			amount = ( $(first_amount).val() == '' ) ? '' : parseInt( $(first_amount).val() ) + 1;
		} else if( 0 < rows.length ) {
			amount = ( $(rows).eq(rows.length - 1).val() == '' ) ? '' : parseInt( $(rows).eq(rows.length-1).val() ) + 1;
		}
		html = '<tr id="row_'+rows.length+'"><td class="cod_f"><span id="amount_'+rows.length+'">'+amount+'</span></td><td class="cod_m"><?php _e(' - ','usces'); ?></td><td class="cod_e"><input name="fee_amounts['+rows.length+']" type="text" class="short_str num" /></td><td class="cod_cod"><input name="fee_fees['+rows.length+']" type="text" class="short_str num" /></td></tr>';
		$("#fee_change_field").append(html);
		rows = $("input[name^='fee_amounts']");
		$(rows).bind("change", function() {
			var cnt = $(rows).length - 1;
			var id = $(rows).index(this);
			if( id >= cnt ) {
				$(end_amount).html( parseInt($(rows).eq(id).val()) + 1 );
			} else if( id < cnt ) {
				$("#amount_"+(id+1)).html( parseInt($(rows).eq(id).val()) + 1 );
			}
		});
	});

	$(document).on("click", "#fee_del_row", function() {
		var rows = $("input[name^='fee_amounts']");
		//$(rows).unbind("change");
		var first_amount = $("input[name='fee_first_amount']");
		var end_amount = $("#end_amount");
		var del_id = rows.length - 1;
		if( 0 < rows.length ) {
			$("#row_"+del_id).remove();
		}
		rows = $("input[name^='fee_amounts']");
		if( 0 == rows.length && $(first_amount).val() != "" ) {
			$(end_amount).html( parseInt($(first_amount).val()) + 1 );
		} else if( 0 < rows.length && $(rows).eq(rows.length-1).val() != "" ) {
			$(end_amount).html( parseInt($(rows).eq(rows.length-1).val()) + 1 );
		}
		//$(rows).bind("change", function() {
		//	var cnt = $(rows).length - 1;
		//	var id = $(rows).index(this);
		///	if( id >= cnt && $(rows).eq(id).val() != "" ) {
		//		$(end_amount).html( parseInt($(rows).eq(id).val()) + 1 );
		//	} else if( id < cnt && $(rows).eq(id).val() != "" ) {
		//		$("#amount_"+(id+1)).html( parseInt($(rows).eq(id).val()) + 1 );
		//	}
		//});
	});

	adminSettlementWelcartPay.setFeeType( "conv", false );
	adminSettlementWelcartPay.setFeeType( "atodene", false );
});
</script>
<?php
			endif;
			break;

		case 'usces_orderlist':
		case 'usces_continue':
			$acting_flg = '';
			$dialog_title = '';

			//受注編集画面・継続課金会員詳細画面
			if( 'usces_orderlist' == $admin_page && ( isset($_GET['order_action']) && ( 'edit' == $_GET['order_action'] || 'editpost' == $_GET['order_action'] || 'newpost' == $_GET['order_action'] ) ) || 
				'usces_continue' == $admin_page && ( isset($_GET['continue_action']) && 'settlement' == $_GET['continue_action'] ) ) {
				$order_id = ( isset($_GET['order_id']) ) ? $_GET['order_id'] : '';
				if( empty($order_id) && isset($_POST['order_id']) ) $order_id = $_POST['order_id'];
				if( empty($order_id) && isset($_REQUEST['order_id']) ) $order_id = $_REQUEST['order_id'];
				if( !empty($order_id) ) {
					$order_data = $usces->get_order_data( $order_id, 'direct' );
					$payment = usces_get_payments_by_name( $order_data['order_payment_name'] );
					if( isset($payment['settlement']) ) {
						$acting_flg = $payment['settlement'];
					}
					if( isset($payment['name']) ) {
						$dialog_title = $payment['name'];
					}
				}
			}
			if( in_array( $acting_flg, $this->pay_method ) ):
?>
<script type="text/javascript">
jQuery(document).ready( function($) {
	adminOrderEdit = {
			<?php if( 'acting_welcart_card' == $acting_flg ): ?>
		getSettlementInfoCard : function() {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			var mode = ( "" != $("#error").val() ) ? "error_welcartpay_card" : "get_welcartpay_card";

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: mode,
					order_id: $("#order_id").val(),
					order_num: $("#order_num").val(),
					trans_id: $("#trans_id").val(),
					member_id: $("#member_id").val(),
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		},

		captureSettlementCard : function( amount ) {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: "capture_welcartpay_card",
					order_id: $("#order_id").val(),
					order_num: $("#order_num").val(),
					trans_id: $("#trans_id").val(),
					member_id: $("#member_id").val(),
					amount: amount,
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				if( $.trim(data[0]) == "OK" ) {
					$("#settlement-status").html(data[2]);
					$("#responsecd-"+$("#trans_id").val()+"-"+$("#order_num").val()).val("");
				} else {
					$("#responsecd-"+$("#trans_id").val()+"-"+$("#order_num").val()).val(data[0]);
				}
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		},

		changeSettlementCard : function( amount ) {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: "change_welcartpay_card",
					order_id: $("#order_id").val(),
					order_num: $("#order_num").val(),
					trans_id: $("#trans_id").val(),
					member_id: $("#member_id").val(),
					amount: amount,
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		},

		deleteSettlementCard : function( amount ) {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: "delete_welcartpay_card",
					order_id: $("#order_id").val(),
					order_num: $("#order_num").val(),
					trans_id: $("#trans_id").val(),
					member_id: $("#member_id").val(),
					amount: amount,
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				if( $.trim(data[0]) == "OK" ) {
					$("#settlement-status").html(data[2]);
					$("#responsecd-"+$("#trans_id").val()+"-"+$("#order_num").val()).val("");
				} else {
					$("#responsecd-"+$("#trans_id").val()+"-"+$("#order_num").val()).val(data[0]);
				}
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		},

		authSettlementCard : function( mode, amount ) {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: mode+"_welcartpay_card",
					order_id: $("#order_id").val(),
					order_num: $("#order_num").val(),
					trans_id: $("#trans_id").val(),
					member_id: $("#member_id").val(),
					amount: amount,
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				if( $.trim(data[0]) == "OK" ) {
					$("#settlement-status").html(data[2]);
					$("#responsecd-"+$("#trans_id").val()+"-"+$("#order_num").val()).val("");
				} else {
					$("#responsecd-"+$("#trans_id").val()+"-"+$("#order_num").val()).val(data[0]);
				}
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		}
			<?php elseif( 'acting_welcart_conv' == $acting_flg ): ?>
		getSettlementInfoConv : function() {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: "get_welcartpay_conv",
					order_id: $("#order_id").val(),
					trans_id: $("#trans_id").val(),
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		},

		changeSettlementConv : function( paylimit, amount ) {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: "change_welcartpay_conv",
					order_id: $("#order_id").val(),
					trans_id: $("#trans_id").val(),
					paylimit: paylimit,
					amount: amount,
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		},

		deleteSettlementConv : function() {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: "delete_welcartpay_conv",
					order_id: $("#order_id").val(),
					trans_id: $("#trans_id").val(),
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				if( $.trim(data[0]) == "OK" ) {
					$("#settlement-response").html(data[2]);
				}
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		},

		addSettlementConv : function( paylimit, amount ) {
			$("#settlement-response").html("");
			$("#settlement-response-loading").html('<img src="'+uscesL10n.USCES_PLUGIN_URL+'/images/loading.gif" />');

			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: "add_welcartpay_conv",
					order_id: $("#order_id").val(),
					trans_id: $("#trans_id").val(),
					paylimit: paylimit,
					amount: amount,
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				$("#settlement-response").html(data[1]);
				if( $.trim(data[0]) == "OK" ) {
					$("#settlement-response").html(data[2]);
				}
				$("#settlement-response-loading").html("");
			}).fail( function( retVal ) {
				$("#settlement-response-loading").html("");
			});
			return false;
		}
			<?php endif; ?>
	};

	$("#settlement_dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: "auto",
		width: 800,
		resizable: true,
		modal: true,
		buttons: {
			"<?php _e('Close'); ?>": function() {
				$(this).dialog("close");
			}
		},
		open: function() {
			<?php if( 'acting_welcart_card' == $acting_flg ): ?>
			adminOrderEdit.getSettlementInfoCard();
			<?php elseif( 'acting_welcart_conv' == $acting_flg ): ?>
			adminOrderEdit.getSettlementInfoConv();
			<?php endif; ?>
		},
		close: function() {
		}
	});

	$(document).on("click", ".settlement-information", function() {
		var idname = $(this).attr("id");
		var ids = idname.split("-");
		$("#trans_id").val( ids[2] );
		$("#order_num").val( ids[3] );
		if( undefined != $("#responsecd-"+ids[2]+"-"+ids[3]) ) {
			$("#error").val( $("#responsecd-"+ids[2]+"-"+ids[3]).val() );
		} else {
			$("#error").val( "" );
		}
		$("#settlement_dialog").dialog("option", "title", "<?php echo $dialog_title; ?>");
		$("#settlement_dialog").dialog("open");
	});

			<?php if( 'acting_welcart_card' == $acting_flg ): ?>
	$(document).on("click", "#capture-settlement", function() {
		if( !confirm("<?php _e('Are you sure you want to execute sales accounting processing?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.captureSettlementCard( $("#amount_change").val() );
	});

	$(document).on("click", "#delete-settlement", function() {
		if( !confirm("<?php _e('Are you sure you want to cancellation processing?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.deleteSettlementCard( $("#amount_change").val() );
	});

	$(document).on("click", "#change-settlement", function() {
		if( $("#amount_change").val() == $("#amount").val() ) {
			return;
		}
		var amount = $("#amount_change").val();
		if( amount == "" || parseInt(amount) === 0 || !checkNum(amount) ) {
			alert("<?php _e('The spending amount format is incorrect. Please enter with numeric value.','usces'); ?>");
			return;
		}
		if( !confirm("<?php _e('Are you sure you want to change the spending amount?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.changeSettlementCard( $("#amount_change").val() );
	});

	$(document).on("click", "#auth-settlement", function() {
		var amount = $("#amount_change").val();
		if( amount == "" || parseInt(amount) === 0 || !checkNum(amount) ) {
			alert("<?php _e('The spending amount format is incorrect. Please enter with numeric value.','usces'); ?>");
			return;
		}
		if( !confirm("<?php _e('Are you sure you want to execute credit processing?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.authSettlementCard( "auth", $("#amount_change").val() );
	});

	$(document).on("click", "#gathering-settlement", function() {
		var amount = $("#amount_change").val();
		if( amount == "" || parseInt(amount) === 0 || !checkNum(amount) ) {
			alert("<?php _e('The spending amount format is incorrect. Please enter with numeric value.','usces'); ?>");
			return;
		}
		if( !confirm("<?php _e('Are you sure you want to execute credit sales processing?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.authSettlementCard( "gathering", $("#amount_change").val() );
	});

	$(document).on("click", "#reauth-settlement", function() {
		var amount = $("#amount_change").val();
		if( amount == "" || parseInt(amount) === 0 || !checkNum(amount) ) {
			alert("<?php _e('The spending amount format is incorrect. Please enter with numeric value.','usces'); ?>");
			return;
		}
		if( !confirm("<?php _e('Are you sure you want to re-authorization?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.authSettlementCard( "reauth", $("#amount_change").val() );
	});

			<?php elseif( 'acting_welcart_conv' == $acting_flg ): ?>
	$(document).on("click", "#delete-settlement", function() {
		if( !confirm("<?php _e('Are you sure you want to cancellation processing?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.deleteSettlementConv();
	});

	$(document).on("click", "#change-settlement", function() {
		if( ( $("#paylimit_change").val() == $("#paylimit").val() ) &&
			( $("#amount_change").val() == $("#amount").val() ) ) {
			return;
		}
		var paylimit = $("#paylimit_change").val();
		var amount = $("#amount_change").val();
		var today = "<?php echo $this->get_transaction_date(); ?>";
		if( paylimit.length != 8 || !checkNum(paylimit) ) {
			alert("<?php _e('The payment due format is incorrect. Please enter with 8 digit number.','usces'); ?>");
			return;
		}
		if( today > paylimit ) {
			alert("<?php _e('The payment due is incorrect. Date before today cannot be specified.','usces'); ?>");
			return;
		}
		if( amount == "" || parseInt(amount) === 0 || !checkNum(amount) ) {
			alert("<?php _e('The payment amount format is incorrect. Please enter with numeric value.','usces'); ?>");
			return;
		}
		if( !confirm("<?php _e('Are you sure you want to change payment due and payment amount?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.changeSettlementConv( $("#paylimit_change").val(), $("#amount_change").val() );
	});

	$(document).on("click", "#add-settlement", function() {
		//if( ( $("#paylimit_change").val() == $("#paylimit").val() ) &&
		//	( $("#amount_change").val() == $("#amount").val() ) ) {
		//	return;
		//}
		var paylimit = $("#paylimit_change").val();
		var amount = $("#amount_change").val();
		var today = "<?php echo $this->get_transaction_date(); ?>";
		if( paylimit.length != 8 || !checkNum(paylimit) ) {
			alert("<?php _e('The payment due format is incorrect. Please enter with 8 digit number.','usces'); ?>");
			return;
		}
		if( today > paylimit ) {
			alert("<?php _e('The payment due is incorrect. Date before today cannot be specified.','usces'); ?>");
			return;
		}
		if( amount == "" || parseInt(amount) === 0 || !checkNum(amount) ) {
			alert("<?php _e('The payment amount format is incorrect. Please enter with numeric value.','usces'); ?>");
			return;
		}
		if( !confirm("<?php _e('Are you sure you want to execute the registration processing?','usces'); ?>") ) {
			return;
		}
		adminOrderEdit.addSettlementConv( $("#paylimit_change").val(), $("#amount_change").val() );
	});

			<?php endif; ?>
			<?php if( 'usces_continue' == $admin_page ): ?>
	adminContinuation = {
		update : function() {
			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				data: {
					action: "usces_admin_ajax",
					mode: "continuation_update",
					member_id: $("#member_id").val(),
					order_id: $("#order_id").val(),
					contracted_year: $("#contracted-year option:selected").val(),
					contracted_month: $("#contracted-month option:selected").val(),
					contracted_day: $("#contracted-day option:selected").val(),
					charged_year: $("#charged-year option:selected").val(),
					charged_month: $("#charged-month option:selected").val(),
					charged_day: $("#charged-day option:selected").val(),
					price: $("#price").val(),
					status: $("#dlseller-status").val(),
					wc_nonce: $("#wc_nonce").val()
				}
			}).done( function( retVal, dataType ) {
				var data = retVal.split("#usces#");
				if( $.trim(data[0]) == "OK" ) {
					adminOperation.setActionStatus( "success", "<?php _e( 'The update was completed.','usces' ); ?>" );
				} else {
					mes = ( data[1] != "" ) ? data[1] : "<?php _e( 'failure in update','usces' ); ?>";
					adminOperation.setActionStatus( "error", mes );
				}
			}).fail( function( retVal ) {
				adminOperation.setActionStatus( "error", "<?php _e( 'failure in update','usces' ); ?>" );
			});
			return false;
		}
	};

	$("#price").bind("change", function(){ usces_check_money($(this)); });
	$(document).on("click", "#continuation-update", function() {
		var status = $("#dlseller-status option:selected").val();
		if( status == "continuation" ) {
			var year = $("#charged-year option:selected").val();
			var month = $("#charged-month option:selected").val();
			var day = $("#charged-day option:selected").val();
			if( year == 0 || month == 0 || day == 0 ) {
				alert("<?php _e( 'Data have deficiency.','usces' ); ?>");
				$("#charged-year").focus();
				return;
			}

			if( $("#price").val() == "" || parseInt($("#price").val()) == 0 ) {
				alert("<?php printf( __("Input the %s",'usces'), __('Amount', 'dlseller') ); ?>");
				$("#price").focus();
				return;
			}
		}

		if( !confirm("<?php _e('Are you sure you want to update the settings?','usces'); ?>") ) {
			return;
		}
		adminContinuation.update();
	});
			<?php endif; ?>
});
</script>
<?php
			endif;
			break;
		endswitch;
	}

	/**********************************************
	* usces_action_admin_settlement_update
	* 決済オプション登録・更新
	* @param  -
	* @return -
	***********************************************/
	public function settlement_update() {
		global $usces;

		if( 'welcart' != $_POST['acting'] ) {
			return;
		}

		$this->error_mes = '';
		$options = get_option( 'usces' );
		$payment_method = usces_get_system_option( 'usces_payment_method', 'settlement' );

		unset( $options['acting_settings']['welcart'] );
		$options['acting_settings']['welcart']['merchant_id'] = ( isset($_POST['merchant_id']) ) ? $_POST['merchant_id'] : '';
		$options['acting_settings']['welcart']['merchant_pass'] = ( isset($_POST['merchant_pass']) ) ? $_POST['merchant_pass'] : '';
		$options['acting_settings']['welcart']['tenant_id'] = ( isset($_POST['tenant_id']) ) ? $_POST['tenant_id'] : '';
		$options['acting_settings']['welcart']['auth_key'] = ( isset($_POST['auth_key']) ) ? $_POST['auth_key'] : '';
		$options['acting_settings']['welcart']['ope'] = ( isset($_POST['ope']) ) ? $_POST['ope'] : '';
		$options['acting_settings']['welcart']['card_activate'] = ( isset($_POST['card_activate']) ) ? $_POST['card_activate'] : '';
		$options['acting_settings']['welcart']['foreign_activate'] = ( isset($_POST['foreign_activate']) ) ? $_POST['foreign_activate'] : '';
		$options['acting_settings']['welcart']['seccd'] = ( isset($_POST['seccd']) ) ? $_POST['seccd'] : 'on';
		$options['acting_settings']['welcart']['token_code'] = ( isset($_POST['token_code']) ) ? $_POST['token_code'] : '';
		$options['acting_settings']['welcart']['quickpay'] = ( isset($_POST['quickpay']) ) ? $_POST['quickpay'] : '';
		$options['acting_settings']['welcart']['operateid'] = ( isset($_POST['operateid']) ) ? $_POST['operateid'] : '1Auth';
		$options['acting_settings']['welcart']['operateid_dlseller'] = ( isset($_POST['operateid_dlseller']) ) ? $_POST['operateid_dlseller'] : '1Auth';
		$options['acting_settings']['welcart']['auto_settlement_mail'] = ( isset($_POST['auto_settlement_mail']) ) ? $_POST['auto_settlement_mail'] : '';
		$options['acting_settings']['welcart']['howtopay'] = ( isset($_POST['howtopay']) ) ? $_POST['howtopay'] : '';
		$options['acting_settings']['welcart']['conv_activate'] = ( isset($_POST['conv_activate']) ) ? $_POST['conv_activate'] : '';
		$options['acting_settings']['welcart']['conv_limit'] = ( !empty($_POST['conv_limit']) ) ? $_POST['conv_limit'] : '7';
		$options['acting_settings']['welcart']['conv_fee_type'] = ( isset($_POST['conv_fee_type']) ) ? $_POST['conv_fee_type'] : '';
		$options['acting_settings']['welcart']['conv_fee'] = ( isset($_POST['conv_fee']) ) ? $_POST['conv_fee'] : '';
		$options['acting_settings']['welcart']['conv_fee_limit_amount'] = ( isset($_POST['conv_fee_limit_amount']) ) ? $_POST['conv_fee_limit_amount'] : '';
		$options['acting_settings']['welcart']['conv_fee_first_amount'] = ( isset($_POST['conv_fee_first_amount']) ) ? $_POST['conv_fee_first_amount'] : '';
		$options['acting_settings']['welcart']['conv_fee_first_fee'] = ( isset($_POST['conv_fee_first_fee']) ) ? $_POST['conv_fee_first_fee'] : '';
		$options['acting_settings']['welcart']['conv_fee_amounts'] = ( isset($_POST['conv_fee_amounts']) ) ? explode( '|', $_POST['conv_fee_amounts'] ) : array();
		$options['acting_settings']['welcart']['conv_fee_fees'] = ( isset($_POST['conv_fee_fees']) ) ? explode( '|', $_POST['conv_fee_fees'] ) : array();
		$options['acting_settings']['welcart']['conv_fee_end_fee'] = ( isset($_POST['conv_fee_end_fee']) ) ? $_POST['conv_fee_end_fee'] : '';
		$options['acting_settings']['welcart']['atodene_activate'] = ( isset($_POST['atodene_activate']) ) ? $_POST['atodene_activate'] : '';
		$options['acting_settings']['welcart']['atodene_byitem'] = ( isset($_POST['atodene_byitem']) ) ? $_POST['atodene_byitem'] : 'off';
		$options['acting_settings']['welcart']['atodene_billing_method'] = ( isset($_POST['atodene_billing_method']) ) ? $_POST['atodene_billing_method'] : '2';
		$options['acting_settings']['welcart']['atodene_fee_type'] = ( isset($_POST['atodene_fee_type']) ) ? $_POST['atodene_fee_type'] : '';
		$options['acting_settings']['welcart']['atodene_fee'] = ( isset($_POST['atodene_fee']) ) ? $_POST['atodene_fee'] : '';
		$options['acting_settings']['welcart']['atodene_fee_limit_amount'] = ( isset($_POST['atodene_fee_limit_amount']) ) ? $_POST['atodene_fee_limit_amount'] : '';
		$options['acting_settings']['welcart']['atodene_fee_first_amount'] = ( isset($_POST['atodene_fee_first_amount']) ) ? $_POST['atodene_fee_first_amount'] : '';
		$options['acting_settings']['welcart']['atodene_fee_first_fee'] = ( isset($_POST['atodene_fee_first_fee']) ) ? $_POST['atodene_fee_first_fee'] : '';
		$options['acting_settings']['welcart']['atodene_fee_amounts'] = ( isset($_POST['atodene_fee_amounts']) ) ? explode( '|', $_POST['atodene_fee_amounts'] ) : array();
		$options['acting_settings']['welcart']['atodene_fee_fees'] = ( isset($_POST['atodene_fee_fees']) ) ? explode( '|', $_POST['atodene_fee_fees'] ) : array();
		$options['acting_settings']['welcart']['atodene_fee_end_fee'] = ( isset($_POST['atodene_fee_end_fee']) ) ? $_POST['atodene_fee_end_fee'] : '';

		if( WCUtils::is_blank($_POST['merchant_id']) ) {
			$this->error_mes .= __('* Please enter the Merchant ID.','usces').'<br />';
		}
		if( WCUtils::is_blank($_POST['merchant_pass']) ) {
			$this->error_mes .= __('* Please enter the Merchant Password.','usces').'<br />';
		}
		if( WCUtils::is_blank($_POST['tenant_id']) ) {
			$this->error_mes .= __('* Please enter the Tenant ID.','usces').'<br />';
		}
		if( WCUtils::is_blank($_POST['auth_key']) ) {
			$this->error_mes .= __('* Please enter the Settlement auth key.','usces').'<br />';
		} else {
			$auth_key = md5($_POST['auth_key']);
			$welcartpay_keys = get_option( 'usces_welcartpay_keys' );
			if( !in_array( $auth_key, $welcartpay_keys ) ) {
				$this->error_mes .= __('* The Settlement auth key is incorrect.','usces').'<br />';
			}
		}
		if( WCUtils::is_blank($_POST['ope']) ) {
			$this->error_mes .= __('* Please select the operating environment.','usces').'<br />';
		}
		if( 'on' == $options['acting_settings']['welcart']['card_activate'] ) {
			$unavailable_activate = false;
			foreach( $payment_method as $key => $payment ) {
				foreach( (array)$this->unavailable_method as $unavailable ) {
					if( $unavailable == $key && 'activate' == $payment['use'] ) {
						$unavailable_activate = true;
						break;
					}
				}
			}
			if( $unavailable_activate ) {
				$this->error_mes .= __('* Settlement that can not be used together is activated.','usces').'<br />';
			}
		}

		if( WCUtils::is_blank($this->error_mes) ) {
			$usces->action_status = 'success';
			$usces->action_message = __('options are updated','usces');
			$options['acting_settings']['welcart']['activate'] = 'on';
			if( 'public' == $options['acting_settings']['welcart']['ope'] ) {
				$options['acting_settings']['welcart']['send_url'] = 'https://www.e-scott.jp/online/aut/OAUT002.do';
				$options['acting_settings']['welcart']['send_url_member'] = 'https://www.e-scott.jp/online/crp/OCRP005.do';
				$options['acting_settings']['welcart']['send_url_conv'] = 'https://www.e-scott.jp/online/cnv/OCNV005.do';
				$options['acting_settings']['welcart']['redirect_url_conv'] = 'https://link.kessai.info/JLP/JLPcon';
				$options['acting_settings']['welcart']['send_url_link'] = 'https://www.e-scott.jp/euser/snp/SSNP005ReferStart.do';
				$options['acting_settings']['welcart']['api_token'] = 'https://www.e-scott.jp/euser/stn/CdGetJavaScript.do';
				$options['acting_settings']['welcart']['send_url_token'] = 'https://www.e-scott.jp/online/atn/OATN005.do';
				$options['acting_settings']['welcart']['key_aes'] = $this->key_aes;
				$options['acting_settings']['welcart']['key_iv'] = $this->key_iv;
			} else {
				$options['acting_settings']['welcart']['send_url'] = 'https://www.test.e-scott.jp/online/aut/OAUT002.do';
				$options['acting_settings']['welcart']['send_url_member'] = 'https://www.test.e-scott.jp/online/crp/OCRP005.do';
				$options['acting_settings']['welcart']['send_url_conv'] = 'https://www.test.e-scott.jp/online/cnv/OCNV005.do';
				$options['acting_settings']['welcart']['redirect_url_conv'] = 'https://link.kessai.info/JLPCT/JLPcon';
				$options['acting_settings']['welcart']['send_url_link'] = 'https://www.test.e-scott.jp/euser/snp/SSNP005ReferStart.do';
				$options['acting_settings']['welcart']['api_token'] = 'https://www.test.e-scott.jp/euser/stn/CdGetJavaScript.do';
				$options['acting_settings']['welcart']['send_url_token'] = 'https://www.test.e-scott.jp/online/atn/OATN005.do';
				$options['acting_settings']['welcart']['key_aes'] = $this->key_aes;
				$options['acting_settings']['welcart']['key_iv'] = $this->key_iv;
				$options['acting_settings']['welcart']['tenant_id'] = '0001';
			}
			if( 'on' == $options['acting_settings']['welcart']['card_activate'] ) {
				if( !empty($options['acting_settings']['welcart']['token_code']) ) {
					$options['acting_settings']['welcart']['card_activate'] = 'token';
				}
			}
			if( 'on' == $options['acting_settings']['welcart']['card_activate'] || 'link' == $options['acting_settings']['welcart']['card_activate'] || 'token' == $options['acting_settings']['welcart']['card_activate'] ) {
				$usces->payment_structure['acting_welcart_card'] = __('Credit card transaction (WelcartPay)','usces');
			} else {
				unset($usces->payment_structure['acting_welcart_card']);
			}
			if( 'on' == $options['acting_settings']['welcart']['conv_activate'] ) {
				$usces->payment_structure['acting_welcart_conv'] = __('Online storage agency (WelcartPay)','usces');
			} else {
				unset($usces->payment_structure['acting_welcart_conv']);
			}
			if( 'on' == $options['acting_settings']['welcart']['atodene_activate'] ) {
				$usces->payment_structure['acting_welcart_atodene'] = __('Postpay settlement (WelcartPay/ATODENE)','usces');
			} else {
				unset($usces->payment_structure['acting_welcart_atodene']);
			}
		} else {
			$usces->action_status = 'error';
			$usces->action_message = __('Data have deficiency.','usces');
			$options['acting_settings']['welcart']['activate'] = 'off';
			unset( $usces->payment_structure['acting_welcart_card'] );
			unset( $usces->payment_structure['acting_welcart_conv'] );
			unset( $usces->payment_structure['acting_welcart_atodene'] );
		}
		ksort( $usces->payment_structure );
		update_option( 'usces', $options );
		update_option( 'usces_payment_structure', $usces->payment_structure );
	}

	/**********************************************
	* usces_action_settlement_tab_body
	* クレジット決済設定画面フォーム
	* @param  -
	* @return -
	* @echo   html
	***********************************************/
	public function settlement_tab_body() {

		$acting_opts = $this->get_acting_settings();
		$settlement_selected = get_option( 'usces_settlement_selected' );
		if( in_array( 'welcart', (array)$settlement_selected ) ):
?>
	<div id="uscestabs_welcart">
	<div class="settlement_service"><span class="service_title"><?php _e('WelcartPay','usces'); ?></span></div>

	<?php if( isset($_POST['acting']) && 'welcart' == $_POST['acting'] ): ?>
		<?php if( '' != $this->error_mes ): ?>
		<div class="error_message"><?php echo $this->error_mes; ?></div>
		<?php elseif( isset($acting_opts['activate']) && 'on' == $acting_opts['activate'] ): ?>
		<div class="message"><?php _e('Test thoroughly before use.','usces'); ?></div>
		<?php endif; ?>
	<?php endif; ?>
	<form action="" method="post" name="welcart_form" id="welcart_form">
		<table class="settle_table">
			<tr>
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_merchant_id_welcart');"><?php _e('Merchant ID','usces');//マーチャントID ?></a></th>
				<td colspan="4"><input name="merchant_id" type="text" id="merchant_id_welcart" value="<?php echo $acting_opts['merchant_id']; ?>" size="20" /></td>
				<td><div id="ex_merchant_id_welcart" class="explanation"><?php _e('Merchant ID (single-byte numbers only) issued from e-SCOTT.','usces'); ?></div></td>
			</tr>
			<tr>
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_merchant_pass_welcart');"><?php _e('Merchant Password','usces');//マーチャントパスワード ?></a></th>
				<td colspan="4"><input name="merchant_pass" type="text" id="merchant_pass_welcart" value="<?php echo $acting_opts['merchant_pass']; ?>" size="20" /></td>
				<td><div id="ex_merchant_pass_welcart" class="explanation"><?php _e('Merchant Password (single-byte alphanumeric characters only) issued from e-SCOTT.','usces'); ?></div></td>
			</tr>
			<tr>
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_tenant_id_welcart');"><?php _e('Tenant ID','usces');//店舗コード ?></a></th>
				<td colspan="4"><input name="tenant_id" type="text" id="tenant_id_welcart" value="<?php echo $acting_opts['tenant_id']; ?>" size="20" /></td>
				<td><div id="ex_tenant_id_welcart" class="explanation"><?php _e('Tenant ID issued from e-SCOTT.<br />If you have only one shop to contract, enter 0001.','usces'); ?></div></td>
			</tr>
			<tr>
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_auth_key_welcart');"><?php _e('Settlement auth key','usces');//決済認証キー ?></a></th>
				<td colspan="4"><input name="auth_key" type="text" id="auth_key_welcart" value="<?php echo $acting_opts['auth_key']; ?>" size="20" /></td>
				<td><div id="ex_auth_key_welcart" class="explanation"><?php _e('Settlement auth key (single-byte numbers only) issued from e-SCOTT.','usces'); ?></div></td>
			</tr>
			<tr>
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_ope_welcart');"><?php _e('Operation Environment','usces');//動作環境 ?></a></th>
				<td><input name="ope" type="radio" id="ope_welcart_1" value="test"<?php if( $acting_opts['ope'] == 'test' ) echo ' checked="checked"'; ?> /></td><td><label for="ope_welcart_1"><?php _e('Testing environment','usces'); ?></label></td>
				<td><input name="ope" type="radio" id="ope_welcart_2" value="public"<?php if( $acting_opts['ope'] == 'public' ) echo ' checked="checked"'; ?> /></td><td><label for="ope_welcart_2"><?php _e('Production environment','usces'); ?></label></td>
				<td><div id="ex_ope_welcart" class="explanation"><?php _e('Switch the operating environment.','usces'); ?></div></td>
			</tr>
		</table>
		<table class="settle_table">
			<tr>
				<th><?php _e('Credit card settlement','usces');//クレジットカード決済 ?></th>
				<td><input name="card_activate" type="radio" class="card_activate_welcart" id="card_activate_welcart_1" value="on"<?php if( $acting_opts['card_activate'] == 'on' || $acting_opts['card_activate'] == 'token' ) echo ' checked="checked"'; ?> /></td><td><label for="card_activate_welcart_1"><?php _e('Use with non-passage type','usces'); ?></label></td>
				<td><input name="card_activate" type="radio" class="card_activate_welcart" id="card_activate_welcart_2" value="link"<?php if( $acting_opts['card_activate'] == 'link' ) echo ' checked="checked"'; ?> /></td><td><label for="card_activate_welcart_2"><?php _e('Use with external link type','usces'); ?></label></td>
				<!--<td><input name="card_activate" type="radio" class="card_activate_welcart" id="card_activate_welcart_3" value="token"<?php if( $acting_opts['card_activate'] == 'token' ) echo ' checked="checked"'; ?> /></td><td><label for="card_activate_welcart_3"><?php _e('Use with token type','usces'); ?></label></td>-->
				<td><input name="card_activate" type="radio" class="card_activate_welcart" id="card_activate_welcart_0" value="off"<?php if( $acting_opts['card_activate'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="card_activate_welcart_0"><?php _e('Do not Use','usces'); ?></label></td>
				<td></td><td></td>
			</tr>
			<!--<tr class="card_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_foreign_activate_welcart');"><?php _e('Foreign exchange settlement','usces');//外貨決済 ?></a></th>
				<td><input name="foreign_activate" type="radio" id="foreign_activate_welcart_1" value="on"<?php //if( $acting_opts['foreign_activate'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="foreign_activate_welcart_1"><?php _e('Use','usces'); ?></label></td>
				<td><input name="foreign_activate" type="radio" id="foreign_activate_welcart_2" value="off"<?php //if( $acting_opts['foreign_activate'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="foreign_activate_welcart_2"><?php _e('Do not Use','usces'); ?></label></td>
				<td></td><td></td>
				<td><div id="ex_foreign_activate_welcart" class="explanation"><?php _e('Foreign exchange settlement, only two VISA and MasterCard card companies are available.<br />If switching to Yen card is done during operation, the member information registered in e-SCOTT will be invalid.','usces'); ?></div></td>
			</tr>-->
			<tr class="card_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_seccd_welcart');"><?php _e('Security code <br /> (authentication assist)','usces');//セキュリティコード ?></a></th>
				<td><input name="seccd" type="radio" id="seccd_welcart_1" value="on"<?php if( $acting_opts['seccd'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="seccd_welcart_1"><?php _e('Use','usces'); ?></label></td>
				<td><input name="seccd" type="radio" id="seccd_welcart_0" value="off"<?php if( $acting_opts['seccd'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="seccd_welcart_0"><?php _e('Do not Use','usces'); ?></label></td>
				<td></td><td></td>
				<td colspan="2"><div id="ex_seccd_welcart" class="explanation"><?php _e("Use 'Security code' of authentication assist matching. If you decide not to use, please also set 'Do not verify matching' on the e-SCOTT management screen.",'usces'); ?></div></td>
			</tr>
			<tr class="card_token_code_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_token_code_welcart');"><?php _e('Token auth code','usces');//トークン決済認証コード ?></a></th>
				<td colspan="6"><input name="token_code" type="text" id="token_code_welcart" value="<?php echo $acting_opts['token_code']; ?>" size="36" maxlength="32" /></td>
				<td colspan="2"><div id="ex_token_code_welcart" class="explanation"><?php _e("Token auth code (single-byte alphanumeric characters only) issued from e-SCOTT.",'usces'); ?></div></td>
			</tr>
			<tr class="card_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_quickpay_welcart');"><?php _e('Quick payment','usces');//クイック決済 ?></a></th>
				<td><input name="quickpay" type="radio" id="quickpay_welcart_1" value="on"<?php if( $acting_opts['quickpay'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="quickpay_welcart_1"><?php _e('Use','usces'); ?></label></td>
				<td><input name="quickpay" type="radio" id="quickpay_welcart_0" value="off"<?php if( $acting_opts['quickpay'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="quickpay_welcart_0"><?php _e('Do not Use','usces'); ?></label></td>
				<td></td><td></td>
				<td colspan="2"><div id="ex_quickpay_welcart" class="explanation"><?php _e("When using automatic continuing charging (required WCEX DLSeller) or subscription (required WCEX Auto Delivery), please make 'Quick payment' of 'Use'.",'usces'); ?></div></td>
			</tr>
			<tr class="card_welcart">
				<th><?php _e('Processing classification','usces');//処理区分 ?></th>
				<td><input name="operateid" type="radio" id="operateid_welcart_1" value="1Auth"<?php if( $acting_opts['operateid'] == '1Auth' ) echo ' checked="checked"'; ?> /></td><td><label for="operateid_welcart_1"><?php _e('Credit','usces');//与信 ?></label></td>
				<td><input name="operateid" type="radio" id="operateid_welcart_2" value="1Gathering"<?php if( $acting_opts['operateid'] == '1Gathering' ) echo ' checked="checked"'; ?> /></td><td><label for="operateid_welcart_2"><?php _e('Credit sales','usces');//与信売上計上 ?></label></td>
				<td></td><td></td><td></td><td></td>
			</tr>
			<?php if( defined('WCEX_DLSELLER') ): ?>
			<tr class="card_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_operateid_dlseller_welcart');"><?php _e('Automatic Continuing Charging Processing Classification','usces');//自動継続課金処理区分 ?></a></th>
				<td><input name="operateid_dlseller" type="radio" id="operateid_dlseller_welcart_1" value="1Auth"<?php if( $acting_opts['operateid_dlseller'] == '1Auth' ) echo ' checked="checked"'; ?> /></td><td><label for="operateid_dlseller_welcart_1"><?php _e('Credit','usces');//与信 ?></label></td>
				<td><input name="operateid_dlseller" type="radio" id="operateid_dlseller_welcart_2" value="1Gathering"<?php if( $acting_opts['operateid_dlseller'] == '1Gathering' ) echo ' checked="checked"'; ?> /></td><td><label for="operateid_dlseller_welcart_2"><?php _e('Credit sales','usces');//与信売上計上 ?></label></td>
				<td></td><td></td>
				<td colspan="2"><div id="ex_operateid_dlseller_welcart" class="explanation"><?php _e('Processing classification when automatic continuing charging (required WCEX DLSeller).','usces'); ?></div></td>
			</tr>
			<tr class="card_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_auto_settlement_mail_welcart');"><?php _e('Automatic Continuing Charging Completion Mail','usces');//自動継続課金完了メール ?></a></th>
				<td><input name="auto_settlement_mail" type="radio" id="auto_settlement_mail_welcart_1" value="on"<?php if( $acting_opts['auto_settlement_mail'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="auto_settlement_mail_welcart_1"><?php _e("Send",'usces'); ?></label></td>
				<td><input name="auto_settlement_mail" type="radio" id="auto_settlement_mail_welcart_0" value="off"<?php if( $acting_opts['auto_settlement_mail'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="auto_settlement_mail_welcart_0"><?php _e("Don't send",'usces'); ?></label></td>
				<td></td><td></td>
				<td colspan="2"><div id="ex_auto_settlement_mail_welcart" class="explanation"><?php _e('Send billing completion mail to the member on which automatic continuing charging processing (required WCEX DLSeller) is executed.','usces'); ?></div></td>
			</tr>
			<?php endif; ?>
			<tr class="card_howtopay_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_howtopay_welcart');"><?php _e('Number of payments','usces');//支払い回数 ?></a></th>
				<td><input name="howtopay" type="radio" id="howtopay_welcart_1" value="1"<?php if( $acting_opts['howtopay'] == '1' ) echo ' checked="checked"'; ?> /></td><td><label for="howtopay_welcart_1"><?php _e('Lump-sum payment only','usces');//一括払いのみ ?></label></td>
				<td><input name="howtopay" type="radio" id="howtopay_welcart_2" value="2"<?php if( $acting_opts['howtopay'] == '2' ) echo ' checked="checked"'; ?> /></td><td><label for="howtopay_welcart_2"><?php _e('Activate installment payment','usces');//分割払いを有効にする ?></label></td>
				<td><input name="howtopay" type="radio" id="howtopay_welcart_3" value="3"<?php if( $acting_opts['howtopay'] == '3' ) echo ' checked="checked"'; ?> /></td><td><label for="howtopay_welcart_3"><?php _e('Activate installment payments and bonus payments','usces');//分割払いとボーナス払いを有効にする ?></label></td>
				<td colspan="2"><div id="ex_howtopay_welcart" class="explanation"><?php _e('It can be selected when using in embedded type.','usces'); ?></div></td>
			</tr>
		</table>
		<table class="settle_table">
			<tr>
				<th><?php _e('Online storage agency','usces');//オンライン収納代行 ?></th>
				<td><input name="conv_activate" type="radio" class="conv_activate_welcart" id="conv_activate_welcart_1" value="on"<?php if( $acting_opts['conv_activate'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="conv_activate_welcart_1"><?php _e('Use','usces'); ?></label></td>
				<td><input name="conv_activate" type="radio" class="conv_activate_welcart" id="conv_activate_welcart_0" value="off"<?php if( $acting_opts['conv_activate'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="conv_activate_welcart_0"><?php _e('Do not Use','usces'); ?></label></td>
				<td></td>
			</tr>
			<tr class="conv_welcart">
				<th><?php _e('Payment due days','usces');//支払期限日数 ?></th>
				<td colspan="4"><input name="conv_limit" type="text" id="conv_limit" value="<?php echo $acting_opts['conv_limit']; ?>" size="5" /><?php _e('days','usces'); ?></td>
				<td></td>
			</tr>
			<tr class="conv_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_conv_fee_welcart');"><?php _e('Fee','usces');//手数料 ?></a></th>
				<td colspan="2" id="conv_fee_type_field"><?php echo $this->get_fee_name( $acting_opts['conv_fee_type'] ); ?></td><td colspan="2"><input type="button" class="button" value="<?php _e('Detailed setting','usces'); ?>" id="conv_fee_setting" /></td>
				<td><div id="ex_conv_fee_welcart" class="explanation"><?php _e('Set the online storage agency commission and settlement upper limit. Leave it blank if you do not need it.','usces'); ?></div></td>
			</tr>
		</table>
		<table class="settle_table">
			<tr>
				<th><?php _e('Postpay settlement (ATODENE)','usces');//後払い決済 ?></th>
				<td><input name="atodene_activate" type="radio" class="atodene_activate_welcart" id="atodene_activate_welcart_1" value="on"<?php if( $acting_opts['atodene_activate'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="atodene_activate_welcart_1"><?php _e('Use','usces'); ?></label></td>
				<td><input name="atodene_activate" type="radio" class="atodene_activate_welcart" id="atodene_activate_welcart_0" value="off"<?php if( $acting_opts['atodene_activate'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="atodene_activate_welcart_0"><?php _e('Do not Use','usces'); ?></label></td>
				<td></td>
			</tr>
			<tr class="atodene_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_atodene_byitem_welcart');"><?php _e('Possibility of each items','usces');//商品ごとの可否 ?></a></th>
				<td><input name="atodene_byitem" type="radio" id="atodene_byitem_welcart_1" value="on"<?php if( $acting_opts['atodene_byitem'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="atodene_byitem_welcart_1"><?php _e("Enabled",'usces'); ?></label></td>
				<td><input name="atodene_byitem" type="radio" id="atodene_byitem_welcart_0" value="off"<?php if( $acting_opts['atodene_byitem'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="atodene_byitem_welcart_0"><?php _e("Disabled",'usces'); ?></label></td>
				<td><div id="ex_atodene_byitem_welcart" class="explanation"><?php _e('It is effective when setting possibility of each items. Invalid when not distinguished in particular.<br />If enabled, a selection field will be added to determine whether postpay settlement is possible on the product registration screen. If there is a product in the cart that can not be postpaid settlement, we exclude postpaid settlement from the payment method options.<br />In addition, availability data is added to the product CSV as a custom field (welcartpay_atodene).','usces'); ?></div></td>
			</tr>
			<tr class="atodene_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_atodene_billing_method_welcart');"><?php _e('Invoice delivery method','usces');//請求書送付方法 ?></a></th>
				<td><input name="atodene_billing_method" type="radio" id="atodene_billing_method_welcart_2" value="2"<?php if( $acting_opts['atodene_billing_method'] == '2' ) echo ' checked="checked"'; ?> /></td><td><label for="atodene_billing_method_welcart_2"><?php _e('Separate shipment','usces');//別送 ?></label></td>
				<td><input name="atodene_billing_method" type="radio" id="atodene_billing_method_welcart_3" value="3"<?php if( $acting_opts['atodene_billing_method'] == '3' ) echo ' checked="checked"'; ?> /></td><td><label for="atodene_billing_method_welcart_3"><?php _e('Include shipment','usces');//同梱 ?></label></td>
				<td><div id="ex_atodene_billing_method_welcart" class="explanation"><?php _e('How to send invoices from ATODENE.','usces'); ?></div></td>
			</tr>
			<tr class="atodene_welcart">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_atodene_fee_welcart');"><?php _e('Fee','usces'); ?></a></th>
				<td colspan="2" id="atodene_fee_type_field"><?php echo $this->get_fee_name( $acting_opts['atodene_fee_type'] ); ?></td><td colspan="2"><input type="button" class="button" value="<?php _e('Detailed setting','usces'); ?>" id="atodene_fee_setting" /></td>
				<td><div id="ex_atodene_fee_welcart" class="explanation"><?php _e('Set up postpaid settlement fee and maximum settlement amount. Leave it blank if you do not need it.','usces'); ?></div></td>
			</tr>
		</table>
		<input type="hidden" name="acting" value="welcart" />
		<input type="hidden" name="conv_fee_type" id="conv_fee_type" value="<?php echo $acting_opts['conv_fee_type']; ?>" />
		<input type="hidden" name="conv_fee" id="conv_fee" value="<?php echo $acting_opts['conv_fee']; ?>" />
		<input type="hidden" name="conv_fee_limit_amount_fix" id="conv_fee_limit_amount_fix" value="<?php echo $acting_opts['conv_fee_limit_amount']; ?>" />
		<input type="hidden" name="conv_fee_first_amount" id="conv_fee_first_amount" value="<?php echo $acting_opts['conv_fee_first_amount']; ?>" />
		<input type="hidden" name="conv_fee_first_fee" id="conv_fee_first_fee" value="<?php echo $acting_opts['conv_fee_first_fee']; ?>" />
		<input type="hidden" name="conv_fee_limit_amount_change" id="conv_fee_limit_amount_change" value="<?php echo $acting_opts['conv_fee_limit_amount']; ?>" />
		<input type="hidden" name="conv_fee_amounts" id="conv_fee_amounts" value="<?php echo implode('|', $acting_opts['conv_fee_amounts']); ?>" />
		<input type="hidden" name="conv_fee_fees" id="conv_fee_fees" value="<?php echo implode('|', $acting_opts['conv_fee_fees']); ?>" />
		<input type="hidden" name="conv_fee_end_fee" id="conv_fee_end_fee" value="<?php echo $acting_opts['conv_fee_end_fee']; ?>" />
		<input type="hidden" name="atodene_fee_type" id="atodene_fee_type" value="<?php echo $acting_opts['atodene_fee_type']; ?>" />
		<input type="hidden" name="atodene_fee" id="atodene_fee" value="<?php echo $acting_opts['atodene_fee']; ?>" />
		<input type="hidden" name="atodene_fee_limit_amount_fix" id="atodene_fee_limit_amount_fix" value="<?php echo $acting_opts['atodene_fee_limit_amount']; ?>" />
		<input type="hidden" name="atodene_fee_first_amount" id="atodene_fee_first_amount" value="<?php echo $acting_opts['atodene_fee_first_amount']; ?>" />
		<input type="hidden" name="atodene_fee_first_fee" id="atodene_fee_first_fee" value="<?php echo $acting_opts['atodene_fee_first_fee']; ?>" />
		<input type="hidden" name="atodene_fee_limit_amount_change" id="atodene_fee_limit_amount_change" value="<?php echo $acting_opts['atodene_fee_limit_amount']; ?>" />
		<input type="hidden" name="atodene_fee_amounts" id="atodene_fee_amounts" value="<?php echo implode('|', $acting_opts['atodene_fee_amounts']); ?>" />
		<input type="hidden" name="atodene_fee_fees" id="atodene_fee_fees" value="<?php echo implode('|', $acting_opts['atodene_fee_fees']); ?>" />
		<input type="hidden" name="atodene_fee_end_fee" id="atodene_fee_end_fee" value="<?php echo $acting_opts['atodene_fee_end_fee']; ?>" />
		<input name="usces_option_update" type="submit" class="button button-primary" value="<?php _e('Update WelcartPay settings','usces'); ?>" />
		<?php wp_nonce_field( 'admin_settlement', 'wc_nonce' ); ?>
	</form>
	<div class="settle_exp">
		<p><strong>WelcartPay based on e-SCOTT</strong></p>
		<a href="http://www.sonypaymentservices.jp/intro/" target="_blank"><?php _e('Details of e-SCOTT Smart is here >>','usces'); ?></a>
		<p>&nbsp;</p>
		<p><?php echo __("'Embedded type' is a settlement system that completes with shop site only, without transitioning to the page of the settlement company.",'usces'); ?><br />
			<?php echo __("Stylish with unified design is possible. However, because we will handle the card number, dedicated SSL is required.",'usces'); ?><br />
			<?php echo __("When there is a setting of 'Token auth code', it becomes settlement of the 'Token settlement type'.",'usces'); ?><br />
			<?php echo __("'External link type' is a settlement system that moves to the page of the settlement company and inputs card information.",'usces'); ?></p>
		<p><?php echo __("In both types, the entered card number will be sent to the e-SCOTT Smart system, so it will not be saved in Welcart.",'usces'); ?></p>
		<p><?php echo __("'WCEX DL Seller' is necessary when using 'automatic continuing charging'.",'usces'); ?><br />
			<?php echo __("'WCEX Auto Delivery' is necessary when using 'subscription'.",'usces'); ?></p>
		<p><?php echo __("In addition, in the production environment, it is SSL communication with only an authorized SSL certificate, so it is necessary to be careful.",'usces'); ?></p>
		<p><?php echo __("The Welcart member account used in the test environment may not be available in the production environment.",'usces'); ?><br />
			<?php echo __("Please make another member registration in the test environment and production environment, or delete the member used in the test environment once and register again in the production environment.",'usces'); ?></p>
	</div>
	</div><!--uscestabs_welcart-->

	<div id="welcartpay_fee_dialog" class="cod_dialog">
		<fieldset>
		<table id="welcartpay_fee_type_table" class="cod_type_table">
			<tr>
				<th><?php _e('Type of the fee','usces'); ?></th>
				<td class="radio"><input name="fee_type" type="radio" id="fee_type_fix" class="fee_type" value="fix" /></td><td><label for="fee_type_fix"><?php _e('Fixation','usces'); ?></label></td>
				<td class="radio"><input name="fee_type" type="radio" id="fee_type_change" class="fee_type" value="change" /></td><td><label for="fee_type_change"><?php _e('Variable','usces'); ?></label></td>
			</tr>
		</table>
		<table id="welcartpay_fee_fix_table" class="cod_fix_table">
			<tr>
				<th><?php _e('Fee','usces'); ?></th>
				<td><input name="fee" type="text" id="fee_fix" class="short_str num" /><?php usces_crcode(); ?></td>
			</tr>
			<tr>
				<th><?php _e('Upper limit','usces'); ?></th>
				<td><input name="fee_limit_amount" type="text" id="fee_limit_amount_fix" class="short_str num" /><?php usces_crcode(); ?></td>
			</tr>
		</table>
		<div id="welcartpay_fee_change_table" class="cod_change_table">
		<input type="button" class="button" id="fee_add_row" value="<?php _e('Add row','usces'); ?>" />
		<input type="button" class="button" id="fee_del_row" value="<?php _e('Delete row','usces'); ?>" />
		<table>
			<thead>
				<tr>
					<th colspan="3"><?php _e('A purchase amount','usces'); ?>(<?php usces_crcode(); ?>)</th>
					<th><?php _e('Fee','usces'); ?>(<?php usces_crcode(); ?>)</th>
				</tr>
				<tr>
					<td class="cod_f">0</td>
					<td class="cod_m"><?php _e(' - ','usces'); ?></td>
					<td class="cod_e"><input name="fee_first_amount" id="fee_first_amount" type="text" class="short_str num" /></td>
					<td class="cod_cod"><input name="fee_first_fee" id="fee_first_fee" type="text" class="short_str num" /></td>
				</tr>
			</thead>
			<tbody id="fee_change_field"></tbody>
			<tfoot>
				<tr>
					<td class="cod_f"><span id="end_amount"></span></td>
					<td class="cod_m"><?php _e(' - ','usces'); ?></td>
					<td class="cod_e"><input name="fee_limit_amount" type="text" id="fee_limit_amount_change" class="short_str num" /></td>
					<td class="cod_cod"><input name="fee_end_fee" type="text" id="fee_end_fee" class="short_str num" /></td>
				</tr>
			</tfoot>
		</table>
		</div>
		</fieldset>
		<input type="hidden" id="welcartpay_fee_mode">
	</div><!--welcartpay_fee_dialog-->
<?php
		endif;
	}

	/**********************************************
	* wcad_filter_admin_notices
	* 
	* @param  $msg
	* @return str $msg
	***********************************************/
	public function admin_notices_autodelivery( $msg ) {
		global $usces;

		$acting_opts = $this->get_acting_settings();
		if( ( isset($acting_opts['activate']) && 'on' == $acting_opts['activate'] ) && 
			( isset($acting_opts['card_activate']) && ( 'on' == $acting_opts['card_activate'] || 'link' == $acting_opts['card_activate'] || 'token' == $acting_opts['card_activate'] ) ) && 
			( isset($acting_opts['quickpay']) && 'on' == $acting_opts['quickpay'] ) ) {
			$msg = '';
		} else {
			$zeus_opts = $usces->options['acting_settings']['zeus'];
			$p_flag = ( ( isset($zeus_opts['activate']) && 'on' == $zeus_opts['activate'] ) && ( isset($zeus_opts['card_activate']) && 'on' == $zeus_opts['card_activate'] ) ) ? true : false;
			$batch = ( isset($zeus_opts['batch']) ) ? $zeus_opts['batch'] : 'off';
			if( !$p_flag || 'off' == $batch ) {
				$msg .= '
				<div class="error">
				<p>'.__("In 'credit settlement Settings', please set to 'use' the quickpay of WelcartPay.",'usces').'</p>
				</div>';
			}
		}
		return $msg;
	}

	/**********************************************
	* usces_after_cart_instant
	* 入金通知処理および、三者間決済画面からのリダイレクト
	* @param  -
	* @return -
	***********************************************/
	public function acting_transaction() {
		global $wpdb, $usces;

		if( isset($_REQUEST['MerchantFree1']) && isset($_REQUEST['MerchantId']) && isset($_REQUEST['TransactionId']) && isset($_REQUEST['RecvNum']) && isset($_REQUEST['NyukinDate']) && 
			( isset($_REQUEST['MerchantFree2']) && 'acting_welcart_conv' == $_REQUEST['MerchantFree2'] ) ) {
			$acting_opts = $this->get_acting_settings();
			if( $acting_opts['merchant_id'] == $_REQUEST['MerchantId'] ) {
				$response_data = $_REQUEST;
				$order_meta_table_name = $wpdb->prefix.'usces_order_meta';
				$query = $wpdb->prepare( "SELECT order_id FROM $order_meta_table_name WHERE meta_key = %s", $response_data['MerchantFree1'] );
				$order_id = $wpdb->get_var($query);
				if( !empty($order_id) ) {

					//オーダーステータス変更
					usces_change_order_receipt( $order_id, 'receipted' );
					//ポイント付与
					usces_action_acting_getpoint( $order_id );

					$response_data['OperateId'] = 'receipted';
					$order_meta = usces_unserialize( $usces->get_order_meta_value( $response_data['MerchantFree2'], $order_id ) );
					$meta_value = array_merge( $order_meta, $response_data );
					$usces->set_order_meta_value( $response_data['MerchantFree2'], usces_serialize($meta_value), $order_id );
					$this->save_acting_history_log( $response_data, $order_id.'_'.$response_data['MerchantFree1'] );
					usces_log('[WelcartPay] conv receipted : '.print_r($response_data, true), 'acting_transaction.log');
				} else {
					usces_log('[WelcartPay] conv receipted order_id error : '.print_r($response_data, true), 'acting_transaction.log');
				}
			}
			header("HTTP/1.0 200 OK");
			die();

		} elseif( isset($_REQUEST['EncryptValue']) ) {
			$acting_opts = $this->get_acting_settings();
			$encryptvalue = openssl_decrypt( $_REQUEST['EncryptValue'], 'aes-128-cbc', $acting_opts['key_aes'], false, $acting_opts['key_iv'] );
			if( $encryptvalue ) {
				parse_str( $encryptvalue, $response_data );
//usces_log(print_r($response_data,true),"test.log");
				if( isset($response_data['OperateId']) && isset($response_data['ResponseCd']) && ( isset($response_data['MerchantId']) && $acting_opts['merchant_id'] == $response_data['MerchantId'] ) ) {
					$cancel = array( 'P51', 'P52', 'P55', 'P56', 'P62', 'P63', 'P64', 'P65', 'P69', 'P70' );
					if( isset($response_data['MerchantFree1']) && ( isset($response_data['MerchantFree2']) && 'acting_welcart_card' == $response_data['MerchantFree2'] ) ) {
						if( 'OK' == $response_data['ResponseCd'] ) {
							//会員登録・会員変更
							if( '4MemAdd' == $response_data['OperateId'] || '4MemChg' == $response_data['OperateId'] ) {
								$member = $usces->get_member();
								$usces->set_member_meta_value( 'wcpay_member_id', $response_data['KaiinId'], $member['ID'] );
								$usces->set_member_meta_value( 'wcpay_member_passwd', $response_data['KaiinPass'], $member['ID'] );

								$usces_entries = $usces->cart->get_entry();
								$cart = $usces->cart->get_cart();

								if( usces_have_continue_charge() ) {
									$chargingday = $usces->getItemChargingDay( $cart[0]['post_id'] );
									if( 99 == $chargingday ) {//受注日課金
										$OperateId = $acting_opts['operateid'];
									} else {
										$OperateId = '1Auth';
									}
								} else {
									$OperateId = $acting_opts['operateid'];
								}

/*								$home_url = str_replace( 'http://', 'https://', home_url('/') );
								$redirecturl = $home_url.'?page_id='.USCES_CART_NUMBER;
								$posturl = $home_url;

								$data_list = array();
								$data_list['OperateId'] = $OperateId;
								$data_list['MerchantPass'] = $acting_opts['merchant_pass'];
								$data_list['TransactionDate'] = $response_data['TransactionDate'];
								$data_list['MerchantFree1'] = $response_data['MerchantFree1'];
								$data_list['MerchantFree2'] = $response_data['MerchantFree2'];
								$data_list['MerchantFree3'] = $this->merchantfree3;
								$data_list['TenantId'] = $acting_opts['tenant_id'];
								$data_list['KaiinId'] = $response_data['KaiinId'];
								$data_list['KaiinPass'] = $response_data['KaiinPass'];
								$data_list['PayType'] = '01';
								$data_list['Amount'] = $usces_entries['order']['total_full_price'];
								$data_list['ProcNo'] = '0000000';
								$data_list['RedirectUrl'] = $redirecturl;
								//$data_list['PostUrl'] = $posturl;
//usces_log(print_r($data_list,true),"test.log");
								$data_query = http_build_query( $data_list );
								$encryptvalue = openssl_encrypt( $data_query, 'aes-128-cbc', $acting_opts['key_aes'], false, $acting_opts['key_iv'] );

								$param_list = array();
								$param_list['MerchantId'] = $acting_opts['merchant_id'];
								$param_list['EncryptValue'] = urlencode($encryptvalue);
								wp_redirect( add_query_arg( $param_list, $acting_opts['send_url_link'] ) );
*/
								$acting = $this->paymod_id.'_card';
								$param_list = array();
								$params = array();

								//共通部
								$param_list['MerchantId'] = $acting_opts['merchant_id'];
								$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
								$param_list['TransactionDate'] = $response_data['TransactionDate'];
								$param_list['MerchantFree1'] = $response_data['MerchantFree1'];
								$param_list['MerchantFree2'] = $response_data['MerchantFree2'];
								$param_list['MerchantFree3'] = $this->merchantfree3;
								$param_list['TenantId'] = $acting_opts['tenant_id'];
								$param_list['KaiinId'] = $response_data['KaiinId'];
								$param_list['KaiinPass'] = $response_data['KaiinPass'];
								$param_list['OperateId'] = $OperateId;
								$param_list['PayType'] = '01';
								$param_list['Amount'] = $usces_entries['order']['total_full_price'];
								$params['send_url'] = $acting_opts['send_url'];
								$params['param_list'] = $param_list;
								//e-SCOTT 決済
								$response_data = $this->connection( $params );
								$response_data['acting'] = $acting;
								$response_data['PayType'] = '01';

								if( 'OK' == $response_data['ResponseCd'] ) {
									$res = $usces->order_processing( $response_data );
									if( 'ordercompletion' == $res ) {
										$response_data['acting_return'] = 1;
										$response_data['result'] = 1;
										$response_data['nonce'] = wp_create_nonce( 'welcart_transaction' );
										wp_redirect( add_query_arg( $response_data, USCES_CART_URL ) );
									} else {
										//$response_data['acting_return'] = 0;
										//$response_data['result'] = 0;
										$logdata = array_merge( $usces_entries['order'], $response_data );
										$log = array( 'acting'=>$acting, 'key'=>$rand, 'result'=>'ORDER DATA REGISTERED ERROR', 'data'=>$logdata );
										usces_save_order_acting_error( $log );
										//wp_redirect( add_query_arg( $response_data, USCES_CART_URL ) );
										wp_redirect( add_query_arg( array( 'acting'=>$this->paymod_id.'_card', 'acting_return'=>0, 'result'=>0 ), USCES_CART_URL ) );
									}
								} else {
									//$response_data['acting_return'] = 0;
									//$response_data['result'] = 0;
									$responsecd = explode( '|', $response_data['ResponseCd'] );
									foreach( (array)$responsecd as $cd ) {
										$response_data[$cd] = $this->response_message( $cd );
									}
									$logdata = array_merge( $params, $response_data );
									$log = array( 'acting'=>$acting, 'key'=>$rand, 'result'=>$response_data['ResponseCd'], 'data'=>$logdata );
									usces_save_order_acting_error( $log );
									//wp_redirect( add_query_arg( $response_data, USCES_CART_URL ) );
									wp_redirect( add_query_arg( array( 'acting'=>$this->paymod_id.'_card', 'acting_return'=>0, 'result'=>0 ), USCES_CART_URL ) );
								}

							//決済
							} else {
								$res = $usces->order_processing( $response_data );
								if( 'ordercompletion' == $res ) {
									$response_data['acting'] = $this->paymod_id.'_card';
									$response_data['acting_return'] = 1;
									$response_data['result'] = 1;
									$response_data['nonce'] = wp_create_nonce( 'welcart_transaction' );
									wp_redirect( add_query_arg( $response_data, USCES_CART_URL ) );
									//wp_redirect( add_query_arg( array( 'acting'=>$this->paymod_id.'_card', 'acting_return'=>1, 'result'=>1 ), USCES_CART_URL ) );
								} else {
									$log = array( 'acting'=>$acting, 'key'=>$response_data['MerchantFree1'], 'result'=>'ORDER DATA REGISTERED ERROR', 'data'=>$response_data );
									usces_save_order_acting_error( $log );
									wp_redirect( add_query_arg( array( 'acting'=>$this->paymod_id.'_card', 'acting_return'=>0, 'result'=>0 ), USCES_CART_URL ) );
								}
							}

						} elseif( in_array( $response_data['ResponseCd'], $cancel ) ) {
//usces_log("cancel=".$response_data['ResponseCd'],"test.log");
							wp_redirect( add_query_arg( array( 'acting'=>$this->paymod_id.'_card', 'confirm'=>1 ), USCES_CART_URL ) );

						} else {
							$log = array( 'acting'=>$acting, 'key'=>$response_data['MerchantFree1'], 'result'=>'ORDER DATA REGISTERED ERROR', 'data'=>$response_data );
							usces_save_order_acting_error( $log );
							wp_redirect( add_query_arg( array( 'acting'=>$this->paymod_id.'_card', 'acting_return'=>0, 'result'=>0 ), USCES_CART_URL ) );
						}

					} else {
						//マイページからの会員登録・会員変更
						if( '4MemAdd' == $response_data['OperateId'] || '4MemChg' == $response_data['OperateId'] ) {
							if( 'OK' == $response_data['ResponseCd'] ) {
								$member = $usces->get_member();
								$usces->set_member_meta_value( 'wcpay_member_id', $response_data['KaiinId'], $member['ID'] );
								$usces->set_member_meta_value( 'wcpay_member_passwd', $response_data['KaiinPass'], $member['ID'] );

							} elseif( in_array( $response_data['ResponseCd'], $cancel ) ) {

							} else {
								usces_log('[WelcartPay] 4MemChg NG : '.print_r($response_data,true), 'acting_transaction.log');
							}
							wp_redirect( USCES_MEMBER_URL );
						}
					}
					exit();
				}
			}
		}
	}

	/**********************************************
	* 管理画面決済処理
	* @param  -
	* @return -
	***********************************************/
	public function admin_ajax() {
		global $usces;

		switch( $_POST['mode'] ) {
		//取引参照
		case 'get_welcartpay_card':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$order_num = ( isset($_POST['order_num']) ) ? $_POST['order_num'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			if( empty($order_id) || empty($order_num) || empty($trans_id) ) {
				die("NG#usces#");
			}
			$res = '';
			$log_data = array();
			if( $trans_id == '9999999999' ) {
				$member_id = ( isset($_POST['member_id']) ) ? $_POST['member_id'] : '';
				$response_member = $this->escott_member_reference( $member_id );//e-SCOTT 会員照会
				if( 'OK' == $response_member['ResponseCd'] ) {
					$order_data = $usces->get_order_data( $order_id, 'direct' );
					$total_full_price = $order_data['order_item_total_price'] - $order_data['order_usedpoint'] + $order_data['order_discount'] + $order_data['order_shipping_charge'] + $order_data['order_cod_fee'] + $order_data['order_tax'];
					$res .= '<div class="welcart-settlement-admin card-new">'.__('New','usces').'</div>';
					$res .= '<table class="welcart-settlement-admin-table">';
					$res .= '<tr><th>'.__('Spending amount','usces').'</th>
						<td><input type="text" id="amount_change" value="'.$total_full_price.'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$total_full_price.'" /></td>
						</tr>';
					$res .= '</table>';
					$res .= '<div class="welcart-settlement-admin-button">';
					$res .= '<input id="auth-settlement" type="button" class="button" value="'.__('Credit','usces').'" />';//与信
					$res .= '<input id="gathering-settlement" type="button" class="button" value="'.__('Credit sales','usces').'" />';//与信売上計上
					$res .= '</div>';
				} else {
					$res .= '<div class="welcart-settlement-admin card-error">'.__('Error','usces').'</div>';
					$res .= '<div class="welcart-settlement-admin-error">';
					$res .= '<div><span class="message">'.__('Credit card information not registered','usces').'</span></div>';//カード情報未登録
					$res .= '</div>';
				}
				die("OK#usces#".$res);
			} else {
				if( $order_num == '1' ) {
					$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_card', $order_id ) );
				} else {
					$log_data = $this->get_acting_log( $order_id, $order_id.'_'.$trans_id );
					$acting_data = usces_unserialize($log_data[0]['log']);
				}
				$operateid = ( isset($acting_data['OperateId']) ) ? $acting_data['OperateId'] : $this->get_acting_first_operateid( $order_id.'_'.$trans_id );
				$acting_opts = $this->get_acting_settings();
				$TransactionDate = $this->get_transaction_date();
				$param_list = array();
				$params = array();
				$param_list['MerchantId'] = $acting_opts['merchant_id'];
				$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
				$param_list['TransactionDate'] = $TransactionDate;
				$param_list['MerchantFree1'] = $trans_id;
				$param_list['MerchantFree2'] = 'acting_welcart_card';
				$param_list['MerchantFree3'] = $this->merchantfree3;
				$param_list['TenantId'] = $acting_opts['tenant_id'];
				$params['send_url'] = $acting_opts['send_url'];
				$params['param_list'] = array_merge( $param_list,
					array(
						'OperateId' => '1Search',
						'ProcessId' => $acting_data['ProcessId'],
						'ProcessPass' => $acting_data['ProcessPass']
					)
				);
				$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
				if( 'OK' == $response_data['ResponseCd'] ) {
					$latest_log = $this->get_acting_latest_log( $order_id.'_'.$trans_id );
					if( isset($latest_log['OperateId']) ) {
						$class = ' card-'.mb_strtolower(substr($latest_log['OperateId'],1));
						$status_name = $this->get_operate_name( $latest_log['OperateId'] );
						$res .= '<div class="welcart-settlement-admin'.$class.'">'.$status_name.'</div>';
						$res .= '<table class="welcart-settlement-admin-table">';
						if( isset($response_data['Amount']) ) {
							$res .= '<tr><th>'.__('Spending amount','usces').'</th>
								<td><input type="text" id="amount_change" value="'.$response_data['Amount'].'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$response_data['Amount'].'" /></td>
								</tr>';
						}
						if( isset($response_data['SalesDate']) ) {
							$res .= '<tr><th>'.__('Recorded sales date','usces').'</th><td>'.$response_data['SalesDate'].'</td></tr>';
						}
						$res .= '</table>';
						$res .= '<div class="welcart-settlement-admin-button">';
						if( '1Delete' == $latest_log['OperateId'] ) {
							$res .= '<input id="reauth-settlement" type="button" class="button" value="'.__('Re-authorization','usces').'" />';//再オーソリ
						} else {
							if( '1Auth' == $operateid && '1Capture' != $latest_log['OperateId'] ) {
								$res .= '<input id="reauth-settlement" type="button" class="button" value="'.__('Re-authorization','usces').'" />';//再オーソリ
								$res .= '<input id="capture-settlement" type="button" class="button" value="'.__('Sales recorded','usces').'" />';//売上計上
							}
							if( '1Delete' != $latest_log['OperateId'] ) {
								$res .= '<input id="delete-settlement" type="button" class="button" value="'.__('Unregister','usces').'" />';//取消
							}
							if( '1Change' != $latest_log['OperateId'] ) {
								$res .= '<input id="change-settlement" type="button" class="button" value="'.__('Change spending amount','usces').'" />';//利用額変更
							}
						}
						$res .= '</div>';
					}
				} else {
					$res .= '<div class="welcart-settlement-admin card-error">'.__('Error','usces').'</div>';
					$res .= '<div class="welcart-settlement-admin-error">';
					$responsecd = explode( '|', $response_data['ResponseCd'] );
					foreach( (array)responsecd as $cd ) {
						$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
					}
					$res .= '</div>';
					usces_log('[WelcartPay] 1Search connection NG : '.print_r($response_data,true), 'acting_transaction.log');
				}
				$res .= $this->settlement_history( $order_id.'_'.$trans_id );
				die($response_data['ResponseCd']."#usces#".$res);
			}
			break;

		//売上計上
		case 'capture_welcartpay_card':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$order_num = ( isset($_POST['order_num']) ) ? $_POST['order_num'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			$amount = ( isset($_POST['amount']) ) ? $_POST['amount'] : '';
			if( empty($order_id) || empty($order_num) || empty($trans_id) ) {
				die("NG#usces#");
			}
			$res = '';
			$status = '';
			$log_data = array();
			if( $order_num == '1' ) {
				$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_card', $order_id ) );
			} else {
				$log_data = $this->get_acting_log( $order_id, $order_id.'_'.$trans_id );
				$acting_data = usces_unserialize($log_data[0]['log']);
			}
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_card';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$member_id = ( isset($_POST['member_id']) ) ? $_POST['member_id'] : '';
			$response_member = $this->escott_member_reference( $member_id );//e-SCOTT 会員照会
			if( 'OK' == $response_member['ResponseCd'] ) {
				$param_list['KaiinId'] = $response_member['KaiinId'];
				$param_list['KaiinPass'] = $response_member['KaiinPass'];
			}
			$params['send_url'] = $acting_opts['send_url'];
			$params['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '1Capture',
					'ProcessId' => $acting_data['ProcessId'],
					'ProcessPass' => $acting_data['ProcessPass'],
					'SalesDate' => $TransactionDate
				)
			);
			$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
			if( 'OK' == $response_data['ResponseCd'] ) {
				$class = ' card-'.mb_strtolower(substr($response_data['OperateId'],1));
				$status_name = $this->get_operate_name( $response_data['OperateId'] );
				$res .= '<div class="welcart-settlement-admin'.$class.'">'.$status_name.'</div>';
				$res .= '<table class="welcart-settlement-admin-table">';
				$res .= '<tr><th>'.__('Spending amount','usces').'</th>
					<td><input type="text" id="amount_change" value="'.$amount.'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$amount.'" /></td>
					</tr>';
				if( isset($response_data['SalesDate']) ) {
					$res .= '<tr><th>'.__('Recorded sales date','usces').'</th><td>'.$response_data['SalesDate'].'</td></tr>';
				}
				$res .= '</table>';
				$res .= '<div class="welcart-settlement-admin-button">';
				$res .= '<input id="delete-settlement" type="button" class="button" value="'.__('Unregister','usces').'" />';//取消
				$res .= '<input id="change-settlement" type="button" class="button" value="'.__('Change spending amount','usces').'" />';//利用額変更
				$res .= '</div>';
				$status = '<span class="acting-status'.$class.'">'.$status_name.'</span>';
			} else {
				$res .= '<div class="welcart-settlement-admin card-error">'.__('Error','usces').'</div>';
				$res .= '<div class="welcart-settlement-admin-error">';
				$responsecd = explode( '|', $response_data['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
				}
				$res .= '</div>';
				usces_log('[WelcartPay] 1Capture connection NG : '.print_r($response_data,true), 'acting_transaction.log');
			}
			do_action( 'usces_action_admin_'.$_POST['mode'], $response_data, $order_id, $trans_id );
			$this->save_acting_history_log( $response_data, $order_id.'_'.$trans_id );
			$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			die($response_data['ResponseCd']."#usces#".$res."#usces#".$status);
			break;

		//取消/返品
		case 'delete_welcartpay_card':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$order_num = ( isset($_POST['order_num']) ) ? $_POST['order_num'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			$amount = ( isset($_POST['amount']) ) ? $_POST['amount'] : '';
			if( empty($order_id) || empty($order_num) || empty($trans_id) ) {
				die("NG#usces#");
			}
			$res = '';
			$status = '';
			$log_data = array();
			if( $order_num == '1' ) {
				$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_card', $order_id ) );
			} else {
				$log_data = $this->get_acting_log( $order_id, $order_id.'_'.$trans_id );
				$acting_data = usces_unserialize($log_data[0]['log']);
			}
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_card';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$member_id = ( isset($_POST['member_id']) ) ? $_POST['member_id'] : '';
			$response_member = $this->escott_member_reference( $member_id );//e-SCOTT 会員照会
			if( 'OK' == $response_member['ResponseCd'] ) {
				$param_list['KaiinId'] = $response_member['KaiinId'];
				$param_list['KaiinPass'] = $response_member['KaiinPass'];
			}
			$params['send_url'] = $acting_opts['send_url'];
			$params['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '1Delete',
					'ProcessId' => $acting_data['ProcessId'],
					'ProcessPass' => $acting_data['ProcessPass']
				)
			);
			$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
			if( 'OK' == $response_data['ResponseCd'] ) {
				$class = ' card-'.mb_strtolower(substr($response_data['OperateId'],1));
				$status_name = $this->get_operate_name( $response_data['OperateId'] );
				$res .= '<div class="welcart-settlement-admin'.$class.'">'.$status_name.'</div>';
				$res .= '<table class="welcart-settlement-admin-table">';
				$res .= '<tr><th>'.__('Spending amount','usces').'</th>
					<td><input type="text" id="amount_change" value="'.$amount.'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$amount.'" /></td>
					</tr>';
				$res .= '</table>';
				$res .= '<div class="welcart-settlement-admin-button">';
				$res .= '<input id="reauth-settlement" type="button" class="button" value="'.__('Re-authorization','usces').'" />';//再オーソリ
				$res .= '</div>';
				$status = '<span class="acting-status'.$class.'">'.$status_name.'</span>';
			} else {
				$res .= '<div class="welcart-settlement-admin card-error">'.__('Error','usces').'</div>';
				$res .= '<div class="welcart-settlement-admin-error">';
				$responsecd = explode( '|', $response_data['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
				}
				$res .= '</div>';
				usces_log('[WelcartPay] 1Delete connection NG : '.print_r($response_data,true), 'acting_transaction.log');
			}
			do_action( 'usces_action_admin_'.$_POST['mode'], $response_data, $order_id, $trans_id );
			$this->save_acting_history_log( $response_data, $order_id.'_'.$trans_id );
			$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			die($response_data['ResponseCd']."#usces#".$res."#usces#".$status);
			break;

		//利用額変更
		case 'change_welcartpay_card':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$order_num = ( isset($_POST['order_num']) ) ? $_POST['order_num'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			$amount = ( isset($_POST['amount']) ) ? $_POST['amount'] : '';
			if( empty($order_id) || empty($order_num) || empty($trans_id) || $amount == '' ) {
				die("NG#usces#");
			}
			$res = '';
			$log_data = array();
			if( $order_num == '1' ) {
				$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_card', $order_id ) );
			} else {
				$log_data = $this->get_acting_log( $order_id, $order_id.'_'.$trans_id );
				$acting_data = usces_unserialize($log_data[0]['log']);
			}
			$operateid = ( isset($acting_data['OperateId']) ) ? $acting_data['OperateId'] : $this->get_acting_first_operateid( $order_id.'_'.$trans_id );
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_card';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$member_id = ( isset($_POST['member_id']) ) ? $_POST['member_id'] : '';
			$response_member = $this->escott_member_reference( $member_id );//e-SCOTT 会員照会
			if( 'OK' == $response_member['ResponseCd'] ) {
				$param_list['KaiinId'] = $response_member['KaiinId'];
				$param_list['KaiinPass'] = $response_member['KaiinPass'];
			}
			$params['send_url'] = $acting_opts['send_url'];
			$params['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '1Change',
					'ProcessId' => $acting_data['ProcessId'],
					'ProcessPass' => $acting_data['ProcessPass'],
					'Amount' => $amount
				)
			);
			$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
			if( 'OK' == $response_data['ResponseCd'] ) {
				$class = ' card-'.mb_strtolower(substr($operateid,1));
				$status_name = $this->get_operate_name( $operateid );
				$res .= '<div class="welcart-settlement-admin'.$class.'">'.$status_name.'</div>';
				$res .= '<table class="welcart-settlement-admin-table">';
				$res .= '<tr><th>'.__('Spending amount','usces').'</th>
					<td><input type="text" id="amount_change" value="'.$amount.'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$amount.'" /></td>
					</tr>';
				if( isset($response_data['SalesDate']) ) {
					$res .= '<tr><th>'.__('Recorded sales date','usces').'</th><td>'.$response_data['SalesDate'].'</td></tr>';
				}
				$res .= '</table>';
				$res .= '<div class="welcart-settlement-admin-button">';
				if( '1Gathering' != $operateid ) {
					$res .= '<input id="capture-settlement" type="button" class="button" value="'.__('Sales recorded','usces').'" />';//売上計上
				}
				$res .= '<input id="delete-settlement" type="button" class="button" value="'.__('Unregister','usces').'" />';//取消
				$res .= '<input id="change-settlement" type="button" class="button" value="'.__('Change spending amount','usces').'" />';//利用額変更
				$res .= '</div>';
			} else {
				$res .= '<div class="welcart-settlement-admin card-error">'.__('Error','usces').'</div>';//エラー
				$res .= '<div class="welcart-settlement-admin-error">';
				$responsecd = explode( '|', $response_data['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
				}
				$res .= '</div>';
				usces_log('[WelcartPay] 1Change connection NG : '.print_r($response_data,true), 'acting_transaction.log');
			}
			do_action( 'usces_action_admin_'.$_POST['mode'], $response_data, $order_id, $trans_id );
			$this->save_acting_history_log( $response_data, $order_id.'_'.$trans_id );
			$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			die($response_data['ResponseCd']."#usces#".$res);
			break;

		//与信
		case 'auth_welcartpay_card':
		//与信売上計上
		case 'gathering_welcartpay_card':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$order_num = ( isset($_POST['order_num']) ) ? $_POST['order_num'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			$amount = ( isset($_POST['amount']) ) ? $_POST['amount'] : '';
			if( empty($order_id) || empty($order_num) || empty($trans_id) || $amount == '' ) {
				die("NG#usces#");
			}
			$res = '';
			$status = '';
			$log_data = array();
			if( $trans_id == '9999999999' ) {
				$trans_id = usces_acting_key();
			} else {
				if( $order_num == '1' ) {
					$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_card', $order_id ) );
				} else {
					$log_data = $this->get_acting_log( $order_id, $order_id.'_'.$trans_id );
					$acting_data = usces_unserialize($log_data[0]['log']);
				}
			}
			$operateid = ( 'auth_welcartpay_card' == $_POST['mode'] ) ? '1Auth' : '1Gathering';
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_card';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$member_id = ( isset($_POST['member_id']) ) ? $_POST['member_id'] : '';
			$response_member = $this->escott_member_reference( $member_id );//e-SCOTT 会員照会
			if( 'OK' == $response_member['ResponseCd'] ) {
				$params['send_url'] = $acting_opts['send_url'];
				$params['param_list'] = array_merge( $param_list,
					array(
						'KaiinId' => $response_member['KaiinId'],
						'KaiinPass' => $response_member['KaiinPass'],
						'OperateId' => $operateid,
						'PayType' => '01',
						'Amount' => $amount
					)
				);
				$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
				if( 'OK' == $response_data['ResponseCd'] ) {
					if( $order_num == '1' ) {
						$cardlast4 = substr($response_member['CardNo'], -4);
						$expyy = substr(date_i18n('Y', current_time('timestamp')), 0, 2).substr($response_member['CardExp'], 0, 2);
						$expmm = substr($response_member['CardExp'], 2, 2);
						$response_data['acting'] = $this->paymod_id.'_card';
						$response_data['CardNo'] = $cardlast4;
						$response_data['CardExp'] = $expyy.'/'.$expmm;
						$usces->set_order_meta_value( 'acting_welcart_card', usces_serialize($response_data), $order_id );
						$usces->set_order_meta_value( 'trans_id', $trans_id, $order_id );
						$usces->set_order_meta_value( 'wc_trans_id', $trans_id, $order_id );
					} else {
						if( $log_data ) {
							$this->update_acting_log( $response_data, $order_id.'_'.$trans_id );
						}
					}

					$class = ' card-'.mb_strtolower(substr($operateid,1));
					$status_name = $this->get_operate_name( $operateid );
					$res .= '<div class="welcart-settlement-admin'.$class.'">'.$status_name.'</div>';
					$res .= '<table class="welcart-settlement-admin-table">';
					$res .= '<tr><th>'.__('Spending amount','usces').'</th>
						<td><input type="text" id="amount_change" value="'.$amount.'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$amount.'" /></td>
						</tr>';
					$res .= '</table>';
					$res .= '<div class="welcart-settlement-admin-button">';
					if( '1Gathering' != $operateid ) {
						$res .= '<input id="capture-settlement" type="button" class="button" value="'.__('Sales recorded','usces').'" />';//売上計上
					}
					$res .= '<input id="delete-settlement" type="button" class="button" value="'.__('Unregister','usces').'" />';//取消
					$res .= '<input id="change-settlement" type="button" class="button" value="'.__('Change spending amount','usces').'" />';//利用額変更
					$res .= '</div>';
					$status = '<span class="acting-status'.$class.'">'.$status_name.'</span>';
				} else {
					$res .= '<div class="welcart-settlement-admin card-error">'.__('Error','usces').'</div>';//エラー
					$res .= '<div class="welcart-settlement-admin-error">';
					$responsecd = explode( '|', $response_data['ResponseCd'] );
					foreach( (array)$responsecd as $cd ) {
						$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
					}
					$res .= '</div>';
					usces_log('[WelcartPay] '.$operateid.' connection NG : '.print_r($response_data,true), 'acting_transaction.log');
				}
				do_action( 'usces_action_admin_'.$_POST['mode'], $response_data, $order_id, $trans_id );
				$this->save_acting_history_log( $response_data, $order_id.'_'.$trans_id );
				$res .= $this->settlement_history( $order_id.'_'.$trans_id );
				die($response_data['ResponseCd']."#usces#".$res."#usces#".$status);
			} else {
				$res .= '<div class="welcart-settlement-admin card-error">'.__('Error','usces').'</div>';//エラー
				$res .= '<div class="welcart-settlement-admin-error">';
				$responsecd = explode( '|', $response_member['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
				}
				$res .= '</div>';
				usces_log('[WelcartPay] 4MemRefM connection NG : '.print_r($response_member,true), 'acting_transaction.log');
				die($response_member['ResponseCd']."#usces#".$res);
			}
			break;

		//再オーソリ
		case 'reauth_welcartpay_card':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$res = '';
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$order_num = ( isset($_POST['order_num']) ) ? $_POST['order_num'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			$amount = ( isset($_POST['amount']) ) ? $_POST['amount'] : '';
			if( empty($order_id) || empty($order_num) || empty($trans_id) || $amount == '' ) {
				die("NG#usces#");
			}
			$res = '';
			$status = '';
			if( $order_num == '1' ) {
				$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_card', $order_id ) );
			} else {
				$log_data = $this->get_acting_log( $order_id, $order_id.'_'.$trans_id );
				$acting_data = usces_unserialize($log_data[0]['log']);
			}
			$operateid = ( isset($acting_data['OperateId']) ) ? $acting_data['OperateId'] : $this->get_acting_first_operateid( $order_id.'_'.$trans_id );
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_card';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$member_id = ( isset($_POST['member_id']) ) ? $_POST['member_id'] : '';
			$response_member = $this->escott_member_reference( $member_id );//e-SCOTT 会員照会
			if( 'OK' == $response_member['ResponseCd'] ) {
				$param_list['KaiinId'] = $response_member['KaiinId'];
				$param_list['KaiinPass'] = $response_member['KaiinPass'];
			}
			if( '1Gathering' == $operateid ) {
				$param_list['SalesDate'] = $TransactionDate;
			}
			$params['send_url'] = $acting_opts['send_url'];
			$params['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '1ReAuth',
					'ProcessId' => $acting_data['ProcessId'],
					'ProcessPass' => $acting_data['ProcessPass'],
					'Amount' => $amount
				)
			);
			$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
			if( 'OK' == $response_data['ResponseCd'] ) {
				$acting_data['TransactionId'] = $response_data['TransactionId'];
				$acting_data['TransactionDate'] = $response_data['TransactionDate'];
				$acting_data['ProcessId'] = $response_data['ProcessId'];
				$acting_data['ProcessPass'] = $response_data['ProcessPass'];
				$usces->set_order_meta_value( 'acting_welcart_card', usces_serialize($acting_data), $order_id );

				$class = ' card-'.mb_strtolower(substr($operateid,1));
				$status_name = $this->get_operate_name( $operateid );
				$res .= '<div class="welcart-settlement-admin'.$class.'">'.$status_name.'</div>';
				$res .= '<table class="welcart-settlement-admin-table">';
				$res .= '<tr><th>'.__('Spending amount','usces').'</th>
					<td><input type="text" id="amount_change" value="'.$amount.'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$amount.'" /></td>
					</tr>';
				if( isset($response_data['SalesDate']) ) {
					$res .= '<tr><th>'.__('Recorded sales date','usces').'</th><td>'.$response_data['SalesDate'].'</td></tr>';
				}
				$res .= '</table>';
				$res .= '<div class="welcart-settlement-admin-button">';
				if( '1Gathering' != $operateid ) {
					$res .= '<input id="capture-settlement" type="button" class="button" value="'.__('Sales recorded','usces').'" />';//売上計上
				}
				$res .= '<input id="delete-settlement" type="button" class="button" value="'.__('Unregister','usces').'" />';//取消
				$res .= '<input id="change-settlement" type="button" class="button" value="'.__('Change spending amount','usces').'" />';//利用額変更
				$res .= '</div>';
				$status = '<span class="acting-status'.$class.'">'.$status_name.'</span>';
			} else {
				$res .= '<div class="welcart-settlement-admin card-error">'.__('Error','usces').'</div>';//エラー
				$res .= '<div class="welcart-settlement-admin-error">';
				$responsecd = explode( '|', $response_data['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
				}
				$res .= '</div>';
				usces_log('[WelcartPay] 1ReAuth connection NG : '.print_r($response_data,true), 'acting_transaction.log');
			}
			do_action( 'usces_action_admin_'.$_POST['mode'], $response_data, $order_id, $trans_id );
			$this->save_acting_history_log( $response_data, $order_id.'_'.$trans_id );
			$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			die($response_data['ResponseCd']."#usces#".$res."#usces#".$status);
			break;

		//決済エラー
		case 'error_welcartpay_card':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$order_num = ( isset($_POST['order_num']) ) ? $_POST['order_num'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			if( empty($order_id) || empty($order_num) || empty($trans_id) ) {
				die("NG#usces#");
			}
			$member_id = ( isset($_POST['member_id']) ) ? $_POST['member_id'] : '';
			$response_member = $this->escott_member_reference( $member_id );//e-SCOTT 会員照会
			if( 'OK' == $response_member['ResponseCd'] ) {
				$order_data = $usces->get_order_data( $order_id, 'direct' );
				$total_full_price = $order_data['order_item_total_price'] - $order_data['order_usedpoint'] + $order_data['order_discount'] + $order_data['order_shipping_charge'] + $order_data['order_cod_fee'] + $order_data['order_tax'];
				$res .= '<div class="welcart-settlement-admin card-error">'.__('Repayment','usces').'</div>';//再決済
				$res .= '<table class="welcart-settlement-admin-table">';
				$res .= '<tr><th>'.__('Spending amount','usces').'</th>
					<td><input type="text" id="amount_change" value="'.$total_full_price.'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$total_full_price.'" /></td>
					</tr>';
				$res .= '</table>';
				$res .= '<div class="welcart-settlement-admin-button">';
				$res .= '<input id="auth-settlement" type="button" class="button" value="'.__('Credit','usces').'" />';//与信
				$res .= '<input id="gathering-settlement" type="button" class="button" value="'.__('Credit sales','usces').'" />';//与信売上計上
				$res .= '</div>';
				$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			} else {
				$res .= '<div class="welcart-settlement-admin card-error">'.__('Settlement error','usces').'</div>';//エラー
				$res .= '<div class="welcart-settlement-admin-error">';
				$res .= '<div><span class="message">'.__('Credit card information not registered','usces').'</span></div>';//カード情報未登録
				$res .= '</div>';
			}
			die("OK#usces#".$res);
			break;

		//継続課金情報更新
		case 'continuation_update':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$res = '';
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$member_id = ( isset($_POST['member_id']) ) ? $_POST['member_id'] : '';
			$contracted_year = ( isset($_POST['contracted_year']) ) ? $_POST['contracted_year'] : '';
			$contracted_month = ( isset($_POST['contracted_month']) ) ? $_POST['contracted_month'] : '';
			$contracted_day = ( isset($_POST['contracted_day']) ) ? $_POST['contracted_day'] : '';
			$charged_year = ( isset($_POST['charged_year']) ) ? $_POST['charged_year'] : '';
			$charged_month = ( isset($_POST['charged_month']) ) ? $_POST['charged_month'] : '';
			$charged_day = ( isset($_POST['charged_day']) ) ? $_POST['charged_day'] : '';
			$price = ( isset($_POST['price']) ) ? $_POST['price'] : 0;
			$status = ( isset($_POST['status']) ) ? $_POST['status'] : '';

			if( version_compare( WCEX_DLSELLER_VERSION, '3.0-beta', '<=' ) ) {
				$continue_data = usces_unserialize( $usces->get_member_meta_value( 'continuepay_'.$order_id, $member_id ) );
			} else {
				$continue_data = $this->get_continuation_data( $order_id, $member_id );
			}
			if( !$continue_data ) {
				die("NG#usces#");
			}

			//継続中→停止
			if( $continue_data['status'] == 'continuation' && $status == 'cancellation' ) {
				if( version_compare( WCEX_DLSELLER_VERSION, '3.0-beta', '<=' ) ) {
					$continue_data['status'] = 'cancellation';
					$usces->set_member_meta_value( 'continuepay_'.$order_id, usces_serialize($continue_data), $member_id );
				} else {
					$this->update_continuation_data( $order_id, $member_id, $continue_data, true );
				}

			} else {
				if( !empty($contracted_year) && !empty($contracted_month) && !empty($contracted_day) ) {
					$contracted_date = ( empty($continue_data['contractedday']) ) ? dlseller_next_contracting( $order_id ) : $continue_data['contractedday'];
					if( $contracted_date ) {
						$new_contracted_date = $contracted_year.'-'.$contracted_month.'-'.$contracted_day;
						if( !$this->isdate($new_contracted_date) ) {
							die("NG#usces#".__('Next contract renewal date is incorrect.','dlseller'));
						}
					}
				} else {
					$new_contracted_date = '';
				}
				$new_charged_date = $charged_year.'-'.$charged_month.'-'.$charged_day;
				if( !$this->isdate($new_charged_date) ) {
					die("NG#usces#".__('Next settlement date is incorrect.','dlseller'));
				}
				$charged_date = ( empty($continue_data['chargedday']) ) ? dlseller_next_charging( $order_id ) : $continue_data['chargedday'];
				if( $new_charged_date < $charged_date ) {
					die("NG#usces#".sprintf(__("The next settlement date must be after %s.",'dlseller'), $charged_date));
				}
				$continue_data['contractedday'] = $new_contracted_date;
				$continue_data['chargedday'] = $new_charged_date;
				$continue_data['price'] = usces_crform( $price, false, false, 'return', false );
				$continue_data['status'] = $status;
				if( version_compare( WCEX_DLSELLER_VERSION, '3.0-beta', '<=' ) ) {
					$usces->set_member_meta_value( 'continuepay_'.$order_id, usces_serialize($continue_data), $member_id );
				} else {
					$this->update_continuation_data( $order_id, $member_id, $continue_data );
				}
			}
			die("OK#usces#");
			break;

		//オンライン収納代行データ登録
		case 'add_welcartpay_conv':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			$paylimit = ( isset($_POST['paylimit']) ) ? $_POST['paylimit'] : '';
			$amount = ( isset($_POST['amount']) ) ? $_POST['amount'] : '';
			if( empty($order_id) || empty($trans_id) || $paylimit == '' || $amount == '' ) {
				die("NG#usces#");
			}
			$res = '';
			$status = '';
			$order_data = $usces->get_order_data( $order_id, 'direct' );
			$NameKanji = urlencode( $order_data['order_name1'].$order_data['order_name2'] );
			$NameKana = ( !empty($order_data['order_name3']) ) ? urlencode( $order_data['order_name3'].$order_data['order_name4'] ) : $NameKanji;
			$TelNo = urlencode( $order_data['order_tel'] );
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_conv';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$params['send_url'] = $acting_opts['send_url_conv'];
			$params['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '2Add',
					'PayLimit' => $paylimit.'2359',
					'Amount' => $amount,
					'NameKanji' => $NameKanji,
					'NameKana' => $NameKana,
					'TelNo' => $TelNo,
					'ReturnURL' => urlencode( home_url('/') )
				)
			);
			$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
			if( 'OK' == $response_data['ResponseCd'] ) {
				$response_data['acting'] = 'welcart_conv';
				$response_data['PayLimit'] = $params['param_list']['PayLimit'];
				$response_data['Amount'] = $params['param_list']['Amount'];
				$usces->set_order_meta_value( 'acting_welcart_conv', usces_serialize($response_data), $order_id );
				$FreeArea = trim($response_data['FreeArea']);
				$url = add_query_arg( array( 'code'=>$FreeArea, 'rkbn'=>2 ), $acting_opts['redirect_url_conv'] );
				$usces->set_order_meta_value( 'welcart_conv_url', $url, $order_id );

				$res .= '<div class="welcart-settlement-admin conv-noreceipt">'.__('Unpaid','usces').'</div>';//未入金
				$res .= '<table class="welcart-settlement-admin-table">';
				$res .= '<tr><th>'.__('Payment due','usces').'</th>
					<td><input type="text" id="paylimit_change" value="'.$paylimit.'" style="ime-mode:disabled" size="10" /><input type="hidden" id="paylimit" value="'.$paylimit.'" /></td>
					</tr>';
				$res .= '<tr><th>'.__('Payment amount','usces').'</th>
					<td><input type="text" id="amount_change" value="'.$amount.'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$amount.'" /></td>
					</tr>';
				$res .= '</table>';
				$res .= '<div class="welcart-settlement-admin-button">';
				$res .= '<input id="delete-settlement" type="button" class="button" value="'.__('Unregister','usces').'" />';//取消
				$res .= '<input id="change-settlement" type="button" class="button" value="'.__('Change').'" />';//変更
				$res .= '</div>';
				$status = '<span class="acting-status conv-noreceipt">'.__('Unpaid','usces').'</span>';
			} else {
				$res .= '<div class="welcart-settlement-admin conv-error">'.__('Error','usces').'</div>';//エラー
				$res .= '<div class="welcart-settlement-admin-error">';
				$responsecd = explode( '|', $response_data['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
				}
				$res .= '</div>';
				usces_log('[WelcartPay] 2Add connection NG : '.print_r($response_data,true), 'acting_transaction.log');
			}
			do_action( 'usces_action_admin_'.$_POST['mode'], $response_data, $order_id, $trans_id );
			$this->save_acting_history_log( $response_data, $order_id.'_'.$trans_id );
			$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			die($response_data['ResponseCd']."#usces#".$res."#usces#".$status);
			break;

		//オンライン収納代行データ変更
		case 'change_welcartpay_conv':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			$paylimit = ( isset($_POST['paylimit']) ) ? $_POST['paylimit'] : '';
			$amount = ( isset($_POST['amount']) ) ? $_POST['amount'] : '';
			if( empty($order_id) || empty($trans_id) || $paylimit == '' || $amount == '' ) {
				die("NG#usces#");
			}
			$res = '';
			$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_conv', $order_id ) );
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_conv';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$params['send_url'] = $acting_opts['send_url_conv'];
			$params['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '2Chg',
					'ProcessId' => $acting_data['ProcessId'],
					'ProcessPass' => $acting_data['ProcessPass'],
					'PayLimit' => $paylimit.'2359',
					'Amount' => $amount
				)
			);
			$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
			if( 'OK' == $response_data['ResponseCd'] ) {
				$acting_data['PayLimit'] = $params['param_list']['PayLimit'];
				$acting_data['Amount'] = $params['param_list']['Amount'];
				$usces->set_order_meta_value( 'acting_welcart_conv', usces_serialize($acting_data), $order_id );
				$FreeArea = trim($response_data['FreeArea']);
				$url = add_query_arg( array( 'code'=>$FreeArea, 'rkbn'=>2 ), $acting_opts['redirect_url_conv'] );
				$usces->set_order_meta_value( 'welcart_conv_url', $url, $order_id );

				$res .= '<div class="welcart-settlement-admin conv-noreceipt">'.__('Unpaid','usces').'</div>';//未入金
				$res .= '<table class="welcart-settlement-admin-table">';
				if( isset($acting_data['PayLimit']) ) {
					$res .= '<tr><th>'.__('Payment due','usces').'</th><td>'.$acting_data['PayLimit'].'</td></tr>';
				}
				if( isset($acting_data['Amount']) ) {
					$res .= '<tr><th>'.__('Payment amount','usces').'</th><td>'.$acting_data['Amount'].'</td></tr>';
				}
				$res .= '</table>';
				$res .= '<div class="welcart-settlement-admin-button">';
				$res .= '<input id="delete-settlement" type="button" class="button" value="'.__('Unregister','usces').'" />';//取消
				$res .= '</div>';
			} else {
				$res .= '<div class="welcart-settlement-admin conv-error">'.__('Error','usces').'</div>';//エラー
				$res .= '<div class="welcart-settlement-admin-error">';
				$responsecd = explode( '|', $response_data['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
				}
				$res .= '</div>';
				usces_log('[WelcartPay] 2Chg connection NG : '.print_r($response_data,true), 'acting_transaction.log');
			}
			do_action( 'usces_action_admin_'.$_POST['mode'], $response_data, $order_id, $trans_id );
			$this->save_acting_history_log( $response_data, $order_id.'_'.$trans_id );
			$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			die($response_data['ResponseCd']."#usces#".$res);
			break;

		//オンライン収納代行データ削除
		case 'delete_welcartpay_conv':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			if( empty($order_id) || empty($trans_id) ) {
				die("NG#usces#");
			}
			$res = '';
			$status = '';
			$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_conv', $order_id ) );
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_conv';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$params['send_url'] = $acting_opts['send_url_conv'];
			$params['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '2Del',
					'ProcessId' => $acting_data['ProcessId'],
					'ProcessPass' => $acting_data['ProcessPass']
				)
			);
			$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
			if( 'OK' == $response_data['ResponseCd'] ) {
				$res .= '<div class="welcart-settlement-admin conv-del">'.__('Canceled','usces').'</div>';//取消済み
				$res .= '<table class="welcart-settlement-admin-table">';
				if( isset($acting_data['PayLimit']) ) {
					$paylimit = substr($acting_data['PayLimit'],0,8);
					$res .= '<tr><th>'.__('Payment due','usces').'</th><td>'.$paylimit.'</td></tr>';
					//$res .= '<tr><th>'.__('Payment due','usces').'</th>
					//	<td><input type="text" id="paylimit_change" value="'.$paylimit.'" style="ime-mode:disabled" size="10" /><input type="hidden" id="paylimit" value="'.$paylimit.'" /></td>
					//	</tr>';
				}
				if( isset($acting_data['Amount']) ) {
					$res .= '<tr><th>'.__('Payment amount','usces').'</th><td>'.$acting_data['Amount'].'</td></tr>';
					//$res .= '<tr><th>'.__('Payment amount','usces').'</th>
					//	<td><input type="text" id="amount_change" value="'.$acting_data['Amount'].'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$acting_data['Amount'].'" /></td>
					//	</tr>';
				}
				$res .= '</table>';
				//$res .= '<div class="welcart-settlement-admin-button">';
				//$res .= '<input id="add-settlement" type="button" class="button" value="'.__('Register').'" />';
				//$res .= '</div>';
				$status = '<span class="acting-status conv-del">'.__('Canceled','usces').'</span>';
			} else {
				$res .= '<div class="welcart-settlement-admin conv-error">'.__('Error','usces').'</div>';//エラー
				$res .= '<div class="welcart-settlement-admin-error">';
				$responsecd = explode( '|', $response_data['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
				}
				$res .= '</div>';
				usces_log('[WelcartPay] 2Del connection NG : '.print_r($response_data,true), 'acting_transaction.log');
			}
			do_action( 'usces_action_admin_'.$_POST['mode'], $response_data, $order_id, $trans_id );
			$this->save_acting_history_log( $response_data, $order_id.'_'.$trans_id );
			$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			die($response_data['ResponseCd']."#usces#".$res."#usces#".$status);
			break;

		//オンライン収納代行データ入金結果参照
		case 'get_welcartpay_conv':
			check_admin_referer( 'order_edit', 'wc_nonce' );
			$order_id = ( isset($_POST['order_id']) ) ? $_POST['order_id'] : '';
			$trans_id = ( isset($_POST['trans_id']) ) ? $_POST['trans_id'] : '';
			if( empty($order_id) || empty($trans_id) ) {
				die("NG#usces#");
			}
			$res = '';
			$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_conv', $order_id ) );
			$acting_opts = $this->get_acting_settings();
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params = array();
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $trans_id;
			$param_list['MerchantFree2'] = 'acting_welcart_conv';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$params['send_url'] = $acting_opts['send_url_conv'];
			$params['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '2Ref',
					'ProcessId' => $acting_data['ProcessId'],
					'ProcessPass' => $acting_data['ProcessPass']
				)
			);
			$response_data = $this->connection( $params );
//usces_log(print_r($response_data,true),"test.log");
			if( 'OK' == $response_data['ResponseCd'] ) {
				if( isset($response_data['NyukinDate']) ) {
					$res .= '<div class="welcart-settlement-admin conv-receipted">'.__('Paid','usces').'</div>';//入金済
					$res .= '<table class="welcart-settlement-admin-table">';
					if( isset($response_data['RecvNum']) ) {
						$res .= '<tr><th>'.__('Receipt number','usces').'</th><td>'.$response_data['RecvNum'].'</td></tr>';//受付番号
					}
					if( isset($response_data['NyukinDate']) ) {
						$res .= '<tr><th>'.__('Deposit date','usces').'</th><td>'.$response_data['NyukinDate'].'</td></tr>';//入金日時
					}
					if( isset($response_data['CvsCd']) ) {
						$cvs_name = $this->get_cvs_name($response_data['CvsCd']);
						$res .= '<tr><th>'.__('Convenience store code','usces').'</th><td>'.$cvs_name.'</td></tr>';//収納機関コード
					}
					if( isset($response_data['TenantCd']) ) {
						$res .= '<tr><th>'.__('Tenant code','usces').'</th><td>'.$response_data['TenantCd'].'</td></tr>';//店舗コード
					}
					if( isset($response_data['Amount']) ) {
						$res .= '<tr><th>'.__('Payment amount','usces').'</th><td>'.$response_data['Amount'].__(usces_crcode('return'),'usces').'</td></tr>';
					}
					$res .= '</table>';
				} else {
					$paylimit = substr($acting_data['PayLimit'],0,8);
					$expiration = $this->check_paylimit( $order_id, $trans_id );
					$res .= '<div class="welcart-settlement-admin conv-noreceipt">'.__('Unpaid','usces');//未入金
					if( $expiration ) {
						$res .= __('(Expired)','usces');//（期限切れ）
					}
					$res .= '</div>';
					$res .= '<table class="welcart-settlement-admin-table">';
					$res .= '<tr><th>'.__('Payment due','usces').'</th>
						<td><input type="text" id="paylimit_change" value="'.$paylimit.'" style="ime-mode:disabled" size="10" /><input type="hidden" id="paylimit" value="'.$paylimit.'" /></td>
						</tr>';
					$res .= '<tr><th>'.__('Payment amount','usces').'</th>
						<td><input type="text" id="amount_change" value="'.$acting_data['Amount'].'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$acting_data['Amount'].'" /></td>
						</tr>';
					$res .= '</table>';
					$latest_log = $this->get_acting_latest_log( $order_id.'_'.$trans_id );
					if( isset($latest_log['OperateId']) ) {
						$res .= '<div class="welcart-settlement-admin-button">';
						if( '2Del' != $latest_log['OperateId'] ) {
							$res .= '<input id="delete-settlement" type="button" class="button" value="'.__('Unregister','usces').'" />';//取消
						}
						if( '2Chg' != $latest_log['OperateId'] ) {
							$res .= '<input id="change-settlement" type="button" class="button" value="'.__('Change').'" />';//変更
						}
						$res .= '</div>';
					}
				}
			} else {
				//$deleted = $this->check_deleted( $order_id.'_'.$trans_id );
				//if( $deleted && 'K12' == $response_data['ResponseCd'] ) {
				$latest_log = $this->get_acting_latest_log( $order_id.'_'.$trans_id );
				if( isset($latest_log['OperateId']) && '2Del' == $latest_log['OperateId'] && 'K12' == $response_data['ResponseCd'] ) {
					$paylimit = substr($acting_data['PayLimit'],0,8);
					$res .= '<div class="welcart-settlement-admin conv-del">'.__('Canceled','usces').'</div>';//取消済み
					$res .= '<table class="welcart-settlement-admin-table">';
					$res .= '<tr><th>'.__('Payment due','usces').'</th>
						<td><input type="text" id="paylimit_change" value="'.$paylimit.'" style="ime-mode:disabled" size="10" /><input type="hidden" id="paylimit" value="'.$paylimit.'" /></td>
						</tr>';
					$res .= '<tr><th>'.__('Payment amount','usces').'</th>
						<td><input type="text" id="amount_change" value="'.$acting_data['Amount'].'" style="text-align:right;ime-mode:disabled" size="10" />'.__(usces_crcode('return'),'usces').'<input type="hidden" id="amount" value="'.$acting_data['Amount'].'" /></td>
						</tr>';
					$res .= '</table>';
					$res .= '<div class="welcart-settlement-admin-button">';
					$res .= '<input id="add-settlement" type="button" class="button" value="'.__('Register').'" />';//登録
					$res .= '</div>';
				} else {
					$res .= '<div class="welcart-settlement-admin conv-error">'.__('Error','usces').'</div>';//エラー
					$res .= '<div class="welcart-settlement-admin-error">';
					$responsecd = explode( '|', $response_data['ResponseCd'] );
					foreach( (array)$responsecd as $cd ) {
						$res .= '<div><span class="code">'.$cd.'</span> : <span class="message">'.$this->response_message( $cd ).'</span></div>';
					}
					$res .= '</div>';
					usces_log('[WelcartPay] 2Ref connection NG : '.print_r($response_data,true), 'acting_transaction.log');
				}
			}
			$res .= $this->settlement_history( $order_id.'_'.$trans_id );
			die($response_data['ResponseCd']."#usces#".$res);
			break;
		}
	}

	/**********************************************
	* usces_filter_orderlist_detail_value
	* 決済状況
	* @param  $detail $value $key $order_id
	* @return array $keys
	***********************************************/
	public function orderlist_settlement_status( $detail, $value, $key, $order_id ) {
		global $usces;

		if( 'wc_trans_id' != $key || empty($value) ) {
			return $detail;
		}

		$order_data = $usces->get_order_data( $order_id, 'direct' );
		$payment = usces_get_payments_by_name( $order_data['order_payment_name'] );
		$acting_flg = ( isset($payment['settlement']) ) ? $payment['settlement'] : '';

		if( 'acting_welcart_card' == $acting_flg ) {
			$trans_id = $usces->get_order_meta_value( 'trans_id', $order_id );
			$latest_log = $this->get_acting_latest_log( $order_id.'_'.$trans_id );
			if( isset($latest_log['OperateId']) ) {
				$class = ( ctype_digit(substr($latest_log['OperateId'],0,1)) ) ? ' card-'.mb_strtolower(substr($latest_log['OperateId'],1)) : ' card-'.$latest_log['OperateId'];
				$detail = '<td>'.$value.'<span class="acting-status'.$class.'">'.$this->get_operate_name( $latest_log['OperateId'] ).'</span></td>';
			} elseif( defined('WCEX_AUTO_DELIVERY') ) {
				$regular_id = $usces->get_order_meta_value( 'regular_id', $order_id );
				if( !empty($regular_id) && empty($trans_id) ) {
					$detail = '<td>'.$value.'<span class="acting-status card-error">'.__('Card unregistered','usces').'</span></td>';
				}
			}

		} elseif( 'acting_welcart_conv' == $acting_flg ) {
			$trans_id = $usces->get_order_meta_value( 'trans_id', $order_id );
			$expiration = $this->check_paylimit( $order_id, $trans_id );
			if( $expiration ) {
				$detail = '<td>'.$value.'<span class="acting-status conv-expiration">'.__('Expired','usces').'</span></td>';
			} else {
				$latest_log = $this->get_acting_latest_log( $order_id.'_'.$trans_id );
				if( isset($latest_log['OperateId']) && '2Del' == $latest_log['OperateId'] ) {
					$detail = '<td>'.$value.'<span class="acting-status conv-del">'.__('Canceled','usces').'</span></td>';
				} else {
					$management_status = apply_filters( 'usces_filter_management_status', get_option( 'usces_management_status' ) );
					if( $usces->is_status('noreceipt', $value) ) {
						$detail = '<td>'.$value.'<span class="acting-status conv-noreceipt">'.esc_html($management_status['noreceipt']).'</span></td>';
					} elseif( $usces->is_status('receipted', $value) ) {
						$detail = '<td>'.$value.'<span class="acting-status conv-receipted">'.esc_html($management_status['receipted']).'</span></td>';
					} else {
						$detail = '<td>'.$value.'</td>';
					}
				}
			}
		}
		return $detail;
	}

	/**********************************************
	* usces_filter_settle_info_field_value
	* 受注編集画面に表示する決済情報の値整形
	* @param  $value $key $acting
	* @return str $value
	***********************************************/
	public function settlement_info_field_value( $value, $key, $acting ) {

		if( 'welcart_card' != $acting && 'welcart_conv' != $acting && 'welcart_atodene' != $acting ) {
			return $value;
		}

		switch( $key ) {
		case 'acting':
			switch( $value ) {
			case 'welcart_card':
				$value = __('WelcartPay - Credit card transaction','usces');
				break;
			case 'welcart_conv':
				$value = __('WelcartPay - Online storage agency','usces');
				break;
			case 'welcart_atodene':
				$value = __('WelcartPay - Postpay settlement','usces');
				break;
			}
			break;
		}

		$value = parent::settlement_info_field_value( $value, $key, $acting );

		return $value;
	}

	/**********************************************
	* usces_action_order_edit_form_status_block_middle
	* 受注編集画面【ステータス】
	* @param  $data $cscs_meta $action_args = array( 'order_action', 'order_id', 'cart' );
	* @return -
	***********************************************/
	public function settlement_status( $data, $cscs_meta, $action_args ) {
		global $usces;
		extract($action_args);

		if( $order_action != 'new' && !empty($order_id) ) {
			$payment = usces_get_payments_by_name( $data['order_payment_name'] );
			if( in_array( $payment['settlement'], $this->pay_method ) ) {
				$acting_data = usces_unserialize( $usces->get_order_meta_value( $payment['settlement'], $order_id ) );
				$MerchantFree1 = ( isset($acting_data['MerchantFree1']) ) ? $acting_data['MerchantFree1'] : '';
				if( !empty($MerchantFree1) ) {
					$status_name = '';
					$class = '';
					$latest_log = $this->get_acting_latest_log( $order_id.'_'.$MerchantFree1 );
					if( isset($latest_log['OperateId']) ) {
						if( 'acting_welcart_conv' == $payment['settlement'] ) {
							$expiration = $this->check_paylimit( $order_id, $MerchantFree1 );
							if( $expiration ) {
								$class = ' conv-expiration';
								$status_name = __('Expired','usces');
							} else {
								if( '2Del' == $latest_log['OperateId'] ) {
									$class = ' conv-del';
									$status_name = __('Canceled','usces');
								}
							}
						} else {
							$class = ' card-'.mb_strtolower(substr($latest_log['OperateId'],1));
							$status_name = $this->get_operate_name( $latest_log['OperateId'] );
						}
						if( !empty($status_name) ) {
							echo '
							<tr>
								<td class="label status">'.__('Settlement status','usces').'</td>
								<td class="col1 status"><span id="settlement-status"><span class="acting-status'.$class.'">'.$status_name.'</span></span></td>
							</tr>';
						}
					}
				} elseif( defined('WCEX_AUTO_DELIVERY') ) {
					$regular_id = $usces->get_order_meta_value( 'regular_id', $order_id );
					if( !empty($regular_id) ) {
						echo '
						<tr>
							<td class="label status">'.__('Settlement status','usces').'</td>
							<td class="col1 status"><span id="settlement-status"><span class="acting-status card-error">'.__('Card unregistered','usces').'</span></span></td>
						</tr>';
					}
				}
			}
		}
	}

	/**********************************************
	* usces_action_order_edit_form_settle_info
	* 受注編集画面【支払情報】
	* @param  $data $action_args = array( 'order_action', 'order_id', 'cart' );
	* @return -
	***********************************************/
	public function settlement_information( $data, $action_args ) {
		global $usces;
		extract($action_args);

		if( $order_action != 'new' && !empty($order_id) ) {
			$payment = usces_get_payments_by_name( $data['order_payment_name'] );
			if( in_array( $payment['settlement'], $this->pay_method ) ) {
				$acting_data = usces_unserialize( $usces->get_order_meta_value( $payment['settlement'], $order_id ) );
				$MerchantFree1 = ( isset($acting_data['MerchantFree1']) && isset($acting_data['ProcessId']) && isset($acting_data['ProcessPass']) ) ? $acting_data['MerchantFree1'] : '9999999999';
				//if( isset($acting_data['MerchantFree1']) && isset($acting_data['ProcessId']) && isset($acting_data['ProcessPass']) ) {
				//	echo '<input type="button" id="settlement-information-'.$acting_data['MerchantFree1'].'-1" class="button settlement-information" value="'.__('Settlement info','usces').'">';
				//}
				echo '<input type="button" id="settlement-information-'.$MerchantFree1.'-1" class="button settlement-information" value="'.__('Settlement info','usces').'">';
			}
		}
	}

	/**********************************************
	* usces_action_endof_order_edit_form
	* 決済情報ダイアログ
	* @param  $data $action_args = array( 'order_action', 'order_id', 'cart' );
	* @return -
	* @echo   html
	***********************************************/
	public function settlement_dialog( $data, $action_args ) {
		global $usces;
		extract($action_args);

		if( $order_action != 'new' && !empty($order_id) ):
			$payment = usces_get_payments_by_name( $data['order_payment_name'] );
			if( in_array( $payment['settlement'], $this->pay_method ) ):
				//$acting_data = usces_unserialize( $usces->get_order_meta_value( $payment['settlement'], $order_id ) );
				//if( isset($acting_data['MerchantFree1']) && isset($acting_data['ProcessId']) && isset($acting_data['ProcessPass']) ):
?>
<div id="settlement_dialog" title="">
	<div id="settlement-response-loading"></div>
	<fieldset>
	<div id="settlement-response"></div>
	<input type="hidden" id="order_num">
	<input type="hidden" id="trans_id">
	<input type="hidden" id="acting" value="<?php echo $payment['settlement']; ?>">
	<input type="hidden" id="error">
	</fieldset>
</div>
<?php
				//endif;
			endif;
		endif;
	}

	/**********************************************
	* usces_action_acting_processing
	* 決済処理
	* @param  $acting_flg $post_query
	* @return -
	***********************************************/
	public function acting_processing( $acting_flg, $post_query ) {
		global $usces;

		parent::acting_processing( $acting_flg, $post_query );

		$acting_opts = $this->get_acting_settings();
		if( 'acting_welcart_card' == $acting_flg && 'link' == $acting_opts['card_activate'] ) {

			$usces_entries = $usces->cart->get_entry();
			$cart = $usces->cart->get_cart();

			if( !$usces_entries || !$cart ) {
				wp_redirect(USCES_CART_URL);
			}

			if( !wp_verify_nonce( $_REQUEST['_nonce'], $acting_flg ) ) {
				wp_redirect(USCES_CART_URL);
			}

			parse_str( $post_query, $post_data );
	//usces_log(print_r($post_data,true),"test.log");
			$TransactionDate = $this->get_transaction_date();
			$rand = $post_data['rand'];
			$member = $usces->get_member();

			usces_save_order_acting_data( $rand );

			$acting = 'welcart_card';
			$param_list = array();
			$params = array();

			$quick_member = ( isset($post_data['quick_member']) ) ? $post_data['quick_member'] : '';
			if( !empty($member['ID']) && 'on' == $acting_opts['quickpay'] ) {
				$KaiinId = $this->get_quick_kaiin_id( $member['ID'] );
				$KaiinPass = $this->get_quick_pass( $member['ID'] );
			} else {
				$KaiinId = '';
				$KaiinPass = '';
			}
			if( empty($KaiinId) || empty($KaiinPass) ) {
				$quick_member = 'no';
			}

			if( usces_is_login() && 'on' == $acting_opts['quickpay'] && empty($quick_member) ) {
				//共通部
				$param_list['MerchantId'] = $acting_opts['merchant_id'];
				$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
				$param_list['TransactionDate'] = $TransactionDate;
				$param_list['MerchantFree1'] = $rand;
				$param_list['MerchantFree2'] = $acting_flg;
				$param_list['MerchantFree3'] = $this->merchantfree3;
				$param_list['TenantId'] = $acting_opts['tenant_id'];
				$param_list['Amount'] = $usces_entries['order']['total_full_price'];
				$param_list['KaiinId'] = $KaiinId;
				$param_list['KaiinPass'] = $KaiinPass;
				if( usces_have_continue_charge() ) {
					$chargingday = $usces->getItemChargingDay( $cart[0]['post_id'] );
					if( 99 == $chargingday ) {//受注日課金
						$param_list['OperateId'] = $acting_opts['operateid'];
					} else {
						$param_list['OperateId'] = '1Auth';
					}
					$param_list['PayType'] = '01';
				} else {
					$param_list['OperateId'] = $acting_opts['operateid'];
					//$param_list['PayType'] = $post_data['paytype'];
					$param_list['PayType'] = '01';
				}
				$params['send_url'] = $acting_opts['send_url'];
				$params['param_list'] = $param_list;
				//e-SCOTT 決済
				$response_data = $this->connection( $params );
				$response_data['acting'] = $acting;

				if( 'OK' == $response_data['ResponseCd'] ) {
					$res = $usces->order_processing( $response_data );
					if( 'ordercompletion' == $res ) {
						$response_data['acting_return'] = 1;
						$response_data['result'] = 1;
						$response_data['nonce'] = wp_create_nonce( 'welcart_transaction' );
						wp_redirect( add_query_arg( $response_data, USCES_CART_URL ) );
					} else {
						$logdata = array_merge( $usces_entries['order'], $response_data );
						$log = array( 'acting'=>$acting, 'key'=>$rand, 'result'=>'ORDER DATA REGISTERED ERROR', 'data'=>$logdata );
						usces_save_order_acting_error( $log );
						wp_redirect( add_query_arg( array( 'acting'=>$this->paymod_id.'_card', 'acting_return'=>0, 'result'=>0 ), USCES_CART_URL ) );
					}
				} else {
					$responsecd = explode( '|', $response_data['ResponseCd'] );
					foreach( (array)$responsecd as $cd ) {
						$response_data[$cd] = $this->response_message( $cd );
					}
					$logdata = array_merge( $params, $response_data );
					$log = array( 'acting'=>$acting, 'key'=>$rand, 'result'=>$response_data['ResponseCd'], 'data'=>$logdata );
					usces_save_order_acting_error( $log );
					wp_redirect( add_query_arg( array( 'acting'=>$this->paymod_id.'_card', 'acting_return'=>0, 'result'=>0 ), USCES_CART_URL ) );
				}

			} else {
				$home_url = str_replace( 'http://', 'https://', home_url('/') );
				$redirecturl = $home_url.'?page_id='.USCES_CART_NUMBER;
				$posturl = $home_url;

				if( !empty($member['ID']) && 'on' == $acting_opts['quickpay'] && ( 'add' == $quick_member || 'update' == $quick_member ) ) {
					$data_list = array();
					$data_list['MerchantPass'] = $acting_opts['merchant_pass'];
					$data_list['TransactionDate'] = $TransactionDate;
					$data_list['MerchantFree1'] = $rand;
					$data_list['MerchantFree2'] = $acting_flg;
					$data_list['MerchantFree3'] = $this->merchantfree3;
					$data_list['TenantId'] = $acting_opts['tenant_id'];
					if( 'add' == $quick_member ) {
						$data_list['OperateId'] = '4MemAdd';
						$data_list['KaiinId'] = $this->make_kaiin_id( $member['ID'] );
						$data_list['KaiinPass'] = $this->make_kaiin_pass();
					} elseif( 'update' == $quick_member ) {
						$data_list['OperateId'] = '4MemChg';
						$data_list['KaiinId'] = $KaiinId;
						$data_list['KaiinPass'] = $KaiinPass;
					}
					$data_list['ProcNo'] = '0000000';
					$data_list['RedirectUrl'] = $redirecturl;
					//$data_list['PostUrl'] = $posturl;
					$data_query = http_build_query( $data_list );
					$encryptvalue = openssl_encrypt( $data_query, 'aes-128-cbc', $acting_opts['key_aes'], false, $acting_opts['key_iv'] );

					$param_list['MerchantId'] = $acting_opts['merchant_id'];
					$param_list['EncryptValue'] = urlencode($encryptvalue);
					wp_redirect( add_query_arg( $param_list, $acting_opts['send_url_link'] ) );
				} else {
					if( usces_have_continue_charge() ) {
						$chargingday = $usces->getItemChargingDay( $cart[0]['post_id'] );
						if( 99 == $chargingday ) {//受注日課金
							$OperateId = $acting_opts['operateid'];
						} else {
							$OperateId = '1Auth';
						}
					} else {
						$OperateId = $acting_opts['operateid'];
					}

					$data_list = array();
					$data_list['OperateId'] = $OperateId;
					$data_list['MerchantPass'] = $acting_opts['merchant_pass'];
					$data_list['TransactionDate'] = $TransactionDate;
					$data_list['MerchantFree1'] = $rand;
					$data_list['MerchantFree2'] = $acting_flg;
					$data_list['MerchantFree3'] = $this->merchantfree3;
					$data_list['TenantId'] = $acting_opts['tenant_id'];
					if( 'on' == $acting_opts['quickpay'] && !empty($KaiinId) && !empty($KaiinPass) ) {
						$data_list['KaiinId'] = $KaiinId;
						$data_list['KaiinPass'] = $KaiinPass;
					}
					$data_list['PayType'] = '01';
					$data_list['Amount'] = $usces_entries['order']['total_full_price'];
					$data_list['ProcNo'] = '0000000';
					$data_list['RedirectUrl'] = $redirecturl;
					//$data_list['PostUrl'] = $posturl;
					$data_query = http_build_query( $data_list );
					$encryptvalue = openssl_encrypt( $data_query, 'aes-128-cbc', $acting_opts['key_aes'], false, $acting_opts['key_iv'] );

					$param_list['MerchantId'] = $acting_opts['merchant_id'];
					$param_list['EncryptValue'] = urlencode($encryptvalue);
					wp_redirect( add_query_arg( $param_list, $acting_opts['send_url_link'] ) );
				}
			}
			exit();
		}
	}

	/**********************************************
	* usces_action_reg_orderdata
	* 受注データ登録
	* call from usces_reg_orderdata() and usces_new_orderdata().
	* @param  $args = array(
	*						'cart'=>$cart, 'entry'=>$entry, 'order_id'=>$order_id, 
	*						'member_id'=>$member['ID'], 'payments'=>$set, 'charging_type'=>$charging_type, 
	*						'results'=>$results
	*						);
	* @return -
	***********************************************/
	public function register_orderdata( $args ) {
		extract($args);

		if( !isset($results['MerchantFree1']) ) {
			return;
		}

		$acting_flg = $payments['settlement'];
		if( !in_array( $acting_flg, $this->pay_method ) ) {
			return;
		}

		if( !$entry['order']['total_full_price'] ) {
			return;
		}

		parent::register_orderdata( $args );

		$this->save_acting_history_log( $results, $order_id.'_'.$results['MerchantFree1'] );
	}

	/**********************************************
	* wp_print_footer_scripts
	* JavaScript
	* @param  -
	* @return -
	***********************************************/
	public function footer_scripts() {

		if( !$this->is_validity_acting('card') ) {
			return;
		}

		parent::footer_scripts();

		//クレジットカード情報更新ページ
		if( isset($_GET['page']) && ( 'member_register_settlement' == $_GET['page'] || 'member_update_settlement' == $_GET['page'] ) ):
			wp_enqueue_script( 'usces_escott_member', USCES_FRONT_PLUGIN_URL.'/js/member_escott.js', array('jquery'), USCES_VERSION, true );
/*
?>
<script type="text/javascript">
(function($) {
	$(document).on( "click", "#card-delete", function() {
		if( confirm("<?php _e('Are you sure delete credit card registration?','usces'); ?>") ) {
			$("input[name='delete']").val("delete");
			$("form#member-card-info").submit();
		}
	});

})(jQuery);
</script>
<?php
*/
		endif;
	}

	/**********************************************
	* usces_fiter_the_payment_method_explanation
	* 
	* @param  $explanation $payment $value
	* @return str $explanation
	***********************************************/
	public function set_payment_method_explanation( $explanation, $payment, $value ) {
		global $usces;

		$quickpay = '';
		if( $this->acting_flg_card == $payment['settlement'] ) {
			$acting_opts = $this->get_acting_settings();
			if( 'link' == $acting_opts['card_activate'] ) {
				if( usces_is_login() && 'on' == $acting_opts['quickpay'] ) {
					$member = $usces->get_member();
					$KaiinId = $this->get_quick_kaiin_id( $member['ID'] );
					$KaiinPass = $this->get_quick_pass( $member['ID'] );
					if( !empty($KaiinId) && !empty($KaiinPass) ) {
						$quickpay = '<p class="'.$this->paymod_id.'_quick_member"><label type="update"><input type="checkbox" name="quick_member" value="update"><span>'.__('Change and register purchased credit card','usces').'</span></label></p>';
					} else {
						if( usces_have_regular_order() || usces_have_continue_charge() ) {
							$quickpay = '<input type="hidden" name="quick_member" value="add">';
						} else {
							$quickpay = '<p class="'.$this->paymod_id.'_quick_member"><label type="add"><input type="checkbox" name="quick_member" value="add"><span>'.__('Register and purchase a credit card','usces').'</span></label></p>';
						}
					}
				} else {
					$quickpay = '<input type="hidden" name="quick_member" value="no">';
				}
			}
		}
		return $quickpay.$explanation;
	}

	/**********************************************
	* usces_filter_available_payment_method
	* 
	* @param  $payments
	* @return array $payments
	***********************************************/
	public function set_available_payment_method( $payments ) {
		global $usces;

		if( $usces->is_member_page($_SERVER['REQUEST_URI']) ) {
			$payment_method = array();
			foreach( (array)$payments as $id => $payment ) {
				if( $this->acting_flg_card == $payment['settlement'] ) {
					$payment_method[$id] = $payments[$id];
					break;
				}
			}
			if( !empty($payment_method) ) {
				$payments = $payment_method;
			}
		}
		return $payments;
	}

	/**********************************************
	* usces_filter_delivery_secure_form_howpay
	* 
	* @param  $html
	* @return str $html
	***********************************************/
	public function delivery_secure_form_howpay( $html ) {

		if( isset($_GET['page'] ) && ( 'member_update_settlement' == $_GET['page'] || 'member_register_settlement' == $_GET['page'] ) ) {
			$html = '';
		}
		return $html;
	}

	/**********************************************
	* wp_enqueue_scripts
	* JavaScript
	* @param  -
	* @return -
	***********************************************/
	public function enqueue_scripts() {
		global $usces;

		//発送・支払方法ページ、クレジットカード情報更新ページ
		if( !is_admin() && $this->is_validity_acting('card') && ( 'delivery' == $usces->page || 'member_register_settlement' == $usces->page || 'member_update_settlement' == $usces->page ) ):
			$acting_opts = $this->get_acting_settings();
			if( isset($acting_opts['card_activate']) && 'token' == $acting_opts['card_activate'] ):
?>
<script type="text/javascript"
src="<?php esc_html_e( $acting_opts['api_token'] ); ?>?k_TokenNinsyoCode=<?php esc_html_e( $acting_opts['token_code'] ); ?>" callBackFunc="setToken" class="spsvToken"></script>
<?php
			endif;
		endif;
	}

	/**********************************************
	* usces_filter_uscesL10n
	* JavaScript
	* @param  -
	* @return -
	***********************************************/
	public function set_uscesL10n() {
		global $usces;

		parent::set_uscesL10n();

		if( $usces->is_member_page($_SERVER['REQUEST_URI']) && ( isset($_GET['page']) && ( 'member_register_settlement' == $_GET['page'] || 'member_update_settlement' == $_GET['page'] ) ) ) {
			$acting_opts = $this->get_acting_settings();
			if( isset($acting_opts['card_activate']) && 'token' == $acting_opts['card_activate'] ) {
				echo "'front_ajaxurl': '".USCES_SSL_URL."',\n";
				echo "'escott_token_error_message': '".__('Credit card information is not appropriate.','usces')."',\n";
			}
		}
	}

	/**********************************************
	* usces_filter_template_redirect
	* クレジットカード登録・変更ページ表示
	* @param  -
	* @return -
	***********************************************/
	public function member_update_settlement() {
		global $usces;

		if( $usces->is_member_page($_SERVER['REQUEST_URI']) ) {
			if( !usces_is_membersystem_state() or !usces_is_login() ) {
				return;
			}

			$acting_opts = $this->get_acting_settings();
			if( 'on' != $acting_opts['quickpay'] ) {
				return;
			}

			if( isset($_REQUEST['page']) && 'member_update_settlement' == $_REQUEST['page'] ) {
				add_filter( 'usces_filter_states_form_js', array( $this, 'states_form_js' ) );
				$usces->page = 'member_update_settlement';
				$this->member_update_settlement_form();
				exit();

			} elseif( isset($_REQUEST['page']) && 'member_register_settlement' == $_REQUEST['page'] ) {
				add_filter( 'usces_filter_states_form_js', array( $this, 'states_form_js' ) );
				$usces->page = 'member_register_settlement';
				$this->member_update_settlement_form();
				exit();
			}
		}
		return false;
	}

	/**********************************************
	* usces_filter_delete_member_check
	* 会員データ削除チェック
	* @param  $del $member_id
	* @return boolean $del
	***********************************************/
	public function states_form_js( $js ) {
		return '';
	}

	/**********************************************
	* usces_filter_delete_member_check
	* 会員データ削除チェック
	* @param  $del $member_id
	* @return boolean $del
	***********************************************/
	public function delete_member_check( $del, $member_id ) {
		$KaiinId = $this->get_quick_kaiin_id( $member_id );
		if( !empty($KaiinId) ) {
			$del = false;
		}
		return $del;
	}

	/**********************************************
	* usces_action_member_submenu_list
	* クレジットカード登録・変更ページリンク
	* @param  -
	* @return -
	* @echo   update_settlement()
	***********************************************/
	public function e_update_settlement() {
		global $usces;

		$member = $usces->get_member();
		$html = $this->update_settlement( '', $member );
		echo $html;
	}

	/**********************************************
	* usces_filter_member_submenu_list
	* クレジットカード登録・変更ページリンク
	* @param  $html $member
	* @return str $html
	***********************************************/
	public function update_settlement( $html, $member ) {

		$acting_opts = $this->get_acting_settings();
		if( 'on' == $acting_opts['quickpay'] ) {
			//e-SCOTT 会員照会
			$response_member = $this->escott_member_reference( $member['ID'] );
			if( 'OK' == $response_member['ResponseCd'] ) {
				$update_settlement_url = add_query_arg( array( 'page'=>'member_update_settlement', 're-enter'=>1 ), USCES_MEMBER_URL );
				$html .= '
				<div class="gotoedit">
				<a href="'.$update_settlement_url.'">'.__("Change the credit card is here >>",'usces').'</a>
				</div>';
			} else {
				$register_settlement_url = add_query_arg( array( 'page'=>'member_register_settlement', 're-enter'=>1 ), USCES_MEMBER_URL );
				$html .= '
				<div class="gotoedit">
				<a href="'.$register_settlement_url.'">'.__("Credit card registration is here >>",'usces').'</a>
				</div>';
			}
		}
		return $html;
	}

	/**********************************************
	* クレジットカード登録・変更ページ
	* @param  -
	* @return -
	* @echo   html
	***********************************************/
	public function member_update_settlement_form() {
		global $usces;

		$member = $usces->get_member();
		$acting_opts = $this->get_acting_settings();

		if( 'link' == $acting_opts['card_activate'] ) {
			$TransactionDate = $this->get_transaction_date();
			$home_url = str_replace( 'http://', 'https://', home_url('/') );
			$redirecturl = $home_url.'?page_id='.USCES_MEMBER_NUMBER;
			$posturl = $home_url;

			$data_list = array();
			$data_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$data_list['TransactionDate'] = $TransactionDate;
			$data_list['MerchantFree3'] = $this->merchantfree3;
			$data_list['TenantId'] = $acting_opts['tenant_id'];
			if( 'member_register_settlement' == $usces->page ) {
				$data_list['OperateId'] = '4MemAdd';
				$data_list['KaiinId'] = $this->make_kaiin_id( $member['ID'] );
				$data_list['KaiinPass'] = $this->make_kaiin_pass();
			} else {
				$data_list['OperateId'] = '4MemChg';
				$data_list['KaiinId'] = $this->get_quick_kaiin_id( $member['ID'] );
				$data_list['KaiinPass'] = $this->get_quick_pass( $member['ID'] );
			}
			$data_list['ProcNo'] = '0000000';
			$data_list['RedirectUrl'] = $redirecturl;
			//$data_list['PostUrl'] = $posturl;
			$data_query = http_build_query( $data_list );
			$encryptvalue = openssl_encrypt( $data_query, 'aes-128-cbc', $acting_opts['key_aes'], false, $acting_opts['key_iv'] );

			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['EncryptValue'] = urlencode($encryptvalue);
			wp_redirect( add_query_arg( $param_list, $acting_opts['send_url_link'] ) );

		} else {
			$script = '';
			$done_message = '';
			$html = '';
			$register = ( 'member_register_settlement' == $usces->page ) ? true : false;
			$deleted = false;

			$cardno = '';
			$seccd = '';
			$expyy = '';
			$expmm = '';

			if( 'on' == $acting_opts['quickpay'] ) {
				if( isset($_POST['update']) ) {
					check_admin_referer( 'member_update_settlement', 'wc_nonce' );
					$response_member = $this->escott_member_update( $member['ID'] );
					if( 'OK' == $response_member['ResponseCd'] ) {
						$done_message = __('Successfully updated.','usces');
					} else {
						$error_message = array();
						$responsecd = explode( '|', $response_member['ResponseCd'] );
						foreach( (array)$responsecd as $cd ) {
							$error_message[] = $this->error_message( $cd );
						}
						$error_message = array_unique( $error_message );
						if( 0 < count($error_message) ) {
							foreach( $error_message as $message ) {
								$usces->error_message .= '<p>'.$message.'</p>';
							}
						}
						$cardno = ( isset($_POST['cardno']) ) ? esc_html($_POST['cardno']) : '';
						$seccd = ( isset($_POST['seccd']) ) ? esc_html($_POST['seccd']) : '';
						$expyy = ( isset($_POST['expyy']) ) ? esc_html($_POST['expyy']) : '';
						$expmm = ( isset($_POST['expmm']) ) ? esc_html($_POST['expmm']) : '';
					}
				} elseif( isset($_POST['register']) ) {
					check_admin_referer( 'member_update_settlement', 'wc_nonce' );
					$response_member = $this->escott_member_register( $member['ID'] );
					if( 'OK' == $response_member['ResponseCd'] ) {
						$done_message = __('Successfully registered.','usces');
						$register = false;
					} else {
						$error_message = array();
						$responsecd = explode( '|', $response_member['ResponseCd'] );
						foreach( (array)$responsecd as $cd ) {
							$error_message[] = $this->error_message( $cd );
						}
						$error_message = array_unique( $error_message );
						if( 0 < count($error_message) ) {
							foreach( $error_message as $message ) {
								$usces->error_message .= '<p>'.$message.'</p>';
							}
						}
						$cardno = ( isset($_POST['cardno']) ) ? esc_html($_POST['cardno']) : '';
						$seccd = ( isset($_POST['seccd']) ) ? esc_html($_POST['seccd']) : '';
						$expyy = ( isset($_POST['expyy']) ) ? esc_html($_POST['expyy']) : '';
						$expmm = ( isset($_POST['expmm']) ) ? esc_html($_POST['expmm']) : '';
					}
				/*} elseif( isset($_POST['delete']) && 'delete' == $_POST['delete'] ) {
					check_admin_referer( 'member_update_settlement', 'wc_nonce' );
					$response_member = $this->escott_member_delete( $member['ID'] );
					if( 'OK' == $response_member['ResponseCd'] ) {
						$done_message = __('Credit card registration deleted.','usces');
						$deleted = true;
					} else {
						$error_message = array();
						$responsecd = explode( '|', $response_member['ResponseCd'] );
						foreach( (array)$responsecd as $cd ) {
							$error_message[] = $this->error_message( $cd );
						}
						$error_message = array_unique( $error_message );
						if( 0 < count($error_message) ) {
							foreach( $error_message as $message ) {
								$usces->error_message .= '<p>'.$message.'</p>';
							}
						}
						$cardno = ( isset($_POST['cardno']) ) ? esc_html($_POST['cardno']) : '';
						$seccd = ( isset($_POST['seccd']) ) ? esc_html($_POST['seccd']) : '';
						$expyy = ( isset($_POST['expyy']) ) ? esc_html($_POST['expyy']) : '';
						$expmm = ( isset($_POST['expmm']) ) ? esc_html($_POST['expmm']) : '';
					}*/
				}

				if( !$deleted ) {
					//e-SCOTT 会員照会
					$response_member = $this->escott_member_reference( $member['ID'] );
					if( 'OK' == $response_member['ResponseCd'] ) {
						$cardlast4 = substr($response_member['CardNo'], -4);
						$expyy = substr(date_i18n('Y', current_time('timestamp')), 0, 2).substr($response_member['CardExp'], 0, 2);
						$expmm = substr($response_member['CardExp'], 2, 2);
					} else {
						$cardlast4 = '';
					}
					$html .= '<input name="acting" type="hidden" value="'.$this->paymod_id.'" />
					<table class="customer_form" id="'.$this->paymod_id.'">';
					if( !empty($cardlast4) ) {
						$html .= '
						<tr>
							<th scope="row">'.__('The last four digits of your card number','usces').'</th>
							<td colspan="2"><p>'.$cardlast4.'</p></td>
						</tr>';
					}
					$cardno_attention = apply_filters( 'usces_filter_cardno_attention', __('(Single-byte numbers only)','usces').'<div class="attention">'.__('* Please do not enter symbols or letters other than numbers such as space (blank), hyphen (-) between numbers.','usces').'</div>' );
					$html .= '
						<tr>
							<th scope="row">'.__('card number','usces').'</th>
							<td colspan="2"><input name="cardno" id="cardno" type="text" size="16" value="'.$cardno.'" />'.$cardno_attention.'</td>
						</tr>';
					if( 'on' == $acting_opts['seccd'] ) {
						$seccd_attention = apply_filters( 'usces_filter_seccd_attention', __('(Single-byte numbers only)','usces') );
						$html .= '
						<tr>
							<th scope="row">'.__('security code','usces').'</th>
							<td colspan="2"><input name="seccd" id="seccd" type="text" size="6" value="'.$seccd.'" />'.$seccd_attention.'</td>
						</tr>';
					}
					$html .= '
						<tr>
							<th scope="row">'.__('Card expiration','usces').'</th>
							<td colspan="2">
							<select id="expmm">
								<option value=""'.(empty($expmm) ? ' selected="selected"' : '').'>----</option>';
					for( $i = 1; $i <= 12; $i++ ) {
						$html .= '
								<option value="'.sprintf('%02d', $i).'"'.(( $i == (int)$expmm ) ? ' selected="selected"' : '').'>'.sprintf('%2d', $i).'</option>';
					}
					$html .= '
							</select>'.__('month','usces').'&nbsp;
							<select id="expyy">
								<option value=""'.(empty($expyy) ? ' selected="selected"' : '').'>------</option>';
					for( $i = 0; $i < 15; $i++ ) {
						$year = date_i18n('Y') - 1 + $i;
						$selected = ( $year == $expyy ) ? ' selected="selected"' : '';
						$html .= '
								<option value="'.$year.'"'.$selected.'>'.$year.'</option>';
					}
					$html .= '
							</select>'.__('year','usces').'
							</td>
						</tr>
					</table>';
				}
			}

			$update_settlement_url = add_query_arg( array( 'page'=>$usces->page, 'settlement'=>1, 're-enter'=>1 ), USCES_MEMBER_URL );
			if( '' != $done_message ) {
				$script .= '
				<script type="text/javascript">
					jQuery.event.add( window, "load", function() {
						alert("'.$done_message.'");
					});
				</script>';
			}

			ob_start();
			get_header();
?>
<?php if( '' != $script ) echo $script; ?>
<div id="content" class="two-column">
<div class="catbox">
<?php if( have_posts() ): usces_remove_filter(); ?>
<div class="post" id="wc_member_update_settlement">
<?php if( $register ): ?>
<h1 class="member_page_title"><?php _e('Credit card registration','usces'); ?></h1>
<?php else: ?>
<h1 class="member_page_title"><?php _e('Credit card update','usces'); ?></h1>
<?php endif; ?>
<div class="entry">
<div id="memberpages">
<div class="whitebox">
	<div id="memberinfo">
	<div class="header_explanation"></div>
	<?php if( !$deleted && !$register ): ?>
	<p><?php _e('If you want to change the expiration date only, please the card number to the blank.','usces'); ?></p>
	<?php endif; ?>
	<div class="error_message"><?php usces_error_message(); ?></div>
	<?php if( 'token' == $acting_opts['card_activate'] ): ?>
		<?php echo $html; ?>
	<?php endif; ?>
	<form id="member-card-info" action="<?php echo $update_settlement_url; ?>" method="post" onKeyDown="if(event.keyCode == 13) {return false;}">
	<?php if( 'on' == $acting_opts['card_activate'] ): ?>
		<?php echo $html; ?>
	<?php endif; ?>
		<div class="send">
			<input type="hidden" name="expmm" value="<?php echo $expmm; ?>" />
			<input type="hidden" name="expyy" value="<?php echo $expyy; ?>" />
	<?php if( 'token' == $acting_opts['card_activate'] ): ?>
			<input type="hidden" name="token" id="token" value="" />
	<?php endif; ?>
	<?php if( $register ): ?>
			<input type="hidden" name="register" value="register" />
			<input type="button" id="card-register" class="card-register" value="<?php _e('Register'); ?>" />
	<?php else: ?>
		<?php if( !$deleted ): ?>
			<input type="hidden" name="update" value="update" />
			<input type="button" id="card-update" class="card-update" value="<?php _e('Update'); ?>" />
			<?php //if( !usces_have_member_continue_order( $member['ID'] ) && !usces_have_member_regular_order( $member['ID'] ) ): ?>
			<!--<input type="button" id="card-delete" value="<?php _e('Remove'); ?>" />
			<input type="hidden" name="delete" value="" />-->
			<?php //endif; ?>
		<?php endif; ?>
	<?php endif; ?>
			<input type="button" name="back" value="<?php _e('Back to the member page.','usces'); ?>" onclick="location.href='<?php echo USCES_MEMBER_URL; ?>'" />
			<input type="button" name="top" value="<?php _e('Back to the top page.','usces'); ?>" onclick="location.href='<?php echo home_url(); ?>'" />
		</div>
	<?php wp_nonce_field( 'member_update_settlement', 'wc_nonce' ); ?>
	</form>
	<div class="footer_explanation"></div>
	</div><!-- end of memberinfo -->
</div><!-- end of whitebox -->
</div><!-- end of memberpages -->
</div><!-- end of entry -->
</div><!-- end of post -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->
<?php
			$sidebar = apply_filters( 'usces_filter_member_update_settlement_page_sidebar', 'cartmember' );
			if( !empty($sidebar) ) {
				get_sidebar( $sidebar );
			}
			get_footer();
			$contents = ob_get_contents();
			ob_end_clean();

			echo $contents;
		}
	}

	/**********************************************
	* クレジットカード変更メール
	* @param  -
	* @return -
	***********************************************/
	public function send_update_settlement_mail() {
		global $usces;

		$member = $usces->get_member();
		$mail_data = $usces->options['mail_data'];

		$subject = apply_filters( 'usces_filter_send_update_settlement_mail_subject', __('Confirmation of credit card update','usces'), $member );
		$mail_header = __('Your credit card information has been updated.','usces')."\r\n\r\n";
		$mail_footer = $mail_data['footer']['thankyou'];
		$name = usces_localized_name( $member['name1'], $member['name2'], 'return' );

		$message  = '--------------------------------'."\r\n";
		$message .= __('Member ID','usces').' : '.$member['ID']."\r\n";
		$message .= __('Name','usces').' : '.sprintf( _x('%s','honorific','usces'), $name )."\r\n";
		$message .= __('e-mail adress','usces').' : '.$member['mailaddress1']."\r\n";
		$message .= '--------------------------------'."\r\n\r\n";
		$message .= __('If you have not requested this email, sorry to trouble you, but please contact us.','usces')."\r\n\r\n";
		$message  = apply_filters( 'usces_filter_send_update_settlement_mail_message', $message, $member );
		$message  = apply_filters( 'usces_filter_send_update_settlement_mail_message_head', $mail_header, $member ).$message.apply_filters( 'usces_filter_send_update_settlement_mail_message_foot', $mail_footer, $member )."\r\n";

		//if( $usces->options['put_customer_name'] == 1 ) {
			$message = sprintf( __('Dear %s','usces'), $name )."\r\n\r\n".$message;
		//}

		$send_para = array(
			'to_name' => sprintf( _x('%s','honorific','usces'), $name ),
			'to_address' => $member['mailaddress1'],
			'from_name' => get_option( 'blogname' ),
			'from_address' => $usces->options['sender_mail'],
			'return_path' => $usces->options['sender_mail'],
			'subject' => $subject,
			'message' => $message
		);
		usces_send_mail( $send_para );

		$admin_para = array(
			'to_name' => apply_filters( 'usces_filter_bccmail_to_admin_name', 'Shop Admin' ), 
			'to_address' => $usces->options['order_mail'],
			'from_name' => apply_filters( 'usces_filter_bccmail_from_admin_name', 'Welcart Auto BCC' ), 
			'from_address' => $usces->options['sender_mail'],
			'return_path' => $usces->options['sender_mail'],
			'subject' => $subject,
			'message' => $message
		);
		usces_send_mail( $admin_para );
	}

	/**********************************************
	* usces_filter_the_continue_payment_method
	* 
	* @param  $payment_method
	* @return array $payment_method
	***********************************************/
	public function continuation_payment_method( $payment_method ) {

		$acting_opts = $this->get_acting_settings();
		if( 'on' == $acting_opts['quickpay'] ) {
			$payment_method[] = 'acting_welcart_card';
		}
		return $payment_method;
	}

	/**********************************************
	* dlseller_filter_the_payment_method_restriction wcad_filter_the_payment_method_restriction
	* 
	* @param  $payments_restriction $value
	* @return array $payments_restriction
	***********************************************/
	public function payment_method_restriction( $payments_restriction, $value ) {

		$acting_opts = $this->get_acting_settings();
		if( ( usces_have_regular_order() || usces_have_continue_charge() ) && usces_is_login() && 'on' == $acting_opts['quickpay'] ) {
			$payments = usces_get_system_option( 'usces_payment_method', 'settlement' );
			$payments_restriction[] = $payments['acting_welcart_card'];
			foreach( (array)$payments_restriction as $key => $value ) {
				$sort[$key] = $value['sort'];
			}
			array_multisort( $sort, SORT_ASC, $payments_restriction );
		}
		return $payments_restriction;
	}

	/**********************************************
	* dlseller_filter_first_charging
	* 「初回引落し日」
	* @param  $time $post_id $usces_item $order_id $continue_data
	* @return datetime $time
	***********************************************/
	public function first_charging_date( $time, $post_id, $usces_item, $order_id, $continue_data ) {

		if( 99 == $usces_item['item_chargingday'] ) {
			if( empty($order_id) ) {
				$today = date_i18n( 'Y-m-d', current_time('timestamp') );
				list( $year, $month, $day ) = explode( "-", $today );
				$time = mktime( 0, 0, 0, (int)$month, (int)$day, (int)$year );
			}
		}
		return $time;
	}

	/**********************************************
	* dlseller_filter_continue_member_list_limitofcard
	* 継続課金会員リスト「有効期限」
	* @param  $limitofcard $member_id $order_id $meta_data
	* @return str $limitofcard
	***********************************************/
	public function continue_member_list_limitofcard( $limitofcard, $member_id, $order_id, $meta_data ) {

		if( isset($meta_data['acting']) ) {
			if( version_compare( WCEX_DLSELLER_VERSION, '3.0-beta', '<=' ) ) {
				$payment = usces_get_payments_by_name( $meta_data['acting'] );
				$acting = $payment['settlement'];
			} else {
				$acting = $meta_data['acting'];
			}
			if( 'acting_welcart_card' != $acting ) {
				return $limitofcard;
			}

			$acting_opts = $this->get_acting_settings();
			if( 'on' != $acting_opts['quickpay'] ) {
				return $limitofcard;
			}

			$KaiinId = $this->get_quick_kaiin_id( $member_id );
			$KaiinPass = $this->get_quick_pass( $member_id );

			if( !empty($KaiinId) && !empty($KaiinPass) ) {
				//e-SCOTT 会員照会
				$response_member = $this->escott_member_reference( $member_id, $KaiinId, $KaiinPass );
				if( 'OK' == $response_member['ResponseCd'] ) {
					$expyy = substr(date_i18n('Y', current_time('timestamp')), 0, 2).substr($response_member['CardExp'], 0, 2);
					$expmm = substr($response_member['CardExp'], 2, 2);
					$limit = $expyy.$expmm;
					$now = date_i18n( 'Ym', current_time('timestamp', 0) );
					$limitofcard = $expmm.'/'.substr($response_member['CardExp'], 0, 2);
					if( $limit <= $now ) {
						$limitofcard .= '<br /><a href="javascript:void(0)" onClick="uscesMail.getMailData(\''.$member_id.'\', \''.$order_id.'\')">'.__('Update Request Email','dlseller').'</a>';
					}
				}
			} else {
				$limitofcard = '';
			}
		}
		return $limitofcard;
	}

	/**********************************************
	* dlseller_filter_continue_member_list_continue_status
	* 継続課金会員リスト「契約」
	* @param  $status $member_id $order_id $meta_data
	* @return str $status
	***********************************************/
	public function continue_member_list_continue_status( $status, $member_id, $order_id, $meta_data ) {
		return $status;
	}

	/**********************************************
	* dlseller_filter_continue_member_list_condition
	* 継続課金会員リスト「状態」
	* @param  $condition $member_id $order_id $meta_data
	* @return str $condition
	***********************************************/
	public function continue_member_list_condition( $condition, $member_id, $order_id, $meta_data ) {
		global $usces;

		$order_data = $usces->get_order_data( $order_id, 'direct' );
		$payment = $usces->getPayments( $order_data['order_payment_name'] );
		if( 'acting_welcart_card' == $payment['settlement'] ) {
			$url = admin_url( 'admin.php?page=usces_continue&continue_action=settlement&member_id='.$member_id.'&order_id='.$order_id );
			$condition = '<a href="'.$url.'">'.__('Detail','usces').'</a>';

			if( $meta_data['status'] == 'continuation' ) {
				$status = $this->get_latest_status( $member_id, $order_id );
				if( !empty($status) && 'OK' != $status ) {
					$condition .= '<div class="acting-status card-error">'.__('Settlement error','usces').'</div>';
				}
			}
		}
		return $condition;
	}

	/**********************************************
	* dlseller_action_continue_member_list_page
	* 継続課金会員決済状況ページ表示
	* @param  $continue_action
	* @return -
	***********************************************/
	public function continue_member_list_page( $continue_action ) {

		if( 'settlement' == $continue_action ) {
			$member_id = ( isset($_GET['member_id']) ) ? $_GET['member_id'] : '';
			$order_id = ( isset($_GET['order_id']) ) ? $_GET['order_id'] : '';
			if( !empty($member_id) && !empty($order_id) ) {
				$this->continue_member_settlement_info_page( $member_id, $order_id );
				exit();
			}
		}
	}

	/**********************************************
	* 継続課金会員決済状況ページ
	* @param  $member_id $order_id
	* @return -
	* @echo   html
	***********************************************/
	public function continue_member_settlement_info_page( $member_id, $order_id ) {
		global $usces;

		if( version_compare( WCEX_DLSELLER_VERSION, '3.0-beta', '<=' ) ) {
			$continue_data = usces_unserialize( $usces->get_member_meta_value( 'continuepay_'.$order_id, $member_id ) );
		} else {
			$continue_data = $this->get_continuation_data( $order_id, $member_id );
		}
		$curent_url = esc_url($_SERVER['REQUEST_URI']);
		$navibutton = '<a href="'.esc_url($_SERVER['HTTP_REFERER']).'" class="back-list"><span class="dashicons dashicons-list-view"></span>'.__('Back to the continue members list','dlseller').'</a>';

		$order_data = $usces->get_order_data( $order_id, 'direct' );
		if( !$order_data ) {
			return;
		}

		$name = usces_localized_name( $order_data['order_name1'], $order_data['order_name2'], 'return' );
		$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_card', $order_id ) );

		$payment = $usces->getPayments( $order_data['order_payment_name'] );
		if( 'acting_welcart_card' != $payment['settlement'] ) {
			return;
		}

		$contracted_date = ( empty($continue_data['contractedday']) ) ? dlseller_next_contracting( $order_id ) : $continue_data['contractedday'];
		if( !empty($contracted_date) ) {
			list( $contracted_year, $contracted_month, $contracted_day ) = explode( '-', $contracted_date );
		} else {
			$contracted_year = 0;
			$contracted_month = 0;
			$contracted_day = 0;
		}
		$charged_date = ( empty($continue_data['chargedday']) ) ? dlseller_next_charging( $order_id ) : $continue_data['chargedday'];
		if( !empty($charged_date) ) {
			list( $charged_year, $charged_month, $charged_day ) = explode( '-', $charged_date );
		} else {
			$charged_year = 0;
			$charged_month = 0;
			$charged_day = 0;
		}
		$year = substr(date_i18n('Y', current_time('timestamp')), 0, 4);
		$total_full_price = $order_data['order_item_total_price'] - $order_data['order_usedpoint'] + $order_data['order_discount'] + $order_data['order_shipping_charge'] + $order_data['order_cod_fee'] + $order_data['order_tax'];

		$log_data = $this->get_acting_log( $order_id );
		$num = count($log_data) + 1;

		$KaiinId = $this->get_quick_kaiin_id( $member_id );
		$card = ( empty($KaiinId) ) ? '&nbsp;<span id="settlement-status"><span class="acting-status card-error">'.__('Card unregistered','usces').'</span></span>' : '';
?>
<div class="wrap">
<div class="usces_admin">
<h1>Welcart Management <?php _e('Continuation charging member information','dlseller'); ?></h1>
<p class="version_info">Version <?php echo WCEX_DLSELLER_VERSION; ?></p>
<?php usces_admin_action_status(); ?>
<div class="edit_pagenav"><?php echo $navibutton; ?></div>
<div id="datatable">
<div id="tablesearch" class="usces_tablesearch">
<div id="searchBox" style="display:block">
	<table class="search_table">
	<tr>
		<td class="label"><?php _e('Continuation charging information','dlseller'); ?></td>
		<td>
			<table class="order_info">
			<tr>
				<th><?php _e('Member ID','dlseller'); ?></th>
				<td><?php echo $member_id.$card; ?></td>
				<th><?php _e('Contractor name','dlseller'); ?></th>
				<td><?php echo esc_html($name); ?></td>
			</tr>
			<tr>
				<th><?php _e('Order ID','dlseller'); ?></th>
				<td><?php echo $order_id; ?></td>
				<th><?php _e('Application Date','dlseller'); ?></th>
				<td><?php echo $order_data['order_date']; ?></td>
			</tr>
			<tr>
				<th><?php _e('Renewal Date','dlseller'); ?></th>
				<td>
					<select id="contracted-year">
						<option value="0"<?php if( $contracted_year == 0 ) echo ' selected="selected"'; ?>></option>
						<option value="<?php echo $year; ?>"<?php if( $contracted_year == $year ) echo ' selected="selected"'; ?>><?php echo $year; ?></option>
						<option value="<?php echo $year+1; ?>"<?php if( $contracted_year == ($year+1) ) echo ' selected="selected"'; ?>><?php echo $year+1; ?></option>
					</select>-
					<select id="contracted-month">
			    		<option value="0"<?php if( $contracted_month == 0 ) echo ' selected="selected"'; ?>></option>
						<?php for( $i = 1; $i <= 12; $i++ ): ?>
				    	<option value="<?php printf("%02d",$i); ?>"<?php if( (int)$contracted_month == $i ) echo ' selected="selected"'; ?>><?php printf("%2d",$i); ?></option>
						<?php endfor; ?>
					</select>-
					<select id="contracted-day">
			    		<option value="0"<?php if( $contracted_day == 0 ) echo ' selected="selected"'; ?>></option>
						<?php for( $i = 1; $i <= 31; $i++ ): ?>
						<option value="<?php printf("%02d",$i); ?>"<?php if( (int)$contracted_day == $i ) echo ' selected="selected"'; ?>><?php printf("%2d",$i); ?></option>
						<?php endfor; ?>
					</select>
				</td>
				<th><?php _e('Next Withdrawal Date','dlseller'); ?></th>
				<td>
					<select id="charged-year">
						<option value="0"<?php if( $charged_year == 0 ) echo ' selected="selected"'; ?>></option>
						<option value="<?php echo $year; ?>"<?php if( $charged_year == $year ) echo ' selected="selected"'; ?>><?php echo $year; ?></option>
						<option value="<?php echo $year+1; ?>"<?php if( $charged_year == ($year+1) ) echo ' selected="selected"'; ?>><?php echo $year+1; ?></option>
					</select>-
					<select id="charged-month">
			    		<option value="0"<?php if( $charged_month == 0 ) echo ' selected="selected"'; ?>></option>
						<?php for( $i = 1; $i <= 12; $i++ ): ?>
				    	<option value="<?php printf("%02d",$i); ?>"<?php if( (int)$charged_month == $i ) echo ' selected="selected"'; ?>><?php printf("%2d",$i); ?></option>
						<?php endfor; ?>
					</select>-
					<select id="charged-day">
			    		<option value="0"<?php if( $charged_day == 0 ) echo ' selected="selected"'; ?>></option>
						<?php for( $i = 1; $i <= 31; $i++ ): ?>
						<option value="<?php printf("%02d",$i); ?>"<?php if( (int)$charged_day == $i ) echo ' selected="selected"'; ?>><?php printf("%2d",$i); ?></option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php _e('Amount on order','usces'); ?></th>
				<td><?php usces_crform( $continue_data['order_price'], false );//usces_crform( $total_full_price, false ); ?></td>
				<th><?php _e('Settlement amount','usces'); ?></th>
				<td><input type="text" id="price" style="text-align: right;" value="<?php usces_crform( $continue_data['price'], false, false, '', false ); ?>"><?php usces_crcode(); ?></td>
			</tr>
			<tr>
				<th><?php _e('Status','dlseller'); ?></th>
				<td><select id="dlseller-status">
				<?php if( $continue_data['status'] == 'continuation' ): ?>
					<option value="continuation" selected="selected"><?php _e('Continuation','dlseller'); ?></option>
					<option value="cancellation"><?php _e('Stop','dlseller'); ?></option>
				<?php else: ?>
					<option value="cancellation" selected="selected"><?php _e('Cancellation','dlseller'); ?></option>
					<option value="continuation"><?php _e('Resumption','dlseller'); ?></option>
				<?php endif; ?>
				</select></td>
				<td colspan="2"><input id="continuation-update" type="button" class="button button-primary" value="<?php _e('Update'); ?>" /></td>
			</tr>
			</table>
			<?php do_action( 'usces_action_continuation_charging_information', $continue_data, $member_id, $order_id ); ?>
		</td>
	</tr>
	</table>
</div><!-- searchBox -->
</div><!-- tablesearch -->
<table id="mainDataTable" class="new-table order-new-table">
	<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php _e('Processing date','usces'); ?></th>
		<th scope="col"><?php _e('Transaction ID','usces'); ?></th>
		<th scope="col"><?php _e('Processing classification','usces'); ?></th>
		<th scope="col">&nbsp;</th>
	</tr>
	</thead>
<?php foreach( (array)$log_data as $log_row ):
		$log = $this->get_acting_latest_log( $log_row['log_key'] );
		if( isset($log['OperateId']) && isset($log['ResponseCd']) && 'OK' == $log['ResponseCd'] ) {
			$class = ' card-'.mb_strtolower(substr($log['OperateId'],1));
			$status_name = $this->get_operate_name( $log['OperateId'] );
			$MerchantFree1 = $log['MerchantFree1'];
			$ResponseCd = '';
		} else {
			$class = ' card-error';
			$status_name = __('Settlement error','usces');
			if( isset($log_row['log']) ) {
				$log = usces_unserialize( $log_row['log'] );
				$MerchantFree1 = $log['MerchantFree1'];
				$ResponseCd = $log['ResponseCd'];
			} else {
				$MerchantFree1 = '9999999999';
				$ResponseCd = 'NG';
			}
		}
?>
	<tbody>
	<tr>
		<td><?php echo $num; ?></td>
		<td><?php echo $log_row['datetime']; ?></td>
		<td><?php echo $MerchantFree1; ?></td>
		<?php if( !empty($status_name) ): ?>
		<td><span id="settlement-status"><span class="acting-status<?php echo $class; ?>"><?php echo $status_name; ?></span></span></td>
		<td>
			<input type="button" id="settlement-information-<?php echo $MerchantFree1; ?>-<?php echo $num; ?>" class="button settlement-information" value="<?php _e('Settlement info','usces'); ?>">
			<input type="hidden" id="responsecd-<?php echo $MerchantFree1; ?>-<?php echo $num; ?>" value="<?php echo $ResponseCd; ?>">
		</td>
		<?php else: ?>
		<td>&nbsp;</td><td>&nbsp;</td>
		<?php endif; ?>
	</tr>
	</tbody>
	<?php $num--; ?>
<?php endforeach; ?>
<?php
	$trans_id = $usces->get_order_meta_value( 'trans_id', $order_id );
	$latest_log = $this->get_acting_latest_log( $order_id.'_'.$trans_id );
	if( $latest_log ):
		$class = ' card-'.mb_strtolower(substr($latest_log['OperateId'],1));
		$status_name = $this->get_operate_name( $latest_log['OperateId'] );
?>
	<tbody>
	<tr>
		<td>1</td>
		<td><?php echo $order_data['order_date']; ?></td>
		<td><?php echo $trans_id; ?></td>
		<?php if( !empty($status_name) ): ?>
		<td><span id="settlement-status"><span class="acting-status<?php echo $class; ?>"><?php echo $status_name; ?></span></span></td>
		<td><input type="button" id="settlement-information-<?php echo $trans_id; ?>-1" class="button settlement-information" value="<?php _e('Settlement info','usces'); ?>"></td>
		<?php else: ?>
		<td>&nbsp;</td><td>&nbsp;</td>
		<?php endif; ?>
	</tr>
	</tbody>
<?php endif; ?>
</table>
</div><!--datatable-->
<input name="member_id" type="hidden" id="member_id" value="<?php echo $member_id; ?>" />
<input name="order_id" type="hidden" id="order_id" value="<?php echo $order_id; ?>" />
<input name="usces_referer" type="hidden" id="usces_referer" value="<?php echo urlencode($curent_url); ?>" />
<?php wp_nonce_field( 'order_edit', 'wc_nonce' ); ?>
</div><!--usces_admin-->
</div><!--wrap-->
<?php
		$order_action = 'edit';
		$cart = array();
		$action_args = compact( 'order_action', 'order_id', 'cart' );
		$this->settlement_dialog( $order_data, $action_args );
		include( ABSPATH.'wp-admin/admin-footer.php' );
	}

	/**********************************************
	* dlseller_filter_card_update_mail
	* 継続課金会員クレジットカード変更依頼メール
	* @param  $message $member_id $order_id
	* @return str $message
	***********************************************/
	public function continue_member_card_update_mail( $message, $member_id, $order_id ) {
		global $usces;

		$acting_opts = $this->get_acting_settings();
		if( !usces_is_membersystem_state() || 'on' != $acting_opts['quickpay'] ) {
			return $message;
		}

		$KaiinId = $this->get_quick_kaiin_id( $member_id );
		$KaiinPass = $this->get_quick_pass( $member_id );

		if( !empty($KaiinId) && !empty($KaiinPass) ) {
			//e-SCOTT 会員照会
			$response_member = $this->escott_member_reference( $member_id, $KaiinId, $KaiinPass );
			if( 'OK' == $response_member['ResponseCd'] ) {
				$expyy = substr(date_i18n('Y', current_time('timestamp')), 0, 2).substr($response_member['CardExp'], 0, 2);
				$expmm = substr($response_member['CardExp'], 2, 2);

				$now = date_i18n( 'Ym', current_time('timestamp', 0) );
				$member_info = $usces->get_member_info( $member_id );
				$mail_data = $usces->options['mail_data'];

				$nonsessionurl = usces_url('cartnonsession', 'return');
				$parts = parse_url($nonsessionurl);
				if( isset($parts['query']) ) {
					parse_str( $parts['query'], $query );
				}
				if( false !== strpos($nonsessionurl, '/usces-cart') ) {
					$nonsessionurl = str_replace( '/usces-cart', '/usces-member', $nonsessionurl );
				} elseif( isset($query['page_id']) && $query['page_id'] == USCES_CART_NUMBER ) {
					$nonsessionurl = str_replace( 'page_id='.USCES_CART_NUMBER, 'page_id='.USCES_MEMBER_NUMBER, $nonsessionurl );
				}
				$delim = ( false === strpos($nonsessionurl, '?') ) ? '?' : '&';

				$regd = $expyy.$expmm;
				if( $regd == $now ) {
					$flag = 'NOW';
				} elseif( $regd < $now ) {
					$flag = 'PASSED';
				} else {
					return $message;
				}

				$exp = mktime( 0, 0, 0, $expmm, 1, $expyy );
				$limit = date_i18n(__('F, Y'), $exp );
				$name = usces_localized_name( $member_info['mem_name1'], $member_info['mem_name2'], 'return' );

				$message  = __('Member ID','dlseller').' : '.$member_id."\n";
				$message .= __('Contractor name','dlseller').' : '.sprintf( _x('%s','honorific','usces'), $name )."\n\n\n";
				$message .= __("Thank you very much for using our service.",'dlseller')."\r\n\r\n";
				$message .= __("Please be sure to check this notification because it is an important contact for continued use of the service under contract.",'dlseller')."\r\n\r\n";
				$message .= __("---------------------------------------------------------",'dlseller')."\r\n";
				$message .= sprintf( __("Currently registered credit card expiration date is %s, ",'dlseller'), $limit )."\r\n";
				if( 'NOW' == $flag ) {
					$message .= __("So you keep on this you will not be able to pay next month.",'dlseller')."\r\n";
				} else {
					$message .= __("So your payment of this month is outstanding payment.",'dlseller')."\r\n";
				}
				$message .= __("---------------------------------------------------------",'dlseller')."\r\n\r\n";
				$message .= __("If you have received a new credit card, ",'dlseller')."\r\n";
				$message .= __("Please click the URL below and update the card information during this month.",'dlseller')."\r\n";
				$message .= __("Sorry for troubling you, please process it.",'dlseller')."\r\n\r\n\r\n";
				$message .= $nonsessionurl.$delim.'dlseller_card_update=login&dlseller_up_mode=1&dlseller_order_id='.$order_id."\r\n";
				$message .= __("If the card information update procedure failed, please contact us at the following email address.",'dlseller')."\r\n\r\n";
				$message .= __("Thank you.",'dlseller')."\r\n\r\n\r\n";
				$message .= $mail_data['footer']['ordermail'];
				$message  = apply_filters( 'usces_filter_continue_member_card_update_mail', $message, $member_id, $member_info );
			}
		}
		return $message;
	}

	/**********************************************
	* dlseller_action_do_continuation_charging
	* 自動継続課金処理
	* @param  $today $member_id $order_id $continue_data
	* @return -
	***********************************************/
	public function auto_continuation_charging( $today, $member_id, $order_id, $continue_data ) {
		global $usces;

		$acting_opts = $this->get_acting_settings();
		if( !usces_is_membersystem_state() || 'on' != $acting_opts['quickpay'] ) {
			return;
		}

		if( 0 >= $continue_data['price'] ) {
			return;
		}

		$order_data = $usces->get_order_data( $order_id, 'direct' );
		if( !$order_data || $usces->is_status( 'cancel', $order_data['order_status'] ) ) {
			return;
		}

		$payment = $usces->getPayments( $order_data['order_payment_name'] );
		if( 'acting_welcart_card' != $payment['settlement'] ) {
			return;
		}

		$acting = $this->paymod_id.'_card';
		$KaiinId = $this->get_quick_kaiin_id( $member_id );
		$KaiinPass = $this->get_quick_pass( $member_id );
		$rand = usces_acting_key();

		if( !empty($KaiinId) && !empty($KaiinPass) ) {
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params_member = array();
			$params = array();

			//共通部
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $rand;
			$param_list['MerchantFree2'] = $payment['settlement'];
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$params_member['send_url'] = $acting_opts['send_url_member'];
			$params_member['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '4MemRefM',
					'KaiinId' => $KaiinId,
					'KaiinPass' => $KaiinPass
				)
			);
			//e-SCOTT 会員照会
			$response_member = $this->connection( $params_member );
//usces_log(print_r($response_member,true),"test.log");
			if( 'OK' == $response_member['ResponseCd'] ) {
				$params['send_url'] = $acting_opts['send_url'];
				$params['param_list'] = array_merge( $param_list,
					array(
						'OperateId' => $acting_opts['operateid_dlseller'],
						'Amount' => usces_crform( $continue_data['price'], false, false, 'return', false ),
						'PayType' => '01',
						'KaiinId' => $KaiinId,
						'KaiinPass' => $KaiinPass
					)
				);
				//e-SCOTT 決済
				$response_data = $this->connection( $params );
				$this->save_acting_history_log( $response_data, $order_id.'_'.$rand );
//usces_log(print_r($response_data,true),"test.log");
				if( 'OK' == $response_data['ResponseCd'] ) {
					//$usces->set_order_meta_value( 'trans_id', $rand, $order_id );
					//$usces->set_order_meta_value( 'wc_trans_id', $rand, $order_id );
					$cardlast4 = substr($response_member['CardNo'], -4);
					$expyy = substr(date_i18n('Y', current_time('timestamp')), 0, 2).substr($response_member['CardExp'], 0, 2);
					$expmm = substr($response_member['CardExp'], 2, 2);
					$response_data['acting'] = $acting;
					$response_data['PayType'] = '01';
					$response_data['CardNo'] = $cardlast4;
					$response_data['CardExp'] = $expyy.'/'.$expmm;
					//$usces->set_order_meta_value( $acting_flg, usces_serialize($response_data), $order_id );
					$this->save_acting_log( $response_data, $order_id.'_'.$rand );
					$this->auto_settlement_mail( $member_id, $order_id, $response_data, $continue_data );
				} else {
					$responsecd = explode( '|', $response_data['ResponseCd'] );
					foreach( (array)$responsecd as $cd ) {
						$response_data[$cd] = $this->response_message( $cd );
					}
					$log = array( 'acting'=>$acting, 'key'=>$rand, 'result'=>$response_data['ResponseCd'], 'data'=>$response_data );
					usces_save_order_acting_error( $log );
					$this->save_acting_log( $response_data, $order_id.'_'.$rand );
					$this->auto_settlement_error_mail( $member_id, $order_id, $response_data, $continue_data );
				}
				do_action( 'usces_action_auto_continuation_charging', $member_id, $order_id, $continue_data, $response_data );
			} else {
				$responsecd = explode( '|', $response_member['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$response_member[$cd] = $this->response_message( $cd );
				}
				$log = array( 'acting'=>$acting.'(member_process)', 'key'=>$member_id, 'result'=>$response_member['ResponseCd'], 'data'=>$response_member );
				usces_save_order_acting_error( $log );
				$this->save_acting_log( $response_member, $order_id.'_'.$rand );
				$this->auto_settlement_error_mail( $member_id, $order_id, $response_member, $continue_data );
				do_action( 'usces_action_auto_continuation_charging', $member_id, $order_id, $continue_data, $response_member );
			}
		} else {
			$logdata = array( 'KaiinId'=>$KaiinId, 'KaiinPass'=>$KaiinPass );
			$log = array( 'acting'=>$acting.'(member_process)', 'key'=>$member_id, 'result'=>'MEMBER ERROR', 'data'=>$logdata );
			usces_save_order_acting_error( $log );
			$log['ResponseCd'] = 'NG';
			$log['MerchantFree1'] = $rand;
			$this->save_acting_log( $log, $order_id.'_'.$rand );
			$this->auto_settlement_error_mail( $member_id, $order_id, $logdata, $continue_data );
			do_action( 'usces_action_auto_continuation_charging', $member_id, $order_id, $continue_data, $log );
		}
	}

	/**********************************************
	* 自動継続課金処理メール（正常）
	* @param  $member_id $order_id $response_data $continue_data
	* @return -
	***********************************************/
	public function auto_settlement_mail( $member_id, $order_id, $response_data, $continue_data ) {
		global $usces;

		$acting_opts = $this->get_acting_settings();
		$order_data = $usces->get_order_data( $order_id, 'direct' );
		$mail_body = $this->auto_settlement_message( $member_id, $order_id, $order_data, $response_data, $continue_data );

		if( 'on' == $acting_opts['auto_settlement_mail'] ) {
			$subject = __('Announcement of automatic continuing charging process','usces');
			$name = usces_localized_name( $order_data['order_name1'], $order_data['order_name2'], 'return' );
			$mail_data = $usces->options['mail_data'];
			$mail_header = __('We will report automated accounting process was carried out as follows.','usces')."\r\n\r\n";
			$mail_footer = __('If you have any questions, please contact us.','usces')."\r\n\r\n".$mail_data['footer']['thankyou'];
			$message = $mail_header.$mail_body.$mail_footer;
			if( isset($usces->options['put_customer_name']) && $usces->options['put_customer_name'] == 1 ) {
				$dear_name = sprintf( __('Dear %s','usces'), $name );
				$message = $dear_name."\r\n\r\n".$message;
			}
			$to_customer = array(
				'to_name' => sprintf( _x('%s','honorific','usces'), $name ),
				'to_address' => $order_data['order_email'],
				'from_name' => get_option( 'blogname' ),
				'from_address' => $usces->options['sender_mail'],
				'return_path' => $usces->options['sender_mail'],
				'subject' => $subject,
				'message' => $message
			);
			usces_send_mail( $to_customer );
		}

		$ok = ( empty($this->continuation_charging_mail['OK']) ) ? 0 : $this->continuation_charging_mail['OK'];
		$this->continuation_charging_mail['OK'] = $ok + 1;
		$this->continuation_charging_mail['mail'][] = $mail_body;
	}

	/**********************************************
	* 自動継続課金処理メール（エラー）
	* @param  $member_id $order_id $response_data $continue_data
	* @return -
	***********************************************/
	public function auto_settlement_error_mail( $member_id, $order_id, $response_data, $continue_data ) {
		global $usces;

		$acting_opts = $this->get_acting_settings();
		$order_data = $usces->get_order_data( $order_id, 'direct' );
		$mail_body = $this->auto_settlement_message( $member_id, $order_id, $order_data, $response_data, $continue_data );

		if( 'on' == $acting_opts['auto_settlement_mail'] ) {
			$subject = __('Announcement of automatic continuing charging process','usces');
			$name = usces_localized_name( $order_data['order_name1'], $order_data['order_name2'], 'return' );
			$mail_data = $usces->options['mail_data'];
			$mail_header = __('We will reported that an error occurred in automated accounting process.','usces')."\r\n\r\n";
			$mail_footer = __('If you have any questions, please contact us.','usces')."\r\n\r\n".$mail_data['footer']['thankyou'];
			$message = $mail_header.$mail_body.$mail_footer;
			if( isset($usces->options['put_customer_name']) && $usces->options['put_customer_name'] == 1 ) {
				$dear_name = sprintf( __('Dear %s','usces'), $name );
				$message = $dear_name."\r\n\r\n".$message;
			}
			$to_customer = array(
				'to_name' => sprintf( _x('%s','honorific','usces'), $name ),
				'to_address' => $order_data['order_email'],
				'from_name' => get_option( 'blogname' ),
				'from_address' => $usces->options['sender_mail'],
				'return_path' => $usces->options['sender_mail'],
				'subject' => $subject,
				'message' => $message
			);
			usces_send_mail( $to_customer );
		}

		$error = ( empty($this->continuation_charging_mail['NG']) ) ? 0 : $this->continuation_charging_mail['NG'];
		$this->continuation_charging_mail['NG'] = $error + 1;
		$this->continuation_charging_mail['mail'][] = $mail_body;
	}

	/**********************************************
	* 自動継続課金処理メール本文
	* @param  $member_id $order_id $order_data $response_data $continue_data
	* @return str $message
	***********************************************/
	public function auto_settlement_message( $member_id, $order_id, $order_data, $response_data, $continue_data ) {

		$name = usces_localized_name( $order_data['order_name1'], $order_data['order_name2'], 'return' );
		$contracted_date = ( isset($continue_data['contractedday']) ) ? $continue_data['contractedday'] : '';
		$charged_date = ( isset($continue_data['chargedday']) ) ? $continue_data['chargedday'] : '';

		$message  = usces_mail_line( 2 );//--------------------
		$message .= __('Order ID','dlseller').' : '.$order_id."\r\n";
		$message .= __('Application Date','dlseller').' : '.$order_data['order_date']."\r\n";
		$message .= __('Member ID','dlseller').' : '.$member_id."\r\n";
		$message .= __('Contractor name','dlseller').' : '.sprintf( _x('%s','honorific','usces'), $name )."\r\n";
		$message .= __('Settlement amount','usces').' : '.usces_crform( $continue_data['price'], true, false, 'return' )."\r\n";
		if( isset($response_data['MerchantFree1']) ) {
			$message .= __('Transaction ID','usces').' : '.$response_data['MerchantFree1']."\r\n";
		}
		if( isset($response_data['TransactionId']) ) {
			$message .= __('Sequence number','usces').' : '.$response_data['TransactionId']."\r\n";
		}
		if( !empty($charged_date) ) {
			$message .= __('Next Withdrawal Date','dlseller').' : '.$charged_date."\r\n";
		}
		if( !empty($contracted_date) ) {
			$message .= __('Renewal Date','dlseller').' : '.$contracted_date."\r\n";
		}
		$message .= "\r\n";
		if( isset($response_data['ResponseCd']) ) {
			if( 'OK' == $response_data['ResponseCd'] ) {
				$message .= __('Result','usces').' : '.__('Normal done','usces')."\r\n";
			} else {
				$message .= __('Result','usces').' : '.__('Error','usces')."\r\n";
				$responsecd = explode( '|', $response_data['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$message .= $cd.' : '.$this->response_message( $cd )."\r\n";
				}
			}
		} else {
			$message .= __('Result','usces').' : '.__('Error','usces')."\r\n";
			$message .= __('Credit card is not registered.','usces')."\r\n";
		}
		$message .= usces_mail_line( 2 )."\r\n";//--------------------
		return $message;
	}

	/**********************************************
	* dlseller_action_do_continuation
	* 自動継続課金処理
	* @param  $today $todays_charging
	* @return -
	***********************************************/
	public function do_auto_continuation( $today, $todays_charging ) {
		global $usces;

		if( empty($todays_charging) ) {
			return;
		}

		$ok = ( empty($this->continuation_charging_mail['OK']) ) ? 0 : $this->continuation_charging_mail['OK'];
		$error = ( empty($this->continuation_charging_mail['NG']) ) ? 0 : $this->continuation_charging_mail['NG'];
		$admin_subject = __('Automatic Continuing Charging Process Result','usces').' '.$today;
		$admin_message = __('Report that automated accounting process has been completed.','usces')."\r\n\r\n"
			.__('Processing date','usces').' : '.date_i18n( 'Y-m-d H:i:s', current_time('timestamp') )."\r\n"
			.__('Normal done','usces').' : '.$ok."\r\n"
			.__('Abnormal done','usces').' : '.$error."\r\n\r\n";
		foreach( $this->continuation_charging_mail['mail'] as $mail ) {
			$admin_message .= $mail."\r\n";
		}
		$admin_message .= __('For details, please check on the administration panel > Continuous charge member list > Continuous charge member information.','usces')."\r\n";

		$to_admin = array(
			'to_name' => apply_filters( 'usces_filter_bccmail_to_admin_name', 'Shop Admin' ), 
			'to_address' => $usces->options['order_mail'],
			'from_name' => apply_filters( 'usces_filter_bccmail_from_admin_name', 'Welcart Auto BCC' ), 
			'from_address' => $usces->options['sender_mail'],
			'return_path' => $usces->options['sender_mail'],
			'subject' => $admin_subject,
			'message' => $admin_message
		);
		usces_send_mail( $to_admin );
		unset( $this->continuation_charging_mail );
	}

	/**********************************************
	* wcad_filter_available_regular_payment_method
	* 
	* @param  $payment_method
	* @return array $payment_method
	***********************************************/
	public function available_regular_payment_method( $payment_method ) {

		$acting_opts = $this->get_acting_settings();
		if( isset($acting_opts['quickpay']) && 'on' == $acting_opts['quickpay'] ) {
			$payment_method[] = 'acting_welcart_card';
		}
		return $payment_method;
	}

	/**********************************************
	* wcad_filter_shippinglist_acting
	* 
	* @param  $acting
	* @return str $acting
	***********************************************/
	public function set_shippinglist_acting( $acting ) {

		$acting = 'acting_welcart_card';
		return $acting;
	}

	/**********************************************
	* wcad_action_reg_auto_orderdata
	* 定期購入決済処理
	* @param  $args = array(
				'cart'=>$cart, 'entry'=>$entry, 'order_id'=>$new_id, 
				'member_id'=>$regular_order['reg_mem_id'], 'payments'=>$payments, 'charging_type'=>$charging_type,
				'total_amount'=>$total_price+$tax, 'reg_id'=>$reg_id, 
				);
	* @return -
	***********************************************/
	public function register_auto_orderdata( $args ) {
		global $usces;
		extract($args);

		$acting_opts = $this->get_acting_settings();
		if( !usces_is_membersystem_state() || 'on' != $acting_opts['quickpay'] ) {
			return;
		}

		if( 0 >= $total_amount ) {
			return;
		}

		$acting_flg = $payments['settlement'];
		if( 'acting_welcart_card' != $payments['settlement'] ) {
			return;
		}

		$settltment_errmsg = '';
		$acting = $this->paymod_id.'_card';
		$KaiinId = $this->get_quick_kaiin_id( $member_id );
		$KaiinPass = $this->get_quick_pass( $member_id );
		$rand = usces_acting_key();

		if( !empty($KaiinId) && !empty($KaiinPass) ) {
			$TransactionDate = $this->get_transaction_date();
			$param_list = array();
			$params_member = array();
			$params = array();

			//共通部
			$param_list['MerchantId'] = $acting_opts['merchant_id'];
			$param_list['MerchantPass'] = $acting_opts['merchant_pass'];
			$param_list['TransactionDate'] = $TransactionDate;
			$param_list['MerchantFree1'] = $rand;
			$param_list['MerchantFree2'] = 'acting_welcart_card';
			$param_list['MerchantFree3'] = $this->merchantfree3;
			$param_list['TenantId'] = $acting_opts['tenant_id'];
			$params_member['send_url'] = $acting_opts['send_url_member'];
			$params_member['param_list'] = array_merge( $param_list,
				array(
					'OperateId' => '4MemRefM',
					'KaiinId' => $KaiinId,
					'KaiinPass' => $KaiinPass
				)
			);
			//e-SCOTT 会員照会
			$response_member = $this->connection( $params_member );
//usces_log(print_r($response_member,true),"test.log");
			if( 'OK' == $response_member['ResponseCd'] ) {
				$params['send_url'] = $acting_opts['send_url'];
				$params['param_list'] = array_merge( $param_list,
					array(
						'OperateId' => $acting_opts['operateid'],
						'Amount' => $total_amount,
						'PayType' => '01',
						'KaiinId' => $KaiinId,
						'KaiinPass' => $KaiinPass
					)
				);
				//e-SCOTT 決済
				$response_data = $this->connection( $params );
				$this->save_acting_history_log( $response_data, $order_id.'_'.$rand );
//usces_log(print_r($response_data,true),"test.log");
				if( 'OK' == $response_data['ResponseCd'] ) {
					$usces->set_order_meta_value( 'trans_id', $rand, $order_id );
					$usces->set_order_meta_value( 'wc_trans_id', $rand, $order_id );
					$cardlast4 = substr($response_member['CardNo'], -4);
					$expyy = substr(date_i18n('Y', current_time('timestamp')), 0, 2).substr($response_member['CardExp'], 0, 2);
					$expmm = substr($response_member['CardExp'], 2, 2);
					$response_data['acting'] = $acting;
					$response_data['PayType'] = '01';
					$response_data['CardNo'] = $cardlast4;
					$response_data['CardExp'] = $expyy.'/'.$expmm;
					$usces->set_order_meta_value( $acting_flg, usces_serialize($response_data), $order_id );
				} else {
					$settltment_errmsg = __('[Regular purchase] Settlement was not completed.','autodelivery');
					$responsecd = explode( '|', $response_data['ResponseCd'] );
					foreach( (array)$responsecd as $cd ) {
						$response_data[$cd] = $this->response_message( $cd );
					}
					$log = array( 'acting'=>$acting, 'key'=>$rand, 'result'=>$response_data['ResponseCd'], 'data'=>$response_data );
					usces_save_order_acting_error( $log );
				}
				do_action( 'usces_action_register_auto_orderdata', $args, $response_data );
			} else {
				$settltment_errmsg = __('[Regular purchase] Member information acquisition error.','autodelivery');
				$responsecd = explode( '|', $response_member['ResponseCd'] );
				foreach( (array)$responsecd as $cd ) {
					$response_member[$cd] = $this->response_message( $cd );
				}
				$log = array( 'acting'=>$acting.'(member_process)', 'key'=>$member_id, 'result'=>$response_member['ResponseCd'], 'data'=>$response_member );
				usces_save_order_acting_error( $log );
				do_action( 'usces_action_register_auto_orderdata', $args, $response_member );
			}
			if( '' != $settltment_errmsg ) {
				$settlement = array( "settltment_status"=>__('Failure','autodelivery'), "settltment_errmsg"=>$settltment_errmsg );
				$usces->set_order_meta_value( $acting_flg, usces_serialize($settlement), $order_id );
				wcad_settlement_error_mail( $order_id, $settltment_errmsg );
			}
		} else {
			$logdata = array( 'KaiinId'=>$KaiinId, 'KaiinPass'=>$KaiinPass );
			$log = array( 'acting'=>$acting.'(member_process)', 'key'=>$member_id, 'result'=>'MEMBER ERROR', 'data'=>$logdata );
			usces_save_order_acting_error( $log );
			do_action( 'usces_action_register_auto_orderdata', $args, $log );
		}
	}

	/**********************************************
	* 決済ログ出力
	* @param  $log $log_key
	* @return $res
	***********************************************/
	private function save_acting_log( $log, $log_key ) {
		global $wpdb;

		$log_table_name = $wpdb->prefix.'usces_log';
		$datetime = current_time('mysql');
		$query = $wpdb->prepare( "INSERT INTO {$log_table_name} ( `datetime`, `log`, `log_type`, `log_key` ) VALUES ( %s, %s, %s, %s )",
			$datetime,
			usces_serialize($log),
			'acting_welcart',
			$log_key
		);
		$res = $wpdb->query( $query );
		return $res;
	}

	/**********************************************
	* 決済ログ取得
	* @param  $order_id ($log_key)
	* @return array $log_data
	***********************************************/
	private function get_acting_log( $order_id, $log_key = '' ) {
		global $wpdb;

		$log_table_name = $wpdb->prefix.'usces_log';
		if( !empty($log_key) ) {
			$query = $wpdb->prepare( "SELECT * FROM {$log_table_name} WHERE `log_type` = 'acting_welcart' AND `log_key` = %s ORDER BY datetime DESC", $log_key );
		} else {
			$query = "SELECT * FROM {$log_table_name} WHERE `log_type` = 'acting_welcart' AND `log_key` LIKE '{$order_id}_%' ORDER BY datetime DESC";
		}
		$log_data = $wpdb->get_results( $query, ARRAY_A );
		return $log_data;
	}

	/**********************************************
	* 決済ログ更新
	* @param  $log $log_key
	* @return $res
	***********************************************/
	private function update_acting_log( $log, $log_key ) {
		global $wpdb;

		$log_table_name = $wpdb->prefix.'usces_log';
		$datetime = current_time('mysql');
		$query = $wpdb->prepare( "UPDATE {$log_table_name} SET `datetime` = %s, `log` = %s WHERE `log_type` = %s AND `log_key` = %s",
			$datetime,
			usces_serialize($log),
			'acting_welcart',
			$log_key
		);
		$res = $wpdb->query( $query );
		return $res;
	}

	/**********************************************
	* 決済履歴ログ出力
	* @param  $log $log_key
	* @return $res
	***********************************************/
	private function save_acting_history_log( $log, $log_key ) {
		global $wpdb;

		$log_table_name = $wpdb->prefix.'usces_log';
		$datetime = current_time('mysql');
		$query = $wpdb->prepare( "INSERT INTO {$log_table_name} ( `datetime`, `log`, `log_type`, `log_key` ) VALUES ( %s, %s, %s, %s )",
			$datetime,
			usces_serialize($log),
			'acting_welcart_history',
			$log_key
		);
		$res = $wpdb->query( $query );
		return $res;
	}

	/**********************************************
	* 決済履歴ログ取得
	* @param  $log_key '[order_id]_[trans_id]'
	* @return array $log_data
	***********************************************/
	private function get_acting_history_log( $log_key ) {
		global $wpdb;

		$log_table_name = $wpdb->prefix.'usces_log';
		$query = $wpdb->prepare( "SELECT * FROM {$log_table_name} WHERE `log_type` = 'acting_welcart_history' AND `log_key` = %s ORDER BY datetime DESC", $log_key );
		$log_data = $wpdb->get_results( $query, ARRAY_A );
		return $log_data;
	}

	/**********************************************
	* 初回決済処理取得
	* @param  $log_key '[order_id]_[trans_id]'
	* @return str $operateid
	***********************************************/
	private function get_acting_first_operateid( $log_key ) {
		global $wpdb;

		$log_table_name = $wpdb->prefix.'usces_log';
		$query = $wpdb->prepare( "SELECT * FROM {$log_table_name} WHERE `log_type` = 'acting_welcart_history' AND `log_key` = %s ORDER BY datetime ASC", $log_key );
		$log_data = $wpdb->get_results( $query, ARRAY_A );
		if( $log_data ) {
			$log = usces_unserialize( $log_data[0]['log'] );
			$operateid = ( isset($log['OperateId']) ) ? $log['OperateId'] : '';
		} else {
			$operateid = '';
		}
		return $operateid;
	}

	/**********************************************
	* 決済履歴
	* @param  $log_key '[order_id]_[trans_id]'
	* @return $html
	***********************************************/
	private function settlement_history( $log_key ) {

		$html = '';
		$log_data = $this->get_acting_history_log( $log_key );
		if( $log_data ) {
			$num = count($log_data);
			$html = '<table class="settlement-history">
				<thead class="settlement-history-head">
					<tr><th></th><th>'.__('Processing date','usces').'</th><th>'.__('Sequence number','usces').'</th><th>'.__('Processing classification','usces').'</th><th>'.__('Result','usces').'</th></tr>
				</thead>
				<tbody class="settlement-history-body">';
			foreach( (array)$log_data as $data ) {
				$log = usces_unserialize( $data['log'] );
				$class = ( $log['ResponseCd'] != 'OK' ) ? ' error' : '';
				$operate_name = ( isset($log['OperateId']) ) ? $this->get_operate_name( $log['OperateId'] ) : '';
				$html .= '<tr>
					<td class="num">'.$num.'</td>
					<td class="datetime">'.$data['datetime'].'</td>
					<td class="transactionid">'.$log['TransactionId'].'</td>
					<td class="operateid">'.$operate_name.'</td>
					<td class="responsecd'.$class.'">'.$log['ResponseCd'].'</td>
				</tr>';
				$num--;
			}
			$html .= '</tbody>
				</table>';
		}
		return $html;
	}

	/**********************************************
	* 最新処理取得
	* @param  $log_key '[order_id]_[trans_id]'
	* @return array $latest_log
	***********************************************/
	private function get_acting_latest_log( $log_key, $responsecd = 'OK' ) {

		$latest_log = array();
		$latest_status = array( '1Auth', '1Capture', '1Gathering', '1Delete', '2Add', '2Chg', '2Del', '5Auth', '5Gathering', '5Capture', '5Delete', 'receipted' );
		$primarily_status = array( '1Auth', '1Capture', '1Gathering', '2Add', '5Auth', '5Gathering', '5Capture', 'receipted' );//取消以外
		$reauth_status = array( '1ReAuth' );//再オーソリ
		$log_data = $this->get_acting_history_log( $log_key );
		if( $log_data ) {
			if( $responsecd == 'OK' ) {
				$reauth = false;
				foreach( (array)$log_data as $data ) {
					$log = usces_unserialize( $data['log'] );
					if( isset($log['ResponseCd']) ) {
						if( $log['ResponseCd'] == 'OK' && in_array( $log['OperateId'], $reauth_status ) ) {
							$reauth = true;
						} else {
							if( $reauth ) {
								if( $log['ResponseCd'] == 'OK' && in_array( $log['OperateId'], $primarily_status ) ) {
									$latest_log = $log;
									break;
								}
							} else {
								if( $log['ResponseCd'] == 'OK' && in_array( $log['OperateId'], $latest_status ) ) {
									$latest_log = $log;
									break;
								}
							}
						}
					}
				}
			} else {
				$latest_log = usces_unserialize( $log_data[0]['log'] );
			}
		}
		return $latest_log;
	}

	/**********************************************
	* 最新処理ステータス取得
	* @param  $member_id $order_id
	* @return str $status
	***********************************************/
	public function get_latest_status( $member_id, $order_id ) {
		global $usces;

		$status = '';
		$log_data = $this->get_acting_log( $order_id );
		if( 0 < count($log_data) ) {
			$acting_data = usces_unserialize($log_data[0]['log']);
			$trans_id = ( isset($acting_data['MerchantFree1']) ) ? $acting_data['MerchantFree1'] : '';
		} else {
			$trans_id = $usces->get_order_meta_value( 'trans_id', $order_id );
		}
		if( $trans_id ) {
			$latest_log = $this->get_acting_latest_log( $order_id.'_'.$trans_id, 'ALL' );
			$status = ( isset($latest_log['ResponseCd']) ) ? $latest_log['ResponseCd'] : 'NG';
		}
		return $status;
	}

	/**********************************************
	* 処理区分名称取得
	* @param  $log_key '[order_id]_[trans_id]'
	* @return str $status_name
	***********************************************/
	private function get_acting_status_name( $log_key ) {

		$status_name = '';
		$log_data = $this->get_acting_history_log( $log_key );
		if( $log_data ) {
			$log = usces_unserialize( $log_data[0]['log'] );
			if( isset($log['OperateId']) ) {
				$status_name = $this->get_operate_name( $log['OperateId'] );
			}
		}
		return $status_name;
	}

	/**********************************************
	* 期限切れチェック
	* @param  $order_id $trans_id
	* @return boolean
	***********************************************/
	private function check_paylimit( $order_id, $trans_id ) {
		global $usces;

		$expiration = false;
		$receipted = false;
		$log_data = $this->get_acting_history_log( $order_id.'_'.$trans_id );
		if( $log_data ) {
			foreach( (array)$log_data as $data ) {
				$log = usces_unserialize( $data['log'] );
				if( isset($log['OperateId']) && 'receipted' == $log['OperateId'] ) {
					$receipted = true;
					break;
				}
			}
		}
		if( $receipted ) {
			return false;
		}
		$today = date_i18n( 'YmdHi', current_time('timestamp') );
		$acting_data = usces_unserialize( $usces->get_order_meta_value( 'acting_welcart_conv', $order_id ) );
		if( $today > $acting_data['PayLimit'] ) {
			$expiration = true;
		}
		return $expiration;
	}

	/**********************************************
	* 削除済みチェック
	* @param  $log_key '[order_id]_[trans_id]'
	* @return boolean
	***********************************************/
	private function check_deleted( $log_key ) {

		$deleted = false;
		$log_data = $this->get_acting_history_log( $log_key );
		if( $log_data ) {
			foreach( (array)$log_data as $data ) {
				$log = usces_unserialize( $data['log'] );
				if( isset($log['OperateId']) && '2Del' == $log['OperateId'] ) {
					$deleted = true;
					break;
				}
			}
		}
		return $deleted;
	}

	/**********************************************
	* 継続課金会員データ取得
	* @param  $order_id ($log_key)
	* @return array $log_data
	***********************************************/
	private function get_continuation_data( $order_id, $member_id ) {
		global $wpdb;

		$continuation_table_name = $wpdb->prefix.'usces_continuation';
		$query = $wpdb->prepare( "SELECT 
			`con_acting` AS `acting`, 
			`con_order_price` AS `order_price`, 
			`con_price` AS `price`, 
			`con_next_charging` AS `chargedday`, 
			`con_next_contracting` AS `contractedday`, 
			`con_startdate` AS `startdate`, 
			`con_status` AS `status` 
			FROM {$continuation_table_name} 
			WHERE con_order_id = %d AND con_member_id = %d", 
			$order_id, $member_id
		);
		$data = $wpdb->get_row( $query, ARRAY_A );
		return $data;
	}

	/**********************************************
	* 継続課金会員データ更新
	* @param  $log $log_key
	* @return $res
	***********************************************/
	private function update_continuation_data( $order_id, $member_id, $data, $stop = false ) {
		global $wpdb;

		$continuation_table_name = $wpdb->prefix.'usces_continuation';
		if( $stop ) {
			$query = $wpdb->prepare( "UPDATE {$continuation_table_name} SET 
				`con_status` = 'cancellation' 
				WHERE `con_order_id` = %d AND `con_member_id` = %d", 
				$order_id, $member_id 
			);
		} else {
			$query = $wpdb->prepare( "UPDATE {$continuation_table_name} SET 
				`con_price` = %f, 
				`con_next_charging` = %s, 
				`con_next_contracting` = %s, 
				`con_status` = %s 
				WHERE `con_order_id` = %d AND `con_member_id` = %d", 
				$data['price'], 
				$data['chargedday'], 
				$data['contractedday'], 
				$data['status'], 
				$order_id, $member_id 
			);
		}
		$res = $wpdb->query( $query );
		return $res;
	}

	/**********************************************
	* 日付チェック
	* @param  $date
	* @return boolean
	***********************************************/
	private function isdate( $date ) {

		if( empty($date) ) {
			return false;
		}
		try {
			new DateTime( $date );
			list( $year, $month, $day ) = explode( '-', $date );
			$res = checkdate( (int)$month, (int)$day, (int)$year );
			return $res;
		} catch( Exception $e ) {
			return false;
		}
	}

	/**********************************************
	* usces_fiter_the_payment_method
	* 支払方法
	* @param  $payments
	* @return array $payments
	***********************************************/
	public function payment_method( $payments ) {
		global $usces;

		$conv_exclusion = false;
		$atodene_exclusion = false;

		if( usces_have_regular_order() ) {
			$conv_exclusion = true;

		} elseif( usces_have_continue_charge() ) {
			$conv_exclusion = true;
			$atodene_exclusion = true;

		} else {
			$acting_opts = $this->get_acting_settings();
			if( 'on' == $acting_opts['atodene_byitem'] ) {//商品ごとの可否が有効
				$cart = $usces->cart->get_cart();
				foreach( $cart as $cart_row ) {
					$atodene_propriety = get_post_meta( $cart_row['post_id'], 'atodene_propriety', true );
					if( 1 == (int)$atodene_propriety ) {
						$atodene_exclusion = true;
						break;
					}
				}
			}
		}

		if( $conv_exclusion ) {
			foreach( $payments as $key => $payment ) {
				if( $this->acting_flg_conv == $payment['settlement'] ) {
					unset( $payments[$key] );
				}
			}
		}
		if( $atodene_exclusion ) {
			foreach( $payments as $key => $payment ) {
				if( $this->acting_flg_atodene == $payment['settlement'] ) {
					unset( $payments[$key] );
				}
			}
		}

		return $payments;
	}

	/**********************************************
	* ATODENE CSVアップロード
	* @param  $order_action
	* @return -
	***********************************************/
	function atodene_upload() {

		if( isset($_POST['page']) && $_POST['page'] == 'atodene_results_csv' && isset($_POST['action']) && $_POST['action'] == 'atodene_upload' ) {

			$path = WP_CONTENT_DIR.'/uploads/';
			$workfile = $_FILES["atodene_upcsv"]["tmp_name"];
			if( !is_uploaded_file( $workfile ) ) {
				$message = __('The file was not uploaded.','usces');
				wp_redirect( add_query_arg( array( 'page'=>'usces_orderlist', 'usces_status'=>'error', 'usces_message'=>urlencode($message), 'order_action'=>'atodene_upload' ), USCES_ADMIN_URL ) );
				exit();
			}

			list( $fname, $fext ) = explode( '.', $_FILES["atodene_upcsv"]["name"], 2 );
			if( $fext != 'csv' ) {
				$message =  __('The file is not supported.','usces').$fname.'.'.$fext;
				wp_redirect( add_query_arg( array( 'page'=>'usces_orderlist', 'usces_status'=>'error', 'usces_message'=>urlencode($message), 'order_action'=>'atodene_upload' ), USCES_ADMIN_URL ) );
				exit();
			}

			$new_filename = base64_encode( $fname.'_'.time().'.'.$fext );
			if( !move_uploaded_file( $_FILES['atodene_upcsv']['tmp_name'], $path.$new_filename ) ) {
				$message = __('The file was not stored.','usces').$_FILES["atodene_upcsv"]["name"];
				wp_redirect( add_query_arg( array( 'page'=>'usces_orderlist', 'usces_status'=>'error', 'usces_message'=>urlencode($message), 'order_action'=>'atodene_upload' ), USCES_ADMIN_URL ) );
				exit();
			}

			wp_redirect( add_query_arg( array( 'page'=>'usces_orderlist', 'usces_status'=>'none', 'usces_message'=>'', 'order_action'=>'upload_atodene_results', 'atodene_upfile'=>urlencode($new_filename), 'wc_nonce'=>wp_create_nonce('order_list') ), USCES_ADMIN_URL ) );
			exit();
		}
	}

	/**********************************************
	* ATODENE CSV出力・CSVアップロード
	* @param  $order_action
	* @return -
	***********************************************/
	public function output_atodene_csv( $order_action ) {

		switch( $order_action ) {
		case 'download_atodene_register':
			$this->download_atodene_register();
			break;
		case 'download_atodene_update':
			$this->download_atodene_update();
			break;
		case 'download_atodene_report':
			$this->download_atodene_report();
			break;
		case 'upload_atodene_results':
			if( isset($_GET['atodene_upfile']) && !WCUtils::is_blank($_GET['atodene_upfile']) ) {
				$res = $this->upload_atodene_results();
				$_GET['usces_status'] = ( isset($res['status']) ) ? $res['status'] : '';
				$_GET['usces_message'] = ( isset($res['message']) ) ? $res['message'] : '';
			}
			break;
		}
	}

	/**********************************************
	* ATODENE アクションボタン
	* @param  -
	* @return -
	***********************************************/
	public function action_atodene_button() {
?>
				<input type="button" id="dl_atodene_register_csv" class="searchbutton" value="<?php _e('ATODENE transaction registration CSV output','usces'); ?>" />
				<!--<input type="button" id="dl_atodene_update_csv" class="searchbutton" value="<?php _e('ATODENE transaction batch change and cancel CSV output','usces'); ?>" />-->
				<input type="button" id="up_atodene_results_csv" class="searchbutton" value="<?php _e('ATODENE credit review result CSV upload','usces'); ?>" />
				<input type="button" id="dl_atodene_report_csv" class="searchbutton" value="<?php _e('ATODENE shipping report registration CSV output','usces'); ?>" />
<?php
	}

	/**********************************************
	* ATODENE CSVアップロードダイアログ
	* @param  -
	* @return -
	***********************************************/
	public function order_list_footer() {

		$html = '
		<div id="atodene_upload_dialog" class="upload_dialog">
			<p>'.__("Upload the prescribed CSV file and import credit screening results.<br />Please choose a file, and press 'Start of capture'.",'usces').'</p>
			<form action="'.USCES_ADMIN_URL.'" method="post" enctype="multipart/form-data" name="atodene_upform" id="atodene_upform">
				<fieldset>
					<p><input name="atodene_upcsv" type="file" class="filename" /></p>
				</fieldset>
				<p><input name="atodene_uploadcsv" type="submit" class="button" value="'.__('Start of capture','usces').'" /></p>
				<input name="page" type="hidden" value="atodene_results_csv" />
				<input name="action" type="hidden" value="atodene_upload" />
			</form>
		</div>';
		echo $html;
	}

	/**********************************************
	* ATODENE CSVダウンロードダイアログ
	* @param  -
	* @return -
	***********************************************/
	public function order_list_page_js() {

		$html = '
		$(document).on( "click", "#dl_atodene_register_csv", function() {
			if( $("input[name*=\'listcheck\']:checked").length == 0 ) {
				alert("'.__('Choose the data.','usces').'");
				$("#orderlistaction").val("");
				return false;
			}
			var listcheck = "";
			$("input[name*=\'listcheck\']").each( function(i) {
				if( $(this).attr("checked") ) {
					listcheck += "&listcheck["+i+"]="+$(this).val();
				}
			});
			location.href = "'.USCES_ADMIN_URL.'?page=usces_orderlist&order_action=download_atodene_register"+listcheck+"&noheader=true&nonce='.wp_create_nonce('csv_nonce').'";
		});';
/*
		$(document).on( "click", "#dl_atodene_update_csv", function() {
			if( $("input[name*=\'listcheck\']:checked").length == 0 ) {
				alert("'.__('Choose the data.','usces').'");
				$("#orderlistaction").val("");
				return false;
			}
			var listcheck = "";
			$("input[name*=\'listcheck\']").each( function(i) {
				if( $(this).attr("checked") ) {
					listcheck += "&listcheck["+i+"]="+$(this).val();
				}
			});
			location.href = "'.USCES_ADMIN_URL.'?page=usces_orderlist&order_action=download_atodene_update"+listcheck+"&noheader=true&nonce='.wp_create_nonce('csv_nonce').'";
		});
*/
		$html .= '
		$(document).on( "click", "#dl_atodene_report_csv", function() {
			if( $("input[name*=\'listcheck\']:checked").length == 0 ) {
				alert("'.__('Choose the data.','usces').'");
				$("#orderlistaction").val("");
				return false;
			}
			var listcheck = "";
			$("input[name*=\'listcheck\']").each( function(i) {
				if( $(this).attr("checked") ) {
					listcheck += "&listcheck["+i+"]="+$(this).val();
				}
			});
			location.href = "'.USCES_ADMIN_URL.'?page=usces_orderlist&order_action=download_atodene_report"+listcheck+"&noheader=true&nonce='.wp_create_nonce('csv_nonce').'";
		});

		$(document).on( "click", "#up_atodene_results_csv", function() {
			$("#atodene_upload_dialog").dialog({
				bgiframe: true,
				autoOpen: false,
				title: "'.__('Credit Review Result CSV Capture','usces').'",
				height: 350,
				width: 550,
				modal: true,
				buttons: {
					"'.__('Close').'": function() {
						$(this).dialog("close");
					}
				},
				close: function() {
				}
			}).dialog( "open" );
		});';

		return $html;
	}

	/**********************************************
	* ATODENE アクションステータス
	* @param  -
	* @return -
	***********************************************/
	public function order_list_action_status( $status ) {
		if( isset($_GET['order_action']) && ( 'atodene_upload' == $_GET['order_action'] || 'upload_atodene_results' == $_GET['order_action'] ) && isset( $_GET['usces_status'] ) && !empty( $_GET['usces_status'] ) ) {
			$status = $_GET['usces_status'];
		}
		return $status;
	}
	public function order_list_action_message( $message ) {
		if( isset($_GET['order_action']) && ( 'atodene_upload' == $_GET['order_action'] || 'upload_atodene_results' == $_GET['order_action'] ) && isset( $_GET['usces_message'] ) && !empty( $_GET['usces_message'] ) ) {
			$message = urldecode($_GET['usces_message']);
		}
		return $message;
	}

	/**********************************************
	* usces_item_master_second_section
	* 後払い決済の可否
	* @param  $second_section $post_id
	* @return html $second_section
	***********************************************/
	public function edit_item_atodene_byitem( $second_section, $post_id ) {
		global $usces;

		$division = $usces->getItemDivision( $post_id );
		$charging_type = $usces->getItemChargingType( $post_id );
		$acting_opts = $this->get_acting_settings();
		if( 'shipped' == $division && 'continue' != $charging_type && 'on' == $acting_opts['atodene_byitem'] ) {//商品ごとの可否が有効
			$atodene_propriety = get_post_meta( $post_id, 'atodene_propriety', true );
			$checked = ( 1 == (int)$atodene_propriety ) ? array( '', ' checked="checked"' ) : array( ' checked="checked"', '' );
			$second_section .= '
			<tr>
				<th>'.__('Atobarai Propriety','usces').'</th>
				<td>
					<label for="atodene_propriety_0"><input name="atodene_propriety" id="atodene_propriety_0" type="radio" value="0"'.$checked[0].'>'.__('available','usces').'</label>
					<label for="atodene_propriety_1"><input name="atodene_propriety" id="atodene_propriety_1" type="radio" value="1"'.$checked[1].'>'.__('not available','usces').'</label>
				</td>
			</tr>';
		}
		return $second_section;
	}

	/**********************************************
	* usces_action_save_product
	* 後払い決済の可否更新
	* @param  $post_id $post
	* @return -
	***********************************************/
	public function save_item_atodene_byitem( $post_id, $post ) {

		if( isset($_POST['atodene_propriety']) ) {
			update_post_meta( $post_id, 'atodene_propriety', $_POST['atodene_propriety'] );
		}
	}

	/**********************************************
	* usces_filter_nonacting_settlements
	* 
	* @param  $cod_fee $entries $total_items_price $use_point $discount $shipping_charge $amount_by_cod
	* @return float $cod_fee
	***********************************************/
	public function nonacting_settlements( $nonacting_settlements ) {

		if( !in_array( 'acting_welcart_atodene', $nonacting_settlements ) ) {
			$nonacting_settlements[] = 'acting_welcart_atodene';
		}
		return $nonacting_settlements;
	}

	/**********************************************
	* wcad_filter_the_payment_method_restriction
	* 
	* @param  $payments_restriction $value
	* @return array $payments_restriction
	***********************************************/
	function payment_method_restriction_atodene( $payments_restriction, $value ) {
		global $usces;

		$acting_opts = $this->get_acting_settings();
		if( usces_have_regular_order() ) {
			$atodene_exclusion = false;
			if( 'on' == $acting_opts['atodene_byitem'] ) {//商品ごとの可否が有効
				$cart = $usces->cart->get_cart();
				foreach( $cart as $cart_row ) {
					$atodene_propriety = get_post_meta( $cart_row['post_id'], 'atodene_propriety', true );
					if( 1 == (int)$atodene_propriety ) {
						$atodene_exclusion = true;
						break;
					}
				}
			}
			if( !$atodene_exclusion ) {
				$payments = usces_get_system_option( 'usces_payment_method', 'settlement' );
				$payments_restriction[] = $payments['acting_welcart_atodene'];
				foreach( (array)$payments_restriction as $key => $value ) {
					$sort[$key] = $value['sort'];
				}
				array_multisort( $sort, SORT_ASC, $payments_restriction );
			}
		}
		return $payments_restriction;
	}

	/**********************************************
	* ATODENE 取引登録CSV出力
	* @param  
	* @return -
	***********************************************/
	public function download_atodene_register() {
		global $usces;

		$acting_opts = $this->get_acting_settings();
		$filename = mb_convert_encoding(__('ATODENE_transaction_','usces'), 'SJIS', 'UTF-8').date_i18n( 'YmdHis', current_time('timestamp') ).".csv";

		$line = '"ご購入店受注番号","購入者注文日","会社名","部署名","氏名","氏名（フリガナ）","郵便番号","住所","電話番号","メールアドレス","配送先会社名","配送先部署名","配送先氏名","配送先氏名（フリガナ）","配送先郵便番号","配送先住所","配送先電話番号","請求書送付方法","予備領域1","予備領域2","予備領域3","顧客請求総額（税込）","明細名（商品名）","単価（税込）","数量"'."\r\n";

		$ids = $_GET['listcheck'];
		foreach( (array)$ids as $order_id ) {
			$order_data = $usces->get_order_data( $order_id, 'direct' );
			$payment = $usces->getPayments( $order_data['order_payment_name'] );
			if( 'acting_welcart_atodene' != $payment['settlement'] ) {
				continue;
			}

			$delivery = usces_unserialize($order_data['order_delivery']);
			$cart = usces_get_ordercartdata( $order_id );

			$order_date = substr( $order_data['order_date'], 0, 10 );
			$date = str_replace( '-', '/', $order_date );

			$company = $usces->get_order_meta_value( 'cscs_company', $order_id );
			$order_name = $order_data['order_name1'].$order_data['order_name2'];
			$order_kana = mb_convert_kana($order_data['order_name3'], 'ak', 'UTF-8').mb_convert_kana($order_data['order_name4'], 'ak', 'UTF-8');
			$order_zip = str_replace("ー", "", mb_convert_kana($order_data['order_zip'], 'a', 'UTF-8'));
			$order_post = str_replace("-", "", $order_zip);
			$order_address = $order_data['order_pref'].$order_data['order_address1'];
			if( !empty($order_data['order_address2']) ) $order_address .= mb_convert_kana($order_data['order_address2'], 'ak', 'UTF-8');
			if( !empty($order_data['order_address3']) ) $order_address .= mb_convert_kana($order_data['order_address3'], 'ak', 'UTF-8');
			$order_tel = $order_data['order_tel'];
			$email = $order_data['order_email'];

			$shipto_company = $usces->get_order_meta_value( 'csde_company', $order_id );
			$shipto_name = $delivery['name1'].$delivery['name2'];
			$shipto_kana = mb_convert_kana($delivery['name3'], 'ak', 'UTF-8').mb_convert_kana($delivery['name4'], 'ak', 'UTF-8');
			$shipto_zip = str_replace("ー", "", mb_convert_kana($delivery['zipcode'], 'a', 'UTF-8'));
			$shipto_post = str_replace("-", "", $delivery['zipcode']);
			$shipto_address = $delivery['pref'].$delivery['address1'];
			if( !empty($delivery['address2']) ) $shipto_address .= mb_convert_kana($delivery['address2'], 'ak', 'UTF-8');
			if( !empty($delivery['address3']) ) $shipto_address .= mb_convert_kana($delivery['address3'], 'ak', 'UTF-8');
			$shipto_tel = $delivery['tel'];

			$amount = $order_data['order_item_total_price'] - $order_data['order_usedpoint'] + $order_data['order_discount'] + $order_data['order_shipping_charge'] + $order_data['order_cod_fee'] + $order_data['order_tax'];

			$line .= '"'.$order_id.'",'.
				'"'.$date.'",'.
				'"'.$company.'","",'.
				'"'.$order_name.'",'.
				'"'.$order_kana.'",'.
				'"'.$order_post.'",'.
				'"'.$order_address.'",'.
				'"'.$order_tel.'",'.
				'"'.$email.'",'.
				'"'.$shipto_company.'","",'.
				'"'.$shipto_name.'",'.
				'"'.$shipto_kana.'",'.
				'"'.$shipto_post.'",'.
				'"'.$shipto_address.'",'.
				'"'.$shipto_tel.'",'.
				'"'.$acting_opts['atodene_billing_method'].'","","","",'.
				'"'.$amount.'",';

			$row = 1;
			foreach( $cart as $cart_row ) {
				if( 1 < $row ) {
					$line .= '"","","","","","","","","","","","","","","","","","","","","","",';
				}
				$line .= '"'.$cart_row['item_name'].'",';
				$line .= '"'.usces_crform( $cart_row['price'], false, false, 'return', false ).'",';
				$line .= '"'.$cart_row['quantity'].'"'."\r\n";
				$row++;
			}

			if( $order_data['order_discount'] != 0 ) {
				$line .= '"","","","","","","","","","","","","","","","","","","","","","",';
				$line .= '"'.apply_filters( 'usces_confirm_discount_label', __('Discount', 'usces'), $order_id ).'",';
				$line .= '"'.usces_crform( $cart_row['order_discount'], false, false, 'return', false ).'","1"'."\r\n";
			}

			if( usces_is_tax_display() && 'products' == usces_get_tax_target() && 'exclude' == usces_get_tax_mode() ) {
				$line .= '"","","","","","","","","","","","","","","","","","","","","","",';
				$line .= '"'.usces_tax_label( $order_data, 'return' ).'",';
				$line .= '"'.usces_tax( $order_data, 'return' ).'","1"'."\r\n";
			}

			if( usces_is_member_system() && usces_is_member_system_point() && 0 == usces_point_coverage() && $order_data['order_usedpoint'] != 0 ) {
				$line .= '"","","","","","","","","","","","","","","","","","","","","","",';
				$line .= '"'.__('use of points','usces').'",';
				$line .= '"'.number_format($order_data['order_usedpoint']).'","1"'."\r\n";
			}

			if ( 0 < $order_data['order_shipping_charge'] ) {
				$line .= '"","","","","","","","","","","","","","","","","","","","","","",';
				$line .= '"'.__('Shipping','usces').'",';
				$line .= '"'.usces_crform( $order_data['order_shipping_charge'], false, false, 'return', false ).'","1"'."\r\n";
			}

			if ( 0 < $order_data['order_cod_fee'] ) {
				$line .= '"","","","","","","","","","","","","","","","","","","","","","",';
				$line .= '"'.apply_filters( 'usces_filter_cod_label', __('COD fee', 'usces') ).'",';
				$line .= '"'.usces_crform( $order_data['order_cod_fee'], false, false, 'return', false ).'","1"'."\r\n";
			}

			if( usces_is_tax_display() && 'all' == usces_get_tax_target() && 'exclude' == usces_get_tax_mode() ) {
				$line .= '"","","","","","","","","","","","","","","","","","","","","","",';
				$line .= '"'.usces_tax_label( $order_data, 'return' ).'",';
				$line .= '"'.usces_tax( $order_data, 'return' ).'","1"'."\r\n";
			}

			if( usces_is_member_system() && usces_is_member_system_point() && 1 == usces_point_coverage() && $order_data['order_usedpoint'] != 0 ) {
				$line .= '"","","","","","","","","","","","","","","","","","","","","","",';
				$line .= '"'.__('use of points','usces').'",';
				$line .= '"'.number_format($order_data['order_usedpoint']).'","1"'."\r\n";
			}
		}

		header("Content-Type: application/octet-stream");
		header("Content-disposition: attachment; filename=\"$filename\"");
		mb_http_output('pass');
		print(mb_convert_encoding($line, "SJIS-win", "UTF-8"));
		exit();
	}

	/**********************************************
	* ATODENE 取引一括変更・キャンセルCSV出力
	* @param  
	* @return -
	***********************************************/
	public function download_atodene_update() {

		exit();
	}

	/**********************************************
	* ATODENE 出荷報告登録CSV出力
	* @param  
	* @return -
	***********************************************/
	public function download_atodene_report() {
		global $usces;

		$acting_opts = $this->get_acting_settings();
		$filename = mb_convert_encoding(__('ATODENE_shippingreport_','usces'), 'SJIS', 'UTF-8').date_i18n( 'YmdHis', current_time('timestamp') ).".csv";

		$line = '"運送会社名","配送伝票番号","購入者注文日","お問合せ番号","ご購入店受注番号","氏名","予備項目","配送先氏名","配送先住所","顧客請求金額（税込）","請求書送付方法","審査結果"'."\r\n";

		$ids = $_GET['listcheck'];
		foreach( (array)$ids as $order_id ) {
			$order_data = $usces->get_order_data( $order_id, 'direct' );
			$payment = $usces->getPayments( $order_data['order_payment_name'] );
			if( 'acting_welcart_atodene' != $payment['settlement'] ) {
				continue;
			}

			$delivery_company = $usces->get_order_meta_value( 'delivery_company', $order_id );
			$tracking_number = $usces->get_order_meta_value( apply_filters( 'usces_filter_tracking_meta_key', 'tracking_number' ), $order_id );
			$atodene_number = $usces->get_order_meta_value( 'atodene_number', $order_id );

			$line .= '"'.$delivery_company.'",'.
				'"'.$tracking_number.'","",'.
				'"'.$atodene_number.'",'.
				'"'.$order_id.'","","","","","","","",""'."\r\n";
		}

		header("Content-Type: application/octet-stream");
		header("Content-disposition: attachment; filename=\"$filename\"");
		mb_http_output('pass');
		print(mb_convert_encoding($line, "SJIS-win", "UTF-8"));
		exit();
	}

	/**********************************************
	* ATODENE 与信審査結果CSV取込
	* @param  
	* @return -
	***********************************************/
	public function upload_atodene_results() {
		global $usces, $wpdb;

		//check_admin_referer( 'order_list', 'wc_nonce' );

		$res = array();
		$path = WP_CONTENT_DIR.'/uploads/';

		if( isset($_GET['atodene_upfile']) && !WCUtils::is_blank($_GET['atodene_upfile']) && isset($_GET['order_action']) && $_GET['order_action'] == 'upload_atodene_results' ) {
			$file_name = urldecode($_GET['atodene_upfile']);
			$decode_filename = base64_decode($file_name);
			if( !file_exists($path.$file_name) ) {
				$res['status'] = 'error';
				$res['message'] = __('CSV file does not exist.', 'usces').esc_html($decode_filename);
				return( $res );
			}
		}

		$wpdb->query( 'SET SQL_BIG_SELECTS=1' );
		set_time_limit( 3600 );

		define( 'COL_ORDER_ID', 0 );//ご購入店受注番号
		define( 'COL_ATODINE_NUMBER', 1 );//お問合せ番号
		define( 'COL_NAME', 2 );//氏名
		define( 'COL_AMOUNT', 3 );//顧客請求金額（税込）
		define( 'COL_BILLING_METHOD', 4 );//請求書送付方法(別送/同梱)
		define( 'COL_RESULTS', 5 );//与信審査結果(OK/NG/保留/審査中)

		if( !( $fpo = fopen( $path.$file_name, "r" ) ) ) {
			$res['status'] = 'error';
			$res['message'] = __('A file does not open.', 'usces').esc_html($decode_filename);
			return $res;
		}

		$orglines = array();
		$sp = ',';

		$fname_parts = explode( '.', $decode_filename );
		if( 'csv' !== end($fname_parts) ) {
			$res['status'] = 'error';
			$res['message'] = __('This file is not in the CSV file.', 'usces').esc_html($decode_filename);
			return $res;
		}

		$buf = '';
		while( !feof($fpo) ) {
			$temp = fgets( $fpo, 10240 );
			if( 0 == strlen($temp) ) continue;
			$orglines[] = str_replace( '"', '', $temp );
		}
		fclose($fpo);

		foreach( $orglines as $sjisline ) {
			$line = mb_convert_encoding( $sjisline, 'UTF-8', 'SJIS' );
			list( $order_id, $atodene_number, $name, $amount, $billing_method, $atodene_results ) = explode( $sp, $line );
			$order_data = $usces->get_order_data( $order_id, 'direct' );
			if( $order_data ) {
				if( 'OK' == trim($atodene_results) ) {
					$res = usces_change_order_receipt( (int)$order_id, 'receipted' );
				}
				if( !empty($atodene_number) ) {
					$usces->set_order_meta_value( 'atodene_number', trim($atodene_number), (int)$order_id );
				}
				if( !empty($atodene_results) ) {
					$usces->set_order_meta_value( 'atodene_results', trim($atodene_results), (int)$order_id );
				}
			}
		}
		unlink( $path.$file_name );

		return $res;
	}
}
