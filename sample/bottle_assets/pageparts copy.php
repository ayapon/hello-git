<?php
if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on') {  
  $protocol = 'https://';  
}else{  
  $protocol = 'http://';
}
$url = $protocol.$_SERVER["HTTP_HOST"];

//ディレクトリ名を取得
$url_dir = basename(dirname($_SERVER['SCRIPT_NAME']));

// define ("file_setting","$url/bottle_assets/configdata/setting.dat");
// ///////////////////////////////////
// // 初期設定ファイルの読み込み    //
// ///////////////////////////////////
// $line_setting = read_setting();
// function read_setting(){
// 	//カスタムファイルの読込み
// 	$line_setting = "";
// 	$temp_line = file(file_setting);
// 	foreach ($temp_line as $value){
// 		if ($value){
// 			$value = trim($value);
// 			list($i,$j) = explode("&&",$value);
// 			$line_setting[$i] = $j;
// 		}
// 	}
// 	return $line_setting;
// }
// $company_logs = $line_setting['company_log'];
// $addr_logs = $line_setting['addr_log'];
// $phone_logs = $line_setting['phone_log'];
// $mobile_logs = $line_setting['mobile_log'];
// $birthday_logs = $line_setting['birthday_log'];
// $pic_logs = $line_setting['pic_log'];


///////////////////////////////////
// ＨＴＭＬヘッダー(1)            //
///////////////////////////////////
function html_header1(){
global $html_title, $url;
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
	<link href="$url/bottle_assets/images/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Bootstrap -->
    <link href="$url/bottle_assets/css/bootstrap.min.css" rel="stylesheet">
<link href="$url/bottle_assets/css/style2.css" rel="stylesheet" media="screen">
<link href="$url/js/fancybox/jquery.fancybox.css" rel="stylesheet" media="screen" />

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<title>$html_title</title>
<meta name="description" content="顧客・ボトル管理システム">\n
END_OF_HTML;
//-------------------------------------------------------↑
}
///////////////////////////////////
// ＨＴＭＬヘッダー(2)            //
///////////////////////////////////
function html_header2(){
global $url, $url_dir;
print <<< END_OF_HTML

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
        <a href="$url/$url_dir" class="navbar-brand"><img src="$url/bottle_assets/images/bottle_logo.svg">顧客・ボトル管理</a>
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="navbar-collapse collapse" id="navbar-main">
        <ul class="nav navbar-nav">
              <li><a href="$url/$url_dir">TOP</a></li>
              <li><a href="$url/$url_dir/regist.php">新規登録</a></li>
        </ul>
		<div class="navbar-news"><a href="$url/news" class="fancybox fancybox.iframe" target="_blank"><span class="glyphicon glyphicon-info-sign"></span></a><a href="admin.php" class=""><span class="glyphicon glyphicon-cog"></span></a></div>
      </div>
    </div>
  </div>
</header>

    <div class="container">

END_OF_HTML;
}
///////////////////////////////////
// ＨＴＭＬフッター              //
///////////////////////////////////
function html_footer(){
global $url, $url_dir;
//-------------------------------------------------------↓
print <<< END_OF_HTML
<div id="footer"><copyright>Powerd by <a href="http://www.dewey.co.jp" target="_blank">DEWEY</a></copyright></div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="$url/bottle_assets/js/bootstrap.min.js"></script>
<script src="$url/bottle_assets/js/common.js"></script>
<script src="$url/bottle_assets/js/tooltip.js"></script>
<script src="$url/js/fancybox/jquery.fancybox.js"></script>
</body>

</html>
END_OF_HTML;
//-------------------------------------------------------↑
}

///////////////////////////////////
// ＨＴＭＬフッター　追加JS用              //
///////////////////////////////////
function html_footer_exj(){
global $url, $url_dir;
//-------------------------------------------------------↓
print <<< END_OF_HTML
<div id="footer"><copyright>Powerd by <a href="http://www.dewey.co.jp" target="_blank">DEWEY</a></copyright></div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="$url/bottle_assets/js/bootstrap.min.js"></script>
<script src="$url/bottle_assets/js/common.js"></script>
<script src="$url/bottle_assets/js/tooltip.js"></script>
<script src="$url/js/fancybox/jquery.fancybox.js"></script>

END_OF_HTML;
//-------------------------------------------------------↑
}

?>