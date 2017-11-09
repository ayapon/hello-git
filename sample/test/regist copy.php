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
require("../bottle_assets/pageparts.php");

$html_title = "新規登録・編集丨顧客・ボトル管理システム";  //ページタイトル
//--------------------------------------------------------------------------------------初期設定
//$this_file = $_SERVER['SCRIPT_NAME'];
$this_file = "regist.php";
define ("file_sdata","data/data.dat");
define ("file_count","data/count.dat");

//契約ユーザーデータファイル
define ("file_cdata","../manager/data/account.dat");

$file_category = "config/category.dat";
//$file_category2 = "data/category2.dat";
//$file_category3 = "data/category3.dat";
$file_pref = "data/pref.dat";
$file_birthyear = "data/birthyear.dat";
$file_birthmonth = "data/birthmonth.dat";
$file_birthday = "data/birthday.dat";
//ＧＥＴ受信
$cnt = @$_GET["cnt"];
$category   = urldecode(@$_GET["category"]);
//$category2   = urldecode(@$_GET["category2"]);
//$category3   = urldecode(@$_GET["category3"]);
$searchword = urldecode(@$_GET["searchword"]);
//ＰＯＳＴ受信
$submit         = @$_POST["Submit"];
$cancel         = @$_POST["cancel"];
$customer       = @$_POST["customer"];
$kana       = @$_POST["kana"];
$company       = @$_POST["company"];
$section       = @$_POST["section"];
$section2       = @$_POST["section2"];
$degree       = @$_POST["degree"];
$zip    = @$_POST["zip"];
$pref = @$_POST["pref"];
$addr = @$_POST["addr"];
$addr2    = @$_POST["addr2"];
$phone    = @$_POST["phone"];
$mobile    = @$_POST["mobile"];
$memo = @$_POST["memo"];
$birthyear = @$_POST["birthyear"];
$birthmonth = @$_POST["birthmonth"];
$birthday = @$_POST["birthday"];
$firsttime = @$_POST["firsttime"];
$lasttime = @$_POST["lasttime"];
$pic = @$_POST["pic"];
$keyword01  = @$_POST["keyword01"];
$keyword02  = @$_POST["keyword02"];
$keyword03  = @$_POST["keyword03"];
$otherbottle = @$_POST["otherbottle"];
$keyword01_2  = @$_POST["keyword01_2"];
$keyword02_2  = @$_POST["keyword02_2"];
$keyword03_2  = @$_POST["keyword03_2"];
$otherbottle_2 = @$_POST["otherbottle_2"];
$keyword01_3  = @$_POST["keyword01_3"];
$keyword02_3  = @$_POST["keyword02_3"];
$keyword03_3  = @$_POST["keyword03_3"];
$otherbottle_3 = @$_POST["otherbottle_3"];
$no_dm      = @$_POST["no_dm"];
$lastvisit      = @$_POST["lastvisit"];
$friends      = @$_POST["friends"];
$dsp            = @$_POST["dsp"];
$reset       = @$_POST["reset"];
//
if (!$category){$category = @$_POST["category"];}
if (!$searchword){$searchword = @$_POST["searchword"];}
//ダイレクトリクエスト
if (file_exists($file_link)){$dir = "";$direct = 1;}
//カテゴリーリスト
$word_list = read_file($dir.$file_category);
if ($category == $word_list[0]){$category = "";}

$word_list4 = read_file($dir.$file_pref);
if ($pref_dat== $word_list4[0]){$pref_dat = "";}
$word_list5 = read_file($dir.$file_birthyear);
if ($birthyear_dat== $word_list5[0]){$birthyear_dat = "";}
$word_list6 = read_file($dir.$file_birthmonth);
if ($birthmonth_dat== $word_list6[0]){$birthmonth_dat = "";}
$word_list7 = read_file($dir.$file_birthday);
if ($birthday_dat== $word_list7[0]){$birthdayf_dat = "";}


//メッセージ初期
$message = "";
$message_error = "";
//--------------------------------------------------------------------------------------メイン処理
//編集キャンセル
if ($cancel){echo "<meta http-equiv='refresh' content='0;url=./index.php'>"."\n";exit();}
//編集モード
if ($cnt && !$submit){
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//不正確認
	if ($sdata_line){
		foreach ($sdata_line as $key => $value){
			//文字列分割
			list($count,$customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$keyword01,$keyword02,$keyword03,$otherbottle,$keyword01_2,$keyword02_2,$keyword03_2,$otherbottle_2,$keyword01_3,$keyword02_3,$keyword03_3,$otherbottle_3,$no_dm,$lastvisit,$friends,$dsp) = explode("&&",$value);
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
	$customer = check_text($customer);
	$kana = check_text($kana);
	$company = check_text($company);
	$section = check_text($section);
	$section2 = check_text($section2);
	$degree = check_text($degree);
	$zip = check_text($zip);
	$pref = check_text($pref);
	$addr = check_text($addr);
	$addr2 = check_text($addr2);
	$phone = check_text($phone);
	$mobile = check_text($mobile);
	$memo = check_text($memo);
	$birthyear = check_text($birthyear);
	$birthmonth = check_text($birthmonth);
	$birthday = check_text($birthday);
	$firsttime = check_text($firsttime);
	$lasttime = check_text($lasttime);
	$pic = check_text($pic);
	$keyword01 = check_text($keyword01);
	$keyword02 = check_text($keyword02);
	$keyword03 = check_text($keyword03);
	$otherbottle = check_text($otherbottle);
	$keyword01_2 = check_text($keyword01_2);
	$keyword02_2 = check_text($keyword02_2);
	$keyword03_2 = check_text($keyword03_2);
	$otherbottle_2 = check_text($otherbottle_2);
	$keyword01_3 = check_text($keyword01_3);
	$keyword02_3 = check_text($keyword02_3);
	$keyword03_3 = check_text($keyword03_3);
	$otherbottle_3 = check_text($otherbottle_3);
	$lastvisit = check_text($lastvisit);
	$friends = check_text($friends);


	if (!$customer){$message_error = "【お客様名】は必須項目です";}
	if (!$kana){$message_error = "【ふりがな】は必須項目です";}
//	if (!$pic){$message_error = "【担当者】は必須項目です";}
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
	global $customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$keyword01,$keyword02,$keyword03,$otherbottle,$keyword01_2,$keyword02_2,$keyword03_2,$otherbottle_2,$keyword01_3,$keyword02_3,$keyword03_3,$otherbottle_3,$no_dm,$lastvisit,$friends,$dsp;
	//カウンタ読み込み
	$counter = file(file_count);
	$count = trim($counter[0]);
	$count++;
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//一行化
	$new_line = $count."&&".$customer."&&".$kana."&&".$company."&&".$section."&&".$section2."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$memo."&&".$birthyear."&&".$birthmonth."&&".$birthday."&&".$firsttime."&&".$lasttime."&&".$pic."&&".$keyword01."&&".$keyword02."&&".$keyword03."&&".$otherbottle."&&".$keyword01_2."&&".$keyword02_2."&&".$keyword03_2."&&".$otherbottle_2."&&".$keyword01_3."&&".$keyword02_3."&&".$keyword03_3."&&".$otherbottle_3."&&".$no_dm."&&".$lastvisit."&&".$friends."&&".$dsp."\n";
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
	$new_file = "regist_dat/".$count.".dat";
	touch($new_file);
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
	global $customer,$kana,$company,$section,$section2,$degree,$zip,$pref,$addr,$addr2,$phone,$mobile,$memo,$birthyear,$birthmonth,$birthday,$firsttime,$lasttime,$pic,$keyword01,$keyword02,$keyword03,$otherbottle,$keyword01_2,$keyword02_2,$keyword03_2,$otherbottle_2,$keyword01_3,$keyword02_3,$keyword03_3,$otherbottle_3,$no_dm,$lastvisit,$friends,$dsp;
	//カウンタ読み込み
	$counter = file(file_count);
	$count = trim($counter[0]);
	$count++;
	//カテゴリー1ファイル読込み
//	$word_list = read_file(file_category);
	//カテゴリー2ファイル読込み
//	$word_list2 = read_file(file_category2);
	//登録ファイルの読み込み
	$sdata_line = read_data();
	//一行化
	$new_line = "";
	foreach ($sdata_line as $value){
		//文字列分割
		list($count,) = explode("&&",$value);
		if ($cnt == $count){
			$new_line .= $count."&&".$customer."&&".$kana."&&".$company."&&".$section."&&".$section2."&&".$degree."&&".$zip."&&".$pref."&&".$addr."&&".$addr2."&&".$phone."&&".$mobile."&&".$memo."&&".$birthyear."&&".$birthmonth."&&".$birthday."&&".$firsttime."&&".$lasttime."&&".$pic."&&".$keyword01."&&".$keyword02."&&".$keyword03."&&".$otherbottle."&&".$keyword01_2."&&".$keyword02_2."&&".$keyword03_2."&&".$otherbottle_2."&&".$keyword01_3."&&".$keyword02_3."&&".$keyword03_3."&&".$otherbottle_3."&&".$no_dm."&&".$lastvisit."&&".$friends."&&".$dsp."\n";
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


//--------------------------------------------------------------------------------------ＨＴＭＬ
///////////////////////////////////
// ＨＴＭＬ                      //
///////////////////////////////////
if ($cnt){$buttom_name = "更新登録";}else{$buttom_name = "新規登録";}

//-------------------------------------------------------↓
//ヘッダー1
html_header1();
print <<< END_OF_HTML

<script src="https://zipaddr.github.io/zipaddrx.js" charset="UTF-8"></script>

END_OF_HTML;
//ヘッダー2
html_header2();


///////////////////////////////////
// ファイルの読み込み            //
///////////////////////////////////
function read_cdata($file_cname){
	$cline = "";
	$temp_cline = file($file_cname);
	foreach ($temp_cline as $value){
		if ($value){
			$value = trim($value);
			$cline[] = $value;
		}
	}
	return $cline;
}

//-----------------------------------------------------契約ユーザーデータ

//契約ユーザーデータファイルの読み込み
$cdata_line = read_cdata(file_cdata);

//契約ユーザーデータ展開
if ($cdata_line){
foreach ($cdata_line as $value){
	//文字列分割
	list($ucount,$uplan,$uplan_limit,$uaccount,$ustore,$ucustomer_id,$ucustomer,$udegree,$uzip,$upref,$uaddr,$uaddr2,$uphone,$umobile,$uemail,$umemo,$ufirsttime,$ulasttime,$uflag,$udsp,$ucancel_flag,$uc_date,$uc_reason1,$uc_reason2,$uc_reason3,$uc_reason4,$uc_reason5,$uc_reasonO,$uc_reasonOf) = explode("&&",$value);
//契約ユーザーデータ抽出
	if ($user == $ucount && !$dsp){

///////////////////////////////////
// 登録限度数設定              //
///////////////////////////////////
	//登録件数チェックのためカウンタ再読み込みfile_sdata
	$fp = fopen( file_sdata, 'r' );
	for( $counts = 0; fgets( $fp ); $counts++ );

	if ($counts >= $uplan_limit){
		$message_error = "登録件数は".$uplan_limit."件までです";
		$message_error2 = "<br /><span>プランのご変更は<a href='/usces-member' target='_blank'>契約ユーザーログイン</a>で行えます</span>";
		$form_none = 1;
		$disabled = "disabled";
	}


print <<< END_OF_HTML

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>

END_OF_HTML;
//-------------------------------------------------------↑
if ($cnt){
	//文字列内のタグを取り除く
	$temp_title = strip_tags($customer);
//	$temp_title2 = strip_tags($kana);
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
	echo "<h3 class='error_msg'>".$message_error." ".$message_error2."</h3>"."\n";
}else{
	echo "<h3 class='error_msg'>".$message."</h3>"."\n";
}

if ($cnt){
	echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$this_file."?cnt=".$cnt."'>"."\n";
}else{
	if (!$form_none){
		echo "  <form name='form' enctype='multipart/form-data' method='post' action='".$this_file."'>"."\n";
	}
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
<div class="detail_tbl">
<table>
<tr>
    <th>お客様名 <span class="hissu">※必須</span> <div class="infotip"><abbr title="お客様の名前は漢字で入力し、姓名の間にスペースを入れておきます。" rel="tooltip">?</abbr></div></th>
END_OF_HTML;
//-------------------------------------------------------↑
$customer =  preg_replace("/<br>/","\n",$customer);
$customer = htmlspecialchars ($customer);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>
<input type="text" name="customer" id="user_name" value="$customer" class="form-control" placeholder="姓　名" style="ime-mode: active;" $disabled>
	</td>
  </tr>
<tr>
    <th>ふりがな <span class="hissu">※必須</span> <div class="infotip"><abbr title="検索時はここの読みから絞り込みます。" rel="tooltip">?</abbr></div></th>
END_OF_HTML;
//-------------------------------------------------------↑
$kana =  preg_replace("/<br>/","\n",$kana);
$kana = htmlspecialchars ($kana);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>
<input type="text" name="kana" id="user_name_kana" value="$kana" class="form-control" placeholder="必ず【ひらがな】で入力" style="ime-mode: active;" onblur="FuriganaCheck();" $disabled>
</td>
  </tr>
END_OF_HTML;

if ($lastvisit_logs <> ""){ //最終来店日記録オンオフ
//-------------------------------------------------------↑
$lastvisit =  preg_replace("/<br>/","\n",$lastvisit);
$lastvisit = htmlspecialchars ($lastvisit);
//-------------------------------------------------------↓
print <<< END_OF_HTML
<tr>
    <th>最終来店日 <div class="infotip"><abbr title="最後にご来店になった日を登録します。" rel="tooltip">?</abbr></div></th>
    <td>
<input type="text" name="lastvisit" value="$lastvisit" class="form-control datepicker" style="ime-mode: active;" $disabled>
</td>
  </tr>
END_OF_HTML;
}

print <<< END_OF_HTML
<tr>
    <th>ボトル <div class="infotip"><abbr title="キープボトルをこの中から選択します。任意でボトル番号や残量(数値等で、基準は個々のお好みで)が入れられます。<br>ここで表示されるボトルリストは[設定] > [ボトルリスト編集]で追加変更できます。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
$keyword01_2 =  preg_replace("/<br>/","\n",$keyword01_2);
$keyword01_2 = htmlspecialchars ($keyword01_2);
$keyword01_3 =  preg_replace("/<br>/","\n",$keyword01_3);
$keyword01_3 = htmlspecialchars ($keyword01_3);
//-------------------------------------------------------↓
if ($word_list){
//if (count($word_list) > 1){
	echo "<select name='keyword01' class='form-category form-control' $disabled>"."\n";
	echo "<option value=''>ボトルリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($keyword01 == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select><input type='text' name='keyword01_2' value='$keyword01_2' class='form-category2 form-control' placeholder='番号等' maxlength='4' $disabled><input type='text' name='keyword01_3' value='$keyword01_3' class='form-category2 form-control' placeholder='残量等' maxlength='4' $disabled>"."\n";
}
//-------------------------------------------------------↓ 
print <<< END_OF_HTML
      </td>
</tr>
<tr>
    <th>ボトル2</th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
$keyword02_2 =  preg_replace("/<br>/","\n",$keyword02_2);
$keyword02_2 = htmlspecialchars ($keyword02_2);
$keyword02_3 =  preg_replace("/<br>/","\n",$keyword02_3);
$keyword02_3 = htmlspecialchars ($keyword02_3);
//-------------------------------------------------------↓
if ($word_list){
//if (count($word_list) > 1){
	echo "<select name='keyword02' class='form-category form-control' $disabled>"."\n";
	echo "<option value=''>ボトルリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($keyword02 == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select><input type='text' name='keyword02_2' value='$keyword02_2' class='form-category2 form-control' placeholder='番号等' maxlength='4' $disabled><input type='text' name='keyword02_3' value='$keyword02_3' class='form-category2 form-control' placeholder='残量等' maxlength='4' $disabled>"."\n";
}
//-------------------------------------------------------↓ 
print <<< END_OF_HTML
      </td>
</tr>
<tr>
    <th>ボトル3</th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
$keyword03_2 =  preg_replace("/<br>/","\n",$keyword03_2);
$keyword03_2 = htmlspecialchars ($keyword03_2);
$keyword03_3 =  preg_replace("/<br>/","\n",$keyword03_3);
$keyword03_3 = htmlspecialchars ($keyword03_3);
//-------------------------------------------------------↓
if ($word_list){
//if (count($word_list) > 1){
	echo "<select name='keyword03' class='form-category form-control' $disabled>"."\n";
	echo "<option value=''>ボトルリストから選択</option>"."\n";
	foreach ($word_list as $value){
		if (strstr($value, '■')) {
			echo "    	<optgroup label='".$value."'>"."\n";
		}elseif ($value == '-/-') {
			echo "    	</optgroup>"."\n";
		}elseif ($keyword03 == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select><input type='text' name='keyword03_2' value='$keyword03_2' class='form-category2 form-control' placeholder='番号等' maxlength='4' $disabled><input type='text' name='keyword03_3' value='$keyword03_3' class='form-category2 form-control' placeholder='残量等' maxlength='4' $disabled>"."\n";
}

//-------------------------------------------------------↑
$otherbottle_2 =  preg_replace("/<br>/","\n",$otherbottle_2);
$otherbottle_2 = htmlspecialchars ($otherbottle_2);
$otherbottle_3 =  preg_replace("/<br>/","\n",$otherbottle_3);
$otherbottle_3 = htmlspecialchars ($otherbottle_3);
//-------------------------------------------------------↓
print <<< END_OF_HTML
      </td>
</tr>
<tr>
    <th>その他ボトル <div class="infotip"><abbr title="珍しいボトルなどプルダウンに含まないボトル名などの場合にお使いください。" rel="tooltip">?</abbr></div></th>
      <td>
		<input type="text" name="otherbottle" value="$otherbottle" class="form-category form-control" $disabled>
		<input type="text" name="otherbottle_2" value="$otherbottle_2" class="form-category3 form-control" placeholder="番号等" maxlength="4" $disabled><input type="text" name="otherbottle_3" value="$otherbottle_3" class="form-category2 form-control" placeholder="残量等" maxlength="4" $disabled>
      </td>
</tr>
END_OF_HTML;
if ($company_logs <> ""){ //会社記録オンオフ
//-------------------------------------------------------↑
$company =  preg_replace("/<br>/","\n",$company);
$company = htmlspecialchars ($company);
$section =  preg_replace("/<br>/","\n",$section);
$section = htmlspecialchars ($section);
$section2 =  preg_replace("/<br>/","\n",$section2);
$section2 = htmlspecialchars ($section2);
//-------------------------------------------------------↓
print <<< END_OF_HTML
<tr>
    <th>会社名・所属 <div class="infotip"><abbr title="㈱、㈲や丸囲み数字、ローマ数字、㏍、℡、№などの機種依存文字は文字化けの原因になるので使わないようにしましょう！" rel="tooltip">?</abbr></th>
    <td>
<input type="text" name="company" value="$company" class="form-control" placeholder="会社名　㈱、㈲などの機種依存文字禁止！" style="ime-mode: active;" $disabled><br />
<input type="text" name="section" value="$section" class="form-control" placeholder="所属1" style="ime-mode: active;" $disabled><br />
<input type="text" name="section2" value="$section2" class="form-control" placeholder="所属2" style="ime-mode: active;" $disabled></td>
  </tr>
<tr>
    <th>役職</th>
END_OF_HTML;
//-------------------------------------------------------↑
$degree =  preg_replace("/<br>/","\n",$degree);
$degree = htmlspecialchars ($degree);
//-------------------------------------------------------↓
print <<< END_OF_HTML
    <td>
<input type="text" name="degree" value="$degree" class="form-control" placeholder="代表取締役など" $disabled></td>
  </tr>
END_OF_HTML;
}
if ($addr_logs <> ""){ //住所記録オンオフ
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
		<th>郵便番号 <div class="infotip"><abbr title="数字7桁だけを入力すれば、以下の都道府県や住所を自動入力します。番地やビル名などは入力ください。" rel="tooltip">?</abbr></div></th><td><input type="number" id="zip" name="zip" value="$zip" class="form-zip form-control" placeholder="(数字のみ) 自動変換" maxlength="8" style="ime-mode: disabled;" $disabled></td>
	</tr><tr>
		<th>都道府県</th><td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list4){
	echo "<select name='pref' class='form-zip form-control' id='pref' $disabled>"."\n";
	echo "<option value=''>選択ください</option>"."\n";
	foreach ($word_list4 as $value){
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
		<th>住所番地 <div class="infotip"><abbr title="自動入力では番地以降は自動入力されませんので、番地やビル名などは入力ください。" rel="tooltip">?</abbr></div></th><td><input type="text" name="addr" value="$addr" class="form-control" id="addr" placeholder="番地まで" $disabled></td>
	</tr><tr>
		<th>ビル名等</th><td><input type="text" name="addr2" value="$addr2" class="form-control" id="addr2" placeholder="ビル名及び号室など" style="ime-mode: active;" $disabled></td>
	</tr>

END_OF_HTML;
}
if ($phone_logs <> ""){ //電話記録オンオフ
//-------------------------------------------------------↑
$phone =  preg_replace("/<br>/","\n",$phone);
$phone = htmlspecialchars ($phone);
//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <th>電話番号</th>
    <td><input type="tel" name="phone" value="$phone" class="form-tel form-control" placeholder="数字のみ(ハイフン不要)" maxlength="13" style="ime-mode: disabled;" $disabled></td>
	</tr>
END_OF_HTML;
}
if ($mobile_logs <> ""){ //携帯電話記録オンオフ
//-------------------------------------------------------↑
$mobile =  preg_replace("/<br>/","\n",$mobile);
$mobile = htmlspecialchars ($mobile);
//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <th>携帯電話</th>
    <td><input type="tel" name="mobile" value="$mobile" class="form-tel form-control" placeholder="数字のみ(ハイフン不要)" maxlength="13" style="ime-mode: disabled;" $disabled></td>
	</tr>
END_OF_HTML;
}
if ($friends_logs <> ""){ //友人知人記録オンオフ
//-------------------------------------------------------↑
$friends =  preg_replace("/<br>/","\n",$friends);
$friends = htmlspecialchars ($friends);
//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <th>友人知人情報 <div class="infotip"><abbr title="友人知人情報に入力した内容も検索時にヒットします。" rel="tooltip">?</abbr></div></th>
    <td><div id="friends"><div class="friends_var"><input type="text" name="friends_0" id="friends_0" value="$friends" class="form-control" placeholder="友人知人名" style="ime-mode: active; width: 90%; float:left;" $disabled><button class="friends_del" style="float: right;" title="削除">×</button>
  </div></div>
<div class="clear"></div>
<input type="button" value="+" class="btn friends_add" title="入力項目追加">
</td>
	</tr>
END_OF_HTML;
}
//-------------------------------------------------------↑
$memo =  preg_replace("/<br>/","\n",$memo);
$memo = htmlspecialchars ($memo);
//-------------------------------------------------------↓
print <<< END_OF_HTML
	<tr>
    <th>メモ <div class="infotip"><abbr title="メモに入力した内容も検索時にヒットしますので、重要なキーワードとなる事項は入力されることをお薦めします。また、㈱、㈲や丸囲み数字、ローマ数字、㏍、℡、№などの機種依存文字は文字化けの原因になるので使わないようにしましょう！" rel="tooltip">?</abbr></div></th>
    <td><textarea name="memo" class="form-control" rows="6" style="ime-mode: active;" $disabled>$memo</textarea></td>
  </tr>
END_OF_HTML;
if ($birthday_logs <> ""){ //誕生日記録オンオフ
//-------------------------------------------------------↑
$birthyear =  preg_replace("/<br>/","\n",$birthyear);
$birthyear = htmlspecialchars ($birthyear);
$birthmonth =  preg_replace("/<br>/","\n",$birthmonth);
$birthmonth = htmlspecialchars ($birthmonth);
$birthday =  preg_replace("/<br>/","\n",$birthday);
$birthday = htmlspecialchars ($birthday);
//-------------------------------------------------------↓
print <<< END_OF_HTML
  <tr>
    <th>生年月日 <div class="infotip"><abbr title="生年月日を入力(月まででも可)すれば誕生月に名前の横に★マークを表示し、現在の年齢も表示します。" rel="tooltip">?</abbr></div></th>
    <td>
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list5){
	echo "<select name='birthyear' class='form-birth form-control' $disabled>"."\n";
	echo "<option value=''>選択...年</option>"."\n";
	foreach ($word_list5 as $value){
		if ($birthyear == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
    / 
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list6){
	echo "<select name='birthmonth' class='form-birth form-control' $disabled>"."\n";
	echo "<option value=''>選択...月</option>"."\n";
	foreach ($word_list6 as $value){
		if ($birthmonth == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
    / 
END_OF_HTML;
//-------------------------------------------------------↑
if ($word_list7){
	echo "<select name='birthday' class='form-birth form-control' $disabled>"."\n";
	echo "<option value=''>選択...日</option>"."\n";
	foreach ($word_list7 as $value){
		if ($birthday == $value){
			echo "    	<option value='".$value."' selected>".$value."</option>"."\n";
		}else{
			echo "    	<option value='".$value."'>".$value."</option>"."\n";
		}
	}
	echo "      </select>"."\n";
}

print <<< END_OF_HTML
生
</td>
  </tr>
END_OF_HTML;
}
if ($company_logs <> ""){ //担当者記録オンオフ
//-------------------------------------------------------↑
$pic =  preg_replace("/<br>/","\n",$pic);
$pic = htmlspecialchars ($pic);
//-------------------------------------------------------↓
print <<< END_OF_HTML
<tr>
    <th>担当者 <div class="infotip"><abbr title="担当スタッフの名前などを入力します。退職などで同名の別人の場合もあるので、名前と一緒に時期がわかるようにすると便利です。" rel="tooltip">?</abbr></div></th>
    <td><input type="text" name="pic" value="$pic" class="form-control" style="ime-mode: active;" $disabled></td>
  </tr>
END_OF_HTML;
}
if ($addr_logs <> ""){ //住所記録オンオフ
print <<< END_OF_HTML
<tr>
    <th>DM可否 <div class="infotip"><abbr title="DMを送ってはいけない場合にチェックしておくと、データを利用してDMをおくる際などに便利です。" rel="tooltip">?</abbr></div></th>
    <td>\n
END_OF_HTML;
//-------------------------------------------------------↑
if ($no_dm){
	echo "	<label><input type='checkbox' name='no_dm' value='true' checked $disabled>"."\n";
}else{
	echo "	<label><input type='checkbox' name='no_dm' value='true' $disabled>"."\n";
}
//-------------------------------------------------------↓
print <<< END_OF_HTML
      チェックでDM禁止</label></td>
  <tr>\n
END_OF_HTML;
}
//-------------------------------------------------------↑
if($firsttime <> ""){
	echo "<th>初回登録日時 <div class='infotip'><abbr title='初めて登録した時の日時です。' rel='tooltip'>?</abbr></div></th>"."\n";
	echo "<td>$firsttime"."\n";
	echo "<input type='hidden' id='firsttime' name='firsttime' value='$firsttime' $disabled>"."\n";
}else{
	$now_date = date("Y/m/d H:i");
	echo "<th>初回登録日時 <div class='infotip'><abbr title='初めて登録した時の日時としてこの時間が登録されます。' rel='tooltip'>?</abbr></div></th>"."\n";
	echo "<td>$now_date"."\n";
	echo "<input type='hidden' id='firsttime' name='firsttime' value='$now_date' $disabled>"."\n";
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
	echo "<input type='hidden' id='lasttime' name='lasttime' value='$now_date2' $disabled>"."\n";
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
	<input type="submit" name="Submit" value="$buttom_name" class="btn btn-primary" $disabled>
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

END_OF_HTML;
//-------------------------------------------------------↑
		//出力チェック
		$check = "true";
	}
}
}
if (@!$check){//該当データなし	
//-------------------------------------------------------↓
print <<< END_OF_HTML

<p>登録できません</p>

END_OF_HTML;
//-------------------------------------------------------↑
}

//フッター
html_footer_exj();

//-------------------------------------------------------↓
print <<< END_OF_HTML
<script src="$url/bottle_assets/js/jquery.autoKana.js"></script>
<script src="$url/bottle_assets/js/pickdate/picker.js"></script>
<script src="$url/bottle_assets/js/pickdate/picker.date.js"></script>
<script src="$url/bottle_assets/js/pickdate/legacy.js"></script>
<script src="$url/bottle_assets/js/pickdate/translations/ja_JP.js"></script>
<script src="$url/bottle_assets/js/jquery.add-input-area.min.js"></script>
<script type="text/javascript">
$('.datepicker').pickadate({
  max: true,
  format: 'yyyy年m月d日',
  selectYears: true,
  selectMonths: true
})
</script>
<script type="text/javascript">
$('#friends').addInputArea({
  maximum : 5
});
</script>
</body>
</html>
END_OF_HTML;
//-------------------------------------------------------↑
?>
