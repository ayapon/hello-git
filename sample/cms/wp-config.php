<?php
/**
 * WordPress の基本設定
 *
 * このファイルは、インストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さずにこのファイルを "wp-config.php" という名前でコピーして
 * 直接編集して値を入力してもかまいません。
 *
 * このファイルは、以下の設定を含みます。
 *
 * * MySQL 設定
 * * 秘密鍵
 * * データベーステーブル接頭辞
 * * ABSPATH
 *
 * @link http://wpdocs.sourceforge.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86
 *
 * @package WordPress
 */

// 注意: 
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.sourceforge.jp/Codex:%E8%AB%87%E8%A9%B1%E5%AE%A4 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - この情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', 'dsx_bottle2');

/** MySQL データベースのユーザー名 */
define('DB_USER', 'dsx_dewey');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'dewey06dewey');

/** MySQL のホスト名 */
define('DB_HOST', 'mysql2107.xserver.jp');

/** データベースのテーブルを作成する際のデータベースの文字セット */
define('DB_CHARSET', 'utf8');

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'z|JI}xc[cSDOwVrzdOFqSVXB0*d=7]o:H#UgiS%m0Y:C(QPqR`@<4FC,G_B!,v-.');
define('SECURE_AUTH_KEY',  'vEt}ry-k-dMW_ _@oKjdeW!KA4|NS&-o0J_YGfj<6e#>4U(k3@~?&7?Ej;sr-1gu');
define('LOGGED_IN_KEY',    'Co.].DY3ef#8X:%DTt}|O)K}-$p+Q%-p[}&H<$1`h<{]Lo-s]96SVBsS6+x^rO Q');
define('NONCE_KEY',        ' ~XgOW{{nR:f8#KSB?(fV*BY&5Jo:puVqy_m]eH.ty0t5 PoOgKV^mv:-lVKs}Pq');
define('AUTH_SALT',        'fYGlE#)ni++9)E_vD#U$M|Zw=)*+*Cz jPA2E2v:|f$!;tx u^b8n-7|]g|2/F+(');
define('SECURE_AUTH_SALT', '6FEu.W|2EZ_}]HNI+p!#G2Dm9bG%8;QB=l%S~u+-f6-0RFT?K6T}gy%#Tha~z5##');
define('LOGGED_IN_SALT',   '#m}ouns>F9c,|w+KP-_8&.Jxhym[|U-?kY=FV* B2KSX)aaE(m_|w(AK,U8GQ?nN');
define('NONCE_SALT',       'I-2)&, bX=^-3:H7p^PPs+7S|!`*G{QP!WoEV2|@1c+g{CrCVshDs*q5Nbv6Y246');

/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'nw_';

/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 *
 * その他のデバッグに利用できる定数については Codex をご覧ください。
 *
 * @link http://wpdocs.osdn.jp/WordPress%E3%81%A7%E3%81%AE%E3%83%87%E3%83%90%E3%83%83%E3%82%B0
 */
define('WP_DEBUG', false);

/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
