<?php
///////////////////////////////////
// 詳細表示                      //
///////////////////////////////////
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

include_once("attestation.php"); 

//--------------------------------------------------------------------------------------初期設定
require("include.php");
$num = @$_GET["num"];//登録番号
define ("file_sdata","data/account.dat");

//--------------------------------------------------------------------------------------メイン処理
$line_custom = read_custom("data/custom.dat");//ユーザーファイルの読み込み
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

html_header2();

if ($sdata_line){
foreach ($sdata_line as $value){
	//文字列分割
	list($count,$plan,$plan_limit,$account,$store,$customer_id,$customer,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$email,$memo,$firsttime,$lasttime,$flag,$dsp,$cancel_flag,$c_date,$c_reason1,$c_reason2,$c_reason3,$c_reason4,$c_reason5,$c_reasonO,$c_reasonOf) = explode("&&",$value);
	if ($num == $count && !$dsp){

//-------------------------------------------------------↓
print <<< END_OF_HTML

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>
			<li class="active">$store $customer 様の詳細情報</li>
		</ul>
	</div>\n
END_OF_HTML;
//-------------------------------------------------------↑
		echo "<div class='detail_tbl'>"."\n";

		echo "<table>"."\n";
		echo "<tr>"."\n";
		echo "<th>契約プラン</th>"."\n";
		//店舗名
		$plan = fix_text($plan);
		echo "<td><strong>$plan</strong>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>現在登録数</th>"."\n";
		echo "<td>"."\n";
		//登録数ログデータファイル読み込み
		$data_file = "../$account/data/data.dat";
		$fd= file($data_file);
		$data_count= sizeof($fd);
		echo "$data_count 件"."\n";
//登録上限数
$logmax = file_get_contents("../$account/data/limit.dat");
		echo " / $logmax 件"."\n";
		echo "</td>"."\n";

		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>アカウントID</th>"."\n";
		//店舗名
		$account = fix_text($account);
		echo "<td><a href='https://nightworks.jp/$account' target='_blank'>$account</a>"."\n";
		echo "（ID：".$count."）</td>"."\n";
		echo "</tr>"."\n";
		echo "<tr>"."\n";
		echo "<th>会社・店舗名</th>"."\n";
		//店舗名
		$store = fix_text($store);
		echo "<td><strong>$store</strong>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "<th>ご契約者様名</th>"."\n";
		$customer = fix_text($customer);
		echo "<td><strong>$customer 様</strong>"."\n";
	if($degree <> '') {
		$degree = fix_text($degree);
		echo "（".$degree."）"."\n";
	}
		echo "</td>"."\n";
		echo "</tr><tr>"."\n";

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

		//電話番号
		echo "<td>"."\n";
		$mobile = fix_text($mobile);
		echo " ".$mobile." "."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}
	if($email <> '') {
		echo "<tr>"."\n";
		echo "<th>メールアドレス</th>"."\n";

		//携帯電話
		echo "<td>"."\n";
		$email = fix_text($email);
		echo "<a href='mailto:$email'>".$email."</a>"."\n";
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
