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
//--------------------------------------------------------------------------------------初期設定
//$url = $_SERVER['SCRIPT_NAME'];
$url = "regist.php";
define ("file_sdata","data/account.dat");
define ("file_count","data/account_count.dat");

//登録上限数
//$logmax = "100";

$file_plan = "data/plan.dat";
$file_pref = "data/pref.dat";

//ＧＥＴ受信
$cnt = @$_GET["cnt"];
$planlist   = urldecode(@$_GET["planlist"]);
$searchword = urldecode(@$_GET["searchword"]);
//ＰＯＳＴ受信
$submit         = @$_POST["Submit"];
$cancel         = @$_POST["cancel"];
$plan       = @$_POST["plan"];
$plan_limit       = @$_POST["plan_limit"];
$account       = @$_POST["account"];
$store       = @$_POST["store"];
$customer_id       = @$_POST["customer_id"];
$customer       = @$_POST["customer"];
$section       = @$_POST["section"];
$section2       = @$_POST["section2"];
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
$flag = @$_POST["flag"];
$dsp  = @$_POST["dsp"];
$cancel_flag  = @$_POST["cancel_flag"];
$c_date  = @$_POST["c_date"];
$c_reason1 = @$_POST["c_reason1"];
$c_reason2  = @$_POST["c_reason2"];
$c_reason3  = @$_POST["c_reason3"];
$c_reason4  = @$_POST["c_reason4"];
$c_reason5 = @$_POST["c_reason5"];
$c_reasonO  = @$_POST["c_reasonO"];
$c_reasonOf  = @$_POST["c_reasonOf"];
$monitor  = @$_POST["monitor"];
$stop  = @$_POST["stop"];
$reset       = @$_POST["reset"];
//
if (!$planlist){$planlist = @$_POST["planlist"];}
if (!$searchword){$searchword = @$_POST["searchword"];}
//ダイレクトリクエスト
if (file_exists($file_link)){$dir = "";$direct = 1;}
//プランリスト
$word_list = read_file($dir.$file_plan);
//if ($word_list){array_unshift($word_list,"変更無し");}else{$word_list[]="";}
if ($planlist == $word_list[0]){$planlist = "";}
$word_list2 = read_file($dir.$file_pref);
if ($pref_dat== $word_list2[0]){$pref_dat = "";}

//メッセージ初期
$message = "";
$message_error = "";
//--------------------------------------------------------------------------------------メイン処理
//編集キャンセル
if ($cancel){echo "<meta http-equiv='refresh' content='0;url=./index.php'>"."\n";exit();}
//検索のリセット
//if ($reset){$submit_search = "";$planlist = "";$searchword = "";}
//サーチ処理
//if ($planlist || $searchword){$line_link = search_data($line_link,$planlist,$searchword);}
//編集モード
if ($cnt && !$submit){
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//不正確認
	if ($sdata_line){
		foreach ($sdata_line as $key => $value){
			//文字列分割
			list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop) = explode("&&",$value);
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
	$plan_limit = check_text($plan_limit);
	$account = check_text($account);
	$store = check_text($store);
	$customer_id = check_text($customer_id);
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
	$flag = check_text($flag);
	$dsp = check_text($dsp);
	$cancel_flag = check_text($cancel_flag);
	$c_date = check_text($c_date);
	$otherbottle = check_text($otherbottle);
	$c_reason1 = check_text($c_reason1);
	$c_reason2 = check_text($c_reason2);
	$c_reason3 = check_text($c_reason3);
	$c_reason4 = check_text($c_reason4);
	$c_reason5 = check_text($c_reason5);
	$c_reasonO = check_text($c_reasonO);
	$c_reasonOf = check_text($c_reasonOf);
	$monitor = check_text($monitor);
	$stop = check_text($stop);

	if (!$plan){$message_error = "【プラン】は必須項目です";}
	if (!$account){$message_error = "【アカウントID】は必須項目です";}
	if (!$store){$message_error = "【店舗名】は必須項目です";}
	//エラーがなければファイル書き込み
	if (!$message_error){
		if ($cnt){
			fix_data();
		}else{
			write_data();
		}

		if ($cnt){
			//書き込み後は一覧に戻る
			echo "<meta http-equiv='refresh' content='0; url=./detail.php?num=$cnt'>"."\n";
		}else{
			echo "<meta http-equiv='refresh' content='0; url=./index.php'>"."\n";
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
	global $plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop;
	//カウンタ読み込み
	$counter = file(file_count);
	$count = trim($counter[0]);
	$count++;
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//一行化
	$new_line = $count."&&".$plan."&&".$plan_limit."&&".$account."&&".$store."&&".$customer_id."&&".$customer."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$email."&&".$memo."&&".$firsttime."&&".$lasttime."&&".$flag."&&".$dsp."&&".$cancel_flag."&&".$c_date."&&".$c_reason1."&&".$c_reason2."&&".$c_reason3."&&".$c_reason4."&&".$c_reason5."&&".$c_reasonO."&&".$c_reasonOf."&&".$monitor."&&".$stop."\n";
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
// 	$new_file = "regist_dat/".$count.".dat";
// 	touch($new_file);
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
	global $plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop;
	//カウンタ読み込み
	$counter = file(file_count);
	$count = trim($counter[0]);
	$count++;
	//カテゴリー1ファイル読込み
//	$word_list = read_file(file_plan);

	//登録ファイルの読み込み
	$sdata_line = read_data();
	//一行化
	$new_line = "";
	foreach ($sdata_line as $value){
		//文字列分割
		list($count,) = explode("&&",$value);
		if ($cnt == $count){
			$new_line .= $count."&&".$plan."&&".$plan_limit."&&".$account."&&".$store."&&".$customer_id."&&".$customer."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$email."&&".$memo."&&".$firsttime."&&".$lasttime."&&".$flag."&&".$dsp."&&".$cancel_flag."&&".$c_date."&&".$c_reason1."&&".$c_reason2."&&".$c_reason3."&&".$c_reason4."&&".$c_reason5."&&".$c_reasonO."&&".$c_reasonOf."&&".$monitor."&&".$stop."\n";
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

///////////////////////////////////
// 登録限度数設定              //
///////////////////////////////////
	//登録件数チェックのためカウンタ再読み込みfile_sdata
// 	$fp = fopen( file_sdata, 'r' );
// 	for( $counts = 0; fgets( $fp ); $counts++ );
// 
// 	if ($counts > $logmax){
// 		$message_error = "登録件数は".$logmax."件までです";
// 		$form_none = 1;
// 	}

//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////
if ($cnt){$buttom_name = "更新登録";}else{$buttom_name = "新規登録";}
//	$pageurl = $url."?";
//	if ($category || $category2 || $searchword){
//		if ($category){$pageurl .= "category=".urlencode($category)."&";}
//		if ($category2){$pageurl .= "category2=".urlencode($category2)."&";}
//		if ($searchword){$pageurl .= "searchword=".urlencode($searchword)."&";}
//	}
//	if ($page){$churl = $pageurl."page=".$page."&";}else{$churl = $pageurl;}
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
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet" media="screen">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<title>登録編集 | ユーザー管理</title>

<script src="https://zipaddr.github.io/zipaddrx.js" charset="UTF-8"></script>

</head>
<body class="no-thank-yu">

<header>
  <div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a href="./" class="navbar-brand"><img src="assets/images/logo.png">ユーザー管理</a>
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="navbar-collapse collapse" id="navbar-main">
        <ul class="nav navbar-nav">
              <li><a href="./regist.php">新規登録</a></li>
			<li><a href="./sequence.php">並替え・削除</a></li>
			<li><a href="./admin.php?log=out">ログアウト</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>

    <div class="container">

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>

END_OF_HTML;
//-------------------------------------------------------↑
if ($cnt){
	//文字列内のタグを取り除く
	$temp_title = strip_tags($store);
//	$temp_title2 = strip_tags($customer);
	//長い文字列は40バイト以降を消去し"..."を付加する
	if(strlen($temp_title) > 40){ $temp_title = substr($temp_title,0,60)."...";}
	echo "	<li class='active'>".$temp_title." 様の編集</li>"."\n";
}else{	
	echo "	<li class='active'>新規登録</li>"."\n";
}
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

if ($cnt){
	echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$url."?cnt=".$cnt."'>"."\n";
}else{
	if (!$form_none){
		echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$url."'>"."\n";
	}
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<div class="detail_tbl">
<table>
<tr>
    <th>契約プラン <span class="hissu">※必須</span></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
$plan =  preg_replace("/<br>/","\n",$plan);
$plan = htmlspecialchars ($plan);
//-------------------------------------------------------↓
if ($word_list){
//if (count($word_list) > 1){
	echo "<select name='plan' class='form-category form-control'>"."\n";
	echo "<option value=''>プランリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($plan == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}


if ($monitor){
	echo "	<label><input type='checkbox' name='monitor' value='true' checked>"."\n";
}else{
	echo "	<label><input type='checkbox' name='monitor' value='true'>"."\n";
}

	echo "<strong>モニターユーザー</strong></label>"."\n";

//-------------------------------------------------------↓
print <<< END_OF_HTML

	</td>
  </tr>
  <tr>
    <th>利用可能数</th>
END_OF_HTML;
//-------------------------------------------------------↑
$plan_limit =  preg_replace("/<br>/","\n",$plan_limit);
$plan_limit = htmlspecialchars ($plan_limit);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td><input type="number" name="plan_limit" value="$plan_limit" class="form-category2 form-control" placeholder="数字のみ" maxlength="13" style="ime-mode: disabled;">
END_OF_HTML;
//-------------------------------------------------------↑
if ($stop){
	echo "	<label><input type='checkbox' name='stop' value='true' checked>"."\n";
}else{
	echo "	<label><input type='checkbox' name='stop' value='true'>"."\n";
}

	echo "<strong><span class='hissu'>新規追加停止</span></strong></label>"."\n";
//-------------------------------------------------------↓
print <<< END_OF_HTML
</td>
	</tr>
<tr>
    <th>アカウントID <span class="hissu">※必須</span></th>
END_OF_HTML;
//-------------------------------------------------------↑
$account =  preg_replace("/<br>/","\n",$account);
$account = htmlspecialchars ($account);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>
<input type="text" name="account" value="$account" class="form-control" placeholder="半角英数小文字のみ" style="ime-mode: active;">
	</td>
  </tr>
<tr>
    <th>会社・店舗名 <span class="hissu">※必須</span></th>
END_OF_HTML;
//-------------------------------------------------------↑
$store =  preg_replace("/<br>/","\n",$store);
$store = htmlspecialchars ($store);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>
<input type="text" name="store" value="$store" class="form-control" placeholder="店舗名" style="ime-mode: active;"  onblur="FuriganaCheck();">
<div class="clear"></div>
</td>
  </tr>
  <tr>
    <th>Welcart会員No</th>
END_OF_HTML;
//-------------------------------------------------------↑
$customer_id =  preg_replace("/<br>/","\n",$customer_id);
$customer_id = htmlspecialchars ($customer_id);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td><input type="number" name="customer_id" value="$customer_id" class="form-tel form-control" placeholder="数字のみ" maxlength="13" style="ime-mode: disabled;"></td>
	</tr>
<tr>
    <th>契約者名 <span class="hissu">※必須</span></th>
END_OF_HTML;
//-------------------------------------------------------↑
$customer =  preg_replace("/<br>/","\n",$customer);
$customer = htmlspecialchars ($customer);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>
<input type="text" name="customer" value="$customer" class="form-control" placeholder="契約者名" style="ime-mode: active;"  onblur="FuriganaCheck();">
<div class="clear"></div>
</td>
  </tr>
<!--<tr>
    <th>役職</th>
END_OF_HTML;
//-------------------------------------------------------↑
$degree =  preg_replace("/<br>/","\n",$degree);
$degree = htmlspecialchars ($degree);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>
<input type="text" name="degree" value="$degree" class="form-control" placeholder="代表取締役など"></td>
  </tr>-->
END_OF_HTML;
//-------------------------------------------------------↑
$zip =  preg_replace("/<br>/","\n",$zip);
$zip = htmlspecialchars ($zip);
$pref =  preg_replace("/<br>/","\n",$pref);
$pref = htmlspecialchars ($pref);
$addr =  preg_replace("/<br>/","\n",$addr);
$addr = htmlspecialchars ($addr);
$addr2 =  preg_replace("/<br>/","\n",$addr2);
$addr2 = htmlspecialchars ($addr2);
//-------------------------------------------------------↓
print <<< END_OF_HTML

	<tr>
		<th>郵便番号 <div class="infotip"><abbr title="数字7桁だけを入力すれば、以下の都道府県や住所を自動入力します。番地やビル名などは入力ください。" rel="tooltip">?</abbr></div></th><td><input type="text" id="zip" name="zip" value="$zip" class="form-zip form-control" placeholder="(数字のみ) 自動変換" maxlength="8" style="ime-mode: disabled;"></td>
	</tr><tr>
		<th>都道府県</th><td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list2){
	echo "<select name='pref' class='form-zip form-control' id='pref'>"."\n";
	echo "<option value=''>選択ください</option>"."\n";
	foreach ($word_list2 as $value){
		if ($pref == $value){
			echo "    	<option  value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option  value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}

//-------------------------------------------------------↓ 
print <<< END_OF_HTML
		</td>
	</tr><tr>
		<th>住所番地 <div class="infotip"><abbr title="自動入力では番地以降は自動入力されませんので、番地やビル名などは入力ください。" rel="tooltip">?</abbr></div></th><td><input type="text" name="addr" value="$addr" class="form-control" id="addr" placeholder="番地まで"></td>
	</tr><tr>
		<th>ビル名等</th><td><input type="text" name="addr2" value="$addr2" class="form-control" id="addr2" placeholder="ビル名及び号室など" style="ime-mode: active;"></td>
	</tr>
  <tr>
    <th>電話番号</th>
END_OF_HTML;
//-------------------------------------------------------↑
$phone =  preg_replace("/<br>/","\n",$phone);
$phone = htmlspecialchars ($phone);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td><input type="tel" name="phone" value="$phone" class="form-tel form-control" placeholder="数字のみ(ハイフン不要)" maxlength="13" style="ime-mode: disabled;"></td>
	</tr>
<!--  <tr>
    <th>携帯電話</th>
END_OF_HTML;
//-------------------------------------------------------↑
$mobile =  preg_replace("/<br>/","\n",$mobile);
$mobile = htmlspecialchars ($mobile);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td><input type="tel" name="mobile" value="$mobile" class="form-tel form-control" placeholder="数字のみ(ハイフン不要)" maxlength="13" style="ime-mode: disabled;"></td>
	</tr>-->
  <tr>
    <th>メールアドレス</th>
END_OF_HTML;
//-------------------------------------------------------↑
$email =  preg_replace("/<br>/","\n",$email);
$email = htmlspecialchars ($email);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td><input type="email" name="email" value="$email" class="form-tel form-control" placeholder="半角英数記号" style="ime-mode: disabled;"></td>
	</tr>
	<tr>
    <th>メモ <div class="infotip"><abbr title="メモに入力した内容も検索時にヒットしますので、重要なキーワードとなる事項は入力されることをお薦めします。また、㈱、㈲や丸囲み数字、ローマ数字、㏍、℡、№などの機種依存文字は文字化けの原因になるので使わないようにしましょう！" rel="tooltip">?</abbr></div></th>
END_OF_HTML;
//-------------------------------------------------------↑
$memo =  preg_replace("/<br>/","\n",$memo);
$memo = htmlspecialchars ($memo);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td><textarea name="memo" class="form-control" rows="4" style="ime-mode: active;">$memo</textarea></td>
  </tr><tr>\n
END_OF_HTML;
//-------------------------------------------------------↑
if($firsttime <> ""){
	echo "<th>初回登録日時 <div class='infotip'><abbr title='初めて登録した時の日時です。' rel='tooltip'>?</abbr></div></th>"."\n";
	echo "<td>$firsttime"."\n";
	echo "<input type='hidden' id='firsttime' name='firsttime' value='$firsttime'>"."\n";
}else{
	$now_date = date("Y/m/d H:i");
	echo "<th>初回登録日時 <div class='infotip'><abbr title='初めて登録した時の日時としてこの時間が登録されます。' rel='tooltip'>?</abbr></div></th>"."\n";
	echo "<td>$now_date"."\n";
	echo "<input type='hidden' id='firsttime' name='firsttime' value='$now_date'>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
      </td>
  </tr>
  <tr>
    <th>前回更新日時 <div class="infotip"><abbr title="最後に更新された時の日時です。" rel="tooltip">?</abbr></div>
</th>\n
END_OF_HTML;
//-------------------------------------------------------↑
	echo "<td>$lasttime"."\n";
	$now_date2 = date("Y/m/d H:i");
	echo "<input type='hidden' id='lasttime' name='lasttime' value='$now_date2'>"."\n";
//-------------------------------------------------------↓
print <<< END_OF_HTML
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
	echo "	<a href='./index.php' class='historyback btn btn-default'>前の画面に戻る</a>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
	<input type="submit" name="Submit" value="$buttom_name" class="btn btn-primary">
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

<div id="footer"><copyright>Powerd by <a href="http://www.dewey.co.jp" target="_blank">DEWEY</a></copyright></div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/common.js"></script>
<script src="assets/js/tooltip.js"></script>

</body>
</html>
END_OF_HTML;
//-------------------------------------------------------↑
?>