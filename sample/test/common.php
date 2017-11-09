<?php

//契約ユーザーID
$user = "1001";

define ("file_setting","data/setting.dat");
///////////////////////////////////
// 初期設定ファイルの読み込み    //
///////////////////////////////////
$line_setting = read_setting();
function read_setting(){
	//カスタムファイルの読込み
	$line_setting = "";
	$temp_line = file(file_setting);
	foreach ($temp_line as $value){
		if ($value){
			$value = trim($value);
			list($i,$j) = explode("&&",$value);
			$line_setting[$i] = $j;
		}
	}
	return $line_setting;
}
$company_logs = $line_setting['company_log'];
$addr_logs = $line_setting['addr_log'];
$phone_logs = $line_setting['phone_log'];
$mobile_logs = $line_setting['mobile_log'];
$birthday_logs = $line_setting['birthday_log'];
$pic_logs = $line_setting['pic_log'];
$lastvisit_logs = $line_setting['lastvisit_log'];
$friends_logs = $line_setting['friends_log'];
$dspimg_logs = $line_setting['dspimg_log'];

?>