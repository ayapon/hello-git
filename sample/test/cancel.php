<?php
///////////////////////////////////
// データ登録                    //
///////////////////////////////////
//セッション開始
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

include_once("attestation.php"); 
require("common.php");
require("../bottle_assets/pageparts.php");
$html_title = "解約申請丨顧客・ボトル管理システム";  //ページタイトル
//--------------------------------------------------------------------------------------初期設定
//$url = $_SERVER['SCRIPT_NAME'];
$this_file = "cancel.php";
define ("file_sdata","../manager/data/account.dat");
define ("file_count","../manager/data/account_count.dat");

//登録上限数
$logmax = "1000";

//メール送信する
$mailsend = "1";
$admin_mail = "admin@dewey.co.jp";

$file_pref = "../manager/data/pref.dat";
//ＧＥＴ受信
$cnt = @$_GET["cnt"];
$searchword = urldecode(@$_GET["searchword"]);
//ＰＯＳＴ受信
$submit         = @$_POST["Submit"];
$cancel         = @$_POST["cancel"];
$plan       = @$_POST["plan"];
$account       = @$_POST["account"];
$store       = @$_POST["store"];
$customer       = @$_POST["customer"];
$degree       = @$_POST["degree"];
$zip    = @$_POST["zip"];
$pref = @$_POST["pref"];
$addr = @$_POST["addr"];
$addr2    = @$_POST["addr2"];
$phone    = @$_POST["phone"];
$mobile    = @$_POST["mobile"];
$email    = @$_POST["email"];
$memo = @$_POST["memo"];
$firsttime = @$_POST["firsttime"];
$lasttime = @$_POST["lasttime"];
$dsp            = @$_POST["dsp"];
$reset       = @$_POST["reset"];
$cancel_flag       = @$_POST["cancel_flag"];
$c_date       = @$_POST["c_date"];
$c_reason1       = @$_POST["c_reason1"];
$c_reason2       = @$_POST["c_reason2"];
$c_reason3       = @$_POST["c_reason3"];
$c_reason4       = @$_POST["c_reason4"];
$c_reason5       = @$_POST["c_reason5"];
$c_reasonO       = @$_POST["c_reasonO"];
$c_reasonOf       = @$_POST["c_reasonOf"];
//
if (!$searchword){$searchword = @$_POST["searchword"];}
//ダイレクトリクエスト
if (file_exists($file_link)){$dir = "";$direct = 1;}


$word_list4 = read_file($dir.$file_pref);
if ($pref_dat== $word_list4[0]){$pref_dat = "";}


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
			list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor) = explode("&&",$value);
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
	$plan = check_text($plan);
	$account = check_text($account);
	$store = check_text($store);
	$customer = check_text($customer);
	$degree = check_text($degree);
	$zip = check_text($zip);
	$pref = check_text($pref);
	$addr = check_text($addr);
	$addr2 = check_text($addr2);
	$phone = check_text($phone);
	$mobile = check_text($mobile);
	$email = check_text($email);
	$memo = check_text($memo);
	$firsttime = check_text($firsttime);
	$lasttime = check_text($lasttime);
	$cancel_flag = check_text($cancel_flag);
	$c_date = check_text($c_date);
	$c_reason1 = check_text($c_reason1);
	$c_reason2 = check_text($c_reason2);
	$c_reason3 = check_text($c_reason3);
	$c_reason4 = check_text($c_reason4);
	$c_reason5 = check_text($c_reason5);
	$c_reasonO = check_text($c_reasonO);
	$c_reasonOf = check_text($c_reasonOf);

	if (!$email){$message_error = "【メールアドレス】は必須項目です";}
	if (!$c_date){$message_error = "【解約希望日】は必須項目です";}
//	if (!$c_reason){$message_error = "【解約理由】は必須項目です";}

// 	if(isset($_REQUEST["Submit"]) and !isset($_REQUEST["c_reason"])){
// 		$message_error="【解約理由】は必須項目です";
// 	}
// 	if(isset($_REQUEST["c_reason"]) and is_array($_REQUEST["c_reason"])){
// 		foreach($_REQUEST["c_reason"] as $val){
// 		$checked["c_reason"][$val]=" checked";
// 		}
// 	}



	//エラーがなければファイル書き込み
	if (!$message_error){
		if ($cnt){
			fix_data();
		}else{
			write_data();
		}

if ($mailsend == 1) {
//メール送信2016/03/26
mb_language("Ja");
mb_internal_encoding("UTF-8");
//宛先
$mailto = "$email";
//投稿者あてCC
$mailbcc = "$admin_mail";
//差出人
$mailfrom = mb_encode_mimeheader("株式会社デューイ【ボトル・顧客管理】")."<info@dewey.co.jp>";
//件名
$subject = "ナイトワークス解約申請";
//本文
$body .= "$customer 様（ 店舗名：$store ）\n";
$body .= "\n";
$body .= "ナイトワークス「ボトル・顧客管理」の解約申請を承りました。\n";
$body .= "これまでのご利用、誠にありがとうございました。\n";
$body .= "下記解約予定日を以って、正式なご解約となります。\n";
$body .= "解約予定日：【 $c_date 】\n";
$body .= "\n";
$body .= "万一、ご継続をご希望になる場合は下記リンクよりお問い合わせください。";
$body .= "また、解約後のデータは消去されるため、ご解約完了後に取り出すことはできません。\n";
$body .= "必ずお客様ご自身でデータをCSVエクスポートなどで保存するようにしてください。\n";
$body .= "\n";
$body .= "また、機会がございましたらぜひナイトワークスをよろしくお願いいたします。\n";
$body .= "\n";
$body .= "──────────────────────\n";
$body .= "株式会社 デューイ　ナイトワーク事業部\n";
$body .= "〒651-0082\n";
$body .= "兵庫県神戸市中央区小野浜町5-48\n";
$body .= "Tel：078-335-5700（代表）\n";
$body .= "Fax：078-330-0044\n";
$body .= "URL: http://www.dewey.co.jp\n";
$body .= "──────────────────────\n";

$header = "From:" .$mailfrom;
$header .= "\n";
$header .= "Bcc:" . $mailbcc;

//メール送信
$result = mb_send_mail($mailto,$subject,$body,$header);
// 送信結果チェック
	if (!$result) die('送信に失敗しました');
}

		if ($cnt){
			//書き込み後は一覧に戻る
			echo "<meta http-equiv='refresh' content='0; url=../index.php?cnt=$cnt'>"."\n";
		}else{
			echo "<meta http-equiv='refresh' content='0; url=../index.php'>"."\n";
		}

		exit();
	}
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
	global $plan,$account,$store,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf;
	//カウンタ読み込み
	$counter = file(file_count);
	$count = trim($counter[0]);
	$count++;

	//登録ファイルの読み込み
	$sdata_line = read_data();
	//一行化
	$new_line = $count."&&".$plan."&&".$account."&&".$store."&&".$customer."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$email."&&".$memo."&&".$firsttime."&&".$lasttime."&&".$flag."&&".$dsp."&&".$cancel_flag."&&".$c_date."&&".$c_reason1."&&".$c_reason2."&&".$c_reason3."&&".$c_reason4."&&".$c_reason5."&&".$c_reasonO."&&".$c_reasonOf."\n";
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
	global $plan,$account,$store,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf;
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
			$new_line .= $count."&&".$plan."&&".$account."&&".$store."&&".$customer."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$email."&&".$memo."&&".$firsttime."&&".$lasttime."&&".$flag."&&".$dsp."&&".$cancel_flag."&&".$c_date."&&".$c_reason1."&&".$c_reason2."&&".$c_reason3."&&".$c_reason4."&&".$c_reason5."&&".$c_reasonO."&&".$c_reasonOf."\n";
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
if ($cnt){$buttom_name = "解約申請";}else{$buttom_name = "ユーザー登録";}

//-------------------------------------------------------↓
html_header1();
print <<< END_OF_HTML

<link href="../bottle_assets/js/pickdate/themes/default.css" rel="stylesheet" media="screen">
<link href="../bottle_assets/js/pickdate/themes/default.date.css" rel="stylesheet" media="screen">

END_OF_HTML;

html_header2();
print <<< END_OF_HTML

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="index.php">HOME</a></li>
            <li><a href="admin.php">管理メニュー</a></li>

END_OF_HTML;
//-------------------------------------------------------↑
if ($cnt){

	if(isset($_SESSION{'flg'}) && $_SESSION{'flg'} == "udataok"){

	//文字列内のタグを取り除く
	$temp_title = strip_tags($store);
	$temp_title2 = strip_tags($customer);
	//長い文字列は40バイト以降を消去し"..."を付加する
	if(strlen($temp_title) > 40){ $temp_title = substr($temp_title,0,60)."...";}
	echo "	<li class='active'>".$temp_title." ".$temp_title2." 様の解約申請</li></ul></div>"."\n";
}
}else{	
	echo "	<li class='active'>新規ユーザー登録</li></ul></div>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML

<h3 class="regist_title">$buttom_name</h3>

END_OF_HTML;
//-------------------------------------------------------↑
//エラーメッセージ
if ($message_error){
	echo "<h3 class='error_msg'>".$message_error."</h3>"."\n";
}else{
	echo "<h3 class='error_msg'>".$message."</h3>"."\n";
}

if ($cnt){
	echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$this_file."?cnt=".$cnt."' onSubmit='return check()' class='form'>"."\n";
}else{
	if (!$form_none){
		echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$this_file."' onSubmit='return check()' class='form'>"."\n";
	}
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<div class="detail_tbl">
<table>
<tr>
    <th>ご利用中のプラン</th>
END_OF_HTML;
//-------------------------------------------------------↑
$plan =  preg_replace("/<br>/","\n",$plan);
$plan = htmlspecialchars ($plan);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>$plan
<input type="hidden" name="plan" value="$plan">
<input type="hidden" name="account" value="$account">
<input type="hidden" name="store" value="$store">
<input type="hidden" name="customer" value="$customer">
<input type="hidden" name="degree" value="$degree">
<input type="hidden" id="zip" name="zip" value="$zip">
<input type="hidden" name="addr" value="$addr">
<input type="hidden" name="addr2" value="$addr2">
<input type="hidden" name="phone" value="$phone">
<input type="hidden" name="mobile" value="$mobile">
<input type="hidden" name="cancel_flag" value="true">
	</td>
  </tr>
<tr>
    <th>アカウントID</th>
END_OF_HTML;
//-------------------------------------------------------↑
$account =  preg_replace("/<br>/","\n",$account);
$account = htmlspecialchars ($account);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>$account
	</td>
  </tr>
<tr>
    <th>店舗名</th>
END_OF_HTML;
//-------------------------------------------------------↑
$store =  preg_replace("/<br>/","\n",$store);
$store = htmlspecialchars ($store);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>$store
	</td>
  </tr>
<tr>
    <th>お名前</th>
END_OF_HTML;
//-------------------------------------------------------↑
$customer =  preg_replace("/<br>/","\n",$customer);
$customer = htmlspecialchars ($customer);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>$customer 様
	</td>
  </tr>
  <tr>
    <th>メールアドレス <span class="hissu">※必須</span>  <div class="infotip"><abbr title="解約通知をお送りいたしますので、メールがお間違いないかご確認ください。なお、必ずパソコンメールが受信できるメールアドレスをご利用ください。" rel="tooltip">?</abbr></div></th>
END_OF_HTML;
//-------------------------------------------------------↑
$email =  preg_replace("/<br>/","\n",$email);
$email = htmlspecialchars ($email);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td><input type="email" name="email" value="$email" class="form-tel form-control" placeholder="メールアドレス" id="EMAIL">
	※解約通知をお送りいたしますので、メールがお間違いないかご確認ください。</td>
	</tr><tr>
    <th>ご利用開始日</th>
	<td>$firsttime
END_OF_HTML;
//-------------------------------------------------------↑
if($firsttime <> ""){
	echo "<input type='hidden' id='firsttime' name='firsttime' value='$firsttime'>"."\n";
}else{
	$now_date = date("Y/m/d H:i");
	echo "<input type='hidden' id='firsttime' name='firsttime' value='$now_date'>"."\n";
}
	$now_date2 = date("Y/m/d H:i");
	echo "<input type='hidden' id='lasttime' name='lasttime' value='$now_date2'>"."\n";
//-------------------------------------------------------↓
print <<< END_OF_HTML
</td>
	</tr>
<tr>
    <th>解約希望日 <span class="hissu">※必須</span> <div class="infotip"><abbr title="手続きの関係上、ご解約は30日前までにお申し出ください。30日前を過ぎますと、さらに1ヶ月延長されることになります。" rel="tooltip">?</abbr></div></th>
	<td>
                <input type="text" name="c_date" value="$c_date" id="c_date" class="datepicker form-control form-tel" placeholder="30日以降で解約日を指定">※ご利用料金は月単位で、日割り返還は行なっておりません。
	</td>
	</tr>
  <tr>
    <th>解約理由 <span class="hissu">※必須(複数回答可)</span> <div class="infotip"><abbr title="今後の参考にさせていただきますので、解約に至った理由について教えてください。" rel="tooltip">?</abbr></div></th>
		<td>

				<label><input type="checkbox" name="c_reason1" value="使わなくなった"> 使わなくなった</label>　
				<label><input type="checkbox" name="c_reason2" value="他のツール等に変えた"> 他のツール等に変えた</label>　
				<label><input type="checkbox" name="c_reason3" value="使いにくい"> 使いにくい</label>　
				<label><input type="checkbox" name="c_reason4" value="費用が高い"> 費用が高い</label>　
				<label><input type="checkbox" name="c_reason5" value="閉店した"> 閉店した</label>
<br />
				<label><input type="checkbox" name="c_reasonO" value="その他" id="check_c_reasonO"> その他</label>
				<div id="box_c_reason"><textarea name="c_reasonOf" class="form-control" rows="4" style="ime-mode: active;" placeholder="その他、解約に至った理由をお願いします">$c_reasonOf</textarea></div>
	</td>
	</tr>
</table>
<div class="footer_navi">
<label><input type="checkbox" id="check" /> 規約に基づく保存データの破棄と契約の解除に同意する</label>（チェックしないと解約申請はできません）<br />
<br />
END_OF_HTML;
//-------------------------------------------------------↑
if ($cnt == ""){
	echo "	<a href='./index.php' class='historyback btn btn-default'>前の画面に戻る</a>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
	<input type="submit" name="Submit" value="$buttom_name" class="btn btn-warning btn_left btn_50" id="submit">
END_OF_HTML;
//-------------------------------------------------------↑
if ($cnt){
//	echo "	<input type='submit' name='cancel' value='キャンセル' class='btn btn-default'>"."\n";
	echo "	<a href='./index.php' class='historyback btn btn-default btn_50'>解約をやめる</a>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML

<div class="clear"></div>
</div>
</form>

END_OF_HTML;

//フッター
html_footer_exj();
print <<< END_OF_HTML

<script src="https://nightworks.jp/bottle_assets/js/pickdate/picker.js"></script>
<script src="https://nightworks.jp/bottle_assets/js/pickdate/picker.date.js"></script>
<script src="https://nightworks.jp/bottle_assets/js/pickdate/legacy.js"></script>
<script src="https://nightworks.jp/bottle_assets/js/pickdate/translations/ja_JP.js"></script>
<script type="text/javascript">
$(function() {
	$('.datepicker').pickadate({
		min: 30
	});
});
</script>
<script type="text/javascript">
<!--
$(function(){
   $('a[href^=#]').click(function() {
      var speed = 400;
      var href= $(this).attr("href");
      var target = $(href == "#" || href == "" ? 'html' : href);
      var position = target.offset().top;
      $('body,html').animate({scrollTop:position}, speed, 'swing');
      return false;
   });
});
function check(){
	var flag = 0;

	// チェック項目
	if(!document.form_mail.EMAIL.value.match(/.+@.+\..+/)){
		flag = 1;
	}

	if(flag){
		window.alert('メールアドレスが正しくありません'); // メールアドレス以外の場合は警告
		return false; // 送信を中止
	}
	else{
		return true; // 送信を実行
	}
}
$(function() {
	$('#submit').attr('disabled', 'disabled');
	
	$('#check').click(function() {
		if ($(this).prop('checked') == false) {
			$('#submit').attr('disabled', 'disabled');
		} else {
			$('#submit').removeAttr('disabled');
		}
	});
});

$('#check_c_reasonO').click(function() {
    //クリックイベントで要素をトグルさせる
    $("#box_c_reason").slideToggle(this.checked);
});
//-->
</script>

</body>
</html>
END_OF_HTML;
//-------------------------------------------------------↑
?>