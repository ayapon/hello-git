<?php
/***********************************************************
*	
*	GD画像リサイズクラス
*	
*	@package	resize_image
*	@author		oldoffice.com
*	@since		PHP 5.*
*	@ver		2.04
*
*	@history
*		2010-12-14 クラス作成
*		2011-02-14 RETURN出力に変更
*		2011-02-22 W,Hのどちらかを「*」でauto指定
*		2011-02-22 リサイズタイプを選択できる機能を追加
*		2011-02-22 引数が適切でない場合の処理追加
*		2011-03-28 http～のファイルに対応
*		2011-04-25 引数の値を再設定
*		2011-04-26 WordPressプラグインに対応
*		2011-05-04 絶対パスにも対応
*
***********************************************************/
class resize_image {
	public $new_file_path;
	public $new_width;           // 生成された画像サイズ
	public $new_height;
	
	private $img_fname_arr;      // [.]で分割されたファイル名-配列
	private $img_ext;
	private $temp_image;
	private $create_img_width;   // 指定した画像サイズ
	private $create_img_height;
	private $temp_image_width;   // アップされた画像サイズ
	private $temp_image_height;
	private $img_create;
	private $src_file_realpath;
	private $new_file_realpath;
	
	protected $rootRealpath;
	protected $url;
	
	### constructor
	
	function __construct() {
	
		$this->url = 'http://'.$_SERVER["SERVER_NAME"];  //末尾のスラッシュなし
		$this->rootRealpath = $_SERVER["DOCUMENT_ROOT"]; //末尾のスラッシュなし
	}
/*----------------------------------------------------------
	■ GD画像リサイズ
	
	引数01 ：画像ファイルパス
	引数02 ：生成画像の横サイズ　※自動の場合は「auto」
	引数03 ：生成画像の縦サイズ　※自動の場合は「auto」
	引数04 ：リサイズタイプ：
	         ・縮小＆トリミング＝「fit」※default
	         ・サイズ内に縮小＝「resize」※縮小は､縦横枠内に収まるように処理
	         ・横幅のみ縮小＝「wfit」※縮小は､縦横枠内に収まるように処理
	         ・縦幅のみ縮小＝「hfit」※縮小は､縦横枠内に収まるように処理
	
	画像ファイルパス「dir/***.jpg」w200,h300の場合（URL可）
	「***-200x300.jpg」のファイル名があればそのまま表示
	なければリサイズ＆トリミング画像を作成の上表示します。
	
-----------------------------------------------------------*/
	public function disp_resize_img_path( $src_img_path, $create_img_width, $create_img_height, $type = 'fit' ) {
	
		/* realpath変換 */
	
		if( substr( $src_img_path, 0 ,1 ) == "/" ) {
			$src_file_realpath = $this->rootRealpath.$src_img_path;
		} else {
			$src_file_realpath = str_replace( $this->url, $this->rootRealpath, $src_img_path );
		}
	
		/* ファイルの存在チェック */
	
		if( !is_file( $src_file_realpath ) ) {
		
			return 'noimage';
			
		} else {
	
			/* 画像情報 */
			
			$img_fname_arr = explode( ".", $src_img_path );
			
			// 拡張子を除いた画像ファイル名（ファイル名に「.」「.jpg」が入っている時にも対応）
			$img_first_name = $img_fname_arr[0];
			
			for( $i = 1; $i < ( count( $img_fname_arr ) - 1 ); $i++ ) {
				$img_first_name .= ".".$img_fname_arr[$i];
			}
			
			// 画像拡張子（小文字）
			$img_ext = strtolower( $img_fname_arr[ count( $img_fname_arr ) - 1 ] );
			
			switch($type) {
				case 'fit':
					$create_img_width = intval( $create_img_width );
					$create_img_height = intval( $create_img_height );
					$img_create = ( is_numeric( $create_img_width ) && is_numeric( $create_img_height ) ) ? 'on' : 'off';
					break;
				case 'resize':
					$create_img_width = intval( $create_img_width );
					$create_img_height = intval( $create_img_height );
					$img_create = ( is_numeric( $create_img_width ) && is_numeric( $create_img_height ) ) ? 'on' : 'off';
					break;
				case 'wfit':
					$create_img_width = intval( $create_img_width );
					$create_img_height = 'auto';
					$img_create = ( is_numeric( $create_img_width ) ) ? 'on' : 'off';
					break;
				case 'hfit':
					$create_img_width = 'auto';
					$create_img_height = intval( $create_img_height );
					$img_create = ( is_numeric( $create_img_height ) ) ? 'on' : 'off';
					break;
				default:
					$img_create = 'off';
			}
			
			// 生成画像パス
			if( $img_create == 'on' ) {
				$new_file_path = $img_first_name.'-'.$type.'-'.$create_img_width.'x'.$create_img_height.'.'.$img_ext; //作成される画像ファイル名
			} else {
				$new_file_path = $src_img_path; //作成されなかった時は元画像
			}
			
			
		/* 画像処理 */
			
			if( !is_file( $new_file_path ) && ( $img_ext == 'jpg' || $img_ext == 'gif' || $img_ext == 'png' ) ) {
				
				//拡張子により処理を分岐
				switch( $img_ext ) {
					case 'jpg':
						$temp_image = ImageCreateFromJPEG( $src_file_realpath );
						break;
					case 'gif':
						$temp_image = ImageCreateFromGIF( $src_file_realpath );
						break;
					case 'png':
						$temp_image = ImageCreateFromPNG( $src_file_realpath );
						break;
				}
				
				// アップされた画像のサイズ
				$temp_image_width = ImageSX( $temp_image ); //横幅（px）
				$temp_image_height = ImageSY( $temp_image ); //縦幅（px）
	
				
				/*- fit -*/
				if( $type == 'fit' ) {
					
					// 対象画像-case横長
					if( ( $temp_image_width / $temp_image_height ) > ( $create_img_width / $create_img_height ) ) {
						$new_height = $create_img_height;
						$rate       = $new_height / $temp_image_height; //縦横比
						$new_width  = $rate * $temp_image_width;
						$x = ( $new_width - $create_img_width ) / 2;
						$y = 0;
						
					// 対象画像-case縦長
					} else {
						$new_width  = $create_img_width;
						$rate       = $new_width / $temp_image_width; //縦横比
						$new_height = $rate * $temp_image_height;
						$x = 0;
						$y = ( $new_height - $create_img_height ) / 2;
					}
					
					$new_image = ImageCreateTrueColor( $create_img_width, $create_img_height ); //空画像
				
				/*- resize -*/
				} elseif ( $type == 'resize' ) {
					
					// 対象画像-サイズが収まる場合
					if( ( $temp_image_width < $create_img_width ) && ( $temp_image_height < $create_img_height ) ) {
						$new_width  = $temp_image_width;
						$new_height = $temp_image_height;
						$x = 0;
						$y = 0;
					
					// 対象画像-case横長
					} elseif( ( $temp_image_width / $temp_image_height ) > ( $create_img_width / $create_img_height ) ) {
						$new_width  = $create_img_width;
						$rate       = $new_width / $temp_image_width; //縦横比
						$new_height = $rate * $temp_image_height;
						$x = 0;
						$y = 0;
						
					// 対象画像-case縦長
					} else {
						$new_height = $create_img_height;
						$rate       = $new_height / $temp_image_height; //縦横比
						$new_width  = $rate * $temp_image_width;
						$x = 0;
						$y = 0;
					}
					
					$new_image = ImageCreateTrueColor( $new_width, $new_height ); //空画像
				
				/*- wfit -*/
				} elseif ( $type == 'wfit' ) {
					
					// 対象画像 : create_img_widthは数値
					$new_width  = $create_img_width;
					$rate       = $new_width / $temp_image_width; //縦横比
					$new_height = $rate * $temp_image_height;
					$x = 0;
					$y = 0;
						
					$new_image = ImageCreateTrueColor( $new_width, $new_height ); //空画像
				
				/*- hfit -*/
				} elseif ( $type == 'hfit' ) {
					
					// 対象画像 : create_img_heightは数値
					$new_height = $create_img_height;
					$rate       = $new_height / $temp_image_height; //縦横比
					$new_width  = $rate * $temp_image_width;
					$x = 0;
					$y = 0;
						
					$new_image = ImageCreateTrueColor( $new_width, $new_height ); //空画像
				}
				
				ImageCopyResampled( $new_image, $temp_image, 0, 0, $x, $y, $new_width, $new_height, $temp_image_width, $temp_image_height );
				
				// realpathで画像生成
				if( substr( $new_file_path, 0 ,1 ) == "/" ) {
					$new_file_realpath = $this->rootRealpath.$new_file_path;
				} else {
					$new_file_realpath = str_replace( $this->url, $this->rootRealpath, $new_file_path );
				}
				ImageJPEG( $new_image, $new_file_realpath, 100 ); //3rd引数:クオリティー（0-100）
			
				imagedestroy( $temp_image );
				imagedestroy( $new_image );
			}
				
			return $new_file_path;
		}
	}
}
?>
