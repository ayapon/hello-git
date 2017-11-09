<?php
//=============初期設定=======================
//スーパーグローバル変数対策
if(!isset($post)){ $post = $_POST['post']; }
if(!isset($todofuken)){ $todofuken = $_POST['todofuken']; }
if(!isset($addr)){ $addr = $_POST['addr']; }
if(!isset($addr_2)){ $addr_2 = $_POST['addr_2']; }
if(!isset($kaisha)){ $kaisha = $_POST['kaisha']; }
if(!isset($buka)){ $buka = $_POST['buka']; }
if(!isset($buka2)){ $buka2 = $_POST['buka2']; }
if(!isset($yaku)){ $yaku = $_POST['yaku']; }
if(!isset($atena)){ $atena = $_POST['atena']; }
if(!isset($atena)){ $atena = $_POST['atena']; }
if(!isset($atena2)){ $atena2 = $_POST['atena2']; }
if(!isset($keisho)){ $keisho = $_POST['keisho']; }
if(!isset($fsize2_down)){ $fsize2_down = $_POST['fsize2_down']; }
if(!isset($fsize4_down)){ $fsize4_down = $_POST['fsize4_down']; }
if(!isset($kind)){ $kind = $_POST['kind']; }
if(!isset($fonts)){ $fonts = $_POST['fonts']; }

//エスケープ文字対策
$addr = stripslashes($addr);
$addr_2 = stripslashes($addr_2);
$kaisha = stripslashes($kaisha);
$buka = stripslashes($buka);
$buka2 = stripslashes($buka2);
$yaku = stripslashes($yaku);
$atena = stripslashes($atena);

//郵便番号データを整形
//$post = str_replace("-", "", $post);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>宛名ラベル | ナイトビジネスサポート</title>
<link href="../bottle_css/print_style.css" rel="stylesheet" />
<style type="text/css">

<?php
//封筒の種類により表組みの幅・高さを調整。
if($kind == 1){ //長3封筒【縦】
$yubinmark = "〒";

	if($fonts == 1){ //明朝
		echo "body { font-family:'ヒラギノ明朝 ProN W6', 'HiraMinProN-W6', 'HG明朝E', 'ＭＳ Ｐ明朝', 'MS PMincho', 'MS 明朝', serif; }";
	}elseif($fonts == 2){//行書
		echo "body { font-family:'HG行書体','HGP行書体','HGS行書体', cursive; }";
	}else{//ゴシック
		echo "body { font-family:'メイリオ', 'Meiryo', 'ヒラギノ角ゴ ProN W3', 'Hiragino Kaku Gothic ProN','ＭＳ Ｐゴシック', 'MS P Gothic', Verdana, Arial, Helvetica, sans-serif; }";
	}
//-------------------------------------------------------↓
print <<< END_OF_HTML

@media screen {
	body {
		background: url(naga3futou2.jpg) no-repeat 160px 0;
	}
}
@media print {
	.noprint { display: none; margin: 2em 0; text-align: center; font-size: 12px; }
}
	#content {
		width: 320px;
		margin: 200px 0 0 250px;
		top: 0;
		left: 0;
	}
	#postalcode {
		font-size: 24px;
		letter-spacing: 0.2em;
		margin: 0 0 10px 0;
	}
	#address {
		font-size: 17px;
		line-height: 1.8em;
	}
	#address2 {
		font-size: 17px;
		line-height: 1.5em;
	}
	#kaisha {
		font-size: 22px;
		margin: 20px 0 0 0;
		line-height: 1.8em;
	}
	#buka {
		font-size: 16px;
		line-height: 1.3em;
	}
	#yaku {
		font-size: 15px;
		margin: 20px 0 0 0;
		line-height: 1.6em;
	}
	#atena {
		font-size: 32px;
		line-height: 1.8em;
	}
	.noprint {
		margin: 15px 0 0 10px;
		font-size: 11px;
		font-family:'メイリオ', 'Meiryo', 'ヒラギノ角ゴ ProN W3', 'Hiragino Kaku Gothic ProN','ＭＳ Ｐゴシック', 'MS P Gothic', Verdana, Arial, Helvetica, sans-serif;
		color: #ff0000;
	}

END_OF_HTML;

}else{ //長3封筒【横】
$yubinmark = "〒";

	if($fonts == 1){ //明朝
		echo "body { font-family:'ヒラギノ明朝 ProN W6', 'HiraMinProN-W6', 'HG明朝E', 'ＭＳ Ｐ明朝', 'MS PMincho', 'MS 明朝', serif; }";
	}elseif($fonts == 2){//行書
		echo "body { font-family:'HG行書体','HGP行書体','HGS行書体', cursive; }";
	}else{//ゴシック
		echo "body { font-family:'メイリオ', 'Meiryo', 'ヒラギノ角ゴ ProN W3', 'Hiragino Kaku Gothic ProN','ＭＳ Ｐゴシック', 'MS P Gothic', Verdana, Arial, Helvetica, sans-serif; }";
	}
//-------------------------------------------------------↓
print <<< END_OF_HTML

@media screen {
	body {
		background: url(naga3futou.jpg) no-repeat 100px 0;
	}
}
@media print {
	.noprint { display: none; }
}
	#content {
		-moz-transform: rotate(90deg);
		-webkit-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		-ms-transform: rotate(90deg);
		transform: rotate(90deg);
		width: 500px;
		margin: 390px 0 0 100px;
		top: 0;
		left: 0;
	}
	#postalcode {
		font-size: 25px;
		letter-spacing: 0.2em;
		margin: 0 0 10px 0;
	}
	#address {
		font-size: 19px;
		line-height: 1.8em;
	}
	#address2 {
		font-size: 19px;
		line-height: 1.5em;
	}
	#kaisha {
		font-size: 22px;
		margin: 20px 0 0 0;
		line-height: 1.8em;
	}
	#buka {
		font-size: 17px;
		line-height: 1.3em;
	}
	#yaku {
		font-size: 16px;
		margin: 20px 0 0 0;
		line-height: 1.5em;
	}
	#atena {
		font-size: 38px;
		line-height: 1.3em;
		letter-spacing: 0.1em;
	}
	.noprint {
		margin: 15px 0 0 10px;
		font-size: 11px;
		font-family:'メイリオ', 'Meiryo', 'ヒラギノ角ゴ ProN W3', 'Hiragino Kaku Gothic ProN','ＭＳ Ｐゴシック', 'MS P Gothic', Verdana, Arial, Helvetica, sans-serif;
		color: #ff0000;
	}

END_OF_HTML;
//-------------------------------------------------------↑
}
?>
</style>
</head>
<body>
<div class="noprint">※印刷時はブラウザのプリント設定画面でヘッダやフッタの印刷を「なし」に設定してください。<br />
※封筒イメージは印刷されません</div>
<div id="content">
	<div id="postalcode"><?php echo $yubinmark; ?>
<?php	//郵便番号を整形 000-0000
$post_code = "$post";
$post_code = preg_replace("/^(\d{3})(\d{4})$/", "$1-$2", $post_code);
echo $post_code;
?>
	</div>
	<div id="address"><?php echo $todofuken; ?><?php echo $addr; ?></div>
<?php
if($addr_2){
	echo "<div id='address2'>";
	echo $addr_2;
	echo "</div>";
}
?>
<?php
if($kaisha){
	echo "<div id='kaisha'>";
	echo $kaisha;
	echo "</div>";
}
?>
<?php
if($buka){
	echo "<div id='buka'>";
	echo $buka;
	echo " " .$buka2;
	echo "</div>";
}
?>
<?php
if($yaku){
	echo "<div id='yaku'>";
	echo $yaku;
	echo "</div>";
}
?>
<?php
if($atena){
	echo "<div id='atena'>";
	echo $atena;
	if($atena2){
		echo "<br />　" . $atena2;
	}
	if($keisho){
		echo " 御中";
	}else{
		echo " 様";
	}
	echo "</div>";
}
?>
</div>
</body>
</html>