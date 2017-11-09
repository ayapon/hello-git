<?php
///////////////////////////////////
// データ登録                    //
///////////////////////////////////
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');
include_once("attestation.php");
require("common.php");
require("../bottle_assets/pageparts.php");

$html_title = "新規登録・編集丨顧客・ボトル管理システム";  //ページタイトル
//--------------------------------------------------------------------------------------初期設定

$mode = @$_GET["mode"];
$url = $_SERVER['SCRIPT_NAME'];
// define ("file_category","category.dat");
define ("file_custom","custom.dat");
define ("file_sdata","data/data.dat");

//契約ユーザーデータファイル
define ("file_cdata","../manager/data/account.dat");

$file_category = "config/category.dat";
$file_pref = "data/pref.dat";
$file_birthyear = "data/birthyear.dat";
$file_birthmonth = "data/birthmonth.dat";
$file_birthday = "data/birthday.dat";

//--------------------------------------------------------------------------------------
if ($mode <> "category"){
//--------------------------------------------------------------------------------------管理者 [ リンク登録 ]
//ＧＥＴ
//$page = @$_GET["page"];
$cnt = @$_GET["cnt"];
$category   = urldecode(@$_GET["category"]);
$searchword = urldecode(@$_GET["searchword"]);
//ＰＯＳＴ
$submit         = @$_POST["Submit"];
$cancel         = @$_POST["cancel"];
$customer       = @$_POST["customer"];
$kana       = @$_POST["kana"];
$company       = @$_POST["company"];
$section       = @$_POST["section"];
$section2       = @$_POST["section2"];
$degree       = @$_POST["degree"];
$zip    = @$_POST["zip"];
$pref = @$_POST["pref"];
$addr = @$_POST["addr"];
$addr2    = @$_POST["addr2"];
$phone    = @$_POST["phone"];
$mobile    = @$_POST["mobile"];
$memo = @$_POST["memo"];
$birthyear = @$_POST["birthyear"];
$birthmonth = @$_POST["birthmonth"];
$birthday = @$_POST["birthday"];
$firsttime = @$_POST["firsttime"];
$lasttime = @$_POST["lasttime"];
$pic = @$_POST["pic"];
$no_dm      = @$_POST["no_dm"];
$lastvisit      = @$_POST["lastvisit"];
$friends      = @$_POST["friends"];
$fname            = @$_POST["fname"];
$delete_img = @$_POST["delete_img"];
$bottle_0  = @$_POST["bottle_0"];
$bottle_1  = @$_POST["bottle_1"];
$bottle_2  = @$_POST["bottle_2"];
$bottle_3  = @$_POST["bottle_3"];
$bottle_4  = @$_POST["bottle_4"];
$otherbottle = @$_POST["otherbottle"];
$bottle_num_0  = @$_POST["bottle_num_0"];
$bottle_num_1  = @$_POST["bottle_num_1"];
$bottle_num_2  = @$_POST["bottle_num_2"];
$bottle_num_3  = @$_POST["bottle_num_3"];
$bottle_num_4  = @$_POST["bottle_num_4"];
$otherbottle_num = @$_POST["otherbottle_num"];
$bottle_quant_0  = @$_POST["bottle_quant_0"];
$bottle_quant_1  = @$_POST["bottle_quant_1"];
$bottle_quant_2  = @$_POST["bottle_quant_2"];
$bottle_quant_3  = @$_POST["bottle_quant_3"];
$bottle_quant_4  = @$_POST["bottle_quant_4"];
$otherbottle_quant = @$_POST["otherbottle_quant"];
$friends_0 = @$_POST["friends_0"];
$friends_1 = @$_POST["friends_1"];
$friends_2 = @$_POST["friends_2"];
$friends_3 = @$_POST["friends_3"];
$friends_4 = @$_POST["friends_4"];
$friends_5 = @$_POST["friends_5"];
$friends_6 = @$_POST["friends_6"];
$friends_7 = @$_POST["friends_7"];
$friends_8 = @$_POST["friends_8"];
$friends_9 = @$_POST["friends_9"];
$reset       = @$_POST["reset"];
//
if (!$category){$category = @$_POST["category"];}
if (!$searchword){$searchword = @$_POST["searchword"];}
//
$shift_up    = @$_POST["shift_up"];
$shift_down  = @$_POST["shift_down"];
$fix         = @$_POST["fix"];
$delete_conf = @$_POST["delete_conf"];
$delete      = @$_POST["delete"];
$radio       = @$_POST["radio"];
//ファイルデータの情報取得
$upfile = @$_FILES["upfile"]["tmp_name"];
$upfile_name = @$_FILES["upfile"]["name"];
$upfile_size = @$_FILES["upfile"]["size"];
//初期設定
$upload_ext = array("jpg","jpeg","png","gif");
$upload_limit = 150000;

//ダイレクトリクエスト
if (file_exists($file_link)){$dir = "";$direct = 1;}
//カテゴリーリスト
$word_list = read_file($dir.$file_category);
if ($category == $word_list[0]){$category = "";}

$word_list4 = read_file($dir.$file_pref);
if ($pref_dat== $word_list4[0]){$pref_dat = "";}
$word_list5 = read_file($dir.$file_birthyear);
if ($birthyear_dat== $word_list5[0]){$birthyear_dat = "";}
$word_list6 = read_file($dir.$file_birthmonth);
if ($birthmonth_dat== $word_list6[0]){$birthmonth_dat = "";}
$word_list7 = read_file($dir.$file_birthday);
if ($birthday_dat== $word_list7[0]){$birthdayf_dat = "";}

//メッセージ初期
$message = "";
$message_error = "";
//タグ
define ("allow_tag","<b><u><i>");
// //カテゴリーリスト
// $word_list = read_file(file_category);
// if ($word_list){array_unshift($word_list,"");}else{$word_list[]="";}
// //--------------------------------------------------------------------------------------メイン処理
// //検索のリセット
// if ($reset){$submit_search = "";$category = "";$searchword = "";}
// //キャンセル
// if ($cancel){
// 	$radio = "";
// 	$fix = "";
// 	$site_name = "";
// 	$site_url = "";
// 	$fname = "";
// 	$keyword01 = "";
// 	$keyword02 = "";
// 	$keyword03 = "";
// 	$comment = "";
// }
// //登録
if ($submit){
	//テキストチェック
	$customer = check_text($customer);
	$kana = check_text($kana);
	$company = check_text($company);
	$section = check_text($section);
	$section2 = check_text($section2);
	$degree = check_text($degree);
	$zip = check_text($zip);
	$pref = check_text($pref);
	$addr = check_text($addr);
	$addr2 = check_text($addr2);
	$phone = check_text($phone);
	$mobile = check_text($mobile);
	$memo = check_text($memo);
	$birthyear = check_text($birthyear);
	$birthmonth = check_text($birthmonth);
	$birthday = check_text($birthday);
	$firsttime = check_text($firsttime);
	$lasttime = check_text($lasttime);
	$pic = check_text($pic);
	$lastvisit = check_text($lastvisit);
	$bottle_0 = check_text($bottle_0);
	$bottle_1 = check_text($bottle_1);
	$bottle_2 = check_text($bottle_2);
	$bottle_3 = check_text($bottle_3);
	$bottle_4 = check_text($bottle_4);
	$otherbottle = check_text($otherbottle);
	$bottle_num_0 = check_text($bottle_num_0);
	$bottle_num_1 = check_text($bottle_num_1);
	$bottle_num_2 = check_text($bottle_num_2);
	$bottle_num_3 = check_text($bottle_num_3);
	$bottle_num_4 = check_text($bottle_num_4);
	$otherbottle_num = check_text($otherbottle_num);
	$bottle_quant_0 = check_text($bottle_quant_0);
	$bottle_quant_1 = check_text($bottle_quant_1);
	$bottle_quant_2 = check_text($bottle_quant_2);
	$bottle_quant_3 = check_text($bottle_quant_3);
	$bottle_quant_4 = check_text($bottle_quant_4);
	$otherbottle_quant = check_text($otherbottle_quant);
	$friends_0 = check_text($friends_0);
	$friends_1 = check_text($friends_1);
	$friends_2 = check_text($friends_2);
	$friends_3 = check_text($friends_3);
	$friends_4 = check_text($friends_4);
	$friends_5 = check_text($friends_5);
	$friends_6 = check_text($friends_6);
	$friends_7 = check_text($friends_7);
	$friends_8 = check_text($friends_8);
	$friends_9 = check_text($friends_9);

	if (!$customer){$message_error = "【お客様名】は必須項目です";}
	if (!$kana){$message_error = "【ふりがな】は必須項目です";}

	//エラーがなければ画像の登録
	if (!$message_error){
		//既存ファイルの読み込み
		$line_link = read_file(file_sdata);
		if ($fix){
			$count = $radio;
		}else{
			//カウント算出
			$new_line = "";
			if ($line_link){
				$count = 0;
				foreach ($line_link as $key => $value){// 
// 					list($temp_count,,,$temp_site_name,$temp_site_url,) = explode("&&",$value);
// 					if (!$key && ($temp_site_name == $site_name) && ($temp_site_url == $site_url)){$reload = "true";break;}
					list($temp_count,,,,,) = explode("&&",$value);
					if (!$key){$reload = "true";break;}
					if ($temp_count > $count){ $count = $temp_count;}
					$new_line .= $value."\n";
				}	
				$count++;
			}else{
				$count = 1;
			}
		}
		//画像の登録
		if (!@$reload && $upfile){
			regist_pic($count.uniqid(""));
			if ($fix && !$message_error){delete_pic($radio);}
		}
		//エラーなし＆＆画像削除が選択
		if (!$message_error && $delete_pic && $radio){
			delete_pic($radio);//画像削除
		}
	}
	//エラーがなければファイル書き込み
	if (!@$reload && !$message_error){
		if($fix && $radio){
			fix_data($radio,$line_link);
			$fix = "";
		}else{
			new_data($count,$new_line);
		}
		//フォームのクリア
// 		$site_name = "";
// 		$site_url = "";
// 		$fname = "";
// 		$keyword01 = "";
// 		$keyword02 = "";
// 		$keyword03 = "";
// 		$comment = "";
// 		$submit = "";
	}
}
//編集項目戻し
if ($fix && !$message_error){
	$line_link = read_file(file_sdata);
	return_data($radio);
}
//入替え
if (($shift_up && $radio)||($shift_down && $radio)){
	$line_link = read_file(file_sdata);
	shift_data($shift_up,$shift_down);
}
//削除確認
if ($delete_conf && $radio){
	$line_link = read_file(file_sdata);
	delete_conf($radio);
}
//削除
if ($delete && $radio){
	$line_link = read_file(file_sdata);
	delete_data($radio);
}
//既存ファイルの読み込み
$line_link = read_file(file_sdata);
//ユーザーファイルの読み込み//ページ当りの表示数
$line_custom = read_custom();
if($line_custom['dspsu']){$dspcount = $line_custom['dspsu'];}else{$dspcount = 10;}
//サーチ処理
if ($category || $searchword){$line_link = search_data($line_link,$category,$searchword);}
//ページ処理 表示配列の開始、終了位置の確定
dsp_startend($dspcount);
//ＨＴＭＬ
html_manager();
}else{
//--------------------------------------------------------------------------------------管理者 [ カテゴリー ]
$url = $url."?mode=category";
//ＰＯＳＴ
$submit1    = @$_POST["Submit1"];
$dspsu      = @$_POST["dspsu"];
$submit4  = @$_POST["Submit4"];
$category = @$_POST["category"];
$cancel   = @$_POST["cancel"];
//
$shift_up    = @$_POST["shift_up"];
$shift_down  = @$_POST["shift_down"];
$fix         = @$_POST["fix"];
$delete_conf = @$_POST["delete_conf"];
$delete      = @$_POST["delete"];
$radio       = @$_POST["radio"];
//--------------------------------------------------------------------------------------メイン処理
//操作キャンセル
if ($cancel){$radio = "";$fix = "";}
//カテゴリー登録
if ($submit4 && $category){
	$category = trim($category);
	$category = strip_tags($category);
	//文字列内の","を"、"に変換する
	$category = ereg_replace("\"","”",$category);
	$category = ereg_replace("'","’",$category);
	if(get_magic_quotes_gpc()){$category = stripslashes($category);}
	if ($category){
		//カテゴリーファイル読込み
		$word_list = read_file(file_category);
		$new_line = "";
		if ($fix){
			foreach ($word_list as $value){
				if ($value == $radio){
					$new_line .= $category."\n";
				}else{
					$new_line .= $value."\n";
				}
			}
			//登録データの読み込み
			$line_link = read_file(file_sdata);
			if ($line_link){
			  $new_links = "";
			  foreach ($line_link as $value){
				list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
				//文字列の置換
// 				$keyword01 = ereg_replace($radio,$category,$keyword01);
// 				$keyword02 = ereg_replace($radio,$category,$keyword02);
// 				$keyword03 = ereg_replace($radio,$category,$keyword03);
				$new_links .= $count."&&".$customer."&&".$kana."&&".$company."&&".$section."&&".$section2."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$memo."&&".$birthyear."&&".$birthmonth."&&".$birthday."&&".$firsttime."&&".$lasttime."&&".$pic."&&".$no_dm."&&".$lastvisit."&&".$fname."&&".$bottle_0."&&".$bottle_num_0."&&".$bottle_quant_0."&&".$bottle_1."&&".$bottle_num_1."&&".$bottle_quant_1."&&".$bottle_2."&&".$bottle_num_2."&&".$bottle_quant_2."&&".$bottle_3."&&".$bottle_num_3."&&".$bottle_quant_3."&&".$bottle_4."&&".$bottle_num_4."&&".$bottle_quant_4."&&".$otherbottle."&&".$otherbottle_num."&&".$otherbottle_quant."&&".$friends_0."&&".$friends_1."&&".$friends_2."&&".$friends_3."&&".$friends_4."&&".$friends_5."&&".$friends_6."&&".$friends_7."&&".$friends_8."&&".$friends_9."\n";
			  }
			  write_data(file_sdata,$new_links);
			}
		}else{
			if ($word_list){
				foreach ($word_list as $value){
					if ($value <> $category){$new_line .= $value."\n";}
				}
			}
			$new_line .= $category."\n";
		}
		write_data(file_category,$new_line);
		//フォームクリア
		$submit4 = "";$category = "";$fix = "";$radio = "";
	}
}
//カテゴリー入替え
if (($shift_up && $radio)||($shift_down && $radio)){
	$word_list = read_file(file_category);
	shift_data_category($shift_up,$shift_down);
}
//削除
if ($delete && $radio){
	$word_list = read_file(file_category);
	delete_data_category($radio);
}
//ページ当りの表示件数
if ($submit1){
	//数値チェック
	if (is_numeric($dspsu)){
		//ユーザーファイルの読み込み
		$line_custom = read_custom();
		//入力数値セット
		$line_custom['dspsu'] = floor($dspsu);
		//ユーザーファイル書き換え
		write_custom($line_custom);
	}
}

//ユーザーファイルの読み込み
$line_custom = read_custom();
//カテゴリーファイル読込み
$word_list = read_file(file_category);
//登録データの読み込み
$line_link = read_file(file_sdata);
//ＨＴＭＬ
html_category();
}
//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////
if ($cnt){$buttom_name = "更新登録";}else{$buttom_name = "新規登録";}

//-------------------------------------------------------↓
//ヘッダー1
html_header1();
print <<< END_OF_HTML

<script src="https://zipaddr.github.io/zipaddrx.js" charset="UTF-8"></script>

END_OF_HTML;
//ヘッダー2
html_header2();

///////////////////////////////////
// ファイルの読み込み            //
///////////////////////////////////
function read_cdata($file_cname){
	$cline = "";
	$temp_cline = file($file_cname);
	foreach ($temp_cline as $value){
		if ($value){
			$value = trim($value);
			$cline[] = $value;
		}
	}
	return $cline;
}

//-----------------------------------------------------契約ユーザーデータ

//契約ユーザーデータファイルの読み込み
$cdata_line = read_cdata(file_cdata);

//契約ユーザーデータ展開
if ($cdata_line){
foreach ($cdata_line as $value){
	//文字列分割
	list($ucount,$uplan,$uplan_limit,$uaccount,$ustore,$ucustomer_id,$ucustomer,$udegree,$uzip,$upref,$uaddr,$uaddr2,$uphone,$umobile,$uemail,$umemo,$ufirsttime,$ulasttime,$uflag,$udsp,$ucancel_flag,$uc_date,$uc_reason1,$uc_reason2,$uc_reason3,$uc_reason4,$uc_reason5,$uc_reasonO,$uc_reasonOf,$umonitor,$ustop) = explode("&&",$value);
//契約ユーザーデータ抽出
	if ($user == $ucount){

///////////////////////////////////
// 登録限度数設定              //
///////////////////////////////////
	//登録件数チェックのためカウンタ再読み込みfile_sdata
	$fp = fopen( file_sdata, 'r' );
	for( $counts = 0; fgets( $fp ); $counts++ );

if (!$cnt){
	if ($counts >= $uplan_limit){
		$message_error = "登録件数は".$uplan_limit."件までです";
		$message_error2 = "<br /><span>プランのご変更は<a href='/usces-member' target='_blank'>契約ユーザーログイン</a>で行えます</span>";
		$form_none = 1;
		$disabled = "disabled";
	}

	//利用停止
	if ($ustop){
		$message_error = "登録が100件までに制限されています";
		$message_error2 = "<br /><span>有料プランのご利用再開は<a href='/usces-member' target='_blank'>契約ユーザーログイン</a>からお手続きください</span>";
		if ($counts >= 100){
			$form_none = 1;
			$disabled = "disabled";
		}
	}
}

///////////////////////////////////
// ファイル書き換え              //
///////////////////////////////////
function write_data($file_name,$data){
	$file = fopen($file_name,"w") or die("$file_name is not found");
	flock($file,LOCK_EX);
	fputs($file,$data);
	flock($file,LOCK_UN);
	fclose($file);
}
///////////////////////////////////
// ファイル読込み                //
///////////////////////////////////
function read_file($file_name){
	$line = "";
	$temp_line = file($file_name);
	foreach ($temp_line as $value){
		if ($value){
			$value = trim($value);
			$line[] = $value;
		}
	}
	return $line;
}
///////////////////////////////////
// ユーザーファイルの読み込み    //
///////////////////////////////////
function read_custom(){
	$line_custom = "";
	$temp_line = file(file_custom);
	foreach ($temp_line as $value){
		if ($value){
			$value = trim($value);
			list($i,$j) = explode("&&",$value);
			$line_custom[$i] = $j;
		}
	}
	return $line_custom;
}

////////////////////////////////////////////////////
// リンク集 [ cutlink ]　データ登録               //
////////////////////////////////////////////////////
//--------------------------------------------------------------------------------------関数定義
///////////////////////////////////
// サーチ処理                    //
///////////////////////////////////
function search_data($line,$category,$word){
	//カテゴリーで抽出
	if ($category){
		$new_line = "";
		foreach ($line as $value){
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
			if (($category == $keyword01)||($category == $keyword02)||($category == $keyword03)){$new_line[] = $value;}
		}
		$line = $new_line;
	}
	//キーワードで抽出
	if ($word){
		//全角空白を半角空白に変換
		$word = ereg_replace("　"," ",$word);
		//先頭と末尾の空白をトリム
		$word = trim($word);
		//検索語の整形(mbstringがあれば）
//		if (extension_loaded("mbstring")){$word = mb_convert_kana($word,"aKCV");$word = strtoupper($word);}
		//文字列を" "で分割する
		if (preg_replace("/ /i",$word)){$word_array = explode(" ",$word);}else{$word_array[] = $word;}
		$new_line = "";
		foreach ($line as $value){
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
			$str = $site_name.$comment;
			//検索対象の文字列を整形(mbstringがあれば）
//			if (extension_loaded("mbstring")){
//				$str = mb_convert_kana($str,"aKCV");
//				$str = strtoupper($str);
//			}
			//検索用正規表現
			$sword = "";
			foreach($word_array as $key => $value2){
				if($value2){
					if ($key){$sword .= "(.*".$value2.".*)";}else{$sword .= "(.*".$value2.".*)";}
				}
			}
			//文字列検索
			if (preg_replace("/$sword/i", $str)){$new_line[] = $value;}
		}
		$line = $new_line;
	}
	return $line;
}
///////////////////////////////////
// 編集項目戻し                  //
///////////////////////////////////
function return_data($radio){
	global $line_link,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9,$message,$fix;
	if ($radio){
		foreach ($line_link as $value){
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
			if ($count == $radio){
				$message = "選択データを編集します";
				break;
			}
		}
	}else{
		$fix = "";
	}
}
///////////////////////////////////
// 入替え                        //
///////////////////////////////////
function shift_data($shift_up,$shift_down){
	global $line_link,$radio,$message;
	foreach ($line_link as $key => $value){
		list($count,) = explode("&&",$value);
		if ($count == $radio){
			if($shift_up){
				if (@$line_link[($key-1)]){
					$temp_line = $line_link[$key]; 
					$line_link[$key] = $line_link[($key-1)];
					$line_link[($key-1)] = $temp_line;
				}			 
			}else{
				if (@$line_link[($key+1)]){
					$temp_line = $line_link[$key]; 
					$line_link[$key] = $line_link[($key+1)];
					$line_link[($key+1)] = $temp_line;
				}			 
			}
			$check = "true";
			$message = "選択データを入替えました";
			break;
		}
	}
	//ファイルの書き換え
	if (@$check){
		$new_line = "";
		foreach ($line_link as $value){
			$new_line .= $value."\n";
		}
		write_data(file_sdata,$new_line);
	}
}
///////////////////////////////////
// 削除確認                      //
///////////////////////////////////
function delete_conf($radio){
	global $line_link,$delete_sw,$message;
	global $count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9;
	foreach ($line_link as $value){
		list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
		if ($count == $radio){
			$delete_sw = "true";
			$message = "選択データを削除します";
			$site_name = fix_text($site_name);
			$comment = fix_text($comment);
			break;
		}
	}
}
///////////////////////////////////
// 削除                          //
///////////////////////////////////
function delete_data($radio){
	global $line_link,$message;
	$new_line = "";
	foreach ($line_link as $value){
		list($count,) = explode("&&",$value);
		if ($count == $radio){
			$check = "true";
		}else{
			$new_line .= $value."\n";
		}
	}
	//ファイル書き換え
	if (@$check){
		write_data(file_sdata,$new_line);
		//画像削除
		delete_pic($radio);
		//メッセージ
		$message = "選択されたデータを削除しました";
		//ログ読み込み
		$line_link = read_file(file_sdata);
	}
}
///////////////////////////////////
// 新規登録                      //
///////////////////////////////////
function new_data($count,$new_line){
	global $customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9;
	global $message;
	$new_line = $count."&&".$customer."&&".$kana."&&".$company."&&".$section."&&".$section2."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$memo."&&".$birthyear."&&".$birthmonth."&&".$birthday."&&".$firsttime."&&".$lasttime."&&".$pic."&&".$no_dm."&&".$lastvisit."&&".$fname."&&".$bottle_0."&&".$bottle_num_0."&&".$bottle_quant_0."&&".$bottle_1."&&".$bottle_num_1."&&".$bottle_quant_1."&&".$bottle_2."&&".$bottle_num_2."&&".$bottle_quant_2."&&".$bottle_3."&&".$bottle_num_3."&&".$bottle_quant_3."&&".$bottle_4."&&".$bottle_num_4."&&".$bottle_quant_4."&&".$otherbottle."&&".$otherbottle_num."&&".$otherbottle_quant."&&".$friends_0."&&".$friends_1."&&".$friends_2."&&".$friends_3."&&".$friends_4."&&".$friends_5."&&".$friends_6."&&".$friends_7."&&".$friends_8."&&".$friends_9."\n".$new_line;
	//ファイル書き換え
	write_data(file_sdata,$new_line);
	//メッセージ
	$message = "新規登録しました";
}
///////////////////////////////////
// 編集登録                      //
///////////////////////////////////
function fix_data($radio,$line_link){
	global $customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9;
	global $delete_pic,$message;
	$new_line = "";
	foreach ($line_link as $value){
		list($count,,,$temp_fname,) = explode("&&",$value);
		if ($count == $radio){
			if ($temp_fname && !$delete_pic && !$fname){$fname = $temp_fname;}
			$new_line .= $count."&&".$customer."&&".$kana."&&".$company."&&".$section."&&".$section2."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$memo."&&".$birthyear."&&".$birthmonth."&&".$birthday."&&".$firsttime."&&".$lasttime."&&".$pic."&&".$no_dm."&&".$lastvisit."&&".$fname."&&".$bottle_0."&&".$bottle_num_0."&&".$bottle_quant_0."&&".$bottle_1."&&".$bottle_num_1."&&".$bottle_quant_1."&&".$bottle_2."&&".$bottle_num_2."&&".$bottle_quant_2."&&".$bottle_3."&&".$bottle_num_3."&&".$bottle_quant_3."&&".$bottle_4."&&".$bottle_num_4."&&".$bottle_quant_4."&&".$otherbottle."&&".$otherbottle_num."&&".$otherbottle_quant."&&".$friends_0."&&".$friends_1."&&".$friends_2."&&".$friends_3."&&".$friends_4."&&".$friends_5."&&".$friends_6."&&".$friends_7."&&".$friends_8."&&".$friends_9."\n";
		}else{
			$new_line .= $value."\n";
		}
	}
	//ファイル書き換え
	write_data(file_sdata,$new_line);
	//メッセージ
	$message = "編集登録しました";
}
///////////////////////////////////
// 画像登録                      //
///////////////////////////////////
function regist_pic($new_filename){//$new_filenameには拡張子を除いたファイル名
	global $upfile,$upfile_name,$upfile_size;
	global $upload_ext,$upload_limit;
	global $up_fname,$fname;
	global $message_error;
	//アップファイルのコピー
	$file_dir = "regist_pic/";
	$extension = preg_replace("/.+\./", "$1", $upfile_name);
	foreach($upload_ext as $value){
		if(!preg_match("/.+\.".$value."/i",$upfile_name)){//拡張子名のチェック
			$message_error = "画像ファイルの形式エラーです。";
		}elseif ($upfile_size > $upload_limit){//画像サイズのチェック
			$message_error = "画像ファイルのサイズエラーです。";
			break;
		}else{
			//ファイルのコピー
			$up_fname = $file_dir.$new_filename.".".$extension;
			move_uploaded_file($upfile,$up_fname) or die("Upload was not completed");
			$message_error = "";
			break;
		}
	}

//拡張子をすべて小文字に統一
$extension_s = mb_strtolower($extension);
//画像保存パス
$fname = $file_dir.$new_filename."_s.".$extension_s;

require_once("resizeimg.class.php");
$ri = new resizeImage();
$input_image =  $up_fname;    //元画像ファイルパス（URLでも可）
$width = 400;
$height = 400;

$output_image = $ri->dispResizeImgPath($input_image,$width,$height);

// サイズを自動計算させるには数値の代わりに * を入れる
//$output_image = $ri->dispResizeImgPath($input_image,$width,'*');

// ※指定サイズの枠内に収まるように ※第4引数に「ss」で縮小のみに指定

//元画像削除
unlink($up_fname);

}
///////////////////////////////////
// 登録画像削除                  //
///////////////////////////////////
function delete_pic($radio){
	global $line_link;
	foreach ($line_link as $value){
		list($count,,,,,,,,,,,,,,,,,,,,,,$fname,,,,,,,,,,,,,,,,,,,,,,,,,,,,) = explode("&&",$value);
		if ($count == $radio){
			@unlink($fname);
			return;
		}
	}
}
///////////////////////////////////
// テキストチェック              //
///////////////////////////////////
function check_text($str){
	$str = trim($str);
	if(get_magic_quotes_gpc()){$str = stripslashes($str);}
	$str = ereg_replace("&&","＆＆",$str);
	$str = ereg_replace("\r|\n|\r\n","<br>",$str);
	return $str;
}
/////////////////////////////////
// テキスト出力整形            //
/////////////////////////////////
function fix_text($str){
	$str = preg_replace("/<br>/","\n",$str);
	$str = strip_tags($str,allow_tag);
	$str = preg_replace("/(style|onmouse|onclick)[^=]*=/i", "", $str);
	$str = preg_replace("/\r|\n|\r\n/","<br>",$str);
	//リンク、メール置き換え
	if(preg_match_all("/http\:\/\/[\w\.\~\-\/\?\&\+\=\:\@\%\#]+/",$str,$match)){
		foreach ($match[0] as $value){
			if(strlen($value) > 60){ $dsp_url = "URL";}else{$dsp_url = $value;}
			$str = preg_replace("/$value/","<a href='".$value."' target='_blank'>\n".$dsp_url."\n</a>",$str);
		}
	}
	if(preg_match_all("/[\w\d\-\.]+\@[\w\d\-\.]+/",$str,$match)){
		foreach ($match[0] as $value){
			if(strlen($value) > 60){ $dsp_url = "MAIL";}else{$dsp_url = $value;}
			$str = preg_replace("/$value/","<a href='mailto:".$value."'>\n".$dsp_url."\n</a>",$str);
		}
	}
	//
	if (!$str){$str = "&nbsp;";}
	return $str;
}
/////////////////////////////////////
// 表示配列の開始、終了位置の確定  //
/////////////////////////////////////
function dsp_startend($dspcount){
	global $page,$all;
	global $line_link,$start,$end,$psu,$amari;
	$all = count($line_link);//総件数
	//総ページ数
	$psu = @floor($all / $dspcount);
	$amari = $all % $dspcount;
	if ($amari){$psu++;}
	//不正処理調整
	if ($page < 0){$page = 0;}
	if ($page >= $psu){$page = $psu - 1;}
	//開始、終了位置の確定
	if (!$dspcount){//『0』の場合は全件表示
		$start=0;
		$end = count($line_link) - 1;
	}else{
		//開始配列位置
		$start = $page * $dspcount;
		//終了配列位置
		$end = $page * $dspcount + $dspcount - 1;
		if ($all <= $end){$end = $all - 1;}
	}
}
///////////////////////////////////
// ページリンクテキストの表示    //
///////////////////////////////////
function html_pagelink($dspcount){
	global $url,$pageurl,$page,$all,$amari;
	global $line_link,$start,$end,$psu;
	$all = count($line_link);//総件数
	if ($dspcount){//『0』の場合は非表示
		//ページリンクの表示
		if ($line_link && $psu > 1){
			echo "<table border='0' class='table-prenext'><tr>"."\n";
			//PREV
			echo "<td nowrap class='td-prev'>"."\n";
			if ($page > 0){
				if (($page -1) == 0){
					echo " <a href='".$pageurl."'><<前の".$dspcount."件</a> "."\n";
				}else{
					echo " <a href='".$pageurl."page=".($page - 1)."'><<前の".$dspcount."件</a> "."\n";
				}
			}else{
				echo "&nbsp;"."\n";
			}
			echo "</td>"."\n";
			//ページリンク表示
			echo "<td class='td-prenext'>"."\n";
			echo $all." 件中 ".($start+1)." 〜 ".($end+1)." 件を表示 ";
			//ページリンク（[1] [2] [3]・・・）の表示は
			$link_limit = 7;//常に7個以下にする
			$go = ($page+1) - ceil($link_limit/2);
			if ($go < 0){$go = 0;}
			$stop = $go + $link_limit;
			if ($stop > $psu){
				$stop = $psu;
				$go = $stop - $link_limit;
				if ($go < 0){$go = 0;}
			}
			//
			for ($i=$go; $i<$stop; $i++){
				if ($i == $page){
					echo " <b>[".($i+1)."]</b> ";
				}else{
					if ($i){
						echo " <a href='".$pageurl."page=".$i."'>[".($i+1)."]</a> ";
					}else{
						echo " <a href='".$pageurl."'>[".($i+1)."]</a> ";
					}
				}
			}
			echo "</td>"."\n";
			//NEXT
			echo "<td nowrap class='td-next'>"."\n";
			if (($page+1) < $psu){
				if (($page+2) == $psu && $amari){
					echo " <a href='".$pageurl."page=".($page + 1)."'>次の".$amari."件>></a> "."\n";
				}else{
					echo " <a href='".$pageurl."page=".($page + 1)."'>次の".$dspcount."件>></a> "."\n";
				}
			}else{
				echo "&nbsp;"."\n";
			}
			echo "</td>"."\n";
			//
			echo "</tr></table>"."\n";
		}
	}
}
//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////
function html_manager(){
	global $pageurl,$url,$category,$searchword,$word_list,$line_link,$dspcount;
	global $submit,$page,$fix,$delete_sw,$radio,$start,$end,$message,$message_error;
	global $customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9;
	html_manager_header("リンク登録","regist");
	//ＵＲＬの設定
	$pageurl = $url."?";

print <<< END_OF_HTML

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>

END_OF_HTML;
//-------------------------------------------------------↑
if ($cnt){
	//文字列内のタグを取り除く
	$temp_title = strip_tags($customer);
//	$temp_title2 = strip_tags($kana);
	//長い文字列は40バイト以降を消去し"..."を付加する
	if(strlen($temp_title) > 40){ $temp_title = substr($temp_title,0,60)."...";}
	echo "	<li class='active'>".$temp_title." 様の編集</li>"."\n";
}else{	
	echo "	<li class='active'>新規登録</li>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
		</ul>
</div>

<h3 class="regist_title">$buttom_name</h3>

END_OF_HTML;
//-------------------------------------------------------↑
//エラーメッセージ
if ($message_error){
	echo "<h3 class='error_msg'>".$message_error." ".$message_error2."</h3>"."\n";
}else{
	echo "<h3 class='error_msg'>".$message."</h3>"."\n";
}

if ($cnt){
	echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$this_file."?cnt=".$cnt."'>"."\n";
}else{
	if (!$form_none){
		echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$this_file."'>"."\n";
	}
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<div class="detail_tbl">
<table>
<tr>
    <th class="detail_tbl_t">お客様名 <span class="hissu">※必須</span> <div class="infotip"><abbr title="お客様の名前は漢字で入力し、姓名の間にスペースを入れておきます。" rel="tooltip">?</abbr></div></th>
END_OF_HTML;
//-------------------------------------------------------↑
$customer =  preg_replace("/<br>/","\n",$customer);
$customer = htmlspecialchars ($customer);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td class="detail_tbl_t">
<input type="text" name="customer" id="user_name" value="$customer" class="form-control" maxlength="40" placeholder="姓　名" style="ime-mode: active;" $disabled>
	</td>
  </tr>
<tr>
    <th class="detail_tbl_t">ふりがな <span class="hissu">※必須</span> <div class="infotip"><abbr title="検索時はここの読みから絞り込みます。" rel="tooltip">?</abbr></div></th>
END_OF_HTML;
//-------------------------------------------------------↑
$kana =  preg_replace("/<br>/","\n",$kana);
$kana = htmlspecialchars ($kana);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td class="detail_tbl_t">
<input type="text" name="kana" id="user_name_kana" value="$kana" class="form-control" maxlength="40" placeholder="必ず【ひらがな】で入力" style="ime-mode: active;" onblur="FuriganaCheck();" $disabled>
</td>
  </tr>
END_OF_HTML;
//-------------------------------------------------------↑
if ($lastvisit_logs <> ""){ //最終来店日記録オンオフ
$lastvisit =  preg_replace("/<br>/","\n",$lastvisit);
$lastvisit = htmlspecialchars ($lastvisit);
//-------------------------------------------------------↓
print <<< END_OF_HTML
<tr>
    <th class="detail_tbl_t">最終来店日 <div class="infotip"><abbr title="最後にご来店になった日を登録します。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t">
<input type="text" name="lastvisit" value="$lastvisit" class="form-control datepicker" style="ime-mode: active;"  placeholder="タップしてカレンダーから入力" $disabled>
</td>
  </tr>
END_OF_HTML;
//-------------------------------------------------------↑
}
if ($dspimg_logs <> ""){ //画像登録オンオフ
//-------------------------------------------------------↓
print <<< END_OF_HTML
<tr>
    <th class="detail_tbl_t">画像アップロード <div class="infotip"><abbr title="画像をアップロードします。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t">
END_OF_HTML;
//-------------------------------------------------------↑
	if (@$delete_sw){
		if ($dsp_img){
			echo "<div class='up_img' style='width:100;height:auto;'><a href='".$dsp_img."'><img src='".$dsp_img."' style='width:100px;height:auto;'></a></div>"."\n";
		}else{
			echo "&nbsp;";
		}
	}else{
		if ($dsp_img){
			echo "<div class='up_img' style='width:100px;height:auto;'><a href='".$dsp_img."'><img src='".$dsp_img."' style='width:100px;height:auto;'></a></div>"."\n";
			echo "<label><input type='checkbox' name='delete_pic' value='delete'> この画像を削除</label><br /><br />"."\n";
		}
		echo "	<input name='upfile' type='file' size='50'>"."\n";
	}
//-------------------------------------------------------↓
print <<< END_OF_HTML
	</td>
  </tr>
END_OF_HTML;
//-------------------------------------------------------↑
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<tr>
    <th class="detail_tbl_t">ボトル <div class="infotip"><abbr title="キープボトルをこの中から選択します。任意でボトルタグの番号や残量(数値等で、基準は個々のお好みで)が入れられます。<br>ここで表示されるボトルリストは[設定] > [ボトルリスト編集]で追加変更できます。<br><br>珍しいボトルなどプルダウンに含まないボトル名の場合は自由入力項目を使用します。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t">
END_OF_HTML;
//-------------------------------------------------------↑
$bottle_num_0 =  preg_replace("/<br>/","\n",$bottle_num_0);
$bottle_num_0 = htmlspecialchars ($bottle_num_0);
$bottle_quant_0 =  preg_replace("/<br>/","\n",$bottle_quant_0);
$bottle_quant_0 = htmlspecialchars ($bottle_quant_0);

$bottle_num_1 =  preg_replace("/<br>/","\n",$bottle_num_1);
$bottle_num_1 = htmlspecialchars ($bottle_num_1);
$bottle_quant_1 =  preg_replace("/<br>/","\n",$bottle_quant_1);
$bottle_quant_1 = htmlspecialchars ($bottle_quant_1);

$bottle_num_2 =  preg_replace("/<br>/","\n",$bottle_num_2);
$bottle_num_2 = htmlspecialchars ($bottle_num_2);
$bottle_quant_2 =  preg_replace("/<br>/","\n",$bottle_quant_2);
$bottle_quant_2 = htmlspecialchars ($bottle_quant_2);

$bottle_num_3 =  preg_replace("/<br>/","\n",$bottle_num_3);
$bottle_num_3 = htmlspecialchars ($bottle_num_3);
$bottle_quant_3 =  preg_replace("/<br>/","\n",$bottle_quant_3);
$bottle_quant_3 = htmlspecialchars ($bottle_quant_3);

$bottle_num_4 =  preg_replace("/<br>/","\n",$bottle_num_4);
$bottle_num_4 = htmlspecialchars ($bottle_num_4);
$bottle_quant_4 =  preg_replace("/<br>/","\n",$bottle_quant_4);
$bottle_quant_4 = htmlspecialchars ($bottle_quant_4);

$otherbottle_num =  preg_replace("/<br>/","\n",$otherbottle_num);
$otherbottle_num = htmlspecialchars ($otherbottle_num);
$otherbottle_quant =  preg_replace("/<br>/","\n",$otherbottle_quant);
$otherbottle_quant = htmlspecialchars ($otherbottle_quant);
//-------------------------------------------------------↓

if (!$cnt){
print <<< END_OF_HTML
<div id="bottles"><div class="bottles_var">
END_OF_HTML;
if ($word_list){
	echo "<select name='bottle_0' class='form-category form-control' $disabled>"."\n";
	echo "<option value=''>ボトルリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($bottle_0 == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select><input type='text' name='bottle_num_0' value='$bottle_num_0' class='form-category2 form-control' placeholder='タグ番号等' maxlength='8' $disabled><input type='text' name='bottle_quant_0' value='$bottle_quant_0' class='form-category2 form-control' placeholder='残量等' maxlength='8' $disabled><button class='btn btn-default bottles_del' style='float: right;' title='削除'>×</button>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
  </div></div>
<input type="button" value="入力項目追加" class="btn btn-success btn-sm bottles_add" title="入力項目追加" />

<div id="otherbottles">
<label for="Panel1"><strong>+</strong> 珍しいボトル名などその他のボトル <div class="infotip"><abbr title="珍しいボトルなどプルダウンに含まないボトル名の場合は、ここをタッチしてボトルの自由入力フォームを使用してください。" rel="tooltip">?</abbr></div></label>
<input type="checkbox" id="Panel1" value="" class="form-control on-off" title="入力項目追加" /> 
	<div class="otherbottle">
		<input type="text" name="otherbottle" value="$otherbottle" class="form-category form-control" $disabled />
		<input type="text" name="otherbottle_num" value="$otherbottle_num" class="form-category3 form-control" placeholder="タグ番号等" maxlength="8" $disabled><input type="text" name="otherbottle_quant" value="$otherbottle_quant" class="form-category2 form-control" placeholder="タグ番号等" maxlength="8" $disabled />
	</div>
</div>
      </td>
</tr>
END_OF_HTML;

} else {
//更新時
if ($word_list){
//	if ($bottle_1) { $bottle_0_disabled = "disabled"; }
	echo "<select name='bottle_0' class='form-category form-control bottle_0' $bottle_0_disabled>"."\n";
	echo "<option value=''>> ボトルリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($bottle_0 == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select><input type='text' name='bottle_num_0' value='$bottle_num_0' class='form-category2 form-control bottle_0' placeholder='タグ番号等' maxlength='8'><input type='text' name='bottle_quant_0' value='$bottle_quant_0' class='form-category2 form-control bottle_0' placeholder='残量等' maxlength='8'><br />"."\n";
}
//if ($bottle_0){
if ($word_list){
//	if ($bottle_2) { $bottle_1_disabled = "disabled"; }
	echo "<select name='bottle_1' class='form-category form-control bottle_1' $bottle_1_disabled>"."\n";
	echo "<option value=''>> ボトルリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($bottle_1 == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select><input type='text' name='bottle_num_1' value='$bottle_num_1' class='form-category2 form-control bottle_1' placeholder='タグ番号等' maxlength='8'><input type='text' name='bottle_quant_1' value='$bottle_quant_1' class='form-category2 form-control bottle_1' placeholder='残量等' maxlength='8'><br />"."\n";
}
//}
//if ($bottle_1){
if ($word_list){
//	if ($bottle_3) { $bottle_2_disabled = "disabled"; }
	echo "<select name='bottle_2' class='form-category form-control bottle_2' $bottle_2_disabled>"."\n";
	echo "<option value=''>> ボトルリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($bottle_2 == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select><input type='text' name='bottle_num_2' value='$bottle_num_2' class='form-category2 form-control bottle_2' placeholder='タグ番号等' maxlength='8'><input type='text' name='bottle_quant_2' value='$bottle_quant_2' class='form-category2 form-control bottle_2' placeholder='残量等' maxlength='8'><br />"."\n";
}
//}
//if ($bottle_2){
if ($word_list){
	echo "<select name='bottle_3' class='form-category form-control bottle_3'>"."\n";
	echo "<option value=''>> ボトルリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($bottle_3 == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select><input type='text' name='bottle_num_3' value='$bottle_num_3' class='form-category2 form-control bottle_3' placeholder='タグ番号等' maxlength='8'><input type='text' name='bottle_quant_3' value='$bottle_quant_3' class='form-category2 form-control bottle_3' placeholder='残量等' maxlength='8'><br />"."\n";
}
//}

// if ($word_list){
// 	echo "<select name='bottle_4' class='form-category form-control' $disabled>"."\n";
// 	echo "<option value=''>ボトルリストから選択</option>"."\n";
// 	foreach ($word_list as $value){
// 		if (strstr($value, '■')) {
// 			echo "    	<optgroup label='".$value."'>"."\n";
// 		}elseif ($value == '-/-') {
// 			echo "    	</optgroup>"."\n";
// 		}elseif ($bottle_4 == $value){
// 			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
// 		}else{
// 			echo "    	<option value='".$value."'>".$value."</option>"."\n";
// 		}
// 	}
// 	echo "      </select><input type='text' name='bottle_num_3' value='$bottle_num_3' class='form-category2 form-control' placeholder='タグ番号等' maxlength='8' $disabled><input type='text' name='bottle_quant_3' value='$bottle_quant_3' class='form-category2 form-control' placeholder='残量等' maxlength='8' $disabled><br />"."\n";
// }

print <<< END_OF_HTML
<input type="text" name="otherbottle" value="$otherbottle" class="form-category form-control otherbottle" maxlength="40" placeholder="その他のボトル" $disabled>
		<input type="text" name="otherbottle_2" value="$otherbottle_2" class="form-category3 form-control otherbottle" placeholder="タグ番号等" maxlength="4" $disabled><input type="text" name="otherbottle_3" value="$otherbottle_3" class="form-category2 form-control otherbottle" placeholder="残量等" maxlength="4" $disabled>
      </td>
</tr>
END_OF_HTML;
}

if ($company_logs <> ""){ //会社記録オンオフ
//-------------------------------------------------------↑
$company =  preg_replace("/<br>/","\n",$company);
$company = htmlspecialchars ($company);
$section =  preg_replace("/<br>/","\n",$section);
$section = htmlspecialchars ($section);
$section2 =  preg_replace("/<br>/","\n",$section2);
$section2 = htmlspecialchars ($section2);
//-------------------------------------------------------↓
print <<< END_OF_HTML
<tr>
    <th class="detail_tbl_t">会社名・所属 <div class="infotip"><abbr title="㈱、㈲や丸囲み数字、ローマ数字、㏍、℡、№などの機種依存文字は文字化けの原因になるので使わないようにしましょう！" rel="tooltip">?</abbr></th>
    <td class="detail_tbl_t">
<input type="text" name="company" value="$company" class="form-control" maxlength="60" placeholder="会社名　㈱、㈲などの機種依存文字禁止！" style="ime-mode: active;" $disabled><br />
<input type="text" name="section" value="$section" class="form-control" maxlength="60" placeholder="所属1" style="ime-mode: active;" $disabled><br />
<input type="text" name="section2" value="$section2" class="form-control" maxlength="60" placeholder="所属2" style="ime-mode: active;" $disabled></td>
  </tr>
<tr>
    <th class="detail_tbl_t">役職</th>
END_OF_HTML;
//-------------------------------------------------------↑
$degree =  preg_replace("/<br>/","\n",$degree);
$degree = htmlspecialchars ($degree);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td class="detail_tbl_t">
<input type="text" name="degree" value="$degree" class="form-control" maxlength="60" placeholder="代表取締役など" $disabled></td>
  </tr>
END_OF_HTML;
}
if ($addr_logs <> ""){ //住所記録オンオフ
//-------------------------------------------------------↑
$zip =  preg_replace("/<br>/","\n",$zip);
$zip = htmlspecialchars ($zip);
$pref =  preg_replace("/<br>/","\n",$pref);
$pref = htmlspecialchars ($pref);
$addr =  preg_replace("/<br>/","\n",$addr);
$addr = htmlspecialchars ($addr);
$addr2 =  preg_replace("/<br>/","\n",$addr2);
$addr2 = htmlspecialchars ($addr2);
//-------------------------------------------------------↓
print <<< END_OF_HTML

	<tr>
		<th class="detail_tbl_t">郵便番号 <div class="infotip"><abbr title="数字7桁だけを入力すれば、以下の都道府県や住所を自動入力します。番地やビル名などは入力ください。" rel="tooltip">?</abbr></div></th>
			<td class="detail_tbl_t"><input type="number" id="zip" name="zip" value="$zip" class="form-zip form-control" placeholder="(数字のみ) 自動変換" maxlength="8" style="ime-mode: disabled;" $disabled></td>
	</tr><tr>
		<th class="detail_tbl_t">都道府県</th>
		<td class="detail_tbl_t">
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list4){
	echo "<select name='pref' class='form-zip form-control' id='pref' $disabled>"."\n";
	echo "<option value=''>選択ください</option>"."\n";
	foreach ($word_list4 as $value){
		if ($pref == $value){
			echo "    	<option  value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option  value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}

//-------------------------------------------------------↓ 
print <<< END_OF_HTML
		</td>
	</tr><tr>
		<th class="detail_tbl_t">住所番地 <div class="infotip"><abbr title="自動入力では番地以降は自動入力されませんので、番地やビル名などは入力ください。" rel="tooltip">?</abbr></div></th>
			<td class="detail_tbl_t"><input type="text" name="addr" value="$addr" class="form-control" id="addr" placeholder="番地まで" $disabled></td>
	</tr><tr>
		<th class="detail_tbl_t">ビル名等</th>
		<td class="detail_tbl_t"><input type="text" name="addr2" value="$addr2" class="form-control" id="addr2" placeholder="ビル名及び号室など" style="ime-mode: active;" $disabled></td>
	</tr>

END_OF_HTML;
}
if ($phone_logs <> ""){ //電話記録オンオフ
//-------------------------------------------------------↑
$phone =  preg_replace("/<br>/","\n",$phone);
$phone = htmlspecialchars ($phone);
//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <th class="detail_tbl_t">電話番号</th>
    <td class="detail_tbl_t"><input type="tel" name="phone" value="$phone" class="form-tel form-control" placeholder="数字のみ(ハイフン不要)" maxlength="13" style="ime-mode: disabled;" $disabled></td>
	</tr>
END_OF_HTML;
}
if ($mobile_logs <> ""){ //携帯電話記録オンオフ
//-------------------------------------------------------↑
$mobile =  preg_replace("/<br>/","\n",$mobile);
$mobile = htmlspecialchars ($mobile);
//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <th class="detail_tbl_t">携帯電話</th>
    <td class="detail_tbl_t"><input type="tel" name="mobile" value="$mobile" class="form-tel form-control" placeholder="数字のみ(ハイフン不要)" maxlength="13" style="ime-mode: disabled;" $disabled></td>
	</tr>
END_OF_HTML;
}

if ($friends_logs <> ""){ //友人知人記録オンオフ
if (!$cnt){
//-------------------------------------------------------↑
$friends =  preg_replace("/<br>/","\n",$friends);
$friends = htmlspecialchars ($friends);
//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <th class="detail_tbl_t">友人知人情報 <div class="infotip"><abbr title="友人知人情報に入力した内容も検索時にヒットします。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t"><div id="friends"><div class="friends_var"><input type="text" name="friends_0" id="friends_0" value="$friends_0" maxlength="20" class="form-control friends_form_new" placeholder="友人知人名" style="ime-mode: active; float:left;" $disabled><button class="btn btn-default friends_del" title="削除">×</button><div class="clear"></div>
  </div></div>

<input type="button" value="入力項目追加" class="btn btn-success btn-sm friends_add" title="入力項目追加">
</td>
	</tr>
END_OF_HTML;
} else {
print <<< END_OF_HTML
  <tr>
    <th class="detail_tbl_t">友人知人情報 <div class="infotip"><abbr title="友人知人情報に入力した内容も検索時にヒットします。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t"><input type="text" name="friends_0" id="friends_0" value="$friends_0" maxlength="20" class="form-control friends_form" placeholder="友人知人名" style="ime-mode: active;" $disabled>
    <input type="text" name="friends_1" id="friends_1" value="$friends_1" maxlength="20" class="form-control friends_form" placeholder="友人知人名" style="ime-mode: active;" $disabled>
    <input type="text" name="friends_2" id="friends_2" value="$friends_2" maxlength="20" class="form-control friends_form" placeholder="友人知人名" style="ime-mode: active;" $disabled>
    <input type="text" name="friends_3" id="friends_3" value="$friends_3" maxlength="20" class="form-control friends_form" placeholder="友人知人名" style="ime-mode: active;" $disabled>
    <input type="text" name="friends_4" id="friends_4" value="$friends_4" maxlength="20" class="form-control friends_form" placeholder="友人知人名" style="ime-mode: active;" $disabled>
    <input type="text" name="friends_5" id="friends_5" value="$friends_5" maxlength="20" class="form-control friends_form" placeholder="友人知人名" style="ime-mode: active;" $disabled>
<div class="clear"></div>
</td>
	</tr>
END_OF_HTML;
}
}
//-------------------------------------------------------↑
$memo =  preg_replace("/<br>/","\n",$memo);
$memo = htmlspecialchars ($memo);
//-------------------------------------------------------↓
print <<< END_OF_HTML
	<tr>
    <th class="detail_tbl_t">メモ <div class="infotip"><abbr title="メモに入力した内容も検索時にヒットしますので、重要なキーワードとなる事項は入力されることをお薦めします。また、㈱、㈲や丸囲み数字、ローマ数字、㏍、℡、№などの機種依存文字は文字化けの原因になるので使わないようにしましょう！" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t"><textarea name="memo" class="form-control" rows="6" style="ime-mode: active;" $disabled>$memo</textarea></td>
  </tr>
END_OF_HTML;
if ($birthday_logs <> ""){ //誕生日記録オンオフ
//-------------------------------------------------------↑
$birthyear =  preg_replace("/<br>/","\n",$birthyear);
$birthyear = htmlspecialchars ($birthyear);
$birthmonth =  preg_replace("/<br>/","\n",$birthmonth);
$birthmonth = htmlspecialchars ($birthmonth);
$birthday =  preg_replace("/<br>/","\n",$birthday);
$birthday = htmlspecialchars ($birthday);
//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <th class="detail_tbl_t">生年月日 <div class="infotip"><abbr title="生年月日を入力(月まででも可)すれば誕生月に名前の横にバースデーマークを表示し、現在の年齢も表示します。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t">
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list5){
	echo "<select name='birthyear' class='form-birth form-control' $disabled>"."\n";
	echo "<option value=''>選択...年</option>"."\n";
	foreach ($word_list5 as $value){
		if ($birthyear == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
    / 
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list6){
	echo "<select name='birthmonth' class='form-birth form-control' $disabled>"."\n";
	echo "<option value=''>選択...月</option>"."\n";
	foreach ($word_list6 as $value){
		if ($birthmonth == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
    / 
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list7){
	echo "<select name='birthday' class='form-birth form-control' $disabled>"."\n";
	echo "<option value=''>選択...日</option>"."\n";
	foreach ($word_list7 as $value){
		if ($birthday == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}

print <<< END_OF_HTML
生
</td>
  </tr>
END_OF_HTML;
}
if ($company_logs <> ""){ //担当者記録オンオフ
//-------------------------------------------------------↑
$pic =  preg_replace("/<br>/","\n",$pic);
$pic = htmlspecialchars ($pic);
//-------------------------------------------------------↓
print <<< END_OF_HTML
<tr>
    <th class="detail_tbl_t">担当者 <div class="infotip"><abbr title="担当スタッフの名前などを入力します。退職などで同名の別人の場合もあるので、名前と一緒に時期がわかるようにすると便利です。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t"><input type="text" name="pic" value="$pic" maxlength="40" class="form-control" style="ime-mode: active;" $disabled></td>
  </tr>
END_OF_HTML;
}
if ($addr_logs <> ""){ //住所記録オンオフ
print <<< END_OF_HTML
<tr>
    <th class="detail_tbl_t">DM可否 <div class="infotip"><abbr title="DMを送ってはいけない場合にチェックしておくと、データを利用してDMをおくる際などに便利です。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t">\n
END_OF_HTML;
//-------------------------------------------------------↑
if ($no_dm){
	echo "	<label><input type='checkbox' name='no_dm' value='true' checked $disabled>"."\n";
}else{
	echo "	<label><input type='checkbox' name='no_dm' value='true' $disabled>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
      チェックでDM禁止</label></td>
  <tr>\n
END_OF_HTML;
}
//-------------------------------------------------------↑
if($firsttime <> ""){
	echo "<th class='detail_tbl_t'>初回登録日時 <div class='infotip'><abbr title='初めて登録した時の日時です。' rel='tooltip'>?</abbr></div></th>"."\n";
	echo "<td class='detail_tbl_t'>$firsttime"."\n";
	echo "<input type='hidden' id='firsttime' name='firsttime' value='$firsttime' $disabled>"."\n";
}else{
	$now_date = date("Y/m/d H:i");
	echo "<th class='detail_tbl_t'>初回登録日時 <div class='infotip'><abbr title='初めて登録した時の日時としてこの時間が登録されます。' rel='tooltip'>?</abbr></div></th>"."\n";
	echo "<td class='detail_tbl_t'>$now_date"."\n";
	echo "<input type='hidden' id='firsttime' name='firsttime' value='$now_date' $disabled>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
      </td>
  </tr>
  <tr>
    <th class="detail_tbl_t">前回更新日時 <div class="infotip"><abbr title="最後に更新された時の日時です。" rel="tooltip">?</abbr></div>
</th>\n
END_OF_HTML;
//-------------------------------------------------------↑
	echo "<td class='detail_tbl_t'>$lasttime"."\n";
	$now_date2 = date("Y/m/d H:i");
	echo "<input type='hidden' id='lasttime' name='lasttime' value='$now_date2' $disabled>"."\n";
//-------------------------------------------------------↓
print <<< END_OF_HTML
      </td>
</tr>
</table>
	<div id="footer_submit_box">
		<div class="submit_box">
END_OF_HTML;
//-------------------------------------------------------↑
if (@$delete_sw){
	echo "	<input type='submit' class='button01' name='cancel' value=' キャンセル '>"."\n";
	echo "	<input type='submit' class='button02' name='delete' value='　削除する　'>"."\n";
	echo "	<input type='hidden' name='radio' value='".$radio."'>"."\n";
}else{
	if ($fix && $radio){
		echo "	<input type='submit' class='button01' name='cancel' value=' キャンセル '>"."\n";
		echo "	<input type='submit' class='button01' name='Submit' value='　編集登録　'>"."\n";
		echo "	<input type='hidden' name='fix' value='true'>"."\n";
		echo "	<input type='hidden' name='radio' value='".$radio."'>"."\n";
	}else{
		echo "	<input type='submit' class='button01' name='Submit' value='　新規登録　'>"."\n";
	}
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
</div>
</div>
</form>
END_OF_HTML;
//-------------------------------------------------------↑
//ページリンク
html_pagelink($dspcount);

//フッター
html_footer_exj();
//-------------------------------------------------------↓
print <<< END_OF_HTML
<script src="$url/bottle_assets/js/jquery.autoKana.js"></script>
<script src="$url/bottle_assets/js/pickdate/picker.js"></script>
<script src="$url/bottle_assets/js/pickdate/picker.date.js"></script>
<script src="$url/bottle_assets/js/pickdate/legacy.js"></script>
<script src="$url/bottle_assets/js/pickdate/translations/ja_JP.js"></script>
<script src="$url/bottle_assets/js/jquery.add-input-area.min.js"></script>
<script type="text/javascript">
$('.datepicker').pickadate({
  max: true,
  format: 'yyyy/m/d'/*,
  selectYears: true,
  selectMonths: true*/
})
</script>
<script type="text/javascript">
$('#bottles').addInputArea({
  maximum : 4
});
$('#friends').addInputArea({
  maximum : 6
});
</script>

</body>
</html>
END_OF_HTML;
//-------------------------------------------------------↑

}
}
?>