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
$html_title = "初期設定丨顧客・ボトル管理システム";  //ページタイトル
//--------------------------------------------------------------------------------------初期設定
//$this_file = $_SERVER['SCRIPT_NAME'];
$this_file = "setting.php";
define ("file_custom","data/setting.dat");

//ＰＯＳＴ受信
$submit       = @$_POST["Submit"];
$company_log         = @$_POST["company_log"];
$addr_log         = @$_POST["addr_log"];
$phone_log       = @$_POST["phone_log"];
$mobile_log       = @$_POST["mobile_log"];
$birthday_log       = @$_POST["birthday_log"];
$pic_log       = @$_POST["pic_log"];
$lastvisit_log         = @$_POST["lastvisit_log"];
$friends_log         = @$_POST["friends_log"];
//

//メッセージ初期
$message = "";
$message_error = "";
//--------------------------------------------------------------------------------------メイン処理
//表示件数の登録
if ($submit){

	if (!$message_error){
		//ユーザーファイルの読み込み
		$line_custom = read_custom();
		//入力項目のセット
		$line_custom['company_log']     = $company_log;
		$line_custom['addr_log']    = $addr_log;
		$line_custom['phone_log']    = $phone_log;
		$line_custom['mobile_log']    = $mobile_log;
		$line_custom['birthday_log'] = $birthday_log;
		$line_custom['pic_log'] = $pic_log;
		$line_custom['lastvisit_log']     = $lastvisit_log;
		$line_custom['friends_log']     = $friends_log;
		//ユーザーファイル書き込み
		write_custom($line_custom);
		//メッセージ
		$message = "初期設定を更新しました";
	}
}
//ユーザーファイルの読み込み
$line_custom = read_custom();
//--------------------------------------------------------------------------------------関数定義
///////////////////////////////////
// ユーザーファイル書き換え      //
///////////////////////////////////
function write_custom($line_custom){
	//データの一行化
	$new_line = "";
	foreach ($line_custom as $key => $value){
		$new_line .= $key."&&".$value."\n";
	}
	//ファイルの書き換え
	$file = fopen(file_custom,"w") or die(file_custom." is not found");
	flock($file,LOCK_EX);
	fputs($file,$new_line);
	flock($file,LOCK_UN);
	fclose($file);
}
///////////////////////////////////
// ユーザーファイルの読み込み    //
///////////////////////////////////
function read_custom(){
	//カスタムファイルの読込み
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

//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////

//ヘッダー1
html_header1();
// print <<< END_OF_HTML
// 
// 
// 
// END_OF_HTML;
//ヘッダー2
html_header2();
print <<< END_OF_HTML

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>
            <li><a href="./admin.php">管理メニュー</a></li>
			<li class="active">初期設定</li>
END_OF_HTML;
//-------------------------------------------------------↓
print <<< END_OF_HTML
		</ul>
</div>

<h3 class="regist_title">$buttom_name</h3>

END_OF_HTML;
//-------------------------------------------------------↑
//エラーメッセージ
if ($message_error){
	echo "<h3 class='error_msg'>".$message_error."</h3>"."\n";
}else{
	echo "<h3 class='error_msg'>".$message."</h3>"."\n";
}

//-------------------------------------------------------↓
print <<< END_OF_HTML
<h2>初期設定</h2>
<p>登録項目を設定します。使わないなもののチェックを外すと表示しません。<br />
すべてチェックを外すと、シンプルにボトルキープの管理のみの仕様になります。</p>
<div class="detail_tbl">
<form name="form" method="post" action="$this_file" class="form">
<table>
<tr>
    <th>最終来店日 <div class="infotip"><abbr title="チェックを外すと、最終来店日項目を非表示にします。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($line_custom['lastvisit_log'] == "true"){
	$value = "checked";
}else{
	$value = "";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="checkbox" name="lastvisit_log" class="form-control" value="true" $value> チェックで表示
	</td>
  </tr>
<tr>
    <th>会社情報 <div class="infotip"><abbr title="チェックを外すと、会社名と合わせて部署や役職も非表示にします。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($line_custom['company_log'] == "true"){
	$value = "checked";
}else{
	$value = "";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="checkbox" name="company_log" class="form-control" value="true" $value> チェックで表示
	</td>
  </tr>
<tr>
    <th>住所 <div class="infotip"><abbr title="チェックを外すと、住所項目を非表示にします。宛名印刷機能も停止します。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($line_custom['addr_log'] == "true"){
	$value = "checked";
}else{
	$value = "";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="checkbox" name="addr_log" class="form-control" value="true" $value> チェックで表示
	</td>
  </tr>
<tr>
    <th>電話番号 <div class="infotip"><abbr title="チェックを外すと、電話番号項目を非表示にします。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($line_custom['phone_log'] == "true"){
	$value = "checked";
}else{
	$value = "";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="checkbox" name="phone_log" class="form-control" value="true" $value> チェックで表示
	</td>
  </tr>
<tr>
    <th>携帯電話 <div class="infotip"><abbr title="チェックを外すと、携帯電話項目を非表示にします。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($line_custom['mobile_log'] == "true"){
	$value = "checked";
}else{
	$value = "";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="checkbox" name="mobile_log" class="form-control" value="true" $value> チェックで表示
	</td>
  </tr>
<tr>
    <th>誕生日 <div class="infotip"><abbr title="チェックを外すと、誕生日項目を非表示にします。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($line_custom['birthday_log'] == "true"){
	$value = "checked";
}else{
	$value = "";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="checkbox" name="birthday_log" class="form-control" value="true" $value> チェックで表示
	</td>
  </tr>
<tr>
    <th>友人知人 <div class="infotip"><abbr title="チェックを外すと、友人知人項目を非表示にします。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($line_custom['friends_log'] == "true"){
	$value = "checked";
}else{
	$value = "";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="checkbox" name="friends_log" class="form-control" value="true" $value> チェックで表示
	</td>
  </tr>
<tr>
    <th>担当者 <div class="infotip"><abbr title="チェックを外すと、担当者項目を非表示にします。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($line_custom['pic_log'] == "true"){
	$value = "checked";
}else{
	$value = "";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<input type="checkbox" name="pic_log" class="form-control" value="true" $value> チェックで表示
	</td>
  </tr>

END_OF_HTML;
//-------------------------------------------------------↑
//	echo "<tr>"."\n";
//	echo "<th>非表示</th>"."\n";
//	echo "<td>"."\n";

//if ($dsp){
//	echo "	<label><input type='checkbox' name='dsp' value='true' checked>"."\n";
//}else{
//	echo "	<label><input type='checkbox' name='dsp' value='true'>"."\n";
//}

//	echo "<b>この情報を非表示にする</b></label></td>"."\n";
//	echo "</tr>"."\n";

//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <td colspan="2" class="td-bottom">
END_OF_HTML;
//-------------------------------------------------------↑
if ($cnt == ""){
	echo "	<a href='./admin.php' class='btn btn-default'>&lt; メニューに戻る</a>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
	<input type="submit" name="Submit" value="更新して設定" class="btn btn-primary">
END_OF_HTML;
//-------------------------------------------------------↑
if ($cnt){
//	echo "	<input type='submit' name='cancel' value='キャンセル' class='btn btn-default'>"."\n";
	echo "	<a href='./index.php' class='historyback btn btn-default'>キャンセル</a>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
</td>
</tr>
</table>
</form>

END_OF_HTML;
//-------------------------------------------------------↑
//フッター
html_footer();
?>