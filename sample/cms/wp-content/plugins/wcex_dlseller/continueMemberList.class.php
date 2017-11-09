<?php
class ContinuationList
{
	var $table;			//テーブル名
	var $rows;			//データ
	var $action;		//アクション
	var $startRow;		//表示開始行番号
	var $maxRow;		//最大表示行数
	var $currentPage;	//現在のページNo
	var $firstPage;		//最初のページNo
	var $previousPage;	//前のページNo
	var $nextPage;		//次のページNo
	var $lastPage;		//最終ページNo
	var $naviMaxButton;	//ページネーション・ナビのボタンの数
	var $dataTableNavigation;	//ナヴィゲーションhtmlコード
	var $arr_period;	//表示データ期間
	var $arr_search;	//サーチ条件
	var $searchSql;		//簡易絞込みSQL
	var $searchSkuSql;	//SKU絞り込み
	var $searchSwitchStatus;	//サーチ表示スイッチ
	var $columns;		//データカラム
	var $sortColumn;	//現在ソート中のフィールド
	var $sortOldColumn;
	var $sortSwitchs;	//各フィールド毎の昇順降順スイッチ
	var $userHeaderNames;	//ユーザー指定のヘッダ名
	var $action_status, $action_message;
	var $pageLimit;		//ページ制限
	var $continue_status;	//処理ステータス
	var $selectSql;
	var $joinTableSql;
	var $con_meta;
	var $currentPageIds;
	var $period;

	//Constructor
	function __construct() {
		global $wpdb;

		$this->listOption = get_option( 'usces_continuelist_option' );

		$this->table = $wpdb->prefix.'usces_continuation';
		$this->set_column();
		$this->rows = array();

		$this->maxRow = ( isset($this->listOption['max_row']) ) ? $this->listOption['max_row'] : 50;
		$this->naviMaxButton = 11;
		$this->firstPage = 1;
		$this->pageLimit = 'on';
		$this->action_status = 'none';
		$this->action_message = '';

		$this->SetParamByQuery();

		$continue_status = array(
			'continuation' => __('Continuation', 'dlseller'),
			'cancellation' => __('Cancellation', 'dlseller')
		);
		$this->continue_status = apply_filters( 'dlseller_filter_continue_status', $continue_status, $this );

		$wpdb->query( 'SET SQL_BIG_SELECTS=1' );
	}

	function set_column() {

		$columns = array();
		$columns['ID'] = __('ID', 'dlseller');
		$columns['order_id'] = __('Order ID', 'dlseller');
		$columns['deco_id'] = __('order number', 'usces');
		$columns['mem_id'] = __('membership number', 'usces');
		$columns['name1'] = __('Last Name', 'usces');
		$columns['name2'] = __('First Name', 'usces');
		$columns['name3'] = __('Last Furigana', 'usces');
		$columns['name4'] = __('First Furigana', 'usces');
		$columns['limitofcard'] = __('Limit of Card(Month/Year)', 'dlseller');
		$columns['price'] = __('Total Amount', 'usces').'('.__(usces_crcode( 'return' ), 'usces').')';
		$columns['acting'] = __('Settlement Supplier', 'dlseller');
		$columns['payment_name'] = __('payment method', 'usces');
		$columns['orderdate'] = __('Application Date', 'dlseller');
		$columns['startdate'] = __('First Withdrawal Date', 'dlseller');
		$columns['contractedday'] = __('Renewal Date', 'dlseller');
		$columns['chargedday'] = __('Next Withdrawal Date', 'dlseller');
		$columns['status'] = __('Status', 'dlseller');
		$columns['condition'] = __('Condition', 'dlseller');
		$columns = apply_filters( 'dlseller_filter_continue_memberlist_column', $columns, $this );
		$this->columns = $columns;
	}

	function get_column() {

		return $this->columns;
	}

	function MakeTable() {

		$this->SetParam();

		switch( $this->action ) {

		case 'searchIn':
			$this->SearchIn();
			$res = $this->GetRows();
			break;

		case 'searchOut':
			$this->SearchOut();
			$res = $this->GetRows();
			break;

		case 'returnList':
		case 'changeSort':
		case 'changePage':
			$res = $this->GetRows();
			break;

		case 'refresh':
		default:
			$this->SetDefaultParam();
			$res = $this->GetRows();
			break;
		}

		$this->SetNavi();
		$this->SetHeaders();
		$this->SetSESSION();

		if( $res ) {
			return true;
		} else {
			return false;
		}
	}

	//DefaultParam
	function SetDefaultParam() {

		unset($_SESSION[$this->table]);
		$this->startRow = 0;
		$this->currentPage = 1;
		if( isset($_SESSION[$this->table]['arr_search']) ) {
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
		} else {
			$arr_search = array(
				'period'=>array('',''), 
				'order_column'=>array('',''), 
				'order_word'=>array('',''), 
				'order_word_term'=>array('contain','contain'), 
				'order_term'=>'AND', 
				'product_column'=>array('',''), 
				'product_word'=>array('',''), 
				'product_word_term'=>array('contain','contain'), 
				'option_word'=>array('',''), 
				'product_term'=>'AND'
			);
			$this->arr_search = apply_filters( 'dlseller_filter_continue_memberlist_arr_search', $arr_search, $this );
		}

		$this->searchWhere = '';
		$this->searchHaving = '';
		$this->sortColumn = 'ID';
		foreach( $this->columns as $key => $value ) {
			$this->sortSwitchs[$key] = 'DESC';
		}

		$this->SetTotalRow();
	}

	function SetParam() {

		$this->startRow = ($this->currentPage-1) * $this->maxRow;
	}

	function SetParamByQuery() {

		if( isset($_REQUEST['changePage']) ) {

			$this->action = 'changePage';
			$this->currentPage = (int)$_REQUEST['changePage'];
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->searchWhere = $_SESSION[$this->table]['searchWhere'];
			$this->searchHaving = $_SESSION[$this->table]['searchHaving'];
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];
			$this->selectedRow = $_SESSION[$this->table]['selectedRow'];

		} elseif(isset($_REQUEST['returnList']) ) {

			$this->action = 'returnList';
			$this->currentPage = $_SESSION[$this->table]['currentPage'];
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->searchWhere = $_SESSION[$this->table]['searchWhere'];
			$this->searchHaving = $_SESSION[$this->table]['searchHaving'];
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];
			$this->selectedRow = $_SESSION[$this->table]['selectedRow'];

		} elseif( isset($_REQUEST['changeSort']) ) {

			$this->action = 'changeSort';
			$this->sortOldColumn = $this->sortColumn;
			$this->sortColumn = str_replace('`', '', $_REQUEST['changeSort']);
			$this->sortColumn = str_replace(',', '', $this->sortColumn);
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->sortSwitchs[$this->sortColumn] = ('ASC' == $_REQUEST['switch']) ? 'ASC' : 'DESC';
			$this->currentPage = $_SESSION[$this->table]['currentPage'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->searchWhere = $_SESSION[$this->table]['searchWhere'];
			$this->searchHaving = $_SESSION[$this->table]['searchHaving'];
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];
			$this->selectedRow = $_SESSION[$this->table]['selectedRow'];

		} elseif( isset($_REQUEST['searchIn']) ) {

			$this->action = 'searchIn';
			$this->arr_search['order_column'][0] = !WCUtils::is_blank($_REQUEST['search']['order_column'][0]) ? str_replace('`', '', $_REQUEST['search']['order_column'][0]) : '';
			$this->arr_search['order_column'][1] = !WCUtils::is_blank($_REQUEST['search']['order_column'][1]) ? str_replace('`', '', $_REQUEST['search']['order_column'][1]) : '';
			$this->arr_search['order_word'][0] = !WCUtils::is_blank($_REQUEST['search']['order_word'][0]) ? trim($_REQUEST['search']['order_word'][0]) : '';
			$this->arr_search['order_word'][1] = !WCUtils::is_blank($_REQUEST['search']['order_word'][1]) ? trim($_REQUEST['search']['order_word'][1]) : '';
			$this->arr_search['order_word_term'][0] = isset($_REQUEST['search']['order_word_term'][0]) ? $_REQUEST['search']['order_word_term'][0] : 'contain';
			$this->arr_search['order_word_term'][1] = isset($_REQUEST['search']['order_word_term'][1]) ? $_REQUEST['search']['order_word_term'][1] : 'contain';
			if( WCUtils::is_blank($_REQUEST['search']['order_column'][0]) ) {
				$this->arr_search['order_column'][1] = '';
				$this->arr_search['order_word'][0] = '';
				$this->arr_search['order_word'][1] = '';
				$this->arr_search['order_word_term'][0] = 'contain';
				$this->arr_search['order_word_term'][1] = 'contain';
			}
			$this->arr_search['order_term'] = $_REQUEST['search']['order_term'];
			$this->arr_search['product_column'][0] = !WCUtils::is_blank($_REQUEST['search']['product_column'][0]) ? str_replace('`', '', $_REQUEST['search']['product_column'][0]) : '';
			$this->arr_search['product_column'][1] = !WCUtils::is_blank($_REQUEST['search']['product_column'][1]) ? str_replace('`', '', $_REQUEST['search']['product_column'][1]) : '';
			$this->arr_search['product_word'][0] = !WCUtils::is_blank($_REQUEST['search']['product_word'][0]) ? trim($_REQUEST['search']['product_word'][0]) : '';
			$this->arr_search['product_word'][1] = !WCUtils::is_blank($_REQUEST['search']['product_word'][1]) ? trim($_REQUEST['search']['product_word'][1]) : '';
			$this->arr_search['product_word_term'][0] = isset($_REQUEST['search']['product_word_term'][0]) ? $_REQUEST['search']['product_word_term'][0] : 'contain';
			$this->arr_search['product_word_term'][1] = isset($_REQUEST['search']['product_word_term'][1]) ? $_REQUEST['search']['product_word_term'][1] : 'contain';
			$this->arr_search['option_word'][0] = (isset($_REQUEST['search']['option_word'][0]) && !WCUtils::is_blank($_REQUEST['search']['option_word'][0])) ? trim($_REQUEST['search']['option_word'][0]) : '';
			$this->arr_search['option_word'][1] = (isset($_REQUEST['search']['option_word'][1]) && !WCUtils::is_blank($_REQUEST['search']['option_word'][1])) ? trim($_REQUEST['search']['option_word'][1]) : '';
			if( WCUtils::is_blank($_REQUEST['search']['product_column'][0]) ) {
				$this->arr_search['product_column'][1] = '';
				$this->arr_search['product_word'][0] = '';
				$this->arr_search['product_word'][1] = '';
				$this->arr_search['product_word_term'][0] = 'contain';
				$this->arr_search['product_word_term'][1] = 'contain';
				$this->arr_search['option_word'][0] = '';
				$this->arr_search['option_word'][1] = '';
			}
			$this->arr_search['product_term'] = $_REQUEST['search']['product_term'];
			$this->currentPage = 1;
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];

		} elseif( isset($_REQUEST['searchOut']) ) {

			$this->action = 'searchOut';
			$this->arr_search['column'] = '';
			$this->arr_search['word'] = '';
			$this->arr_search['order_column'][0] = '';
			$this->arr_search['order_column'][1] = '';
			$this->arr_search['order_word'][0] = '';
			$this->arr_search['order_word'][1] = '';
			$this->arr_search['order_word_term'][0] = 'contain';
			$this->arr_search['order_word_term'][1] = 'contain';
			$this->arr_search['order_term'] = 'AND';
			$this->arr_search['product_column'][0] = '';
			$this->arr_search['product_column'][1] = '';
			$this->arr_search['product_word'][0] = '';
			$this->arr_search['product_word'][1] = '';
			$this->arr_search['product_word_term'][0] = 'contain';
			$this->arr_search['product_word_term'][1] = 'contain';
			$this->arr_search['option_word'][0] = '';
			$this->arr_search['option_word'][1] = '';
			$this->arr_search['product_term'] = 'AND';
			$this->currentPage = 1;
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];

		} elseif( isset($_REQUEST['refresh']) ) {

			$this->action = 'refresh';
			$this->currentPage = $_SESSION[$this->table]['currentPage'];
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->searchWhere = $_SESSION[$this->table]['searchWhere'];
			$this->searchHaving = $_SESSION[$this->table]['searchHaving'];
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];
			$this->selectedRow = $_SESSION[$this->table]['selectedRow'];

		} elseif(isset($_REQUEST['collective']) ) {

			$this->action = 'collective_'.str_replace(',', '', $_POST['allchange']['column']);
			$this->currentPage = $_SESSION[$this->table]['currentPage'];
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->searchWhere = $_SESSION[$this->table]['searchWhere'];
			$this->searchHaving = $_SESSION[$this->table]['searchHaving'];
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];
			$this->selectedRow = $_SESSION[$this->table]['selectedRow'];

		} else {
			$this->action = 'default';
		}
	}

	//GetRows
	function GetRows() {
		global $wpdb;

		$continuation_meta_table = $wpdb->prefix.'usces_continuation_meta';
		$order_table = $wpdb->prefix.'usces_order';
		$order_meta_table = $wpdb->prefix.'usces_order_meta';
		$ordercart_table = $wpdb->prefix.'usces_ordercart';
		$ordercart_meta_table = $wpdb->prefix.'usces_ordercart_meta';
		$member_table = $wpdb->prefix.'usces_member';
		$member_meta_table = $wpdb->prefix.'usces_member_meta';

		$where = $this->GetWhere();
		$having = $this->GetHaving();

		$csod = "";
		$join = "";
		$join .= "INNER JOIN {$member_table} AS mem ON con_member_id = mem.ID ";
		$join .= "INNER JOIN {$order_table} AS ord ON con_order_id = ord.ID ";
		$join .= "LEFT JOIN {$member_meta_table} AS mm ON con_member_id = mm.member_id AND mm.meta_key = 'limitofcard' ";
		$join .= "LEFT JOIN {$order_meta_table} AS om ON con_order_id = om.order_id AND om.meta_key = 'dec_order_id' ";
		if( $where ) {
			$join .= " LEFT JOIN {$ordercart_table} AS cart ON con_order_id = cart.order_id ";
			$csod .= ", cart.item_code, cart.item_name, cart.sku_code, cart.sku_name ";
			$join .= " LEFT JOIN {$ordercart_meta_table} AS itemopt ON cart.cart_id = itemopt.cart_id AND itemopt.meta_type = 'option' ";
			$csod .= ", itemopt.meta_key, itemopt.meta_value ";
		}
		$join = apply_filters( 'dlseller_filter_continue_memberlist_sql_jointable', $join, $this );

		$group = ' GROUP BY `ID` ';
		$switch = ( 'ASC' == $this->sortSwitchs[$this->sortColumn] ) ? 'ASC' : 'DESC';
		$order = ' ORDER BY `'.esc_sql($this->sortColumn).'` '.$switch;
		$query = "SELECT 
			`con_id` AS `ID`, 
			`con_order_id` AS `order_id`, 
			om.meta_value AS `deco_id`, 
			`con_member_id` AS `mem_id`, 
			mem.mem_name1 AS `name1`, 
			mem.mem_name2 AS `name2`, 
			mem.mem_name3 AS `name3`, 
			mem.mem_name4 AS `name4`, 
			mm.meta_value AS `limitofcard`, 
			`con_price` AS `price`, 
			`con_acting` AS `acting`, 
			ord.order_payment_name AS `payment_name`, 
			DATE_FORMAT(ord.order_date, '%Y-%m-%d') AS `orderdate`, 
			DATE_FORMAT(con_startdate, '%Y-%m-%d') AS `startdate`, 
			DATE_FORMAT(con_next_contracting, '%Y-%m-%d') AS `contractedday`, 
			DATE_FORMAT(con_next_charging, '%Y-%m-%d') AS `chargedday`, 
			`con_status` AS `status`, 
			`con_condition` AS `condition` 
			{$csod} 
			FROM {$this->table} ";
		$query = apply_filters( 'dlseller_filter_continue_memberlist_sql_select', $query, $csod, $this );
		$query .= $join.$where.$group.$having.$order;
		$rows = $wpdb->get_results( $query, ARRAY_A );
		$this->selectedRow = count($rows);
		if( $this->pageLimit == 'on' ) {
			$this->rows = array_slice( $rows, $this->startRow, $this->maxRow );
			$this->currentPageIds = array();
			foreach( $this->rows as $row ) {
				$this->currentPageIds[] = $row['ID'];
			}
		} else {
			$this->rows = $rows;
		}

		return $this->rows;
	}

	function SetTotalRow() {
		global $wpdb;

		$member_table = $wpdb->prefix.'usces_member';
		$order_table = $wpdb->prefix.'usces_order';
		$query = "SELECT COUNT(con_id) AS ct FROM {$this->table} 
			INNER JOIN {$member_table} AS mem ON `con_member_id` = mem.ID 
			INNER JOIN {$order_table} AS ord ON `con_order_id` = ord.ID ".
			apply_filters( 'dlseller_filter_continue_memberlist_sql_where', '', $this );
		$query = apply_filters( 'dlseller_filter_continue_memberlist_set_total_row', $query, $this );
		$res = $wpdb->get_var( $query );
		$this->totalRow = $res;
	}

	function GetHaving() {

		$query = '';
		if( !WCUtils::is_blank($this->searchHaving) ) {
			$query .= ' HAVING '.$this->searchHaving;
		}
		$query = apply_filters( 'dlseller_filter_continue_memberlist_sql_having', $query, $this );
		return $query;
	}

	function GetWhere() {

		$query = '';
		if( !WCUtils::is_blank($this->searchWhere) ) {
			$query .= ' WHERE '.$this->searchWhere;
		}
		$query = apply_filters( 'dlseller_filter_continue_memberlist_sql_where', $query, $this );
		return $query;
	}

	function SearchIn() {
		global $wpdb;

		$this->searchWhere = '';
		$this->searchHaving = '';

		if( !empty($this->arr_search['order_column'][0]) && !WCUtils::is_blank($this->arr_search['order_word'][0]) ) {
			switch( $this->arr_search['order_word_term'][0] ) {
				case 'notcontain':
					$wordterm0 = ' NOT LIKE %s';
					$word0 = "%".$this->arr_search['order_word'][0]."%";
					break;
				case 'equal':
					$wordterm0 = ' = %s';
					$word0 = $this->arr_search['order_word'][0];
					break;
				case 'morethan':
					$wordterm0 = ' > %d';
					$word0 = $this->arr_search['order_word'][0];
					break;
				case 'lessthan':
					$wordterm0 = ' < %d';
					$word0 = $this->arr_search['order_word'][0];
					break;
				case 'contain':
				default:
					$wordterm0 = ' LIKE %s';
					$word0 = "%".$this->arr_search['order_word'][0]."%";
					break;
			}
			switch( $this->arr_search['order_word_term'][1] ) {
				case 'notcontain':
					$wordterm1 = ' NOT LIKE %s';
					$word1 = "%".$this->arr_search['order_word'][1]."%";
					break;
				case 'equal':
					$wordterm1 = ' = %s';
					$word1 = $this->arr_search['order_word'][1];
					break;
				case 'morethan':
					$wordterm1 = ' > %d';
					$word1 = $this->arr_search['order_word'][1];
					break;
				case 'lessthan':
					$wordterm1 = ' < %d';
					$word1 = $this->arr_search['order_word'][1];
					break;
				case 'contain':
				default:
					$wordterm1 = ' LIKE %s';
					$word1 = "%".$this->arr_search['order_word'][1]."%";
					break;
			}
			$this->searchHaving .= ' ( ';
			$this->searchHaving .= $wpdb->prepare( esc_sql($this->arr_search['order_column'][0]).$wordterm0, $word0 );
			if( !empty($this->arr_search['order_column'][1]) && !WCUtils::is_blank($this->arr_search['order_word'][1]) ) {
				$this->searchHaving .= ' '.$this->arr_search['order_term'].' ';
				$this->searchHaving .= $wpdb->prepare( esc_sql($this->arr_search['order_column'][1]).$wordterm1, $word1 );
			}
			$this->searchHaving .= ' ) ';
		}

		if( !empty($this->arr_search['product_column'][0]) && !WCUtils::is_blank($this->arr_search['product_word'][0]) ) {

			switch( $this->arr_search['product_word_term'][0] ) {
				case 'notcontain':
					$prowordterm0 = ' NOT LIKE %s';
					$proword0 = "%".$this->arr_search['product_word'][0]."%";
					break;
				case 'equal':
					$prowordterm0 = ' = %s';
					$proword0 = $this->arr_search['product_word'][0];
					break;
				case 'morethan':
					$prowordterm0 = ' > %d';
					$proword0 = $this->arr_search['product_word'][0];
					break;
				case 'lessthan':
					$prowordterm0 = ' < %d';
					$proword0 = $this->arr_search['product_word'][0];
					break;
				case 'contain':
				default:
					$prowordterm0 = ' LIKE %s';
					$proword0 = "%".$this->arr_search['product_word'][0]."%";
					break;
			}
			switch( $this->arr_search['product_word_term'][1] ) {
				case 'notcontain':
					$prowordterm1 = ' NOT LIKE %s';
					$proword1 = "%".$this->arr_search['product_word'][1]."%";
					break;
				case 'equal':
					$prowordterm1 = ' = %s';
					$proword1 = $this->arr_search['product_word'][1];
					break;
				case 'morethan':
					$prowordterm1 = ' > %d';
					$proword1 = $this->arr_search['product_word'][1];
					break;
				case 'lessthan':
					$prowordterm1 = ' < %d';
					$proword1 = $this->arr_search['product_word'][1];
					break;
				case 'contain':
				default:
					$prowordterm1 = ' LIKE %s';
					$proword1 = "%".$this->arr_search['product_word'][1]."%";
					break;
			}
			$this->searchWhere .= ' ( ';
			if( 'item_option' == $this->arr_search['product_column'][0] ) {
				$this->searchWhere .= $wpdb->prepare( '( itemopt.meta_key LIKE %s AND itemopt.meta_value LIKE %s )' , "%".$this->arr_search['product_word'][0]."%" , "%".$this->arr_search['option_word'][0]."%" );
			} else {
				$this->searchWhere .= $wpdb->prepare( esc_sql($this->arr_search['product_column'][0]).$prowordterm0, $proword0 );
			}
			if( !empty($this->arr_search['product_column'][1]) && !WCUtils::is_blank($this->arr_search['product_word'][1]) ) {
				$this->searchWhere .= ' '.$this->arr_search['product_term'].' ';
				if( 'item_option' == $this->arr_search['product_column'][1] ) {
					$this->searchWhere .= $wpdb->prepare( '( itemopt.meta_key LIKE %s AND itemopt.meta_value LIKE %s )' , "%".$this->arr_search['product_word'][1]."%" , "%".$this->arr_search['option_word'][1]."%" );
				} else {
					$this->searchWhere .= $wpdb->prepare( esc_sql($this->arr_search['product_column'][1]).$prowordterm1, $proword1 );
				}
			}
			$this->searchWhere .= ' ) ';
		}
	}

	function SearchOut() {

		$this->searchWhere = '';
		$this->searchHaving = '';
	}

	function SetNavi() {

		$this->lastPage = ceil($this->selectedRow / $this->maxRow);
		$this->previousPage = ( $this->currentPage - 1 == 0 ) ? 1 : $this->currentPage - 1;
		$this->nextPage = ( $this->currentPage + 1 > $this->lastPage ) ? $this->lastPage : $this->currentPage + 1;

		for( $i = 0; $i < $this->naviMaxButton; $i++ ) {
			if( $i > $this->lastPage - 1 ) break;
			if( $this->lastPage <= $this->naviMaxButton ) {
				$box[] = $i + 1;
			} else {
				if( $this->currentPage <= 6 ) {
					$label = $i + 1;
					$box[] = $label;
				} else {
					$label = $i + 1 + $this->currentPage - 6;
					$box[] = $label;
					if( $label == $this->lastPage ) break;
				}
			}
		}

		$html = '';
		$html .= '<ul class="clearfix">'."\n";
		$html .= '<li class="rowsnum">'.$this->selectedRow.' / '.$this->totalRow.' '.__('cases', 'usces').'</li>'."\n";
		if( ( $this->currentPage == 1 ) || ( $this->selectedRow == 0 ) ) {
			$html .= '<li class="navigationStr">first&lt;&lt;</li>'."\n";
			$html .= '<li class="navigationStr">prev&lt;</li>'."\n";
		} else {
			$html .= '<li class="navigationStr"><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_continue&changePage=1">first&lt;&lt;</a></li>'."\n";
			$html .= '<li class="navigationStr"><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_continue&changePage='.$this->previousPage.'">prev&lt;</a></li>'."\n";
		}
		if( $this->selectedRow > 0 ) {
			for( $i = 0; $i < count($box); $i++ ) {
				if( $box[$i] == $this->currentPage ) {
					$html .= '<li class="navigationButtonSelected"><span>'.$box[$i].'</span></li>'."\n";
				} else {
					$html .= '<li class="navigationButton"><a href="'.site_url().'/wp-admin/admin.php?page=usces_continue&changePage='.$box[$i].'">'.$box[$i].'</a></li>'."\n";
				}
			}
		}
		if( ( $this->currentPage == $this->lastPage ) || ( $this->selectedRow == 0 ) ) {
			$html .= '<li class="navigationStr">&gt;next</li>'."\n";
			$html .= '<li class="navigationStr">&gt;&gt;last</li>'."\n";
		} else {
			$html .= '<li class="navigationStr"><a href="'.site_url().'/wp-admin/admin.php?page=usces_continue&changePage='.$this->nextPage.'">&gt;next</a></li>'."\n";
			$html .= '<li class="navigationStr"><a href="'.site_url().'/wp-admin/admin.php?page=usces_continue&changePage='.$this->lastPage.'">&gt;&gt;last</a></li>'."\n";
		}
		$html .= '</ul>'."\n";

		$this->dataTableNavigation = $html;
	}

	function SetSESSION() {

		$_SESSION[$this->table]['startRow'] = $this->startRow;		//表示開始行番号
		$_SESSION[$this->table]['sortColumn'] = $this->sortColumn;	//現在ソート中のフィールド
		$_SESSION[$this->table]['totalRow'] = $this->totalRow;		//全行数
		$_SESSION[$this->table]['selectedRow'] = $this->selectedRow;	//絞り込まれた行数
		$_SESSION[$this->table]['currentPage'] = $this->currentPage;	//現在のページNo
		$_SESSION[$this->table]['previousPage'] = $this->previousPage;	//前のページNo
		$_SESSION[$this->table]['nextPage'] = $this->nextPage;		//次のページNo
		$_SESSION[$this->table]['lastPage'] = $this->lastPage;		//最終ページNo
		$_SESSION[$this->table]['userHeaderNames'] = $this->userHeaderNames;//全てのフィールド
		$_SESSION[$this->table]['headers'] = $this->headers;//表示するヘッダ文字列
		$_SESSION[$this->table]['sortSwitchs'] = $this->sortSwitchs;	//各フィールド毎の昇順降順スイッチ
		$_SESSION[$this->table]['dataTableNavigation'] = $this->dataTableNavigation;
		$_SESSION[$this->table]['searchWhere'] = $this->searchWhere;
		$_SESSION[$this->table]['searchHaving'] = $this->searchHaving;
		$_SESSION[$this->table]['arr_search'] = $this->arr_search;
		if( $this->pageLimit == 'on' ) {
			$_SESSION[$this->table]['currentPageIds'] = $this->currentPageIds;
		}
		do_action( 'dlseller_action_continue_memberlist_set_session', $this );
	}

	function SetHeaders() {

		foreach( $this->columns as $key => $value ) {
			if( $key == $this->sortColumn ) {
				if( $this->sortSwitchs[$key] == 'ASC' ) {
					$str = __('[ASC]', 'usces');
					$switch = 'DESC';
				} else {
					$str = __('[DESC]', 'usces');
					$switch = 'ASC';
				}
				$this->headers[$key] = '<a href="'.site_url().'/wp-admin/admin.php?page=usces_continue&changeSort='.$key.'&switch='.$switch.'"><span class="sortcolumn">'.$value.' '.$str.'</span></a>';
			} else {
				$switch = $this->sortSwitchs[$key];
				$this->headers[$key] = '<a href="'.site_url().'/wp-admin/admin.php?page=usces_continue&changeSort='.$key.'&switch='.$switch.'"><span>'.$value.'</span></a>';
			}
		}
	}

	function GetSearchs() {

		return $this->arr_search;
	}

	function GetListheaders() {

		return $this->headers;
	}

	function GetDataTableNavigation() {

		return $this->dataTableNavigation;
	}

	function set_action_status( $status, $message ) {

		$this->action_status = $status;
		$this->action_message = $message;
	}

	function get_action_status() {

		return $this->action_status;
	}

	function get_action_message() {

		return $this->action_message;
	}
}

?>
