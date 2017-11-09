<?php
/*
Plugin Name:Dashboars Extra
Plugin URI: 
Description: Allows you to customise the dashboard.
Author: DEWEY Inc.
Version: 1.0
Author URI: http://www.dewey.co.jp/
*/
function plugin_dashboard_extra() {
	echo "<ul style='margin:0;'>";
    echo "<form name='form' method='post' action='/manager/index.php' target='_blank' /><li style='margin-bottom: 10px;'><strong>ユーザーデータ管理</strong><br />契約ユーザーのプラン情報等の管理。<input name='Submit' type='hidden' value='regist' /><input name='f_id' type='hidden' value='dewey' /><input name='f_pass' type='hidden' value='Tomonori25$x' /><input name='buttom' type='submit' value='LOGIN' style='float:right;margin-top: -13px;' /></form></li>";
    echo "<form name='form' method='post' action='/filemanager.php' target='_blank' /><li style='margin-bottom: 10px;'><strong>ファイルマネージャー</strong><br />ファイルやフォルダの管理。<input type='hidden' name='act' value='dologin' /><input name='ft_user' type='hidden' value='dewey' /><input name='ft_pass' type='hidden' value='Tomonori25' /><input name='buttom' type='submit' value='LOGIN' style='float:right;margin-top: -13px;' /></form></li>";
    echo "<form name='form' method='post' action='/news/asset/admin/index.php' target='_blank' /><li><strong>最新情報管理</strong><br />契約ユーザーへの最新情報管理。<input name='userid' type='hidden' value='dewey' /><input name='password' type='hidden' value='dewey06dewey' /><input name='buttom' type='submit' value='LOGIN' style='float:right;margin-top: -13px;' /></form></li>";
    echo "<form name='form' method='post' action='/mail/login_ctl.cgi' target='_blank' /><li><strong>ユーザーメール配信</strong><br />契約ユーザーへのメール配信管理。<input name='login_id' type='hidden' value='dewey' /><input name='login_pass' type='hidden' value='dewey06dewey' /><input name='buttom' type='submit' value='LOGIN' style='float:right;margin-top: -13px;' /></form></li>";
    echo "</ul>";
}
    //この上のechoを増やせば色々項目を追加できる。

function plugin_dashboard_setup() {
global $user_level;
if ( 0 == $user_level ) {
} else {
	wp_add_dashboard_widget( 'plugin_dashboard_extra', __( '各種管理ページリンク' ),  'plugin_dashboard_extra');
}
}
add_action('wp_dashboard_setup', 'plugin_dashboard_setup');

?>