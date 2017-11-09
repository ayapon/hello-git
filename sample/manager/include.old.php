<?php
define ("allow_tag","");
///////////////////////////////////
// ＨＴＭＬヘッダー(1)            //
///////////////////////////////////
function html_header1($title){
//-------------------------------------------------------↓
print <<< END_OF_HTML
<!DOCTYPE HTML>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-title" content="ユーザー管理" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="/manager/assets/images/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Bootstrap -->
    <link href="/manager/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="/manager/assets/css/style.css" rel="stylesheet" media="screen">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<title>ユーザー管理</title>
<meta name="description" content="ユーザー管理システム">\n
END_OF_HTML;
//-------------------------------------------------------↑
}
///////////////////////////////////
// ＨＴＭＬヘッダー(2)            //
///////////////////////////////////
function html_header2(){
print <<< END_OF_HTML

</head>

	<body class="no-thank-yu">

<header>
  <div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a href="./" class="navbar-brand"><img src="/manager/assets/images/bottle_logo.svg">ユーザー管理</a>
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
			<li><a href="./sequence.php">並替え・削除</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>

    <div class="container">

END_OF_HTML;
}

///////////////////////////////////
// ＨＴＭＬヘッダー(3)            //
///////////////////////////////////
function html_header3(){
print <<< END_OF_HTML

</head>

	<body class="userinfo">

    <div class="container">

END_OF_HTML;
}


///////////////////////////////////
// ＨＴＭＬフッター              //
///////////////////////////////////
function html_footer(){
//-------------------------------------------------------↓
print <<< END_OF_HTML
<div id="footer"><copyright>Powerd by <a href="http://www.dewey.co.jp" target="_blank">DEWEY</a></copyright></div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/manager/assets/js/bootstrap.min.js"></script>
<script src="/manager/assets/js/common.js"></script>
<script src="/manager/assets/js/tooltip.js"></script>

</body>

</html>
END_OF_HTML;
//-------------------------------------------------------↑
}

///////////////////////////////////
// ＨＴＭＬフッター              //
///////////////////////////////////
function html_footer3(){
//-------------------------------------------------------↓
print <<< END_OF_HTML


<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/manager/assets/js/bootstrap.min.js"></script>
<script src="/manager/assets/js/common.js"></script>
<script src="/manager/assets/js/tooltip.js"></script>

</body>

</html>
END_OF_HTML;
//-------------------------------------------------------↑
}

///////////////////////////////////
// ファイルの読み込み            //
///////////////////////////////////
function read_data($file_name){
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
/////////////////////////////////
// テキスト出力整形            //
/////////////////////////////////
function fix_text($str){
	$str = str_replace("<br>","\n",$str);
	$str = strip_tags($str,allow_tag);
	$str = str_replace("(style|onmouse|onclick)[^=]*=", "", $str);
	$str = str_replace("\r|\n|\r\n","<br>",$str);
	return $str;
}
///////////////////////////////////
// ユーザーファイルの読み込み    //
///////////////////////////////////
function read_custom($file_name){
	$line_custom = "";
	$temp_line = file($file_name);
	foreach ($temp_line as $value){
		if ($value){
			$value = trim($value);
			list($i,$j) = explode("&&",$value);
			$line_custom[$i] = $j;
		}
	}
	return $line_custom;
}
?>