<?php
//セッション開始
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

include_once("attestation.php");
require("common.php");
require("../bottle_assets/pageparts.php");
$html_title = "ボトルリスト編集丨顧客・ボトル管理システム";  //ページタイトル

html_header1();
html_header2();
?>

        <div class="bs-component">
          <ul class="breadcrumb">
            <li><a href="./index.php">HOME</a></li>
            <li><a href="./admin.php">管理メニュー</a></li>
			<li class="active">ボトルリスト編集</li>
		</ul>
</div>

<h3  class="acenter">ボトルリスト編集</h3>
<div class="well bs-component">

<form method="post" action="config.php">
<div class="helpbox">
<div class="read_reset_btn f_right">
<input type="submit" name="open_default" value="初期に戻す" class="btn btn-xs btn-default">
</div>
<h4>ご利用方法</h4>
「データ読込み」ボタンを押すことで、プルダウンで表示するボトルメニューのリストが編集できます。
	<ul>
		<li>メニューは必ず1アイテムごとに1行になるように入力します。</li>
		<li>グループごとにまとめるには、まずグループの項目名に「■」を含めて入力し、続けて1行ごとにメニューを入力、末尾には「-/-」のみを入力してください。<div class="infotip"><abbr title="プルダウンで表示するボトルメニューを種類ごとにグループ化するための方法です。決められた記号を入れることで、その範囲をグループとしてまとめ、使いやすくすることができます。" rel="tooltip">i</abbr></div></li>
		<li>特殊記号や機種依存文字、半角カタカナ及びタグを含んだ文字を入力しないでください。</li>
	</ul>
<div class="read_btn">
<input type="submit" name="open" value="データ読込み" class="btn btn-success">
</div>
</div>
<input type="hidden" name="file" value="config/category.dat">
<input type="hidden" name="file_liset" value="config/category_default.dat">
<textarea name="contents" cols="80" class="form-control" rows="20" id="textArea">
<?php
// ファイル内容を表示
$disabled = "disabled";
$file = $_POST['file'];
if ($_POST['open'] && $file) {
  $text = file_get_contents($file);
  $text = htmlspecialchars($text);
  print $text;
  print $disabled = "";
}
$file_liset = $_POST['file_liset'];
if ($_POST['open_default'] && $file_liset) {
  $text2 = file_get_contents($file_liset);
  $text2 = htmlspecialchars($text2);
  print $text2;
  print $disabled = "";
}
?>
</textarea>
<div class="footer_navi">
            <a href="./admin.php" class="btn btn-default">&lt; メニューに戻る</a>
<div class="update_btn">
<input type="submit" name="save" value="更新して保存" class="btn btn-primary" <?php print $disabled ?> />
</div>
<div class="clear"></div></div>

<input type="hidden" name="editfile" value="<?php print $file ?>">
</form>
<?php
// ファイルを保存
$editfile = $_POST['editfile'];
if ($_POST['save'] && $editfile) {
  $fp = @fopen($editfile, 'w');
  if (!$fp) print "このファイルには書き込みできません。<br>\n";
  else {
    $contents = htmlspecialchars($_POST['contents']);
    fwrite($fp, $contents);
    fclose($fp);
    print "書き込み完了しました。<br>\n";
  }
}

//フッター
html_footer();
?>
