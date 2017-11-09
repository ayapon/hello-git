<?php
session_start();
///////////////////////////////////////////////////////
// DEWEY 顧客・ボトル管理
// 2015/08
///////////////////////////////////////////////////////
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');
include_once("attestation.php");
require("common.php");
require("../bottle_assets/pageparts.php");
$html_title = "顧客・ボトル管理システム";  //ページタイトル
//--------------------------------------------------------------------------------------初期設定
//$dir        = "stock/";//スクリプトフォルダ名
$file_link = "data/data.dat";
$file_regist = "data/regist_dat/";
$file_category = "config/category.dat";
$file_custom = "data/custom.dat";
$this_file = $_SERVER['SCRIPT_NAME'];
//ＧＥＴ
$category   = urldecode(@$_GET["category"]);
$searchname   = urldecode(@$_GET["searchname"]);
$searchword = urldecode(@$_GET["searchword"]);
$dsps  = @$_GET["dsps"];//表示・非表示
$page = @$_GET["page"];//ページ数
//ＰＯＳＴ
$submit_search = @$_POST["Submit_search"];
$reset         = @$_POST["reset"];
if (!$category){$category = @$_POST["category"];}
if (!$searchname){$searchname = @$_POST["searchname"];}
if (!$searchword){$searchword = @$_POST["searchword"];}
//ダイレクトリクエスト
if (file_exists($file_link)){$dir = "";$direct = 1;}
//カテゴリーリスト
$word_list = read_file($dir.$file_category);
if ($word_list){array_unshift($word_list,"ボトル名で絞る");}else{$word_list[]="";}
if ($category == $word_list[0]){$category = "";}

//タグ
define ("allow_tag","<b><u><i>");
//--------------------------------------------------------------------------------------メイン処理
//検索のリセット
if ($reset){$submit_search = "";$category = "";$searchname = "";$searchword = "";}
//ユーザーファイルの読み込み//ページ当りの表示数
//表示件数の設定
// $line_custom = read_custom("data/custom.dat");//ユーザーファイルの読み込み
// if (@!$line_custom['page_cnt']){$line_custom['page_cnt'] = 0;}
// define ("dsp_cnt",$line_custom['page_cnt']);//表示件数/page 『0』の場合は全件表示

//$line_custom = read_custom();
 $dspcount = @$line_custom['dspsu'];//件/ページ
 if (!$dspcount){$dspcount = 100;}
 if (!$dsp){$dsp = $line_custom['firstdsp_dsp'];}//初期表示
//ファイル読込み
$line_link = read_file($dir.$file_link);

//サーチ処理
if ($category || $searchname || $searchword){$line_link = search_data($line_link,$category,$searchname,$searchword);}
//表示配列の開始、終了位置
dsp_startend($dspcount,$line_link);
//ＨＴＭＬ
if (@$direct){html_header1();html_header2();}
html_linkmain($line_link);
if (@$direct){html_footer();}
//--------------------------------------------------------------------------------------関数定義
///////////////////////////////////
// サーチ処理                    //
///////////////////////////////////
function search_data($line,$category_search,$name_search,$word){
	//カテゴリーで抽出
	if ($category_search && $line){
		$category_search = preg_replace("/　/"," ",$category_search);
		$category_search = trim($category_search);
//		if (extension_loaded("mbstring")){$word = mb_convert_kana($word,"aKCV");$word = strtoupper($word);}
		if (preg_match("/ /i", $category_search)){$category_search_array = explode(" ",$category_search);}else{$category_search_array[] = $category_search;}
		$new_line = "";
		foreach ($line as $value){
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
			$str = $bottle_0.$bottle_1.$bottle_2.$bottle_3.$bottle_4.$otherbottle;
			$scategory_search = "";
			foreach($category_search_array as $key => $value2){
				if($value2){
					if ($key){$scategory_search .= "(.*".$value2.".*)";}else{$scategory_search .= "(.*".$value2.".*)";}
				}
			}
			if (preg_match("/$scategory_search/i", $str)){$new_line[] = $value;}
		}
		$line = $new_line;
	}


	//お名前で抽出
	if ($name_search && $line){
		$name_search = preg_replace("/　/"," ",$name_search);
		$name_search = trim($name_search);
//		if (extension_loaded("mbstring")){$word = mb_convert_kana($word,"aKCV");$word = strtoupper($word);}
		if (preg_match("/ /i", $name_search)){$name_search_array = explode(" ",$name_search);}else{$name_search_array[] = $name_search;}
		$new_line = "";
		foreach ($line as $value){
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
			$str = $customer.$kana;
// 			if (extension_loaded("mbstring")){
// 				$str = mb_convert_kana($str,"aKCV");
// 				$str = strtoupper($str);
// 			}
			$sname_search = "";
			foreach($name_search_array as $key => $value2){
				if($value2){
					if ($key){$sname_search .= "(.*".$value2.".*)";}else{$sname_search .= "(.*".$value2.".*)";}
				}
			}
			if (preg_match("/$sname_search/i", $str)){$new_line[] = $value;}
		}
		$line = $new_line;
	}

	//キーワードで抽出
	if ($word && $line){
		$word = preg_replace("/　/"," ",$word);
		$word = trim($word);
//		if (extension_loaded("mbstring")){$word = mb_convert_kana($word,"aKCV");$word = strtoupper($word);}
		if (preg_match("/ /i", $word)){$word_array = explode(" ",$word);}else{$word_array[] = $word;}
		$new_line = "";
		foreach ($line as $value){
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
			$str = $company.$phone.$memo.$pic.$otherbottle.$friends_0.$friends_1.$friends_2.$friends_3.$friends_4.$friends_5.$friends_6.$friends_7.$friends_8.$friends_9;
// 			if (extension_loaded("mbstring")){
// 				$str = mb_convert_kana($str,"aKCV");
// 				$str = strtoupper($str);
// 			}
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
	global $category,$searchname,$searchword;
	$pageurl = $pageurl."dsp=".$dsps."&";
	$all = count($dat_line);//総件数
	if ($dspcount){//『0』の場合は非表示
		//ページリンクの表示
		if ($dat_line && $psu > 1){
			echo "<div class='pagenavi'>"."\n";
			echo "<div class='pagetext'>"."\n";
			echo $all." 件中 ".($start+1)." 〜 ".($end+1)." 件を表示";
			echo "</div>"."\n";
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
			echo "<table>"."\n";
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
			echo "</div>"."\n";
		}
	}
}

///////////////////////////////////
// ＨＴＭＬメイン                //
///////////////////////////////////
function html_linkmain($line_link){
	global $this_file,$page,$pageurl,$dir,$out_url,$dsps,$word_list,$word_list2,$category,$searchname,$searchword,$dspcount;
	global $start,$end,$direct,$company_logs,$addr_logs,$birthday_logs,$pic_logs,$phone_logs,$mobile_logs,$lastvisit_logs,$friends_logs,$dspimg_logs;
	//ＵＲＬの設定
	$pageurl = $this_file."?";
	if ($category || $searchname || $searchword){
		if ($category){$pageurl .= "category=".urlencode($category)."&";}
		if ($searchname){$pageurl .= "searchname=".urlencode($searchname)."&";}
		if ($searchword){$pageurl .= "searchword=".urlencode($searchword)."&";}
	}
	if ($page){$churl = $pageurl."page=".$page."&";}else{$churl = $pageurl;}


if (count($word_list) > 1){
	echo "	<form name='form' method='post' action='".$this_file."' class='search_form' role='search'>"."\n";
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


//-------------------------------------------------------↓
print <<< END_OF_HTML
      <input name="searchname" type="text" class="form-searchword form-control ClearX" value="$searchname" size="20" placeholder="お名前">
      <input name="searchword" type="text" class="form-searchword form-control ClearX" value="$searchword" size="20" placeholder="キーワード">
    <input name="Submit_search" type="submit" class="btn btn-primary" value="検索">\n
END_OF_HTML;
//-------------------------------------------------------↑
}

//if ($category || $searchname || $searchword){
	echo " <input name='reset' type='submit' class='btn btn-default re' value='Reset'>"."\n";
//}

if ($category || $searchname || $searchword){
		echo "	<div class='form-group search_result'><strong>検索結果</strong>　"."\n";
	$result_txt = "";
	if ($category){$result_txt .= "ボトル： <b style='color: #CC0000;'>".$category."</b> ";}
	if($category <> "") {
		if ($searchname){$result_txt .= "＆ お名前： <b style='color: #CC0000;'>".$searchname."</b> ";}
	} elseif ($searchname){$result_txt .= " お名前： <b style='color: #CC0000;'>".$searchname."</b> ";}

	if(($category || $searchname) <> "") {
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
//	echo "<h5>".$customer." (".$company.")</h5>"."\n";

	echo "<div class='list_tbl'>"."\n";
		echo "<table class='table table-striped table-hover'>"."\n";
		echo "<thead>"."\n";
		echo "<tr>"."\n";
if ($dspimg_logs <> ""){ //画像登録オンオフ
            echo "<th class='data_img'>画像</th>"."\n";
}
            echo "<th>お名前</th>"."\n";
            echo "<th class='bottle_tbl'>ボトル</th>"."\n";
if ($company_logs <> ""){ //会社記録オンオフ
            echo "<th class='tab_none data_company'>会社名</th>"."\n";
}
            echo "<th class='sp_none'>メモ</th>"."\n";
//            echo "<th>誕生日</th>"."\n";
if ($pic_logs <> ""){ //担当者記録オンオフ
            echo "<th class='data_pic'>担当</th>"."\n";
}
//            echo "<th class='sp_none date_tbl'>更新日</th>"."\n";
if ($lastvisit_logs <> ""){ //最終来店日記録オンオフ
            echo "<th class='sp_none date_tbl'>来店日</th>"."\n";
} else {
            echo "<th class='sp_none date_tbl'>更新日</th>"."\n";
}
if ($addr_logs <> ""){ //住所記録オンオフ
            echo "<th class='tab_side_none data_addr'>宛</th>"."\n";
}

//            echo "<th class='sp_none'>DM</th>"."\n";
//            echo "<th>編</th>"."\n";
        echo "</tr>"."\n";
		echo "</thead>"."\n";
		echo " <tbody>"."\n";

//ループスタート
if ($line_link){
	for ($i=$start; $i<=$end; $i++){
		list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$line_link[$i]);
	if (!$dsps){
		$keyword = "";
		if ($bottle_0){$keyword .= "[ ".$bottle_0." ]";}
	
		echo " <tr>"."\n";
if ($dspimg_logs <> ""){ //画像登録オンオフ
	if ($fname) {
            echo "<td class='data_img_thum'><a href='".$fname."' class='fancybox'><img src='".$fname."'></a></td>"."\n";
	} else {
            echo "<td class='data_img_thum'><img src='$url/bottle_assets/images/img_thum.png'></td>"."\n";
	}
}

	//タイトル整形
	$customer = fix_text($customer);
	$company = fix_text($company);
		//お名前
			//長い文字列は20バイト以降を消去し"..."を付加する
			$string = mb_strimwidth( $customer, 0, 50, "...", "UTF-8" );
		echo "<td class='name_td'><a href='details.php?cnt=$count'><div class='name_btn'><strong>".$string."</strong>"."\n";

	//今月の誕生日にマーク
		$nowm = date("m");
		if($nowm == $birthmonth) {
			echo " <span class='birthday' title='誕生月'>&nbsp;</span>";
		}

			//長い文字列は20バイト以降を消去し"..."を付加する
			$string = mb_strimwidth( $kana, 0, 50, "...", "UTF-8" );
		echo "<br /><span class='x-small'>".$string."</span></div></a></td>"."\n";


		//ボトル
		echo "<td class='bottle_tbl'>"."\n";
	if($bottle_0 <> '') {
		$bottle_0 = fix_text($bottle_0);
		echo "$bottle_0"."\n";
		if($bottle_num_0 <> '') {
// 			$bottle_num_0 = fix_text($bottle_num_0);
			echo " [".$bottle_num_0."] "."\n";
		}
		if($bottle_quant_0 <> '') {
// 			$bottle_quant_0 = fix_text($bottle_quant_0);
			echo " (".$bottle_quant_0.") "."\n";
		}
//		echo "</a>"."\n";
	}

	if($bottle_1 <> '') {
		$bottle_1 = fix_text($bottle_1);
		echo "$bottle_1"."\n";
		if($bottle_num_1 <> '') {
// 			$bottle_num_1 = fix_text($bottle_num_1);
			echo " [".$bottle_num_1."] "."\n";
		}
		if($bottle_quant_1 <> '') {
// 			$bottle_quant_1 = fix_text($bottle_quant_1);
			echo " (".$bottle_quant_1.") "."\n";
		}
//		echo "</a>"."\n";
	}

	if($bottle_2 <> '') {
		$bottle_2 = fix_text($bottle_2);
		echo "$bottle_2"."\n";
		if($bottle_num_2 <> '') {
// 			$bottle_num_2 = fix_text($bottle_num_2);
			echo " [".$bottle_num_0."] "."\n";
		}
		if($bottle_quant_2 <> '') {
// 			$bottle_quant_2 = fix_text($bottle_quant_2);
			echo " (".$bottle_quant_2.") "."\n";
		}
//		echo "</a>"."\n";
	}

	if($bottle_3 <> '') {
		$bottle_3 = fix_text($bottle_3);
		echo "$bottle_3"."\n";
		if($bottle_num_3 <> '') {
// 			$bottle_num_3 = fix_text($bottle_num_3);
			echo " [".$bottle_num_3."] "."\n";
		}
		if($bottle_quant_3 <> '') {
// 			$bottle_quant_3 = fix_text($bottle_quant_3);
			echo " (".$bottle_quant_3.") "."\n";
		}
//		echo "</a>"."\n";
	}

	if($bottle_4 <> '') {
		$bottle_4 = fix_text($bottle_4);
		echo "$bottle_4"."\n";
		if($bottle_num_4 <> '') {
// 			$bottle_num_4 = fix_text($bottle_num_4);
			echo " [".$bottle_num_4."] "."\n";
		}
		if($bottle_quant_4 <> '') {
// 			$bottle_quant_4 = fix_text($bottle_quant_4);
			echo " (".$bottle_quant_4.") "."\n";
		}
//		echo "</a>"."\n";
	}

	if($otherbottle <> '') {
		$otherbottle = fix_text($otherbottle);
		echo " $otherbottle"."\n";
		if($otherbottle_num <> '') {
// 			$otherbottle_num = fix_text($otherbottle_num);
			echo " [".$otherbottle_num."] "."\n";
		}
		if($otherbottle_quant <> '') {
// 			$otherbottle_quant = fix_text($otherbottle_quant);
			echo " (".$otherbottle_quant.") "."\n";
		}
//		echo "</a>"."\n";
	}
		echo "</td>"."\n";

		//会社名
if ($company_logs <> ""){ //会社記録オンオフ
		$company = fix_text($company);
			//長い文字列は20バイト以降を消去し"..."を付加する
			$string = mb_strimwidth( $company, 0, 40, "...", "UTF-8" );
		//役職
		$degree = fix_text($degree);
		echo "<td class='tab_none data_company'>".$string."<br /><span class='small'>".$degree."</span></td>"."\n";
}
		//メモ
		//$memo = fix_text($memo);
			$memo = str_replace("<br>", "", $memo);
			//長い文字列は20バイト以降を消去し"..."を付加する
			$string = mb_strimwidth( $memo, 0, 40, "...", "UTF-8" );
		echo "<td class='sp_none data_memo'>".$string."</td>"."\n";

		//年齢計算
//		$now = date("Ymd"); 
//		$birth = "$birthyear.$birthmonth.$birthday"; 
//		$howold = floor(($now-$birth)/10000);

		//誕生日
//		echo "<td class='acenter'>";
//		if($birthmonth <> ""){
//		echo "$birthmonth";
//		}else{
//			echo "&nbsp;";
//		}
//		if($birthday <> ""){
//		echo "/".$birthday."\n";
//		}else{
//			echo "&nbsp;";
//		}
//		if($birthyear <> ""){
//		echo "<span>($howold)</span>";
//		}

		//担当
if ($pic_logs <> ""){ //担当者記録オンオフ
		$pic = fix_text($pic);
		echo "<td class='pic_tbl data_pic'>".$pic."</td>"."\n";
}
		//登録日
//		$firsttime = fix_text($firsttime);
		//更新日
//		$lasttime = fix_text($lasttime);
//		echo "<td class='small acenter'><p>".$firsttime."</p><p>".$lasttime."</p></td>"."\n";
//		echo "<td class='small sp_none date_tbl'>".$lasttime."</td>"."\n";

		//最終来店日
if ($lastvisit_logs <> ""){ //最終来店日記録オンオフ
		$lastvisit = fix_text($lastvisit);
		echo "<td class='small sp_none date_tbl'>".$lastvisit."</td>"."\n";
} else {
		//更新日
		$lasttime = fix_text($lasttime);
		$cuttime = 6;
		$lastdate = substr( $lasttime , 0 , strlen($lasttime)-$cuttime );
		echo "<td class='small sp_none date_tbl'>".$lastdate."</td>"."\n";
}
		//宛名
if ($addr_logs <> ""){ //住所記録オンオフ
		if($zip <> '') {
			echo "<td class='acenter tab_side_none data_addr'><a href='atena.php?num=$count'>〒</a></td>"."\n";
		} else {

			echo "<td class='acenter tab_side_none data_addr'>&nbsp;</td>"."\n";
		}
}

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
//ループエンド

		echo "</tbody>"."\n";
		echo "</table>"."\n";
if ($category || $searchname || $searchword){
	echo "	<a href='./' class='btn btn-primary historyback'>&lt; 前画面に戻る</a>"."\n";
}

}else{

//-------------------------------------------------------↓
print <<< END_OF_HTML
</table>
        <div class="bs-component">
          <div class="jumbotron">
            <h2 class="alart_message">登録データはありません</h2>
          </div>
        </div>
END_OF_HTML;
if ($category || $searchname || $searchword){
	echo "	<a href='./' class='btn btn-primary historyback'>&lt; 前画面に戻る</a>"."\n";
}
//-------------------------------------------------------↑

}
	//ページリンク
	html_pagelink($dspcount,$line_link);
	echo "<p class=\"pageup\"><a href=\"#\">▲</a></p>";
}


?>