<?php
///////////////////////////////////
// 詳細表示                      //
///////////////////////////////////
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

// include_once("attestation.php"); 

//--------------------------------------------------------------------------------------初期設定
require("../../bottle_assets/pageparts.php");
$html_title = "解約申請丨顧客・ボトル管理システム";  //ページタイトル
$num = @$_GET["num"];//登録番号
define ("file_sdata","../data/account.dat");

//--------------------------------------------------------------------------------------メイン処理
//$line_custom = read_custom("../data/custom.dat");//ユーザーファイルの読み込み
//データファイルの読み込み
//$sdata_line = read_data(file_sdata);
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////
html_header1("顧客詳細");

print <<< END_OF_HTML

<link href="../bottle_assets/js/pickdate/themes/default.css" rel="stylesheet" media="screen">
<link href="../bottle_assets/js/pickdate/themes/default.date.css" rel="stylesheet" media="screen">

END_OF_HTML;

html_header2();

print <<< END_OF_HTML
		<div class="container">
END_OF_HTML;

if ($sdata_line){
foreach ($sdata_line as $value){
	//文字列分割
	list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop) = explode("&&",$value);
	if ($num == $count && !$dsp){

		echo "<div class='detail_tbl'>"."\n";

		echo "<table>"."\n";
		echo "<tr>"."\n";
		echo "<th>ご利用プラン</th>"."\n";
		//店舗名
		$plan = fix_text($plan);
		echo "<td>"."\n";
//		echo "<strong>$plan</strong>"."\n";

		if (strstr($plan, 'フリープラン')) { $value1 = ""; $selected1 = "selected"; $nowplan1 = "（現在利用中）"; } else { $value1 = "https://nightworks.jp/item/205.html"; }
		if (strstr($plan, 'ベーシックプラン')) { $value2 = ""; $selected2 = "selected"; $nowplan2 = "（現在利用中）"; } else { $value2 = "https://nightworks.jp/item/220.html"; }
		if (strstr($plan, 'スタンダードラン')) { $value3 = ""; $selected3 = "selected"; $nowplan3 = "（現在利用中）"; } else { $value3 = "https://nightworks.jp/item/208.html"; }
		if (strstr($plan, 'プレミアムプラン')) { $value4 = ""; $selected4 = "selected"; $nowplan4 = "（現在利用中）"; } else { $value4 = "https://nightworks.jp/item/207.html"; }
//-------------------------------------------------------↓
print <<< END_OF_HTML

<form name="form_plan" target="_top">
<select name='plan_select' class='plan_select'>
<option value="$value1" $selected1>フリープラン【100件】0円/月 $nowplan1</option>
<option value="$value2" $selected2>ベーシックプラン【500件】980円/月 $nowplan2</option>
<option value="$value3" $selected3>スタンダードプラン【2000件】2,980円/月 $nowplan3</option>
<option value="$value4" $selected4>プレミアムプラン【無制限】4,980円/月 $nowplan4</option>
</select> <input type="button" onclick="if(document.form_plan.plan_select.value){top.location.href=document.form_plan.plan_select.value;}" value="選択してプラン移行詳細へ"></form>

END_OF_HTML;
//-------------------------------------------------------↑
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>現在登録数</th>"."\n";
		echo "<td>"."\n";
		//登録数ログデータファイル読み込み
		$data_file = "../../$account/data/data.dat";
		$fd= file($data_file);
		$data_count= sizeof($fd);
		echo "$data_count 件"."\n";
//登録上限数
		echo " / $plan_limit 件"."\n";

		if ($monitor){
		echo "（<strong>モニターユーザー</strong>）"."\n";
		}

		if ($stop){
		echo "（<strong><span class='hissu'>現在ご利用停止中です</span></strong>）"."\n";

		if (strstr($plan, 'ベーシックプラン')) { $restartplan = "https://nightworks.jp/item/280.html"; }
		if (strstr($plan, 'スタンダードラン')) { $restartplan = "https://nightworks.jp/item/282.html"; }
		if (strstr($plan, 'プレミアムプラン')) { $restartplan = "https://nightworks.jp/item/207.html"; }

//-------------------------------------------------------↓
print <<< END_OF_HTML

<a href="$restartplan" target="_top">ご利用再開はこちらより再度お申込みご決済ください。</a>

END_OF_HTML;
//-------------------------------------------------------↑
		}


		echo "</td>"."\n";

		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>アカウントURL</th>"."\n";
		//店舗名
		$account = fix_text($account);
		echo "<td><a href='https://nightworks.jp/$account' target='_blank'>https://nightworks.jp/$account</a>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>アカウントID</th>"."\n";
		//店舗名
		echo "<td>$account"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>店舗名</th>"."\n";
		//店舗名
		$store = fix_text($store);
		echo "<td><strong>$store</strong>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";

//-------------------------------------------------------↓
print <<< END_OF_HTML

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

        <div class="bs-component">
          <div class="jumbotron">
            <h2 class="alart_message">該当データはありません</h2>
          </div>
        </div>


END_OF_HTML;
//-------------------------------------------------------↑
}

html_footer_exj();

print <<< END_OF_HTML

<script src="https://nightworks.jp/bottle_assets/js/pickdate/picker.js"></script>
<script src="https://nightworks.jp/bottle_assets/js/pickdate/picker.date.js"></script>
<script src="https://nightworks.jp/bottle_assets/js/pickdate/legacy.js"></script>
<script src="https://nightworks.jp/bottle_assets/js/pickdate/translations/ja_JP.js"></script>
<script type="text/javascript">
$(function() {
	$('.datepicker').pickadate({
		min: 30
	});
});
</script>
<script type="text/javascript">
<!--
$(function(){
   $('a[href^=#]').click(function() {
      var speed = 400;
      var href= $(this).attr("href");
      var target = $(href == "#" || href == "" ? 'html' : href);
      var position = target.offset().top;
      $('body,html').animate({scrollTop:position}, speed, 'swing');
      return false;
   });
});
function check(){
	var flag = 0;

	// チェック項目
	if(!document.form_mail.EMAIL.value.match(/.+@.+\..+/)){
		flag = 1;
	}

	if(flag){
		window.alert('メールアドレスが正しくありません'); // メールアドレス以外の場合は警告
		return false; // 送信を中止
	}
	else{
		return true; // 送信を実行
	}
}
$(function() {
	$('#submit').attr('disabled', 'disabled');
	
	$('#check').click(function() {
		if ($(this).prop('checked') == false) {
			$('#submit').attr('disabled', 'disabled');
		} else {
			$('#submit').removeAttr('disabled');
		}
	});
});

$('#check_c_reasonO').click(function() {
    //クリックイベントで要素をトグルさせる
    $("#box_c_reason").slideToggle(this.checked);
});
//-->
</script>
END_OF_HTML;
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

</body>
</html>

END_OF_HTML;
//-------------------------------------------------------↑


?>