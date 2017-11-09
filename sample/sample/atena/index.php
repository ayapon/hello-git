<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=utf-8">
<TITLE>封筒宛名印刷</TITLE>
</HEAD>
<BODY>
<P><FONT SIZE="5">封筒宛名印刷</FONT></P>
<HR><FONT SIZE="2">※印刷に必要なブラウザの設定…ヘッダー・フッター表示を空白、上下左右の余白「10mm」、文字のサイズ「中」。</FONT>
<P>
<FORM ACTION="atena_label.php" METHOD="POST" ENCTYPE="application/x-www-form-urlencoded" TARGET="_blank">
<INPUT TYPE="HIDDEN" NAME="from" VALUE="free">
<TABLE BORDER="1" CELLSPACING="0" WIDTH="600">
	<TR>
		<TD WIDTH="15%">郵便番号</TD>
		<TD><INPUT TYPE="TEXT" NAME="post" SIZE="14">　<FONT SIZE="2" COLOR="#CC0000">*例：760-0080（半角英数で）</FONT></TD>
	</TR>
	<TR>
		<TD WIDTH="15%">都道府県</TD>
		<TD><SELECT NAME="todofuken">

<?php
include("./todofuken_opt.php");
?>
		</SELECT></TD>
	</TR>
	<TR>
		<TD WIDTH="15%">住所</TD>
		<TD><INPUT TYPE="TEXT" NAME="addr" SIZE="45">　<FONT SIZE="2" COLOR="#CC0000">*長い場合は二行に分ける事！</FONT><BR><INPUT TYPE="TEXT" NAME="addr_2" SIZE="45"><FONT SIZE="2">（マンション等）　<INPUT TYPE="CHECKBOX" NAME="fsize2_down" VALUE="1">フォントサイズを下げる</FONT></TD>
	</TR>
	<TR>
		<TD WIDTH="15%">会社名</TD>
		<TD><INPUT TYPE="TEXT" NAME="kaisha" SIZE="40"></TD>
	</TR>
	<TR>
		<TD WIDTH="15%">部課・役職</TD>
		<TD><INPUT TYPE="TEXT" NAME="buka_yaku" SIZE="40"></TD>
	</TR>
	<TR>
		<TD WIDTH="15%">宛名</TD>
		<TD><INPUT TYPE="TEXT" NAME="atena" SIZE="30">　<FONT SIZE="2" COLOR="#CC0000">*長い場合は二行に分ける事！</FONT><BR><INPUT TYPE="TEXT" NAME="atena2" SIZE="30">　<INPUT TYPE="CHECKBOX" NAME="fsize4_down" VALUE="1"><FONT SIZE="2">フォントサイズを下げる</FONT></TD>
	</TR>
	<TR>
		<TD WIDTH="15%">敬称</TD>
		<TD><INPUT TYPE="RADIO" NAME="keisho" VALUE="0" CHECKED>様 　<INPUT TYPE="RADIO" NAME="keisho" VALUE="1">御中</TD>
	</TR>
</TABLE>
<FONT SIZE="2" COLOR="blue">※印刷可能範囲は、郵便番号表示の右端辺りまでです。（大型封筒の場合はもう少し余裕あり。）<BR>住所・宛名が長すぎて印刷が封筒からはみ出る場合は、各フォントサイズを下げる事。</FONT>
</P>

<P>＜印刷画面を表示後、出力プリンタにインクジェットプリンタを指定して印刷する事。＞<BR>
<INPUT TYPE="RADIO" NAME="kind" VALUE="0" CHECKED>会社通常封筒<BR>
<INPUT TYPE="RADIO" NAME="kind" VALUE="1">会社大型封筒　<FONT SIZE="2" COLOR="#CC0000">*印刷時、プリンタの詳細設定で出力サイズをB4に変更する事。</FONT><BR>
<INPUT TYPE="RADIO" NAME="kind" VALUE="2">小型茶封筒</P>

<P><INPUT TYPE="SUBMIT" NAME="Submit" VALUE="印刷画面表示">　<INPUT TYPE="RESET" NAME="Reset" VALUE="リセット">
</FORM>

</BODY>
</HTML>