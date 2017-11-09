<?php
///////////////////////////////////
// ユーザーカスタム）            //
///////////////////////////////////
//セッション開始
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

include_once("attestation.php"); 
//--------------------------------------------------------------------------------------初期設定
$this_file = $_SERVER['SCRIPT_NAME'];
define ("file_custom","data/custom.dat");
//ＰＯＳＴ受信
$submit       = @$_POST["Submit"];
$page_cnt     = @$_POST["page_cnt"];
$dsp_align    = @$_POST["dsp_align"];
$mail_address = @$_POST["mail_address"];
//メッセージ初期
$message = "";
$message_error = "";
//--------------------------------------------------------------------------------------メイン処理
//表示件数の登録
if ($submit){
	//メールチェック
//	if((!preg_match("/[\w\d\-\.]+\@[\w\d\-\.]+\.[\w\d\-]+$/",$mail_address))&& $mail_address){$message_error = "[お問い合せメール送信先] メールアドレスに誤りがあります";}
	//数値チェック
	if (!is_numeric($page_cnt)){$message_error = "[表示件数/ページ] は半角数字で入力してください。";}
	//エラーが無ければ書き込み
	if (!$message_error){
		//ユーザーファイルの読み込み
		$line_custom = read_custom();
		//入力項目のセット
		$line_custom['page_cnt']     = floor($page_cnt);
//		$line_custom['dsp_align']    = $dsp_align;
//		$line_custom['mail_address'] = $mail_address;
		//ユーザーファイル書き込み
		write_custom($line_custom);
		//メッセージ
		$message = "ユーザー設定を変更しました";
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
//-------------------------------------------------------↓
print <<< END_OF_HTML
<!DOCTYPE HTML>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-title" content="ボトル管理" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="img/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Bootstrap -->
    <link href="../bottle_assets/css/bootstrap.min.css" rel="stylesheet">
<link href="../bottle_assets/css/style.css" rel="stylesheet" media="screen">
<link href="../js/fancybox/jquery.fancybox.css" rel="stylesheet" media="screen" />

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<title>基本設定 | 顧客・ボトル管理</title>

<script src="https://zipaddr.github.io/zipaddrx.js" charset="UTF-8"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-5737659-5', 'auto');
  ga('send', 'pageview');

</script>

</head>
<body class="no-thank-yu">

<header>
  <div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a href="./" class="navbar-brand"><img src="../bottle_assets/images/bottle_logo.svg">顧客・ボトル管理</a>
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="navbar-collapse collapse" id="navbar-main">
        <ul class="nav navbar-nav">
              <li><a href="./">TOP</a></li>
              <li><a href="./regist.php">新規登録</a></li>
        </ul>
		<div class="navbar-news"><a href="../news" class="fancybox fancybox.iframe" target="_blank"><span class="glyphicon glyphicon-info-sign"></span></a><a href="admin.php" class=""><span class="glyphicon glyphicon-cog"></span></a></div>
      </div>
    </div>
  </div>
</header>

    <div class="container">

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>
            <li class="active">管理メニュー</li>
		</ul>
</div>

<h3  class="acenter">基本設定</h3>
<div class="well bs-component admin_list">

END_OF_HTML;
//-------------------------------------------------------↑

if ($message_error){
	echo "<h4 class='error_msg'>".$message_error."</h4>"."\n";
}else{
	echo "<h4 class='error_msg'>".$message."</h4>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML


  <form name="form" method="post" action="$this_file">

END_OF_HTML;
//-------------------------------------------------------↑
if (@!$line_custom['page_cnt']){$line_custom['page_cnt'] = 0;}
if ($message_error){$line_custom['page_cnt'] = $page_cnt;}
//-------------------------------------------------------↓
print <<< END_OF_HTML

              <div class="form-group">
                <label for="pageCnt" class="col-lg-5 control-label">表示件数/ページ</label>
                <div class="col-lg-12">
                  <input type="text" name="page_cnt" class="form-control page_cnt" id="pageCnt" value="$line_custom[page_cnt]" size="5" maxlength="3">件/ページ（半角数字）<br />
<span class="hissu small">※全件表示は0を入力してください。</span>
                </div>
				<div class="clear"></div>
              </div>
<div class="update_btn">
<input type="submit" name="Submit" value="　登　録　" class="btn btn-primary">
</div>
  </form>
</div>

<div id="footer"><copyright>Powerd by <a href="http://www.dewey.co.jp" target="_blank">DEWEY</a></copyright></div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="../bottle_assets/js/bootstrap.min.js"></script>
<script src="../bottle_assets/js/common.js"></script>
<script src="../bottle_assets/js/tooltip.js"></script>
<script src="../js/fancybox/jquery.fancybox.js"></script>

</body>

</html>

END_OF_HTML;
//-------------------------------------------------------↑
?>