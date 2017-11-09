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

$html_title = "お客様詳細丨顧客・ボトル管理システム";  //ページタイトル
//--------------------------------------------------------------------------------------初期設定
//$this_file = $_SERVER['SCRIPT_NAME'];
$this_file = "details.php";
define ("file_sdata","data/data.dat");
define ("file_count","data/count.dat");

$file_category = "config/category.dat";
$file_pref = "data/pref.dat";
$file_birthyear = "data/birthyear.dat";
$file_birthmonth = "data/birthmonth.dat";
$file_birthday = "data/birthday.dat";
//ＧＥＴ受信
$cnt = @$_GET["cnt"];
$category   = urldecode(@$_GET["category"]);
$searchword = urldecode(@$_GET["searchword"]);
//ＰＯＳＴ受信
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
//--------------------------------------------------------------------------------------メイン処理
//編集キャンセル
if ($cancel){echo "<meta http-equiv='refresh' content='0;url=./index.php'>"."\n";exit();}
//編集モード
if ($cnt && !$submit){
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//不正確認
	if ($sdata_line){
		foreach ($sdata_line as $key => $value){
			//文字列分割
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
			if ($count == $cnt){
				$check = "true";
				break;
			}
		}
	}
	if (!@$check){echo "<meta http-equiv='refresh' content='0; url=./index.php'>"."\n";exit();}
}
//登録
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

	//ファイル書き込み
		if ($cnt){
			fix_data();
		}else{
			write_data();
		}

			echo "<meta http-equiv='refresh' content='0; url=./details.php?cnt=$cnt'>"."\n";

//			echo "<meta http-equiv='refresh' content='0; url=./index.php'>"."\n";

		exit();
}
//--------------------------------------------------------------------------------------関数定義

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
// ファイル書き込み              //
///////////////////////////////////
function write_data(){
	global $customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9;
	//カウンタ読み込み
	$counter = file(file_count);
	$count = trim($counter[0]);
	$count++;
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//一行化
	$new_line = $count."&&".$customer."&&".$kana."&&".$company."&&".$section."&&".$section2."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$memo."&&".$birthyear."&&".$birthmonth."&&".$birthday."&&".$firsttime."&&".$lasttime."&&".$pic."&&".$no_dm."&&".$lastvisit."&&".$fname."&&".$bottle_0."&&".$bottle_num_0."&&".$bottle_quant_0."&&".$bottle_1."&&".$bottle_num_1."&&".$bottle_quant_1."&&".$bottle_2."&&".$bottle_num_2."&&".$bottle_quant_2."&&".$bottle_3."&&".$bottle_num_3."&&".$bottle_quant_3."&&".$bottle_4."&&".$bottle_num_4."&&".$bottle_quant_4."&&".$otherbottle."&&".$otherbottle_num."&&".$otherbottle_quant."&&".$friends_0."&&".$friends_1."&&".$friends_2."&&".$friends_3."&&".$friends_4."&&".$friends_5."&&".$friends_6."&&".$friends_7."&&".$friends_8."&&".$friends_9."\n";
	if ($sdata_line){
		foreach($sdata_line as $value){
			$new_line .= $value."\n";
		}
	}
	$file = fopen(file_sdata,"w") or die(file_sdata." is not found");
	flock($file,LOCK_EX);
	fputs($file,$new_line);
	flock($file,LOCK_UN);
	fclose($file);
	//空DATファイル作成
	$new_file = "regist_dat/".$count.".dat";
	touch($new_file);
	//カウンター更新
	$file = fopen(file_count,"w") or die(file_count." is not found");
	flock($file,LOCK_EX);
	fputs($file,$count."\n");
	flock($file,LOCK_UN);
	fclose($file);
}
///////////////////////////////////
// ファイル編集書き込み          //
///////////////////////////////////
function fix_data(){
	global $cnt;
	global $customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9;
	//カウンタ読み込み
	$counter = file(file_count);
	$count = trim($counter[0]);
	$count++;

	//登録ファイルの読み込み
	$sdata_line = read_data();
	//一行化
	$new_line = "";
	foreach ($sdata_line as $value){
		//文字列分割
		list($count,) = explode("&&",$value);
		if ($cnt == $count){
			$new_line .= $count."&&".$customer."&&".$kana."&&".$company."&&".$section."&&".$section2."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$memo."&&".$birthyear."&&".$birthmonth."&&".$birthday."&&".$firsttime."&&".$lasttime."&&".$pic."&&".$no_dm."&&".$lastvisit."&&".$fname."&&".$bottle_0."&&".$bottle_num_0."&&".$bottle_quant_0."&&".$bottle_1."&&".$bottle_num_1."&&".$bottle_quant_1."&&".$bottle_2."&&".$bottle_num_2."&&".$bottle_quant_2."&&".$bottle_3."&&".$bottle_num_3."&&".$bottle_quant_3."&&".$bottle_4."&&".$bottle_num_4."&&".$bottle_quant_4."&&".$otherbottle."&&".$otherbottle_num."&&".$otherbottle_quant."&&".$friends_0."&&".$friends_1."&&".$friends_2."&&".$friends_3."&&".$friends_4."&&".$friends_5."&&".$friends_6."&&".$friends_7."&&".$friends_8."&&".$friends_9."\n";
		}else{
			$new_line .= $value."\n";
		}
	}
	$file = fopen(file_sdata,"w") or die(file_sdata." is not found");
	flock($file,LOCK_EX);
	fputs($file,$new_line);
	flock($file,LOCK_UN);
	fclose($file);
}
///////////////////////////////////
// 登録ファイルの読み込み        //
///////////////////////////////////
function read_data(){
	$line = "";
	$temp_line = file(file_sdata);
	foreach ($temp_line as $value){
		if ($value){
			$value = trim($value);
			$line[] = $value;
		}
	}
	return $line;
}

///////////////////////////////////
// テキストチェック              //
///////////////////////////////////
function check_text($str){
	$str = trim($str);
	if(get_magic_quotes_gpc()){$str = stripslashes($str);}
	$str = preg_replace("/&&/","＆＆",$str);
	$str = preg_replace("/\n/","<br>",$str);
	return $str;
}


//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////
html_header1();
//-------------------------------------------------------↓
print <<< END_OF_HTML

END_OF_HTML;
//-------------------------------------------------------↑

html_header2();

if ($cnt){
print <<< END_OF_HTML

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>
			<li class="active">$customer 様の詳細情報</li>
		</ul>
	</div>\n

END_OF_HTML;
//-------------------------------------------------------↑
	echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$this_file."?cnt=".$cnt."'>"."\n";
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="hidden" name="customer" value="$customer">
<input type="hidden" name="kana" value="$kana">
<input type="hidden" name="bottle_0" value="$bottle_0">
<input type="hidden" name="bottle_num_0" value="$bottle_num_0">
<input type="hidden" name="bottle_quant_0" value="$bottle_quant_0">
<input type="hidden" name="bottle_1" value="$bottle_1">
<input type="hidden" name="bottle_num_1" value="$bottle_num_1">
<input type="hidden" name="bottle_quant_1" value="$bottle_quant_1">
<input type="hidden" name="bottle_2" value="$bottle_2">
<input type="hidden" name="bottle_num_2" value="$bottle_num_2">
<input type="hidden" name="bottle_quant_2" value="$bottle_quant_2">
<input type="hidden" name="bottle_3" value="$bottle_3">
<input type="hidden" name="bottle_num_3" value="$bottle_num_3">
<input type="hidden" name="bottle_quant_3" value="$bottle_quant_3">
<input type="hidden" name="otherbottle" value="$otherbottle">
<input type="hidden" name="otherbottle_num" value="$otherbottle_num">
<input type="hidden" name="otherbottle_quant" value="$otherbottle_quant">
<input type="hidden" name="company" value="$company">
<input type="hidden" name="section" value="$section">
<input type="hidden" name="section2" value="$section2">
<input type="hidden" name="degree" value="$degree">
<input type="hidden" name="zip" value="$zip">
<input type="hidden" name="addr" value="$addr">
<input type="hidden" name="addr2" value="$addr2">
<input type="hidden" name="phone" value="$phone">
<input type="hidden" name="mobile" value="$mobile">
<input type="hidden" name="friends_0" value="$friends_0">
<input type="hidden" name="friends_1" value="$friends_1">
<input type="hidden" name="friends_2" value="$friends_2">
<input type="hidden" name="friends_3" value="$friends_3">
<input type="hidden" name="friends_4" value="$friends_4">
<input type="hidden" name="memo" value="$memo">
<input type="hidden" name="birthyear" value="$birthyear">
<input type="hidden" name="birthmonth" value="$birthmonth">
<input type="hidden" name="birthday" value="$birthday">
<input type="hidden" name="pic" value="$pic">
<input type="hidden" name="no_dm" value="$no_dm">
<input type="hidden" name="firsttime" value="$firsttime">
<input type="hidden" name="lasttime" value="$lasttime">
<input type="hidden" name="fname" value="$fname">

END_OF_HTML;

		echo "<div class='detail_tbl'>"."\n";

		echo "<table>"."\n";
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>お客様名</th>"."\n";
		echo "<td class='detail_tbl_t'>"."\n";
if ($dspimg_logs <> ""){ //画像登録オンオフ
		if ($fname){
			echo "<div class='up_img'><a href='".$fname."' class='fancybox'><img src='".$fname."'></a></div>"."\n";
		}
		echo "<span class='name_box'>$customer 様</span>"."\n";
		if($kana <> '') {
			echo "<br /><span class='name_kana'>".$kana." 様</span>"."\n";
		}
		echo "</span>"."\n";
		echo "<div class='clear'></div>"."\n";
	} else {
		echo "<span class='name_box'>$customer 様</span>"."\n";
	if($kana <> '') {
// 		$kana = fix_text($kana);
		echo " <span class='name_kana'>".$kana." 様</span>"."\n";
	}
		echo "</span>"."\n";
}
		echo "</td>"."\n";
		echo "</tr>"."\n";


if ($lastvisit_logs <> ""){ //最終来店日記録オンオフ
//-------------------------------------------------------↑
$lastvisit =  preg_replace("/<br>/","\n",$lastvisit);
$lastvisit = htmlspecialchars ($lastvisit);
//-------------------------------------------------------↓
//	echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$this_file."?cnt=".$cnt."'>"."\n";
print <<< END_OF_HTML
<tr>
    <th class="detail_tbl_t">最終来店日 <div class="infotip"><abbr title="最後にご来店になった日を更新登録します。" rel="tooltip">?</abbr></div></th>
    <td class="detail_tbl_t">
<input type="text" name="lastvisit" value="$lastvisit" class="form-control datepicker update_form_lastvisit" style="ime-mode: active;" placeholder="タップして入力後【更新】">
<input type="submit" name="Submit" value="更新" class="btn btn-primary update_submit_lastvisit">
</td>
  </tr>
END_OF_HTML;
}

		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>ボトル</th>"."\n";
		//ボトル
		echo "<td class='detail_tbl_t'>"."\n";
	if($bottle_0 <> '') {
		echo "".$bottle_0." "."\n";
		if($bottle_num_0 <> '') {
			echo " [".$bottle_num_0."] "."\n";
		}
		if($bottle_quant_0 <> '') {
			echo " (".$bottle_quant_0.") "."\n";
		}
	}

	if($bottle_1 <> '') {
		if ($bottle_0) { echo "<span class='sepa'> | </span>"."\n"; }
		echo "".$bottle_1." "."\n";
		if($bottle_num_1 <> '') {
			echo " [".$bottle_num_1."] "."\n";
		}
		if($bottle_quant_1 <> '') {
			echo " (".$bottle_quant_1.") "."\n";
		}
	}

	if($bottle_2 <> '') {
		if ($bottle_0 || $bottle_1 ) { echo "<span class='sepa'> | </span>"."\n"; }
		echo "".$bottle_2." "."\n";
		if($bottle_num_2 <> '') {
			echo " [".$bottle_num_2."] "."\n";
		}
		if($bottle_quant_2 <> '') {
			echo " (".$bottle_quant_2.") "."\n";
		}
	}

	if($bottle_3 <> '') {
		if ($bottle_0 || $bottle_1 || $bottle_2 ) { echo "<span class='sepa'> | </span>"."\n"; }
		echo "".$bottle_3." "."\n";
		if($bottle_num_3 <> '') {
			echo " [".$bottle_num_3."] "."\n";
		}
		if($bottle_quant_3 <> '') {
			echo " (".$bottle_quant_3.") "."\n";
		}
	}

	if($bottle_4 <> '') {
		if ($bottle_0 || $bottle_1 || $bottle_2 || $bottle_3 ) { echo "<span class='sepa'> | </span>"."\n"; }
		echo "".$bottle_4." "."\n";
		if($bottle_num_4 <> '') {
			echo " [".$bottle_num_4."] "."\n";
		}
		if($bottle_quant_4 <> '') {
			echo " (".$bottle_quant_4.") "."\n";
		}
	}

	if($otherbottle <> '') {
		if ($bottle_0 || $bottle_1 || $bottle_2 || $bottle_3 || $bottle_4 ) { echo "<span class='sepa'> | </span>"."\n"; }
		echo " ".$otherbottle." "."\n";
		if($otherbottle_num <> '') {
			echo " [".$otherbottle_num."] "."\n";
		}
		if($otherbottle_quant <> '') {
			echo " (".$otherbottle_quant.") "."\n";
		}
	}

		echo "</td>"."\n";
		echo "</tr>"."\n";
	if($company <> '') {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>会社名</th>"."\n";
		//会社名
		echo "<td class='detail_tbl_t'>"."\n";

// 		$company = fix_text($company);
		echo " ".$company." "."\n";

		if($section <> '') {
// 			$section = fix_text($section);
			echo "：".$section." "."\n";
		}
		if($section2 <> '') {
// 			$section2 = fix_text($section2);
			echo " ".$section2." "."\n";
		}

		//役職
// 			$degree = fix_text($degree);
			echo "<br /><span class='small'>".$degree."</span>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($addr <> '') {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>ご住所</th>"."\n";

		//住所
		echo "<td class='detail_tbl_t'>"."\n";
		if($zip <> '') {
// 			$zip = fix_text($zip);
			echo "〒".$zip."<br />"."\n";
		}
		if($pref <> '') {
// 			$pref = fix_text($pref);
			echo " ".$pref." "."\n";
		}

// 			$addr = fix_text($addr);
			echo " ".$addr." "."\n";

		if($addr2 <> '') {
// 			$addr2 = fix_text($addr2);
			echo " ".$addr2." "."\n";
		}
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($phone <> '') {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>電話番号</th>"."\n";

		//電話番号
		echo "<td class='detail_tbl_t'>"."\n";
// 		$phone = fix_text($phone);
		echo " ".$phone." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($mobile <> '') {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>携帯電話</th>"."\n";

		//携帯電話
		echo "<td class='detail_tbl_t'>"."\n";
// 		$mobile = fix_text($mobile);
		echo " ".$mobile." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}

	if($friends_0 || $friends_1 || $friends_2 || $friends_3 || $friends_4 || $friends_5 <> '') {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>友人知人情報 <div class='infotip'><abbr title='友人知人のお名前をタッチすると、そのお名前で検索します。' rel='tooltip'>?</abbr></div></th>"."\n";

		//友人知人情報
		echo "<td class='detail_tbl_t'>"."\n";
// 		$friends_0 = fix_text($friends_0);
// 		$friends_1 = fix_text($friends_1);
// 		$friends_2 = fix_text($friends_2);
// 		$friends_3 = fix_text($friends_3);
// 		$friends_4 = fix_text($friends_4);
// 		$friends_5 = fix_text($friends_5);
		echo " <a href='./?searchname=$friends_0'>".$friends_0."</a> "."\n";

if ($friends_1) {
	if ($friends_0) { echo "<span class='sepa'> | </span>"."\n"; }
		echo " <a href='./?searchname=$friends_1'>".$friends_1."</a> "."\n";
}
if ($friends_2) {
	if ($friends_0 || $friends_1 ) { echo "<span class='sepa'> | </span>"."\n"; }
		echo " <a href='./?searchname=$friends_2'>".$friends_2."</a> "."\n";
}
if ($friends_3) {
	if ($friends_0 || $friends_1 || $friends_2 ) { echo "<span class='sepa'> | </span>"."\n"; }
		echo " <a href='./?searchname=$friends_3'>".$friends_3."</a> "."\n";
}
if ($friends_4) {
	if ($friends_0 || $friends_1 || $friends_2 || $friends_3 ) { echo "<span class='sepa'> | </span>"."\n"; }
		echo " <a href='./?searchname=$friends_4'>".$friends_4."</a> "."\n";
}
if ($friends_5) {
	if ($friends_0 || $friends_1 || $friends_2 || $friends_3 || $friends_4 ) { echo "<span class='sepa'> | </span>"."\n"; }
		echo " <a href='./?searchname=$friends_5'>".$friends_5."</a> "."\n";
}
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}

	if($memo <> '') {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>メモ</th>"."\n";

		//メモ
		echo "<td class='detail_tbl_t'>"."\n";
		$memo = strip_tags($memo,allow_tag);
		$memo = preg_replace("/\r|\n|\r\n/","<br>",$memo);
		echo " ".$memo." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($birthmonth <> "") {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>誕生日</th>"."\n";

		//誕生日
		echo "<td class='detail_tbl_t'>";

		if($birthyear <> "") {
		echo " ".$birthyear." 年 ";
		}
		if($birthmonth <> "") {
		echo "$birthmonth 月";
		}else{
			echo "&nbsp;";
		}
		if($birthday <> "") {
		echo " ".$birthday." 日"."\n";
		}else{
			echo "&nbsp;";
		}

	//年齢計算
	if($birthyear <> "") {
		$birthmonth2 = sprintf("%02d",$birthmonth);
		$birthday2 = sprintf("%02d",$birthday);
		$howold = $birthyear.$birthmonth2.$birthday2;
		$now = date("Ymd"); 
		$birth = "$howold"; 
		echo " (現 ";
		echo floor(($now-$birth)/10000);
		echo " 歳)";
		}
	//今月の誕生日にマーク
		$nowm = date("m");
		if($nowm == $birthmonth) {
			echo " <span class='birthday' title='誕生月'>&nbsp;</span>";
		}

		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($pic <> '') {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>担当者</th>"."\n";

		//担当者
		echo "<td class='detail_tbl_t'>"."\n";
// 		$pic = fix_text($pic);
		echo " ".$pic." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($no_dm <> '') {
		echo "<tr>"."\n";
		echo "<th class='detail_tbl_t'>DM可否</th>"."\n";

		//DM可否
		echo "<td class='detail_tbl_t'>"."\n";
// 		$no_dm = fix_text($no_dm);
		echo "<span class='hissu'>DM禁止</span>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
//-------------------------------------------------------↓
print <<< END_OF_HTML
			<tr>
				<th class="detail_tbl_t">初回登録日時</th>
				<td class="detail_tbl_t">$firsttime</td>
			</tr><tr>
				<th class="detail_tbl_t">前回更新日時</th>
				<td class="detail_tbl_t">$lasttime</td>
			</tr>
	</table>
</form>

END_OF_HTML;
// 		echo "</div>"."\n";

}else{//該当データなし	
//-------------------------------------------------------↓
print <<< END_OF_HTML
		<ul class="breadcrumb">
			<li><a href="./index.php">HOME</a></li>
			<li class="active">該当データはありません</li>
		</ul>
        <div class="bs-component">
          <div class="jumbotron">
            <h2 class="alart_message">該当データはありません</h2>
          </div>
        </div>

END_OF_HTML;
//-------------------------------------------------------↑
}

echo "<div class='footer_navi'>"."\n";
echo "	<a href='./' class='btn btn-primary historyback'>&lt; 一覧に戻る</a>"."\n";

echo" <div class='edit_btn'><form name='form2' method='post' action='sequence.php'><input type='hidden' name='radio' value='$count'><input type='submit' name='delete_conf' value='この情報を削除' class='btn btn-danger'><a href='regist.php?cnt=$count' class='btn btn-warning'>この情報を編集</a></div></form>"."\n";
echo "<div class='clear'></div></div>"."\n";

//-------------------------------------------------------↓
print <<< END_OF_HTML
<script>
JQuery(document).ready(function(){
	(function(){
	    var ans; //1つ前のページが同一ドメインかどうか
	    var bs  = false; //unloadイベントが発生したかどうか
	    var ref = document.referrer;
	    JQuery(window).bind("unload beforeunload",function(){
	        bs = true;
	    });
	    re = new RegExp(location.hostname,"i");
	    if(ref.match(re)){
	        ans = true;
	    }else{
	        ans = false;
	    }
	    JQuery('.historyback').bind("click",function(){
                var that = this;
	        if(ans){
	            history.back();
	            setTimeout(function(){
	                if(!bs){
	                    location.href = $(that).attr("href");
	                }
	            },100);
	        }else{
                    location.href = $(this).attr("href");
                }
	        return false;
	    });
	})();
});
</script>
END_OF_HTML;
//-------------------------------------------------------↑

//フッター
html_footer_exj();

//-------------------------------------------------------↓
print <<< END_OF_HTML
<script src="$url/bottle_assets/js/pickdate/picker.js"></script>
<script src="$url/bottle_assets/js/pickdate/picker.date.js"></script>
<script src="$url/bottle_assets/js/pickdate/legacy.js"></script>
<script src="$url/bottle_assets/js/pickdate/translations/ja_JP.js"></script>
<script type="text/javascript">
$('.datepicker').pickadate({
  max: true,
  format: 'yyyy/m/d'/*,
  selectYears: true,
  selectMonths: true*/
})
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox( {
//     	openEffect	: 'elastic',
//     	closeEffect	: 'elastic'
		});
	});
</script>
</body>
</html>
END_OF_HTML;
//-------------------------------------------------------↑
?>
