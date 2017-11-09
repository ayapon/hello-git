<?php
///////////////////////////////////
// 管理メニュー画面              //
///////////////////////////////////

    //セッション開始
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');
    //セッション値設定
    $_SESSION{'flg'}= "udataok";

include_once("attestation.php");
require("common.php");
require("../bottle_assets/pageparts.php");
$html_title = "管理メニュー丨顧客・ボトル管理システム";  //ページタイトル
//ログアウト処理
$log = @$_GET["log"];//logout
if ($log == "out"){
	unset($_SESSION["id"]);
	unset($id);
	unset($_SESSION["pass"]);
	unset($pass);
	html_logout();
	exit();
}

////////////////////////////
//ＨＴＭＬログアウト      //
////////////////////////////
function html_logout(){
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
	<link href="../bottle_assets/images/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Bootstrap -->
    <link href="../bottle_assets/css/bootstrap.min.css" rel="stylesheet">
<link href="../bottle_assets/css/style.css" rel="stylesheet" media="screen">
<link href="../js/fancybox/jquery.fancybox.css" rel="stylesheet" media="screen" />

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<title>ログアウト | 顧客・ボトル管理システム</title>
<meta name="description" content="顧客・ボトル管理システム">

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
        <a href="./" class="navbar-brand">顧客・ボトル管理</a>

      </div>

    </div>
  </div>
</header>

    <div class="container">

	<div class="bs-component">
		<div class="jumbotron acenter"><img src="../bottle_assets/images/logout02.gif">
			<h2>ログアウトしました...</h2>
			<div class="small"><a href="./">もう一度ログインする</a></div>
		</div>
	</div>

<div id="footer"><copyright>Powerd by <a href="http://www.dewey.co.jp" target="_blank">DEWEY</a></copyright></div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="../bottle_assets/js/bootstrap.min.js"></script>
<script src="../bottle_assets/js/tooltip.js"></script>
<script src="../js/fancybox/jquery.fancybox.js"></script>

</body>

</html>
END_OF_HTML;
//-------------------------------------------------------↑
}

html_header1();
html_header2();
print <<< END_OF_HTML

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>
            <li class="active">管理メニュー</li>
		</ul>
</div>

<h3  class="acenter">管理メニュー</h3>
<div class="well bs-component admin_list">
<table class="table table-striped table-hover">
	<tr>
		<td><a href="config.php"><img src="../bottle_assets/images/icons/bottle.png" alt="ボトルリスト編集" border="0" class="icons"></a></td>
		<td><a href="config.php">ボトルリスト編集</a><br />
<span class="small">プルダウンで表示、選択するボトルリストの編集</span></td>
	</tr>
	<tr>
		<td><a href="backup.php"><img src="../bottle_assets/images/icons/config.png" alt="データの並替え・削除" border="0" class="icons"></a></td>
		<td><a href="sequence.php">データの並替え・削除</a><br />
<span class="small">ボトル・顧客データの並び順の変更または削除</span></td>
	</tr>
	<tr>
		<td><a href="backup.php"><img src="../bottle_assets/images/icons/settings.png" alt="登録項目の初期設定" border="0" class="icons"></a></td>
		<td><a href="setting.php">登録項目の初期設定</a><br />
<span class="small">登録する項目を使用するものだけに絞ります。</span></td>
	</tr>
<!--	<tr>
		<td><a href="backup.php"><img src="../bottle_assets/images/icons/database.png" alt="ボトル・顧客データのバックアップ" border="0" class="icons"></a></td>
		<td><a href="backup.php">データのバックアップ</a><br />
<span class="small">ボトル・顧客データをバックアップ保存します。</span></td>
	</tr>-->
	<tr>
		<td><a href="csvexport.php"><img src="../bottle_assets/images/icons/attibutes.png" alt="登録データのCSV出力" border="0" class="icons"></a></td>
		<td><a href="csvexport.php">登録データのCSV出力</a>：PCのみ対応<br />
<span class="small">登録されているデータのCSV一括ダウンロード</span></td>
	</tr>
</table>
</div>
<br />
<div class="well bs-component admin_list">
<table class="table table-striped table-hover">
	<tr>
		<td><a href="attestation_fix.php"><img src="../bottle_assets/images/icons/lock.png" alt="管理者パスワードの変更" border="0" class="icons"></a></td>
		<td><a href="attestation_fix.php">ログイン情報の変更</a><br />
<span class="small">ログインのユーザーIDとパスワードの変更</span></td>
	</tr>
	<tr>
		<td><a href="cancel.php?cnt=$user"><img src="../bottle_assets/images/icons/sign-out.png" alt="このアプリアカウントの解約" border="0" class="icons"></a></td>
		<td><a href="cancel.php?cnt=$user">アプリアカウントの解約</a><br />
<span class="small">このアプリアカウントの解約申請</span></td>
	</tr>
	<tr>
		<td><a href="admin.php?log=out"><img src="../bottle_assets/images/icons/logout.png" alt="ログアウト" border="0" class="icons"></a></td>
		<td><a href="admin.php?log=out">ログアウト</a><br />
<span class="small">ブラウザの 『 閉じる 』 でもログアウトできます</span></td>
	</tr>
</table>
</div>
<br />
<div class="well bs-component admin_list">
<table class="table table-striped table-hover">
	<tr>
		<td><a href="https://nightworks.jp/usces-member" target="_blank"><img src="../bottle_assets/images/icons/user.png" alt="ユーザー登録情報の変更" border="0" class="icons"></a></td>
		<td><a href="https://nightworks.jp/usces-member" target="_blank">ご契約ユーザー登録情報の変更</a><br />
<span class="small">ご契約ユーザーの確認・編集・申込履歴、<strong>プラン変更</strong></span></td>
	</tr>
</table>

END_OF_HTML;

html_footer();
?>

