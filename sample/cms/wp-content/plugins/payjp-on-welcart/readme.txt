=== Plugin Name ===
Contributors: GTI Inc.
Tags: Welcart, e-Commerce, Credit Card, PAY.JP
Requires at least: 4.4
Tested up to: 4.8.1
Stable tag: 2.2

It is developed in Japan.

== Description ==


== Installation ==

1. Upload the entire `usc-e-shop` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==


== Changelog ==

= Ver 2.2 =
2017/09/19
・全体的にコード整理
・定期課金時のボタン・ラベルテキスト変更が出来なかったのを修正

= Ver 1.1.5 = 
2016/12/26
・「定期課金（月単位）を日割り課金にするか」のパラメータがどのような状態でも
　受注メールの内容に	
　「定期課金の今回支払日割計算額は・・・円となっています。」
　の表記が表示されておりました。
　こちらを「日割課金」しない場合では表示しないように修正いたしました。
　この「日割課金」金額についてはPAY.JPに記載してある計算方法で独自に計算し
　出力した結果となりますので実際には「日割課金」しない場合は日割課金がされておりません。
　課金されてもされていなくても表示のみの金額となります。

　このメールに掲載されるメッセージ部分は下記フィルタで調整可能です。
　apply_filters(
	'payjp_order_mail_payment', 	// フィルタ名
	$mes, 	// メールに掲載される元の文章
	$payjp_card_type, 	// カードタイプ（VISA,MasterCardなど）
	$payjp_card_last4, 	// カードの下４桁
	$card_info, 	// （カード番号: MasterCard **** **** **** 4444） の部分
	$hiwari, 	// 日割計算金額
	$payjp_use_recursion_month_prorate	// 日割計算するか 1:する
	);

= Ver 1.1.4 =
2016/12/21
・定期課金で「前回のカード決済」が必ずエラーになってしまう不具合を修正。

= Ver 1.1.3 =
2016/12/20
・「定期課金（月単位）を日割り課金にするか」のパラメータを変更していたものの
　表示の際にチェックが漏れておりましたので状態を反映するように修正しました。

= Ver 1.1.2 =
2016/11/19
・前回のカードで決済について不具合修正

= Ver 1.1.0 - 1.1.1 =
2016/08/11
・定期課金の一時停止と再開機能追加

= Ver 1.0.2 =
2016/09/26
・メッセージの識別子が小文字だったのを修正

= Ver 1.0.1 =
CheckoutHelperからのSubmit時にローディング画像表示

= Ver 1.0 =
Created.
