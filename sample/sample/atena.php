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

$html_title = "宛名印刷丨顧客・ボトル管理システム";  //ページタイトル

//--------------------------------------------------------------------------------------初期設定
require("common.php");
require("../bottle_assets/pageparts.php");
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
			<li class="active">$customer 様の宛名印刷</li>
		</ul>
	</div>\n
END_OF_HTML;
//-------------------------------------------------------↑
		echo "<div class='detail_tbl'>"."\n";
		echo "<h2>$customer 様の宛名を印刷します。"."\n";
		//DM可否
		if($no_dm <> '') {
			$no_dm = fix_text($no_dm);
			echo "<span class='hissu'>DM禁止</span>"."\n";
		}
		echo "</h2>"."\n";
		echo "<div class='small'>※ここでの修正は印刷以外には反映されません。データの修正は<a href='regist.php?cnt=$count'>こちら</a>から行なってください。</div>"."\n";
		echo "<form action='atena/atena_label.php' method='post' enctype='application/x-www-form-urlencoded' target='_blank'>"."\n";
		echo "<input type='hidden' name='from' value='free'>"."\n";
		echo "<table>"."\n";
		echo "<tr>"."\n";
		echo "<th>宛名</th>"."\n";
		$customer = fix_text($customer);
		echo "<td><input type='text' name='atena' size='30' value='$customer'>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	if($company <> '') {
		echo "<tr>"."\n";
		echo "<th>会社名</th>"."\n";
		//会社名
		$company = fix_text($company);
		echo "<td><input type='text' name='kaisha' size='50' value='$company'>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		}
		if($section <> '') {
		echo "<tr>"."\n";
		echo "<th>所属</th>"."\n";
		$section = fix_text($section);
		echo "<td><input type='text' name='buka' size='30' value='$section'>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		}
		if($section2 <> '') {
		echo "<tr>"."\n";
		echo "<th>所属2</th>"."\n";
		$section2 = fix_text($section2);
		echo "<td><input type='text' name='buka2' size='30' value='$section2'>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		}

		//役職
		if($degree <> '') {
		echo "<tr>"."\n";
		echo "<th>役職</th>"."\n";
			$degree = fix_text($degree);
		echo "<td><input type='text' name='yaku' size='50' value='$degree'>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		}

		//住所
	if($addr <> '') {
		echo "<tr>"."\n";
		echo "<th>郵便番号</th>"."\n";
		if($zip <> '') {
			$zip = fix_text($zip);
		echo "<td><input type='text' name='post' size='10' value='$zip'>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		}
		if($pref <> '') {
		echo "<tr>"."\n";
		echo "<th>都道府県</th>"."\n";
			$pref = fix_text($pref);
		echo "<td><input type='text' name='todofuken' size='10' value='$pref'>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>住所1</th>"."\n";
			$addr = fix_text($addr);
		echo "<td><input type='text' name='addr' size='50' value='$addr'>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>住所2</th>"."\n";
			$addr2 = fix_text($addr2);
		echo "<td><input type='text' name='addr_2' size='50' value='$addr2'>"."\n";
		echo "</td>"."\n";
		}
		echo "</tr>"."\n";
	}


//-------------------------------------------------------↓
print <<< END_OF_HTML
		<tr>
		<th>敬称</th>
		<td>	<label><input type="radio" name="keisho" value="0" checked> 様</label> 　<label><input type="radio" name="keisho" value="1"> 御中</label></td>
		</tr>
		<tr>
		<th>封筒種別</th>
		<td><label><input type="radio" name="kind" value="0" checked> 長3封筒【横】</label>　<label><input type="radio" name="kind" value="1"> 長3封筒【縦】</label></td>
		</tr>
		<tr>
		<th>フォント</th>
		<td><label title="読みやすい四角い書体"><input type="radio" name="fonts" value="0" checked> ゴシック系</label>　<label title="清潔感のある書体"><input type="radio" name="fonts" value="1"> 明朝系</label>　<label title="筆書き風の書体"><input type="radio" name="fonts" value="2"> 行書系</label><br /><span class="small">※書体によっては正しく表示されない場合があります。またご使用のPCにインストールされているフォントによります。</span></td>
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

echo" <div class='edit_btn'><input type='submit' name='delete_conf' value='印刷画面表示' class='btn btn-warning'></div></form>"."\n";
echo "<div class='clear'></div></div>"."\n";
//-------------------------------------------------------↓
print <<< END_OF_HTML
<script>
$(document).ready(function(){
	(function(){
	    var ans; //1つ前のページが同一ドメインかどうか
	    var bs  = false; //unloadイベントが発生したかどうか
	    var ref = document.referrer;
	    $(window).bind("unload beforeunload",function(){
	        bs = true;
	    });
	    re = new RegExp(location.hostname,"i");
	    if(ref.match(re)){
	        ans = true;
	    }else{
	        ans = false;
	    }
	    $('.historyback').bind("click",function(){
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
