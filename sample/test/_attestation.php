<?php
//ユーザー認証（セッション）
//--------------------------------------------------------------------------------------初期設定
$this_file = $_SERVER['SCRIPT_NAME'];
//パスワードファイル
$file_pass = "data/attestation.dat";
if (file_exists($file_pass)){$dir = "";
}elseif (file_exists("../".$file_pass)){$dir = "../";
}else{echo "ACCESS ERROR";exit();}
$file_pass = $dir.$file_pass;
//セッションの開始
session_name("sid");
session_start();

require("../bottle_assets/pageparts.php");
$html_title = "ログイン丨顧客・ボトル管理システム";  //ページタイトル
//ＰＯＳＴ受信
$submit = @$_POST["Submit"];
$f_id     = @$_POST["f_id"];
$f_pass   = @$_POST["f_pass"];
//メッセージ初期
$message_error = "";
//--------------------------------------------------------------------------------------メイン処理
//ＩＤ、ＰＡＳＳ入力
if ($submit && $f_id && $f_pass){
	//パスワードファイル読込み
	read_passfile();
	//フォーム入力項目との照合
	foreach ($line_pass as $value){
		list($file_id,$file_pass) = explode(":",$value);
		$crypt_id   = crypt($f_id, "Su");
		$cript_pass = crypt($f_pass, "Ap");
		if ($crypt_id == $file_id && $cript_pass == $file_pass){//認証ＯＫ
			//セッションにＩＤとＰＡＳＳをセット
			$_SESSION["id"]   = $crypt_id;
			$_SESSION["pass"] = $cript_pass;
			return;
		}
	}
	$message_error = "ユーザー名またはパスワードが違います。<br />再度入力してください。";
	//ＨＴＭＬ入力画面(フォームより入力があったが、認証エラー）
	html_login();
	exit();
}
//セッションの存在
if (isset($_SESSION["id"]) && isset($_SESSION["pass"])){
	//セッション読込み
	$id = $_SESSION["id"];
	$pass = $_SESSION["pass"];
	//パスワードファイル読込み
	read_passfile();
	//フォーム入力項目との照合
	foreach ($line_pass as $value){
		list($file_id,$file_pass) = explode(":",$value);
		if ($id == $file_id && $pass == $file_pass){//認証ＯＫ
			return;
		}
	}
	$message_error = "ユーザー名またはパスワードが変更されています。<br />再度ログインしてください。";
	//ＨＴＭＬ入力画面（セッションに認証できないＩＤ、ＰＡＳＳが存在する）
	html_login();
	exit();
}else{
	//ＨＴＭＬ入力画面（初回アクセス）
	html_login();
	exit();
}
//--------------------------------------------------------------------------------------関数定義
///////////////////////////////////
// パスワードファイルの読込み    //
///////////////////////////////////
function read_passfile(){
	global $file_pass;
	global $line_pass;
	$line_pass = "";
	$temp_line = file($file_pass);
	foreach ($temp_line as $value){
		if ($value){
			$value = trim($value);
			$line_pass[] = $value;
		}
	}
}
//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬ入力画面              //
///////////////////////////////////
function html_login(){
global $this_file,$dir;
global $f_id,$f_pass;
global $message_error;

//-------------------------------------------------------↓
html_header1();
html_header3();

print <<< END_OF_HTML
	<h3 class="login">ログイン</h3>
	<div class="well bs-component login_form">
		<h4 class="error_msg">$message_error</h4>
		<form name="form" method="post" action="$this_file">

              <div class="form-group">
                <label for="inputID" class="col-lg-4 control-label">ユーザーID</label>
                <div class="col-lg-12">
                  <input type="text" name="f_id" class="form-control" id="inputID" value="$f_id" placeholder="ID">
                </div>
				<div class="clear"></div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-lg-4 control-label">パスワード</label>
                <div class="col-lg-12">
                  <input type="password" name="f_pass" class="form-control" id="inputPassword" value="$f_pass" placeholder="Password">
                </div>
				<div class="clear"></div>
              </div>

              <div class="form-group bottom">
                <div class="col-lg-12">
					<input name="Submit" type="hidden" value="regist">
                  <button type="submit" name="buttom" class="btn btn-primary">ログイン</button>
                </div>
				<div class="clear"></div>
              </div>

	</form>\n
END_OF_HTML;
//-------------------------------------------------------↑

//フッター
html_footer_exj();

//-------------------------------------------------------↓
print <<< END_OF_HTML
<script src="../bottle_assets/js/bookmark_bubble.js"></script>


<script type="text/javascript">
window.addEventListener('load', function() {
  window.setTimeout(function() {
    var bubble = new google.bookmarkbubble.Bubble();

    //var parameter = 'bmb=1';
    var parameter = '#';

    bubble.hasHashParameter = function() {
      //return window.location.hash.indexOf(parameter) != -1;
      return location.hash == "" && location.href.indexOf(parameter) == location.href.length-1;
    };

    bubble.setHashParameter = function() {
      if (!this.hasHashParameter()) {
        //window.location.hash = parameter;
        location.href = parameter;
      }
    };

    bubble.getViewportHeight = function() {
      window.console.log('Example of how to override getViewportHeight.');
      return window.innerHeight;
    };

    bubble.getViewportScrollY = function() {
      window.console.log('Example of how to override getViewportScrollY.');
      return window.pageYOffset;
    };

    bubble.registerScrollHandler = function(handler) {
      window.console.log('Example of how to override registerScrollHandler.');
      window.addEventListener('scroll', handler, false);
    };

    bubble.deregisterScrollHandler = function(handler) {
      window.console.log('Example of how to override deregisterScrollHandler.');
      window.removeEventListener('scroll', handler, false);
    };

    bubble.showIfAllowed();
  }, 1000);
}, false);
</script>
</body>

</html>

END_OF_HTML;
//-------------------------------------------------------↑
}
?>