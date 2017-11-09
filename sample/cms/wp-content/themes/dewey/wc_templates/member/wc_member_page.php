<?php
/**
 * @package Welcart
 * @subpackage Welcart_Basic
 */

get_header();
?>
<section class="container spacer">
<div id="primary" class="site-content">
	<div id="content" class="member-page" role="main">

	<?php if( have_posts() ) : usces_remove_filter(); ?>

		<article class="post" id="wc_<?php usces_page_name(); ?>">

			<h1 class="member_page_title"><?php _e('My page', 'welcart_basic'); ?></h1>

			<div id="memberpages">
				<div class="whitebox">
					<div id="memberinfo">

						<table>
							<tr>
								<th scope="row"><?php _e('member number', 'usces'); ?></th>
								<td class="num"><?php usces_memberinfo( 'ID' ); ?></td>
								<th><?php _e('Strated date', 'usces'); ?></th>
								<td><?php usces_memberinfo( 'registered' ); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Full name', 'usces'); ?></th>
								<td><?php esc_html_e(sprintf(_x('%s', 'honorific', 'usces'), usces_localized_name( usces_memberinfo( 'name1', 'return' ), usces_memberinfo( 'name2', 'return' ), 'return' ))); ?></td>
							<?php if( usces_is_membersystem_point() ) : ?>
								<th><?php _e('The current point', 'usces'); ?></th>
								<td class="num"><?php usces_memberinfo( 'point' ); ?></td>
							<?php else : ?>
								<th scope="row"><?php _e('e-mail adress', 'usces'); ?></th>
								<td><?php usces_memberinfo('mailaddress1'); ?></td>
							<?php endif; ?>
								<?php echo apply_filters( 'usces_filter_memberinfo_page_reserve', $html_reserve, usces_memberinfo( 'ID', 'return' ) ); ?>
							</tr>
						</table>

	<iframe src="/manager/userinfo/?num=<?php usces_memberinfo( 'ID' ); ?>" id="userinfo" class="autoHeight"></iframe>

						<ul class="member_submenu">
							<li class="member-edit"><a href="#edit"><?php _e('To member information editing', 'usces'); ?></a></li>
							<li class="member-add"><a href="planlist">追加店舗のお申込み</a></li>
							<?php do_action( 'usces_action_member_submenu_list' ); ?>
							<li class="member-logout"><?php usces_loginout(); ?></li>
						</ul>
<div class="clear"></div>

						<div class="header_explanation">
							<?php do_action( 'usces_action_memberinfo_page_header' ); ?>
						</div><!-- .header_explanation -->

						<h3><?php _e('Purchase history', 'usces'); ?></h3>

<!--						<div class="currency_code"><?php _e('Currency','usces'); ?> : <?php usces_crcode(); ?></div>-->
						<?php usces_member_history(); ?>

						<h3><a name="edit"></a><?php _e('Member information editing', 'usces'); ?>（ご契約ユーザー様情報の編集。ご利用アプリのログイン情報ではありません。）</h3>

						<div class="error_message"><?php usces_error_message(); ?></div>

	<form action="<?php usces_url('member'); ?>#edit" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
	<table class="customer_form">
					<tr class="customkey_shopname">
					<th scope="row"><em>＊</em>会社名又は店舗名</th><td colspan="2"><input type="text" name="custom_member[shopname]" value="<?php usces_get_custom_field_value( 'member','shopname',usces_memberinfo( 'ID', 'return' )); ?>" /></td>
					</tr>
					<tr class="customkey_position">
					<th scope="row">ご役職</th><td colspan="2"><input type="text" name="custom_member[position]" value="<?php usces_get_custom_field_value( 'member','position',usces_memberinfo( 'ID', 'return' )); ?>" /></td>
					</tr><tr class="inp1">
    <th width="127" scope="row"><em>＊</em>お名前</th><td class="name_td">姓<input name="member[name1]" id="name1" type="text" value="<?php usces_memberinfo('name1'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td><td class="name_td">名<input name="member[name2]" id="name2" type="text" value="<?php usces_memberinfo('name2'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td></tr><tr class="inp1">
    <th scope="row">フリガナ</th><td>姓<input name="member[name3]" id="name3" type="text" value="<?php usces_memberinfo('name3'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td><td>名<input name="member[name4]" id="name4" type="text" value="<?php usces_memberinfo('name4'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td></tr>
<script type="text/javascript" src="https://zipaddr-com.ssl-xserver.jp/js/zipaddrx.js?v=1.18" charset="UTF-8"></script><script type="text/javascript" charset="UTF-8">function zipaddr_ownb(){D.wp='1';D.welcart='1';D.min=7;D.uver='4.6.1';}</script>	
<tr>
    <th scope="row"><em>＊</em>郵便番号</th>
    <td colspan="2"><input name="member[zipcode]" id="zipcode" type="text" value="<?php usces_memberinfo('zipcode'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: inactive" /></td>
    </tr>
<tr>
    <th scope="row"><em>＊</em>都道府県</th>
    <td colspan="2"><input type="hidden" name="member[country]" id="member_country" value="JP">
<?php 
$pref_data = usces_memberinfo('pref', 'return');
//echo "$pref_data";
if ($pref_data === "北海道") { $pref_select01 = "selected"; }
if ($pref_data === "青森県") { $pref_select02 = "selected"; }
if ($pref_data === "岩手県") { $pref_select03 = "selected"; }
if ($pref_data === "宮城県") { $pref_select04 = "selected"; }
if ($pref_data === "秋田県") { $pref_select05 = "selected"; }
if ($pref_data === "山形県") { $pref_select06 = "selected"; }
if ($pref_data === "福島県") { $pref_select07 = "selected"; }
if ($pref_data === "茨城県") { $pref_select08 = "selected"; }
if ($pref_data === "栃木県") { $pref_select09 = "selected"; }
if ($pref_data === "群馬県") { $pref_select10 = "selected"; }
if ($pref_data === "埼玉県") { $pref_select11 = "selected"; }
if ($pref_data === "千葉県") { $pref_select12 = "selected"; }
if ($pref_data === "東京都") { $pref_select13 = "selected"; }
if ($pref_data === "神奈川県") { $pref_select14 = "selected"; }
if ($pref_data === "新潟県") { $pref_select15 = "selected"; }
if ($pref_data === "富山県") { $pref_select16 = "selected"; }
if ($pref_data === "石川県") { $pref_select17 = "selected"; }
if ($pref_data === "福井県") { $pref_select18 = "selected"; }
if ($pref_data === "山梨県") { $pref_select19 = "selected"; }
if ($pref_data === "長野県") { $pref_select20 = "selected"; }
if ($pref_data === "岐阜県") { $pref_select21 = "selected"; }
if ($pref_data === "静岡県") { $pref_select22 = "selected"; }
if ($pref_data === "愛知県") { $pref_select23 = "selected"; }
if ($pref_data === "三重県") { $pref_select24 = "selected"; }
if ($pref_data === "滋賀県") { $pref_select25 = "selected"; }
if ($pref_data === "京都府") { $pref_select26 = "selected"; }
if ($pref_data === "大阪府") { $pref_select27 = "selected"; }
if ($pref_data === "兵庫県") { $pref_select28 = "selected"; }
if ($pref_data === "奈良県") { $pref_select29 = "selected"; }
if ($pref_data === "和歌山県") { $pref_select30 = "selected"; }
if ($pref_data === "鳥取県") { $pref_select31 = "selected"; }
if ($pref_data === "島根県") { $pref_select32 = "selected"; }
if ($pref_data === "岡山県") { $pref_select33 = "selected"; }
if ($pref_data === "広島県") { $pref_select34 = "selected"; }
if ($pref_data === "山口県") { $pref_select35 = "selected"; }
if ($pref_data === "徳島県") { $pref_select36 = "selected"; }
if ($pref_data === "香川県") { $pref_select37 = "selected"; }
if ($pref_data === "愛媛県") { $pref_select38 = "selected"; }
if ($pref_data === "高知県") { $pref_select39 = "selected"; }
if ($pref_data === "福岡県") { $pref_select40 = "selected"; }
if ($pref_data === "佐賀県") { $pref_select41 = "selected"; }
if ($pref_data === "長崎県") { $pref_select42 = "selected"; }
if ($pref_data === "熊本県") { $pref_select43 = "selected"; }
if ($pref_data === "大分県") { $pref_select44 = "selected"; }
if ($pref_data === "宮崎県") { $pref_select45 = "selected"; }
if ($pref_data === "鹿児島県") { $pref_select46 = "selected"; }
if ($pref_data === "沖縄県") { $pref_select47 = "selected"; }
echo <<<EOF
<select name="member[pref]" id="member_pref" class="pref">
	<option value="--選択--">--選択--</option>
	<option value="北海道" $pref_select01>北海道</option>
	<option value="青森県" $pref_select02>青森県</option>
	<option value="岩手県" $pref_select03>岩手県</option>
	<option value="宮城県" $pref_select04>宮城県</option>
	<option value="秋田県" $pref_select05>秋田県</option>
	<option value="山形県" $pref_select06>山形県</option>
	<option value="福島県" $pref_select07>福島県</option>
	<option value="茨城県" $pref_select08>茨城県</option>
	<option value="栃木県" $pref_select09>栃木県</option>
	<option value="群馬県" $pref_select10>群馬県</option>
	<option value="埼玉県" $pref_select11>埼玉県</option>
	<option value="千葉県" $pref_select12>千葉県</option>
	<option value="東京都" $pref_select13>東京都</option>
	<option value="神奈川県" $pref_select14>神奈川県</option>
	<option value="新潟県" $pref_select15>新潟県</option>
	<option value="富山県" $pref_select16>富山県</option>
	<option value="石川県" $pref_select17>石川県</option>
	<option value="福井県" $pref_select18>福井県</option>
	<option value="山梨県" $pref_select19>山梨県</option>
	<option value="長野県" $pref_select20>長野県</option>
	<option value="岐阜県" $pref_select21>岐阜県</option>
	<option value="静岡県" $pref_select22>静岡県</option>
	<option value="愛知県" $pref_select23>愛知県</option>
	<option value="三重県" $pref_select25>三重県</option>
	<option value="滋賀県" $pref_select25>滋賀県</option>
	<option value="京都府" $pref_select26>京都府</option>
	<option value="大阪府" $pref_select27>大阪府</option>
	<option value="兵庫県" $pref_select28>兵庫県</option>
	<option value="奈良県" $pref_select29>奈良県</option>
	<option value="和歌山県" $pref_select30>和歌山県</option>
	<option value="鳥取県" $pref_select31>鳥取県</option>
	<option value="島根県" $pref_select32>島根県</option>
	<option value="岡山県" $pref_select33>岡山県</option>
	<option value="広島県" $pref_select34>広島県</option>
	<option value="山口県" $pref_select35>山口県</option>
	<option value="徳島県" $pref_select36>徳島県</option>
	<option value="香川県" $pref_select37>香川県</option>
	<option value="愛媛県" $pref_select38>愛媛県</option>
	<option value="高知県" $pref_select39>高知県</option>
	<option value="福岡県" $pref_select40>福岡県</option>
	<option value="佐賀県" $pref_select41>佐賀県</option>
	<option value="長崎県" $pref_select42>長崎県</option>
	<option value="熊本県" $pref_select43>熊本県</option>
	<option value="大分県" $pref_select44>大分県</option>
	<option value="宮崎県" $pref_select45>宮崎県</option>
	<option value="鹿児島県" $pref_select46>鹿児島県</option>
	<option value="沖縄県" $pref_select47>沖縄県</option>
</select>
EOF;
?>
</td>
    </tr>
    <tr class="inp2">
    <th scope="row"><em>＊</em>市区郡町村</th>
    <td colspan="2"><input name="member[address1]" id="address1" type="text" value="<?php usces_memberinfo('address1'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>
    </tr>
    <tr>
    <th scope="row"><em>＊</em>番地</th>
    <td colspan="2"><input name="member[address2]" id="address2" type="text" value="<?php usces_memberinfo('address2'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>
    </tr>
    <tr>
    <th scope="row">ビル名・号室等</th>
    <td colspan="2"><input name="member[address3]" id="address3" type="text" value="<?php usces_memberinfo('address3'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: active" /></td>
    </tr>
    <tr>
    <th scope="row"><em>＊</em>電話番号</th>
    <td colspan="2"><input name="member[tel]" id="tel" type="tel" value="<?php usces_memberinfo('tel'); ?>" onKeyDown="if (event.keyCode == 13) {return false;}" style="ime-mode: inactive" /></td>
		<tr>
			<th scope="row"><?php _e('e-mail adress', 'usces'); ?></th>
			<td colspan="2"><input name="member[mailaddress1]" id="mailaddress1" type="text" value="<?php usces_memberinfo('mailaddress1'); ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('password', 'usces'); ?></th>
			<td colspan="2"><input class="hidden" value=" " /><input name="member[password1]" id="password1" type="password" value="<?php usces_memberinfo('password1'); ?>" autocomplete="off" placeholder="<?php _e('Leave it blank in case of no change.', 'usces'); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Password (confirm)', 'usces'); ?></th>
			<td colspan="2"><input name="member[password2]" id="password2" type="password" value="<?php usces_memberinfo('password2'); ?>" placeholder="<?php _e('Leave it blank in case of no change.', 'usces'); ?>" />
			</td>
		</tr>
	</table>
	<input name="member_regmode" type="hidden" value="editmemberform" />
	<div class="send">
	<input name="top" type="button" value="<?php _e('Back to the top page.', 'usces'); ?>" onclick="location.href='<?php echo home_url(); ?>'" class="backtotop" />
	<input name="editmember" type="submit" value="<?php _e('update it', 'usces'); ?>" />
	<input name="deletemember" type="submit" value="<?php _e('delete it', 'usces'); ?>" onclick="return confirm('<?php _e('All information about the member is deleted. Are you all right?', 'usces'); ?>');" />
	</div>
	<?php do_action('usces_action_memberinfo_page_inform'); ?>
	</form>

						<div class="footer_explanation">
							<?php do_action( 'usces_action_memberinfo_page_footer' ); ?>
						</div><!-- .footer_explanation -->

					</div><!-- #memberinfo -->
				</div><!-- .whitebox -->
			</div><!-- #memberpages -->

		</article><!-- .post -->

	<?php else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>

	</div><!-- #content -->
</div><!-- #primary -->
</section>

<?php get_footer(); ?>
