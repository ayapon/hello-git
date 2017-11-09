<?php
///////////////////////////////////
// 情報一覧                      //
///////////////////////////////////
//セッション開始
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

include_once("attestation.php"); 
require("common.php");
require("../bottle_assets/pageparts.php");
$html_title = "並び替え・削除丨顧客・ボトル管理システム";  //ページタイトル
//--------------------------------------------------------------------------------------初期設定
//$this_file = $_SERVER['SCRIPT_NAME'];
$this_file = "sequence.php";
define ("file_sdata","data/data.dat");
define ("file_count","data/count.dat");
define ("allow_tag","");
//ＧＥＴ受信
//$sw = @$_GET["sw"];//画像表示切替え
//ＰＯＳＴ受信
$shift_down  = @$_POST["shift_down"];
$shift_up    = @$_POST["shift_up"];
$fix         = @$_POST["fix"];
$delete_conf = @$_POST["delete_conf"];
$delete      = @$_POST["delete"];
$regist_pic  = @$_POST["regist_pic"];
$radio       = @$_POST["radio"];
//メッセージ初期
$message = "";
$message_error = "";
//--------------------------------------------------------------------------------------メイン処理
//初回セッション
//if (!isset($_SESSION["switch"])){
//		$_SESSION["switch"] = "on";
//}
//セッション（画像切り替え）
//$switch = $_SESSION["switch"];
//if ($sw){
//	if ($switch == "on"){
//		$_SESSION["switch"] = "off";
//		$switch = "off";
//	}else{
//		$_SESSION["switch"] = "on";
//		$switch = "on";
//	}
//}
//編集処理
if ($fix && $radio){
	echo "<meta http-equiv='refresh' content='0;url=regist.php?cnt=".$radio."'>"."\n";
	exit();
}
//編集処理
if ($regist_pic && $radio){
	echo "<meta http-equiv='refresh' content='0;url=regist_pic.php?cnt=".$radio."'>"."\n";
	exit();
}
//入替え(shift_down)
if ($shift_down && $radio){
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//不正確認
	foreach ($sdata_line as $key => $value){
		//文字列分割
		list($count,) = explode("&&",$value);
		if ($count == $radio){
			if (@$sdata_line[($key+1)]){
				$temp_line = $sdata_line[$key]; 
				$sdata_line[$key] = $sdata_line[($key+1)];
				$sdata_line[($key+1)] = $temp_line;
				$check = "true";
				$message = "選択データを入替えました";
			}
			break;
		}
	}
	//ファイルの書き換え
	if (@$check){
		$new_line = "";
		foreach ($sdata_line as $value){
			$new_line .= $value."\n";
		}
		$file = fopen(file_sdata,"w") or die(file_sdata." is not found");
		flock($file,LOCK_EX);
		fputs($file,$new_line);
		flock($file,LOCK_UN);
		fclose($file);
	}
}
//入替え(shift_up)
if ($shift_up && $radio){
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//不正確認
	foreach ($sdata_line as $key => $value){
		//文字列分割
		list($count,) = explode("&&",$value);
		if ($count == $radio){
			if (@$sdata_line[($key-1)]){
				$temp_line = $sdata_line[$key]; 
				$sdata_line[$key] = $sdata_line[($key-1)];
				$sdata_line[($key-1)] = $temp_line;
				$check = "true";
				$message = "選択データを入替えました";
			}
			break;
		}
	}
	//ファイルの書き換え
	if (@$check){
		$new_line = "";
		foreach ($sdata_line as $value){
			$new_line .= $value."\n";
		}
		$file = fopen(file_sdata,"w") or die(file_sdata." is not found");
		flock($file,LOCK_EX);
		fputs($file,$new_line);
		flock($file,LOCK_UN);
		fclose($file);
	}
}
//削除確認
if ($delete_conf && $radio){
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//不正確認
	foreach ($sdata_line as $value){
		//文字列分割
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$dsp,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
		if ($count == $radio){
			$message = "選択されたデータを削除します";
			html_header1();
			html_header2();
			html_delete_conf($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$dsp,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9);
			html_footer();
			exit();
		}
	}
}
//削除
if ($delete){
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//不正確認
	$new_line = "";
	foreach ($sdata_line as $value){
		//文字列分割
		list($count,) = explode("&&",$value);
		if ($count == $radio){
			$check = "true";
		}else{
			$new_line .= $value."\n";
		}
	}
	//ファイル書き換え
	if (@$check){
		$file = fopen(file_sdata,"w") or die(file_sdata." is not found");
		flock($file,LOCK_EX);
		fputs($file,$new_line);
		flock($file,LOCK_UN);
		fclose($file);
		$message = "選択されたデータを削除しました";
		//登録画像の削除
// 		$pic_line = read_pic($radio);//画像名ファイルの読み込み
// 		if ($pic_line){
// 			foreach ($pic_line as $value){
// 				list(,$picname) = explode("&&",$value);
// 				@unlink($picname);
// 			}
// 		}
		//画像名ファイルの削除
// 		@unlink("regist_dat/".$radio.".dat");
	}
}
//登録ファイルの読み込み
$sdata_line = read_data();
//ＨＴＭＬ
html_header1();
html_header2();
// html_header3();
html_list();
html_footer();
//--------------------------------------------------------------------------------------関数定義
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
// 画像名ファイルの読み込み      //
///////////////////////////////////
// function read_pic($cnt){
// 	$line = "";
// 	$temp_line = file("regist_dat/".$cnt.".dat");
// 	foreach ($temp_line as $value){
// 		if ($value){
// 			$value = trim($value);
// 			$line[] = $value;
// 		}
// 	}
// 	return $line;
// }
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
//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬヘッダー              //
///////////////////////////////////
// function html_header3(){
// 	global $this_file,$customer,$company,$delete_conf,$radio;
// 	global $message,$message_error;
// //-------------------------------------------------------↓
// print <<< END_OF_HTML
// 
//         <div class="bs-component">
//           <ul class="breadcrumb">
//             <li><a href='./index.php'>HOME</a></li>
//             <li><a href='./admin.php'>管理メニュー</a></li>
// 
// END_OF_HTML;
// //-------------------------------------------------------↑
// //文字列内のタグを取り除く
// $temp_title = strip_tags($company);
// $temp_title2 = strip_tags($customer);
// //長い文字列は40バイト以降を消去し"..."を付加する
// if(strlen($temp_title2) > 40){ $temp_title2 = substr($temp_title2,0,40)."...";}
// if ($delete_conf && $radio){
// 	echo "	<li><a href='sequence.php'>並び替え・削除</a></li><li class='active'>[".$temp_title2."：".$temp_title." ] の削除</li>"."\n";
// }else{	
// 	echo "	<li class='active'>並び替え・削除</li>"."\n";
// }
// //-------------------------------------------------------↓
// print <<< END_OF_HTML
// 		</ul>
// </div>
// 
// END_OF_HTML;
// //-------------------------------------------------------↑
// if ($message_error){
// 	echo "<h3 style='color:#cc0000;'>".$message_error."</h3>"."\n";
// }else{
// 	echo "<h3>".$message."</h3>"."\n";
// }
// 
// }
///////////////////////////////////
// ＨＴＭＬ削除確認              //
///////////////////////////////////
function html_delete_conf($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$dsp,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9){
	global $this_file,$case_line;
//-------------------------------------------------------↓
print <<< END_OF_HTML
<div class="list_tbl">
<form name="form" method="post" action="$this_file">
<table class="table table-striped table-hover">
  <tr>
      <th>お名前</th>
      <th>会社名</th>
      <th>担当者</th>
      <th class='small sp_none'>更新日</th>
  </tr>
END_OF_HTML;
//-------------------------------------------------------↑
	echo "    <tr>"."\n";
//	echo "      <td>".$count."</td>"."\n";
	//非表示
//	if ($dsp){
//		echo "      <td><b><font color='#FF0000'>非</font></b></td>"."\n";
//	}else{
//		echo "      <td>&nbsp;</td>"."\n";
//	}
	//お客様名
	$customer = fix_text($customer);
	echo "      <td><strong>".$customer."</strong><br /><span class='small'>".$kana."</span></td>"."\n";
	//会社名
	$company = fix_text($company);
	echo "      <td>".$company."</td>"."\n";
	//担当者
	$pic = fix_text($pic);
	echo "      <td>".$pic."</td>"."\n";
	//更新日
	$lasttime = fix_text($lasttime);
	echo "      <td class='sp_none'>".$lasttime."</td>"."\n";

	echo "    </tr>"."\n";
//-------------------------------------------------------↓
print <<< END_OF_HTML
</table>
  <div class="list_btn">
	<input type="hidden" name="radio" value="$count">
	<a href="./" class="historyback btn btn-default">キャンセル</a>
	<input type="submit" name="delete" value="削除する" class="btn btn-primary">
  </div>
  </form>

END_OF_HTML;
//-------------------------------------------------------↑
}
///////////////////////////////////
// ＨＴＭＬ一覧                  //
///////////////////////////////////
function html_list(){
	global $this_file,$sdata_line,$radio;
	global $switch;
//-------------------------------------------------------↓
print <<< END_OF_HTML

<h3>並び替え・削除</h3>
<div class="list_tbl">
<form name="form" method="post" action="$this_file">
<table class="table table-striped table-hover">
    <tr>
      <th>No.</th>
      <th>お名前(編)</th>
      <th>会社名</th>
      <th class="tab_none">担当者</th>
      <th>更新日</th>
      <th nowrap class="list_center">
        <input type="submit" name="shift_down" value="▼" class="list_input">
        <input type="submit" name="shift_up" value="▲" class="list_input">
        <!--<input type="submit" name="fix" value="編集" class="list_input">-->
        <input type="submit" name="delete_conf" value="削除" class="list_input">
      </th>
    </tr>\n
END_OF_HTML;
//-------------------------------------------------------↑
if ($sdata_line){
  foreach($sdata_line as $value){
	//文字列分割
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$dsp,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
	echo "    <tr>"."\n";
	echo "      <td>".$count."</td>"."\n";
	//非表示
//	if ($dsp){
//		echo "      <td><b><font color='#FF0000'>非</font></b></td>"."\n";
//	}else{
//		echo "      <td>&nbsp;</td>"."\n";
//	}

	//お名前
	$customer = fix_text($customer);
	$kana = fix_text($kana);
	echo "      <td><a href='regist.php?cnt=".$count."' title='この情報を編集する'><b>".$customer."</b></a><br /><span class='small'>".$kana."</span></td>"."\n";

		//ボトル
//		echo "<td>"."\n";
//	if($keyword01 <> '') {
//		$keyword01 = fix_text($keyword01);
//		echo " ".$keyword01." "."\n";
//	}
//	if($keyword02 <> '') {
//		$keyword02 = fix_text($keyword02);
//		echo " / ".$keyword02." "."\n";
//	}
//	if($keyword03 <> '') {
//		$keyword03 = fix_text($keyword03);
//		echo " / ".$keyword03." "."\n";
//	}
//	if($otherbottle <> '') {
//		$otherbottle = fix_text($otherbottle);
//		echo " / ".$otherbottle." "."\n";
//	}
//		echo "</td>"."\n";

		//会社名
		$company = fix_text($company);
		//役職
		$degree = fix_text($degree);
		echo "<td>".$company."<br /><span class='small'>".$degree."</span></td>"."\n";
		//担当者
		$pic = fix_text($pic);
		echo "<td class='tab_none'>".$pic."</td>"."\n";

		//更新日
		$lasttime = fix_text($lasttime);
		echo "<td>".$lasttime."</td>"."\n";

	//ラジオボタン
	if ($count == $radio){
		echo "      <td class='list_center'><input type='radio' name='radio' value='".$count."' checked></td>"."\n";
	}else{
		echo "      <td class='list_center'><input type='radio' name='radio' value='".$count."'></td>"."\n";
	}
	echo "    </tr>"."\n";
  }
}else{
//-------------------------------------------------------↓
print <<< END_OF_HTML

	</div>
        <div class="bs-component">
          <div class="jumbotron">
            <h2 class="alart_message">該当データはありません</h2>
          </div>
        </div>

END_OF_HTML;
//-------------------------------------------------------↑
	//カウンターのクリア
	$file = fopen(file_count,"w") or die(file_count." is not found");
	flock($file,LOCK_EX);
	fputs($file,"0\n");
	flock($file,LOCK_UN);
	fclose($file);	
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
  
</table>
</form>
END_OF_HTML;
//-------------------------------------------------------↑
}

?>