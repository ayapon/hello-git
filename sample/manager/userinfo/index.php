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
require("../include.php");
$num = @$_GET["num"];//登録番号
define ("file_sdata","../data/account.dat");

//--------------------------------------------------------------------------------------メイン処理
$line_custom = read_custom("../data/custom.dat");//ユーザーファイルの読み込み
//データファイルの読み込み
$sdata_line = read_data(file_sdata);
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////
html_header1("顧客詳細");
//-------------------------------------------------------↓
print <<< END_OF_HTML
<!--//-->
END_OF_HTML;
//-------------------------------------------------------↑

html_header3();

if ($sdata_line){
foreach ($sdata_line as $value){
	//文字列分割
	list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf,$monitor,$stop) = explode("&&",$value);
	if ($num == $customer_id && !$dsp){


		echo "<div class='detail_tbl'>"."\n";

		echo "<table>"."\n";
		echo "<tr>"."\n";
		echo "<th>店舗名</th>"."\n";
		//店舗名
		$store = fix_text($store);
		echo "<td><strong>$store</strong>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>ご利用プラン</th>"."\n";
		//店舗名
		$plan = fix_text($plan);
		echo "<td>"."\n";
//		echo "<strong>$plan</strong>"."\n";

		if (strstr($plan, 'フリープラン')) { $value1 = ""; $selected1 = "selected"; $nowplan1 = "（現在利用中）"; } else { $value1 = "https://nightworks.jp/plan/205.html"; }
		if (strstr($plan, 'ベーシックプラン')) { $value2 = ""; $selected2 = "selected"; $nowplan2 = "（現在利用中）"; } else { $value2 = "https://nightworks.jp/plan/220.html"; }
		if (strstr($plan, 'スタンダードプラン')) { $value3 = ""; $selected3 = "selected"; $nowplan3 = "（現在利用中）"; } else { $value3 = "https://nightworks.jp/plan/208.html"; }
		if (strstr($plan, 'プレミアムプラン')) { $value4 = ""; $selected4 = "selected"; $nowplan4 = "（現在利用中）"; } else { $value4 = "https://nightworks.jp/plan/434.html"; }

		
//-------------------------------------------------------↓
print <<< END_OF_HTML

<form name="form_plan_$count" target="_top">
<select name='plan_select' class='plan_select'>
<option value="$value1" $selected1>フリープラン【100件】0円/月 $nowplan1</option>
<option value="$value2" $selected2>ベーシックプラン【500件】980円/月 $nowplan2</option>
<option value="$value3" $selected3>スタンダードプラン【2000件】2,980円/月 $nowplan3</option>
<option value="$value4" $selected4>プレミアムプラン【無制限】4,980円/月 $nowplan4</option>
</select> 
<input type="hidden" name
<input type="button" onclick="if(document.form_plan_$count.plan_select.value){top.location.href=document.form_plan_$count.plan_select.value;}" value="選択してプラン移行へ"></form>

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

		if (strstr($plan, 'ベーシックプラン')) { $restartplan = "https://nightworks.jp/plan/280.html"; }
		if (strstr($plan, 'スタンダードラン')) { $restartplan = "https://nightworks.jp/plan/282.html"; }
		if (strstr($plan, 'プレミアムプラン')) { $restartplan = "https://nightworks.jp/plan/207.html"; }

//-------------------------------------------------------↓
print <<< END_OF_HTML
<br />
<a href="$restartplan" target="_blank">ご利用再開はこちらより再度お申込みご決済ください。</a>
<div class="infobox">メールにてご案内の通り、ご利用有料プランについて当月1日のご決済ができなかったため、現在フリープランを超える新規の登録ができなくなっております。(ご登録済みのデータに影響はございません。)<br />
お手数ではございますが、<a href="$restartplan" target="_blank" class="button">こちらより【<strong>異なるクレジットカード</strong>】にてあらためてお申込みご決済</a>いただきますよう、よろしくお願いいたします。</div>

END_OF_HTML;
//-------------------------------------------------------↑
		}

		echo "</td>"."\n";

		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>アカウントURL</th>"."\n";
		//店舗名
		$account = fix_text($account);
		echo "<td><form action='https://nightworks.jp/$account' target='_blank'>https://nightworks.jp/$account <input type='submit' class='button' value='表示する'></form>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>アカウントのログイン情報紛失<br /><span style='font-weight:normal;font-size:80%;'>（ご利用のボトル管理アプリ）</span></th>"."\n";
		//店舗名
		echo "<td>"."\n";
		echo "<form action='https://nightworks.jp/$account/lost/' target='_blank'><input type='submit' class='button' value='新たにログインIDとパスワードを登録'> こちらから上書き登録してください。</form>※<span style='color:#CC0000;'>アカウントURLは変更されません。</td>"."\n";
		echo "</tr>"."\n";

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
            <h2 class="alart_message">ご利用のプランはまだありません</h2>
						<form action='https://nightworks.jp/#pricing' target='_blank'><p style="text-align: center;"><input type='submit' class='button' value='プランを選んでお申込みする'></p></form>
          </div>
        </div>


END_OF_HTML;
//-------------------------------------------------------↑
}

// echo "<div class='footer_navi'>"."\n";
// echo "	<a href='./' class='btn btn-primary historyback'>&lt; 一覧に戻る</a>"."\n";
// 
// echo" <div class='edit_btn'><form name='form' method='post' action='sequence.php'><input type='hidden' name='radio' value='$num'><input type='submit' name='delete_conf' value='この情報を削除' class='btn btn-danger'><a href='regist.php?cnt=$num' class='btn btn-warning'>この情報を編集</a></div></form>"."\n";
// echo "<div class='clear'></div></div>"."\n";
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
html_footer3();
?>
