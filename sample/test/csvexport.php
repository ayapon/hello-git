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

//--------------------------------------------------------------------------------------初期設定
//$this_file = $_SERVER['SCRIPT_NAME'];
$this_file = "csvexport.php";
define ("file_sdata","data/data.dat");
define ("file_count","data/count.dat");
define ("allow_tag","");

//--------------------------------------------------------------------------------------メイン処理
//登録ファイルの読み込み
$sdata_line = read_data();
//ＨＴＭＬ
html_header();
html_list();

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
//			$line[] = $value;
			$line[] = mb_convert_encoding($value, 'SJIS-win', 'UTF-8');
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
//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬヘッダー              //
///////////////////////////////////
function html_header(){
	global $this_file,$customer,$company,$delete_conf,$radio;
	global $message,$message_error;
//-------------------------------------------------------↓
header("Content-disposition: attachment; filename=csvexport.csv");
header("Content-Type: text/csv; charset=Shift_JIS");
//-------------------------------------------------------↑


		$str = "No.,お客様名,ふりがな,会社名,所属1,所属2,役職,郵便番号,都道府県,住所番地,ビル名等,電話番号,携帯電話,メモ,生年,生月,生日,登録日,更新日,担当者,DM禁止,最終来店日,表示,ボトル1,ボトル1のNo.,ボトル1の残,ボトル2,ボトル2のNo.,ボトル2の残,ボトル3,ボトル3のNo.,ボトル3の残,ボトル4,ボトル4のNo.,ボトル4の残,ボトル5,ボトル5のNo.,ボトル5の残,その他ボトル,その他ボトルのNo.,その他ボトルの残,友人知人1,友人知人2,友人知人3,友人知人4,友人知人5,友人知人6,友人知人7,友人知人8,友人知人9,友人知人10"."\n";
		$str = mb_convert_encoding($str, "SJIS-win", "UTF-8");
		print($str);

//文字列内のタグを取り除く
$temp_title = strip_tags($company);
$temp_title2 = strip_tags($customer);
}
///////////////////////////////////
// ＨＴＭＬ一覧                  //
///////////////////////////////////
function html_list(){
	global $this_file,$sdata_line,$radio;
	global $switch;

if ($sdata_line){
  foreach($sdata_line as $value){
	//文字列分割
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$fname,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);

$memo = str_replace(array("\r", "\n"), '', $memo);

		//データ
		echo "".$count.",".$customer.",".$kana.",".$company.",".$section.",".$section2.",".$degree.",".$zip.",".$pref.",".$addr.",".$addr2.",".$phone.",".$mobile.",".$memo.",".$birthyear.",".$birthmonth.",".$birthday.",".$firsttime.",".$lasttime.",".$pic.",".$no_dm.",".$lastvisit.",".$fname.",".$bottle_0.",".$bottle_num_0.",".$bottle_quant_0.",".$bottle_1.",".$bottle_num_1.",".$bottle_quant_1.",".$bottle_2.",".$bottle_num_2.",".$bottle_quant_2.",".$bottle_3.",".$bottle_num_3.",".$bottle_quant_3.",".$bottle_4.",".$bottle_num_4.",".$bottle_quant_4.",".$otherbottle.",".$otherbottle_num.",".$otherbottle_quant.",".$friends_0.",".$friends_1.",".$friends_2.",".$friends_3.",".$friends_4.",".$friends_5.",".$friends_6.",".$friends_7.",".$friends_8.",".$friends_9.""."\n";
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

}


?>