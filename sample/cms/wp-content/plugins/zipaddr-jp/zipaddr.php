<?php
function zipaddr_jp_change($output, $opt=""){
$ac= '4'; // 4:無償,2:有償,3:御社,(4:スピードアップ版)
$kt= '7'; // 5-7:ガイダンス表示桁数
$ta= "";  // 縦
$yo= "";  // 横
$pf= "";  // pc-fsize
$sf= "";  // sp-fsize
$fo= "";  // focus
$si= "";  // sysid
$dl= "-"; // dli
$pr= "";  // param
$ph= "";  // placeholder
$dr= "";  // direct
$fname= zipaddr_FILE1;
if( file_exists($fname) ) { // ファイルの確認
	$data= trim( file_get_contents($fname) );
	$prm= explode(",", $data);
	while( count($prm) < 8 ) {$prm[]="";}
	$ac= $prm[0];
	$kt= $prm[1];
	$ta= $prm[2];
	$yo= $prm[3];
	$pf= $prm[4];
	$sf= $prm[5];
	$fo= $prm[6];
	$si= $prm[7];
	$dl= isset($prm[8]) ?  $prm[8] : "-";
	$pr= isset($prm[9]) ?  $prm[9] : "";
	$ph= isset($prm[10])?  $prm[10]: "";
	$dr= isset($prm[11])?  $prm[11]: "";
}
     if( strstr($output,'zip') == true ) {;} // keyword(1)
else if( strstr($output,'postc')==true ) {;} // keyword(2)
else if( $dr != "" ) {;}
else {return $output;}

if( $kt < "5" || "7" < $kt ) $kt= "7";
if( $pf < 12  || 20  < $pf ) $pf= "";
if( $sf < 12  || 20  < $sf ) $sf= "";
if( isset($_SERVER['HTTPS']) ) {
	$http= (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS']=='off' ) ?  'http' : 'https';
}
else $http= 'http';
$svr= isset($_SERVER['SERVER_NAME']) ?  $_SERVER['SERVER_NAME'] : "";
$pth= $http.'://'.$svr; // 実働環境
$ul= 'https://zipaddr.com/js/zipaddr7.js';
$u4= 'https://zipaddr.com/js/zipaddrx.js';
$u2='http://zipaddr2.com/js/zipaddr3.js';
$u3=$pth.'/js/zipaddr.js';
$uls= 'https://zipaddr.com/js/zipaddr7.js';
$u4s= 'https://zipaddr.com/js/zipaddrx.js';
$u2s='https://zipaddr2-com.ssl-sixcore.jp/js/zipaddr3.js';
$ph2='https://zipaddr2-com.ssl-sixcore.jp/css/zipaddr.css';
	 if( $ac == "3" ) $lpath= $pth.'/js/zipaddr.css';
else if( $ac == "2" ) {
	$lpath= $pth.'/css/zipaddr.css';
	$wk= @file_get_contents($lpath);
	$wk2=strstr($wk,"autozip");
	if( empty($wk) || empty($wk2) ) $lpath= $ph2; // 定義がなければ補う
}
else $lpath= '';
	$wp_version= get_bloginfo('version');
	$ssl= '1';
	if( $opt != "" ) $ac= "4";   // welcartはzipaddrx.js
	if( $ac == "1" ) $ac= "4";
	 if( $ac == "3" ) $uls= $u3;
else if( $ac == "2"&& $ssl == "1" ) $uls= $u2s;
else if( $ac == "2" ) $uls= $u2;
else if( $ac == "4"&& $ssl == "1" ) $uls= $u4s;
else if( $ac == "4" ) $uls= $u4;
else if( $ssl== "1" ) $uls= $uls;
else $uls= $ul;
$pre= $ac=="4" ?  "D." : "ZP.";
$js = '<script type="text/javascript" src="'.$uls.'?v='.zipaddr_VERS.'" charset="UTF-8"></script>';
$js.= '<script type="text/javascript" charset="UTF-8">function zipaddr_ownb(){';
$js.= $pre."wp='1';";
if( $opt!="") $js.= $pre."welcart='1';";
$js.= $pre.'min='.$kt.';';
if( $ta!="" ) $js.= $pre.'top='. $ta.';';
if( $yo!="" ) $js.= $pre.'left='.$yo.';';
if( $pf!="" ) $js.= $pre.'pfon='.$pf.';';
if( $sf!="" ) $js.= $pre.'sfon='.$sf.';';
if( $fo!="" ) $js.=$pre."focus='".$fo."';";
if( $si!="" ) $js.=$pre."sysid='".$si."';";
              $js.= $pre."dli='".$dl."';";
if( $ph!="" ) $js.= $pre."holder='".$ph."';";
if( defined('zipaddr_IDENT') && zipaddr_IDENT == "3" ) $js.= $pre."usces='1';";
$js.= $pre."uver='".$wp_version."';";
$js.= '}</script>';
if( $ac=="2" || $ac=="3" ) $js.= '<link rel="stylesheet" href="'.$lpath.'" />';
if( $pr!="" ) {
	$pr= str_replace("|", ",", $pr);
	$js.= '<input type="hidden" name="zipaddr_param" id="zipaddr_param" value="'.$pr.'">';
}
$ky = '<form';
	if( !empty($opt) ) $ans=$output.$js;
	else
	if( !empty($dr) ) {
		$ans= $output;
		$urlh= isset($_SERVER['REQUEST_URI']) ?  $_SERVER['REQUEST_URI'] : "";
		$wk= explode(";", $dr);
		foreach($wk as $ka => $da){
			if( strstr($urlh,$da)==true ){$ans=$output.$js; break;}
		}
	}
	else  $ans=str_ireplace($ky, $js.$ky, $output);
	return $ans;
}

function zipaddr_jp_usces($formtag, $type, $data){
	return zipaddr_jp_change($formtag, "1");
}

function zipaddr_jp_welcart($script){
$addon="
if(typeof Zip.welorder==='function'){
	var wk1= $('#delivery_country').val();
	var wk2= $('#delivery_pref').val();
	if( wk1!='' && wk2!='' ) {delivery_country=wk1; delivery_pref=wk2;}
}
";
	$keywd1= "if(delivery_days[selected]";
	$wk0= strstr($script,$keywd1);
	if( !empty($wk0) ){$script= str_replace($keywd1, $addon.$keywd1, $script);}
	return $script;
}
?>
