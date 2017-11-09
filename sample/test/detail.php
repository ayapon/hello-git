<?php
///////////////////////////////////
// 詳細表示                      //
///////////////////////////////////
//セッション開始
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

include_once("attestation.php"); 
//--------------------------------------------------------------------------------------初期設定
require("common.php");
require("../bottle_assets/pageparts.php");
$html_title = "お客様詳細丨顧客・ボトル管理システム";  //ページタイトル
$num = @$_GET["num"];//登録番号
define ("file_sdata","data/data.dat");
define ("allow_tag","");
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

//--------------------------------------------------------------------------------------メイン処理
$line_custom = read_custom("data/custom.dat");//ユーザーファイルの読み込み
//データファイルの読み込み
$sdata_line = read_data(file_sdata);
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////
html_header1();
//-------------------------------------------------------↓
print <<< END_OF_HTML
<!--//-->
END_OF_HTML;
//-------------------------------------------------------↑

html_header2();

if ($sdata_line){
foreach ($sdata_line as $value){
	//文字列分割
	list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$no_dm,$lastvisit,$dsp,$bottle_0,$bottle_num_0,$bottle_quant_0,$bottle_1,$bottle_num_1,$bottle_quant_1,$bottle_2,$bottle_num_2,$bottle_quant_2,$bottle_3,$bottle_num_3,$bottle_quant_3,$bottle_4,$bottle_num_4,$bottle_quant_4,$otherbottle,$otherbottle_num,$otherbottle_quant,$friends_0,$friends_1,$friends_2,$friends_3,$friends_4,$friends_5,$friends_6,$friends_7,$friends_8,$friends_9) = explode("&&",$value);
	if ($num == $count && !$dsp){

//-------------------------------------------------------↓
print <<< END_OF_HTML

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>
			<li class="active">$customer 様の詳細情報</li>
		</ul>
	</div>\n
END_OF_HTML;
//-------------------------------------------------------↑
		echo "<div class='detail_tbl'>"."\n";

		echo "<table>"."\n";
		echo "<tr>"."\n";
		echo "<th>お客様名</th>"."\n";
		$customer = fix_text($customer);
		echo "<td><strong>$customer 様</strong>"."\n";
	if($kana <> '') {
		$kana = fix_text($kana);
		echo "（".$kana."）"."\n";
	}
		echo "</td>"."\n";
		echo "</tr>"."\n";
	if($lastvisit <> '') {
		echo "<tr>"."\n";
		echo "<th>最終来店日</th>"."\n";
		$lastvisit = fix_text($lastvisit);
		echo "<td><strong>$lastvisit</strong>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
		echo "<tr>"."\n";
		echo "<th>ボトル</th>"."\n";
		//ボトル
		echo "<td>"."\n";
	if($bottle_0 <> '') {
		$bottle_0 = fix_text($bottle_0);
		echo "".$bottle_0." "."\n";
		if($bottle_num_0 <> '') {
			$bottle_num_0 = fix_text($bottle_num_0);
			echo " [".$bottle_num_0."] "."\n";
		}
		if($bottle_quant_0 <> '') {
			$bottle_quant_0 = fix_text($bottle_quant_0);
			echo " (".$bottle_quant_0.") "."\n";
		}
	}

	if($bottle_1 <> '') {
		$bottle_1 = fix_text($bottle_1);
		echo "".$bottle_1." "."\n";
		if($bottle_num_1 <> '') {
			$bottle_num_1 = fix_text($bottle_num_1);
			echo " [".$bottle_num_1."] "."\n";
		}
		if($bottle_quant_1 <> '') {
			$bottle_quant_1 = fix_text($bottle_quant_1);
			echo " (".$bottle_quant_1.") "."\n";
		}
	}

	if($bottle_2 <> '') {
		$bottle_2 = fix_text($bottle_2);
		echo "".$bottle_2." "."\n";
		if($bottle_num_2 <> '') {
			$bottle_num_2 = fix_text($bottle_num_2);
			echo " [".$bottle_num_2."] "."\n";
		}
		if($bottle_quant_2 <> '') {
			$bottle_quant_2 = fix_text($bottle_quant_0);
			echo " (".$bottle_quant_2.") "."\n";
		}
	}

	if($bottle_3 <> '') {
		$bottle_3 = fix_text($bottle_3);
		echo "".$bottle_3." "."\n";
		if($bottle_num_3 <> '') {
			$bottle_num_3 = fix_text($bottle_num_3);
			echo " [".$bottle_num_3."] "."\n";
		}
		if($bottle_quant_3 <> '') {
			$bottle_quant_3 = fix_text($bottle_quant_3);
			echo " (".$bottle_quant_3.") "."\n";
		}
	}

	if($bottle_4 <> '') {
		$bottle_4 = fix_text($bottle_4);
		echo "".$bottle_4." "."\n";
		if($bottle_num_4 <> '') {
			$bottle_num_4 = fix_text($bottle_num_4);
			echo " [".$bottle_num_4."] "."\n";
		}
		if($bottle_quant_4 <> '') {
			$bottle_quant_4 = fix_text($bottle_quant_4);
			echo " (".$bottle_quant_4.") "."\n";
		}
	}

	if($otherbottle <> '') {
		$otherbottle = fix_text($otherbottle);
		echo " ".$otherbottle." "."\n";
		if($otherbottle_num <> '') {
			$otherbottle_num = fix_text($otherbottle_num);
			echo " [".$otherbottle_num."] "."\n";
		}
		if($otherbottle_quant <> '') {
			$otherbottle_quant = fix_text($otherbottle_quant);
			echo " (".$otherbottle_quant.") "."\n";
		}
	}

		echo "</td>"."\n";
		echo "</tr>"."\n";
	if($company <> '') {
		echo "<tr>"."\n";
		echo "<th>会社名</th>"."\n";
		//会社名
		echo "<td>"."\n";

		$company = fix_text($company);
		echo " ".$company." "."\n";

		if($section <> '') {
			$section = fix_text($section);
			echo "：".$section." "."\n";
		}
		if($section2 <> '') {
			$section2 = fix_text($section2);
			echo " ".$section2." "."\n";
		}

		//役職
			$degree = fix_text($degree);
			echo "<br /><span class='small'>".$degree."</span>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($addr <> '') {
		echo "<tr>"."\n";
		echo "<th>ご住所</th>"."\n";

		//住所
		echo "<td>"."\n";
		if($zip <> '') {
			$zip = fix_text($zip);
			echo "〒".$zip."<br />"."\n";
		}
		if($pref <> '') {
			$pref = fix_text($pref);
			echo " ".$pref." "."\n";
		}

			$addr = fix_text($addr);
			echo " ".$addr." "."\n";

		if($addr2 <> '') {
			$addr2 = fix_text($addr2);
			echo " ".$addr2." "."\n";
		}
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($phone <> '') {
		echo "<tr>"."\n";
		echo "<th>電話番号</th>"."\n";

		//電話番号
		echo "<td>"."\n";
		$phone = fix_text($phone);
		echo " ".$phone." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($mobile <> '') {
		echo "<tr>"."\n";
		echo "<th>携帯電話</th>"."\n";

		//携帯電話
		echo "<td>"."\n";
		$mobile = fix_text($mobile);
		echo " ".$mobile." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}

	if($friends_0 || $friends_1 || $friends_2 || $friends_3 || $friends_4 <> '') {
		echo "<tr>"."\n";
		echo "<th>友人知人情報</th>"."\n";

		//友人知人情報
		echo "<td>"."\n";
		$friends_0 = fix_text($friends_0);
		$friends_1 = fix_text($friends_1);
		$friends_2 = fix_text($friends_2);
		$friends_3 = fix_text($friends_3);
		$friends_4 = fix_text($friends_4);
		echo " ".$friends_0." ".$friends_1." ".$friends_2." ".$friends_3." ".$friends_4." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}

	if($memo <> '') {
		echo "<tr>"."\n";
		echo "<th>メモ</th>"."\n";

		//メモ
		echo "<td>"."\n";
		$memo = strip_tags($memo,allow_tag);
		$memo = preg_replace("/\r|\n|\r\n/","<br>",$memo);
		echo " ".$memo." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($birthmonth <> "") {
		echo "<tr>"."\n";
		echo "<th>誕生日</th>"."\n";

		//誕生日
		echo "<td>";

		if($birthyear <> "") {
		echo " ".$birthyear." 年 ";
		}
		if($birthmonth <> "") {
		echo "$birthmonth 月";
		}else{
			echo "&nbsp;";
		}
		if($birthday <> "") {
		echo " ".$birthday." 日"."\n";
		}else{
			echo "&nbsp;";
		}

	//年齢計算
	if($birthyear <> "") {
		$birthmonth2 = sprintf("%02d",$birthmonth);
		$birthday2 = sprintf("%02d",$birthday);
		$howold = $birthyear.$birthmonth2.$birthday2;
		$now = date("Ymd"); 
		$birth = "$howold"; 
		echo " (現 ";
		echo floor(($now-$birth)/10000);
		echo " 歳)";
		}
	//今月の誕生日にマーク
		$nowm = date("m");
		if($nowm == $birthmonth) {
			echo " <span class='birthday' title='誕生月'>&nbsp;</span>";
		}

		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($pic <> '') {
		echo "<tr>"."\n";
		echo "<th>担当者</th>"."\n";

		//担当者
		echo "<td>"."\n";
		$pic = fix_text($pic);
		echo " ".$pic." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($no_dm <> '') {
		echo "<tr>"."\n";
		echo "<th>DM可否</th>"."\n";

		//DM可否
		echo "<td>"."\n";
		$no_dm = fix_text($no_dm);
		echo "<span class='hissu'>DM禁止</span>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
//-------------------------------------------------------↓
print <<< END_OF_HTML
			<tr>
				<th>初回登録日時</th>
				<td>$firsttime</td>
			</tr><tr>
				<th>前回更新日時</th>
				<td>$lasttime</td>
			</tr>
	</table>

END_OF_HTML;
//-------------------------------------------------------↑
		echo "</div>"."\n";
		//出力チェック
		$check = "true";
	}
}
}
if (@!$check){//該当データなし	
//-------------------------------------------------------↓
print <<< END_OF_HTML
		<ul class="breadcrumb">
			<li><a href="./index.php">HOME</a></li>
			<li class="active">該当データはありません</li>
		</ul>
        <div class="bs-component">
          <div class="jumbotron">
            <h2 class="alart_message">該当データはありません</h2>
          </div>
        </div>


END_OF_HTML;
//-------------------------------------------------------↑
}

echo "<div class='footer_navi'>"."\n";
echo "	<a href='./' class='btn btn-primary historyback'>&lt; 一覧に戻る</a>"."\n";

echo" <div class='edit_btn'><form name='form' method='post' action='sequence.php'><input type='hidden' name='radio' value='$num'><input type='submit' name='delete_conf' value='この情報を削除' class='btn btn-danger'><a href='regist.php?cnt=$num' class='btn btn-warning'>この情報を編集</a></div></form>"."\n";
echo "<div class='clear'></div></div>"."\n";
//-------------------------------------------------------↓
print <<< END_OF_HTML
<script>
JQuery(document).ready(function(){
	(function(){
	    var ans; //1つ前のページが同一ドメインかどうか
	    var bs  = false; //unloadイベントが発生したかどうか
	    var ref = document.referrer;
	    JQuery(window).bind("unload beforeunload",function(){
	        bs = true;
	    });
	    re = new RegExp(location.hostname,"i");
	    if(ref.match(re)){
	        ans = true;
	    }else{
	        ans = false;
	    }
	    JQuery('.historyback').bind("click",function(){
                var that = this;
	        if(ans){
	            history.back();
	            setTimeout(function(){
	                if(!bs){
	                    location.href = $(that).attr("href");
	                }
	            },100);
	        }else{
                    location.href = $(this).attr("href");
                }
	        return false;
	    });
	})();
});
</script>
END_OF_HTML;
//-------------------------------------------------------↑
html_footer();
?>
