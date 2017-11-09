<?php
///////////////////////////////////
// 管理者パスワードの変更        //
///////////////////////////////////
//セッション開始
// session_start();
// header('Expires: -1');
// header('Cache-Control:');
// header('Pragma:');
// 
// include_once("attestation.php");
require("common.php");
require("../bottle_assets/pageparts.php");
$html_title = "ユーザー名・パスワード変更丨顧客・ボトル管理システム";  //ページタイトル
$this_file = $_SERVER['SCRIPT_NAME'];
$pass_file = "data/attestation.dat";
$new_id = @$_POST["new_id"];
$new_pass1 = @$_POST["new_pass1"];
$new_pass2 = @$_POST["new_pass2"];
/////////////////////////
//メイン処理           //
/////////////////////////
if ($new_id || $new_pass1 || $new_pass2){
	if(!$new_id){    html_pass("！ユーザー名が入力されていません"); }
	if(!$new_pass1){ html_pass("！パスワードが入力されていません"); }
	if(!$new_pass2){ html_pass("！確認パスワードが入力されていません"); }
	
	if(!preg_match("/^\w{3,12}$/",$new_id)){ html_pass("！ユーザー名は半角文字3〜12文字で入力してください"); }
	if(!preg_match("/^\w{4,12}$/",$new_pass1)){ html_pass("！パスワードは半角文字4〜12文字で入力してください"); }
	
	if($new_id == $new_pass1){   html_pass("！ユーザー名とパスワードは違う文字を入力してください"); }
	if($new_pass1 <> $new_pass2){ html_pass("！パスワードと確認パスワードの文字が違います"); }
	//パスワード変更処理
	Write_pass();
	exit();
}
html_pass("");
//////////////////////////
//パスワード変更処理    //
//////////////////////////
function Write_pass(){
	global $pass_file,$new_id,$new_pass1;
	//パスワードファイルの読込み
	$line_pass = "";
	$temp_line = file($pass_file);
	foreach ($temp_line as $value){
		if ($value){
			$value = trim($value);
			$line_pass[] = $value;
		}
	}
	//現在のＩＤとＰＡＳＳを読込み
// 	$Fid = $_SESSION["id"];
// 	$Fpass = $_SESSION["pass"];
	//新規一行化
	$new_line = "";
	foreach ($line_pass as $value){
		//ID,PASSを分割
		list($id, $pass) = explode(":", $value);
// 		if (($id == $Fid) && ($pass == $Fpass)) {
			//入力データの暗号化
			$new_id = crypt($new_id, "Su");
			$new_pass1 = crypt($new_pass1, "Ap");
			$new_line .= $new_id.":".$new_pass1."\n";
			$check = "true";
// 		}else{
// 			$new_line .= $value."\n";
// 		}
	}
	if (@$check){
		//パスワードファイルの書き換え
		$file = @fopen($pass_file,"w")or die("$pass_file is not find");
		flock($file, LOCK_EX);
		fputs($file, $new_line);
		flock($file, LOCK_UN);
		fclose($file);
	}else{
		echo "パスワード書き換エラー";
		exit();
	}
//-------------------------------------------------------↓
html_header1();
html_header3();

print <<< END_OF_HTML

<h3  class="acenter">パスワード変更完了</h3>
<div class="well bs-component admin_list">
	<table class="table table-striped table-hover">

              <tr>
                <td><h4 class="errormsg">管理者パスワードの変更を完了しました。</h4>
                  <br>ユーザー名とパスワードは紛失しないよう保管してください。</td>
              </tr>
            <tr>
              <td><a href="./admin.php" class="btn btn-primary">管理メニュー</a></td>
            </tr>
	</table>

END_OF_HTML;
//-------------------------------------------------------↑
html_footer();
}
//////////////////////////
//ＨＴＭＬ出力          //
//////////////////////////
function html_pass($message){
global $this_file,$new_id,$new_pass1,$new_pass2;
//-------------------------------------------------------↓
html_header1();

print <<< END_OF_HTML

<SCRIPT LANGUAGE="JavaScript">
<!--
//入力項目チェック
function check(){
	if(!document.form.new_id.value){
		window.alert("ユーザー名を入力してください");
		return false;
	}
	if(!document.form.new_pass1.value){
		window.alert("パスワードを入力してください");
		return false;
	}
	if(!document.form.new_pass2.value){
		window.alert("確認パスワードを入力してください");
		return false;
	}
	//パターンマッチ
	if(!document.form.new_id.value.match(/^\w{3,12}$/)){
		window.alert("ユーザー名は半角文字3〜12文字で入力してください");
		return false;
	}
	if(!document.form.new_pass1.value.match(/^\w{4,12}$/)){
		window.alert("パスワードは半角文字4〜12文字で入力してください");
		return false;
	}
	//確認パスワードチェック
	if(document.form.new_id.value == document.form.new_pass1.value){
		window.alert("ユーザー名と確認パスワードは同じにしないでください");
		return false;
	}
	if(document.form.new_pass1.value != document.form.new_pass2.value){
		window.alert("パスワードと確認パスワードの文字が違います");
		return false;
	}
}
//-->
END_OF_HTML;
//-------------------------------------------------------↑
echo "</SCRIPT>"."\n";
//-------------------------------------------------------↓
html_header3();
print <<< END_OF_HTML

<h3  class="acenter">ユーザー名・パスワード変更</h3>
<div class="well bs-component admin_list">

		<table>\n
END_OF_HTML;
//-------------------------------------------------------↑
if ($message){

	echo "            <h4 class='error_msg'>".$message."</h4>"."\n";

}
//-------------------------------------------------------↓
//
//		サンプルなので、パスワードを変更させない
//		<form name="form" method="post" action="$this_file" onSubmit="return check()" class="form-horizontal">
//
print <<< END_OF_HTML
		<form name="form" method="post" action="$this_file" onSubmit="return check()" class="form-horizontal">
			<div class="form-group">
			<label for="inputUser" class="col-lg-4 control-label">新規ユーザー名</label>
			<div class="col-lg-12">
				<input name="new_id" type="text" value="$new_id" maxlength="12" class="form-control" placeholder="半角文字4〜12文字">
			</div>
			<div class="clear"></div>
			</div>

			<div class="form-group">
			<label for="inputPassword" class="col-lg-4 control-label">新規パスワード</label>
			<div class="col-lg-12">
				<input name="new_pass1" type="password" value="$new_pass1" maxlength="12" class="form-control" placeholder="半角文字4〜12文字">
			</div>
			<div class="clear"></div>
			</div>

			<div class="form-group">
			<label for="inputPasswordCofirm" class="col-lg-4 control-label">確認入力</label>
			<div class="col-lg-12">
			  <input name="new_pass2" type="password" value="$new_pass2" maxlength="12" class="form-control" placeholder="再度同じパスワードを入力">
			</div>
			<div class="clear"></div>
			</div>

			<p>※入力したユーザー名とパスワードは忘れないよう控えてください。</p>
			
<div class="footer_navi">
            <a href="./admin.php" class="btn btn-default">&lt; メニューに戻る</a>
			<div class="edit_btn"><input type="submit" name="Submit" value="変更登録" class="btn btn-warning"></div>
			<div class="clear"></div>
</div>
		  <!--</form>-->
</div>
<div class="clear"></div>

END_OF_HTML;
//-------------------------------------------------------↑
html_footer();

exit();
}
?>