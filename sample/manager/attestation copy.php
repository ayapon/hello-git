<?php
//ユーザー認証（セッション）
//--------------------------------------------------------------------------------------初期設定
$url = $_SERVER['SCRIPT_NAME'];
//パスワードファイル
$file_pass = "data/attestation.dat";
if (file_exists($file_pass)){$dir = "";
}elseif (file_exists("../".$file_pass)){$dir = "../";
}else{echo "ACCESS ERROR";exit();}
$file_pass = $dir.$file_pass;
//セッションの開始
session_name("sid");
session_start();
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
global $url,$dir;
global $f_id,$f_pass;
global $message_error;

//-------------------------------------------------------↓
print <<< END_OF_HTML
<!DOCTYPE HTML>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="apple-mobile-web-app-title" content="ボトル管理" />
	<link href="img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet" media="screen">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<title>ログイン | TESTユーザー管理</title>
<meta name="description" content="ユーザー管理システム">
<link rel="stylesheet" type="text/css" href="css/addtohomescreen.css">
</head>

	<body class="no-thank-yu">

<header>
  <div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a href="./" class="navbar-brand"><img src="assets/images/logo.png">TESTユーザー管理</a>

      </div>

    </div>
  </div>
</header>

    <div class="container">
		<h3 class="login">TESTログイン</h3>
	<div class="well bs-component login_form">
		<h4 class="error_msg">$message_error</h4>
		<form name="form" method="post" action="$url">

              <div class="form-group">
                <label for="inputID" class="col-lg-4 control-label">ID</label>
                <div class="col-lg-12">
                  <input type="text" name="f_id" class="form-control" id="inputID" value="$f_id" placeholder="ID">
                </div>
				<div class="clear"></div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-lg-4 control-label">Password</label>
                <div class="col-lg-12">
                  <input type="password" name="f_pass" class="form-control" id="inputPassword" value="$f_pass" placeholder="Password">
                </div>
				<div class="clear"></div>
              </div>

              <div class="form-group">
                <div class="col-lg-12">
					<input name="Submit" type="hidden" value="regist">
                  <button type="submit" name="buttom" class="btn btn-primary">ログイン</button>
                </div>
              </div>

	</form>
<div class="clear"></div>
</div>
<div id="footer"><copyright>Powerd by <a href="http://www.dewey.co.jp" target="_blank">DEWEY</a></copyright></div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/common.js"></script>
<script src="js/addtohomescreen.min.js"></script>
<script src="js/tooltip.js"></script>

<script type="text/javascript">
addToHomescreen({
	appID:"AddToHomescreenKindleHighlights",
	lifespan:0
	});
</script>
</body>

</html>

END_OF_HTML;
//-------------------------------------------------------↑
}
?>