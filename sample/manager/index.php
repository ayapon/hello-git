<?php
///////////////////////////////////////////////////////
// DEWEY ユーザー管理
// 2015/08
///////////////////////////////////////////////////////
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

include_once("attestation.php"); 

$html_title = "TESTユーザー管理";  //ページタイトル
//--------------------------------------------------------------------------------------初期設定
$dir        = "stock/";//スクリプトフォルダ名
$file_link = "data/account.dat";
$file_regist = "data/regist_dat/";
$file_category = "data/plan.dat";
$file_pref = "data/pref.dat";
$file_custom = "data/custom.dat";
$url = $_SERVER['SCRIPT_NAME'];
//ＧＥＴ
$category   = urldecode(@$_GET["category"]);
$searchpref   = urldecode(@$_GET["searchpref"]);
$searchword = urldecode(@$_GET["searchword"]);
$dsps  = @$_GET["dsps"];//表示・非表示
$page = @$_GET["page"];//ページ数
//ＰＯＳＴ
$submit_search = @$_POST["Submit_search"];
$reset         = @$_POST["reset"];
if (!$category){$category = @$_POST["category"];}
if (!$searchpref){$searchpref = @$_POST["searchpref"];}
if (!$searchword){$searchword = @$_POST["searchword"];}
//ダイレクトリクエスト
if (file_exists($file_link)){$dir = "";$direct = 1;}
//カテゴリーリスト
$word_list = read_file($dir.$file_category);
if ($word_list){array_unshift($word_list,"プラン名で絞る");}else{$word_list[]="";}
if ($category == $word_list[0]){$category = "";}
//都道府県リスト
$word_list2 = read_file($dir.$file_pref);
if ($word_list2){array_unshift($word_list2,"都道府県で絞る");}else{$word_list2[]="";}
if ($searchpref == $word_list2[0]){$searchpref = "";}

//タグ
define ("allow_tag","<b><u><i>");
//--------------------------------------------------------------------------------------メイン処理
//検索のリセット
if ($reset){$submit_search = "";$category = "";$searchpref = "";$searchword = "";}
//ユーザーファイルの読み込み//ページ当りの表示数
//表示件数の設定
$line_custom = read_custom("data/custom.dat");//ユーザーファイルの読み込み
if (@!$line_custom['page_cnt']){$line_custom['page_cnt'] = 0;}
define ("dsp_cnt",$line_custom['page_cnt']);//表示件数/page 『0』の場合は全件表示

//$line_custom = read_custom();
$dspcount = @$line_custom['dspsu'];//件/ページ
if (!$dspcount){$dspcount = 100;}
if (!$dsp){$dsp = $line_custom['firstdsp_dsp'];}//初期表示
//ファイル読込み
$line_link = read_file($dir.$file_link);

//非表示のチェック 2013/11/20追加
$all = 0;
$temp_line = "";
$check = "";
if ($line_link){
	foreach ($line_link as $value){
		list(,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,$dsp) = explode("&&",$value);
		if (!$dsp){
			$temp_line[] = $value; 
			$check = "true";
			$all++;
		}
	}
	$line_link = $temp_line;
}

//サーチ処理
if ($category || $searchpref || $searchword){$line_link = search_data($line_link,$category,$searchpref,$searchword);}
//表示配列の開始、終了位置
dsp_startend($dspcount,$line_link);
//ＨＴＭＬ
if (@$direct){html_linkheader1();html_linkheader2();}
html_linkmain($line_link);
if (@$direct){html_linkfooter();}
//--------------------------------------------------------------------------------------関数定義
///////////////////////////////////
// サーチ処理                    //
///////////////////////////////////
function search_data($line,$category,$searchpref,$word){
	//カテゴリーで抽出
	if ($category && $line){
		$new_line = "";
		foreach ($line as $value){
			list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop) = explode("&&",$value);
			if ($category == $plan){$new_line[] = $value;}
		}
		$line = $new_line;
	}

	//都道府県で抽出
	if ($searchpref && $line){
		$new_line = "";
		foreach ($line as $value){
			list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop) = explode("&&",$value);
			if ($searchpref == $pref){$new_line[] = $value;}
		}
		$line = $new_line;
	}

	//キーワードで抽出
	if ($word && $line){
		$word = preg_replace("/　/"," ",$word);
		$word = trim($word);
//		if (extension_loaded("mbstring")){$word = mb_convert_store($word,"aKCV");$word = strtoupper($word);}
		if (preg_match("/ /i", $word)){$word_array = explode(" ",$word);}else{$word_array[] = $word;}
		$new_line = "";
		foreach ($line as $value){
			list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop) = explode("&&",$value);
			$str = $account.$store.$customer.$addr.$customer_id;
//			if (extension_loaded("mbstring")){
//				$str = mb_convert_store($str,"aKCV");
//				$str = strtoupper($str);
//			}
			$sword = "";
			foreach($word_array as $key => $value2){
				if($value2){
					if ($key){$sword .= "(.*".$value2.".*)";}else{$sword .= "(.*".$value2.".*)";}
				}
			}
			if (preg_match("/$sword/i", $str)){$new_line[] = $value;}
		}
		$line = $new_line;
	}
	return $line;
}
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
/////////////////////////////////
// テキスト出力整形            //
/////////////////////////////////
function fix_text($str){
	$str = preg_replace("/<br>/","\n",$str);
	$str = strip_tags($str,allow_tag);
	$str = preg_replace("/(style|onmouse|onclick)[^=]*=/i", "", $str);
	$str = preg_replace("/\r|\n|\r\n/","<br>",$str);
	//リンク、メール置き換え
	if(preg_match_all("/http\:\/\/[\w\.\~\-\/\?\&\+\=\:\@\%\#]+/",$str,$match)){
		foreach ($match[0] as $value){
			if(strlen($value) > 60){ $dsp_url = "URL";}else{$dsp_url = $value;}
			$str = preg_replace("/$value/","<a href='".$value."' target='_blank'>\n".$dsp_url."\n</a>",$str);
		}
	}
	if(preg_match_all("/[\w\d\-\.]+\@[\w\d\-\.]+/",$str,$match)){
		foreach ($match[0] as $value){
			if(strlen($value) > 60){ $dsp_url = "MAIL";}else{$dsp_url = $value;}
			$str = preg_replace("/$value/","<a href='mailto:".$value."'>\n".$dsp_url."\n</a>",$str);
		}
	}
	//
	if (!$str){$str = "&nbsp;";}
	return $str;
}
///////////////////////////////////
// ユーザーファイルの読み込み    //
///////////////////////////////////
function read_custom(){
	global $file_custom,$dir;
	$line_custom = "";
	$temp_line = file($dir.$file_custom);
	foreach ($temp_line as $value){
		if ($value){
			$value = trim($value);
			list($i,$j) = explode("&&",$value);
			$line_custom[$i] = $j;
		}
	}
	return $line_custom;
}
/////////////////////////////////////
// 表示配列の開始、終了位置        //
/////////////////////////////////////
function dsp_startend($dspcount,$dat_line){
	global $page,$all,$amari;
	global $start,$end,$psu;
	$all = count($dat_line);//総件数
	//総ページ数
	$psu = @floor($all / $dspcount);
	$amari = $all % $dspcount;
	if ($amari){$psu++;}
	//不正処理調整
	if ($page < 0){$page = 0;}
	if ($page >= $psu){$page = $psu - 1;}
	//開始、終了位置の確定
	if (!$dspcount){//『0』の場合は全件表示
		$start=0;
		$end = count($dat_line) - 1;
	}else{
		//開始配列位置
		$start = $page * $dspcount;
		//終了配列位置
		$end = $page * $dspcount + $dspcount - 1;
		if ($all <= $end){$end = $all - 1;}
	}
}
//--------------------------------------------------------------------------------------関数ＨＴＭＬ
///////////////////////////////////
// ページリンクテキストの表示    //
///////////////////////////////////
function html_pagelink($dspcount,$dat_line){
	global $pageurl,$page,$all,$amari,$start,$end,$psu;
	global $category,$searchname,$searchword,$dsp;
	$pageurl = $pageurl."dsp=".$dsps."&";
	$all = count($dat_line);//総件数
//	if ($dspcount){//『0』の場合は非表示
		//ページリンクの表示
		if ($dat_line && $psu >= 1){
			echo "<div class='pagenavi'>"."\n";
			echo "<div class='pagetext'>"."\n";
			echo $all." 件中 ".($start+1)." 〜 ".($end+1)." 件を表示";
			echo "</div>"."\n";

	if ($dat_line && $psu > 1){
			//ページリンク（[1] [2] [3]・・・）の表示は
			$link_limit = 7;//常に7個以下にする
			$go = ($page+1) - ceil($link_limit/2);
			if ($go < 0){$go = 0;}
			$stop = $go + $link_limit;
			if ($stop > $psu){
				$stop = $psu;
				$go = $stop - $link_limit;
				if ($go < 0){$go = 0;}
			}
			//
			echo "<table><tr>"."\n";
			echo "<tr>"."\n";
			//PREV
			echo "<td nowrap>"."\n";
			if ($page > 0){
				if (($page -1) == 0){
					echo " <a href='".$pageurl."' class='btn btn-default pagecnt'>&lt; 前の".$dspcount."件</a>"."\n";
				}else{
					echo " <a href='".$pageurl."page=".($page - 1)."' class='btn btn-default pagecnt'>&lt; 前の".$dspcount."件</a>"."\n";
				}
			}else{
				echo "&nbsp;"."\n";
			}
			echo "</td>"."\n";
			//ページリンク表示

			echo "<td><ul class='pagination'>"."\n";
			for ($i=$go; $i<$stop; $i++){
				if ($i == $page){
					echo " <li class='active'><a href='#'>".($i+1)."</a></li> ";
				}else{
					if ($i){
						echo " <li><a href='".$pageurl."page=".$i."'>".($i+1)."</a></li> ";
					}else{
						echo " <li><a href='".$pageurl."'>".($i+1)."</a></li> ";
					}
				}
			}
			echo "</ul>"."\n";
			//NEXT
			echo "</td><td nowrap>"."\n";
			if (($page+1) < $psu){
				if (($page+2) == $psu && $amari){
					echo "<a href='".$pageurl."page=".($page + 1)."' class='btn btn-default pagecnt'>次の".$amari."件 &gt;</a> "."\n";
				}else{
					echo "<a href='".$pageurl."page=".($page + 1)."' class='btn btn-default pagecnt'>次の".$dspcount."件 &gt;</a> "."\n";
				}
			}else{
				echo "&nbsp;"."\n";
			}
			echo "</td>"."\n";
			//
			echo "</tr></table>"."\n";
			}
			echo "</div>"."\n";
		}
//	}
}
///////////////////////////////////
// ＨＴＭＬヘッダー(1)            //
///////////////////////////////////
function html_linkheader1(){
	global $html_title;
//-------------------------------------------------------↓
print <<< END_OF_HTML
<!DOCTYPE HTML>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="apple-mobile-web-app-title" content="ユーザー管理" />
	<link href="assets/images/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet" media="screen">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<title>$html_title</title>
<meta name="description" content="ユーザー管理システム">\n
END_OF_HTML;
//-------------------------------------------------------↑
}
///////////////////////////////////
// ＨＴＭＬヘッダー(2)            //
///////////////////////////////////
function html_linkheader2(){
//-------------------------------------------------------↓
print <<< END_OF_HTML

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
        </ul>
		<div class="navbar-news"><a href="$url/news" class="fancybox fancybox.iframe" target="_blank"><span class="glyphicon glyphicon-info-sign"></span></a><!--<a href="admin.php" class=""><span class="glyphicon glyphicon-cog"></span></a>--></div>
      </div>
    </div>
  </div>
</header>

    <div class="container">

END_OF_HTML;
//-------------------------------------------------------↑
}
///////////////////////////////////
// ＨＴＭＬメイン                //
///////////////////////////////////
function html_linkmain($line_link){
	global $url,$page,$pageurl,$dir,$out_url,$dsps,$word_list,$word_list2,$category,$searchpref,$searchword,$dspcount;
	global $dspcount,$bannersize,$wsize,$start,$end,$direct;
	//ＵＲＬの設定
	$pageurl = $url."?";
	if ($category || $searchpref || $searchword){
		if ($category){$pageurl .= "category=".urlencode($category)."&";}
		if ($searchpref){$pageurl .= "category=".urlencode($searchpref)."&";}
		if ($searchword){$pageurl .= "searchword=".urlencode($searchword)."&";}
	}
	if ($page){$churl = $pageurl."page=".$page."&";}else{$churl = $pageurl;}


if (count($word_list) > 1){
	echo "	<form name='form' method='post' action='".$url."' class='search_form' role='search'>"."\n";
	echo "    <div class='form-group index_search'>"."\n";
	echo "      <select name='category' class='form-control select_form'>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($category == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";

	echo "      <select name='searchpref' class='form-control select_form'>"."\n";
	foreach ($word_list2 as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($searchpref == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";

//-------------------------------------------------------↓
print <<< END_OF_HTML
      <input name="searchword" type="text" class="form-searchword form-control" value="$searchword" size="20" placeholder="キーワード">
    <input name="Submit_search" type="submit" class="btn btn-primary" value="検索">\n
END_OF_HTML;
//-------------------------------------------------------↑
}

if ($category || $searchpref || $searchword){
	echo " <input name='reset' type='submit' class='btn btn-default' value='Reset'>"."\n";
}

if ($category || $searchpref || $searchword){
		echo "	<div class='form-group search_result'><strong>検索結果</strong>　"."\n";
	$result_txt = "";
	if ($category){$result_txt .= "プラン： <b style='color: #CC0000;'>".$category."</b> ";}
	if($category <> "") {
		if ($searchpref){$result_txt .= "＆ 都道府県： <b style='color: #CC0000;'>".$searchpref."</b> ";}
	} elseif ($searchpref){$result_txt .= " 都道府県： <b style='color: #CC0000;'>".$searchpref."</b> ";}

	if(($category || $searchpref || $searchpref) <> "") {
		if ($searchword){$result_txt .= "＆ 検索語： <b style='color: #CC0000;'>".$searchword."</b> ";}
	} elseif ($searchword){$result_txt .= " 検索語： <b style='color: #CC0000;'>".$searchword."</b> ";}
	if ($line_link){$recnt = count($line_link);}else{$recnt=0;}
	$result_txt .= " <span class='badge'>".$recnt."</span>";
	echo "".$result_txt.""."\n";
//	echo "<a href =javascript:history.back()>&lt;&lt;戻る</a>"."\n";

	echo " </div>"."\n";
	echo " </form>"."\n";

}
	echo " <div class='clear'></div>"."\n";



//ページリンク
html_pagelink($dspcount,$line_link);
//登録情報表示
//	echo "<h5>".$plan." (".$company.")</h5>"."\n";

	echo "<div class='list_tbl'>"."\n";
		echo "<table class='table table-striped table-hover'>"."\n";

		echo "<thead>"."\n";
		echo "<tr>"."\n";
            echo "<th>プラン/ID</th>"."\n";
            echo "<th class='bottle_tbl'>店舗/契約ID</th>"."\n";
//            echo "<th class='tab_none'>会社名</th>"."\n";
            echo "<th>状況/更新日時</th>"."\n";
            echo "<th class='sp_none'>住所/担当者</th>"."\n";
//             echo "<th>電話/メール</th>"."\n";
            echo "<th class='tab_none date_tbl'>登録日/更新日</th>"."\n";
//            echo "<th class='tab_side_none'>宛</th>"."\n";
//            echo "<th class='sp_none'>DM</th>"."\n";
//            echo "<th>編</th>"."\n";
        echo "</tr>"."\n";
		echo "</thead>"."\n";
		echo " <tbody>"."\n";

if ($line_link){
	for ($i=$start; $i<=$end; $i++){
		list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop) = explode("&&",$line_link[$i]);
	if (!$dsps){
		$keyword = "";
		if ($plan){$keyword .= "[ ".$plan." ]";}
	
	
	//タイトル整形
	$plan = fix_text($plan);
	$account = fix_text($account);

		echo " <tr>"."\n";

		//プラン
		echo "<td><a href='https://nightworks.dewey.co.jp/$account' target='_blank'><span class='plan_name'>".$plan."</span>"."\n";
		echo "<br /><span class='small'>$account</a></span></a>"."\n";
		if ($monitor){
		echo "<span class='small'>（<strong>モニター</strong>）</span>"."\n";
		}
		if ($stop){
		echo "<span class='small'>（<strong><span class='hissu'>停止中</span></strong>）</span>"."\n";
		}
		echo "</td>"."\n";

		//店舗名・契約者
		echo "<td><a href='detail.php?num=$count'><span class='plan_name'>".$store."</span>"."\n";
		echo "<br /><span class='small'>".$pref."</span><span class='small'>（".$customer_id."）</span></a>"."\n";
		echo "</td>"."\n";

		//登録数ログデータファイル読み込み
		$data_file = "../$account/data/data.dat";
		$fd= file($data_file);
		$data_count= sizeof($fd);
		echo "<td nowrap>"."\n";
		echo "$data_count 件"."\n";
		echo " / $plan_limit 件"."\n";
		echo "<br />"."\n";
		echo "<span class='small'>"."\n";
if (file_exists($data_file)) {
    echo "" . date ("Y/m/d H:i:s", filemtime($data_file));
}
		echo "</span></td>"."\n";

		//住所
		echo "<td class='sp_none'><span class='small'>".$pref."".$addr."<br />"."\n";
		echo "".$customer."</span></td>"."\n";

		//メール
// 		echo "<td nowrap><span class='small'>".$phone."<br /><a href='mailto:$email'>".$email."</a></span>"."\n";
// 		echo "</td>"."\n";

		//登録日
		$firsttime = fix_text($firsttime);
		//更新日
		$lasttime = fix_text($lasttime);
		echo "<td class='tab_none small acenter'><p>".$firsttime."</p><p>".$lasttime."</p></td>"."\n";
//		echo "<td class='small acenter sp_none date_tbl'>".$lasttime."</td>"."\n";


		//宛名
// 		if($zip <> '') {
// 			echo "<td class='acenter tab_side_none'><a href='atena.php?num=$count'>〒</a></td>"."\n";
// 		} else {
// 
// 			echo "<td class='acenter tab_side_none'>&nbsp;</td>"."\n";
// 		}

		//DM可否
//		if($no_dm <> ""){
//		echo "<td class='sp_none'>NG</td>"."\n";
//		}else{
//		echo "<td class='sp_none'>&nbsp;</td>"."\n";
//		}

		//編集ページリンク
//		echo "<td class='acenter small'><a href='regist.php?cnt=$count'>■</a></td>"."\n";


		echo "</tr>"."\n";

			}
		}
		echo "</tbody>"."\n";
		echo "</table>"."\n";
		echo "</div>"."\n";
}else{

//html_linkheader1();html_linkheader2();
//-------------------------------------------------------↓
print <<< END_OF_HTML
</table>
        <div class="bs-component">
          <div class="jumbotron">
            <h2 class="alart_message">登録データはありません</h2>
          </div>
        </div>
END_OF_HTML;
//-------------------------------------------------------↑

	//フッター
//	html_linkfooter();
}
	//ページリンク
	html_pagelink($dspcount,$line_link);

}


///////////////////////////////////
// ＨＴＭＬフッター              //
///////////////////////////////////
function html_linkfooter(){
//-------------------------------------------------------↓
print <<< END_OF_HTML
<p class="pageup"><a href="#">▲</a></p>
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

}
?>