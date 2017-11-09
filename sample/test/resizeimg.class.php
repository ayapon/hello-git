<?php

/***********************************************************
*	
*	GD画像リサイズクラス
*	
*	@package	resizeimage
*	@author		oldoffice.com
*	@since		PHP 5.0
*	@ver		1.03
*
*	@history
*		2010-12-14 クラス作成
*		2011-02-14 RETURN出力に変更
*		2011-02-22 W,Hのどちらかを「*」でauto指定
*		2011-02-22 リサイズタイプを選択できる機能を追加
*		2011-02-22 引数が適切でない場合の処理追加


*		2016-10-24 #31あたりのコンストラクタの記述をPHP7対応に変更
***********************************************************/

class resizeImage
{
	var $_srcImgPath;
	var $_createImgWidth;
	var $_createImgHeight;
	
	### constructor
	
	function __construct()
	{
	}

/*----------------------------------------------------------

	■ GD画像リサイズ
	
	引数01 ： 画像ファイルパス
	引数02 ： 生成画像の横サイズ　※autoの場合は「*」
	引数03 ： 生成画像の縦サイズ　※autoの場合は「*」
	引数04 ： リサイズタイプ：縮小＆トリミング＝「st」(default)、縮小のみ＝「ss」※縮小は､縦横枠内に収まるように処理
	
	画像ファイルパス「dir/***.jpg」w200,h300の場合（URL可）
	「***-200x300.jpg」のファイル名があればそのまま表示
	なければリサイズ＆トリミング画像を作成の上表示します。
	
-----------------------------------------------------------*/

	function dispResizeImgPath($_srcImgPath,$_createImgWidth,$_createImgHeight,$_type = 'st')
	{
	
		/* 画像情報 */
		
		$imgFnameArr = explode(".",$_srcImgPath);
		
		$imgFirstName = $imgFnameArr[0];
		
		for($i = 1; $i < (count($imgFnameArr)-1); $i++)
		{
			$imgFirstName .= ".".$imgFnameArr[$i];	//拡張子を除いた画像ファイル名（ファイル名に「.」「.jpg」が入っている時にも対応）
		}
		
		$imgExt = strtolower($imgFnameArr[count($imgFnameArr)-1]);	//画像拡張子（小文字）
		
		$_createImgWidth = ($_createImgWidth == '*') ? 'auto' : intval($_createImgWidth);
		$_createImgHeight = ($_createImgHeight == '*') ? 'auto' : intval($_createImgHeight);
		
		if(
			($_createImgWidth == 'auto' && is_numeric($_createImgHeight)) // autoと数値
			or
			(is_numeric($_createImgWidth) && $_createImgHeight == 'auto') // autoと数値
			or
			(is_numeric($_createImgWidth) && is_numeric($_createImgHeight)) // 両方数値
		)
		{
//			$newFpath = $imgFirstName.'-'.$_createImgWidth.'x'.$_createImgHeight.'.'.$imgExt; //作成される画像ファイル名
			$newFpath = $imgFirstName.'_s.'.$imgExt; //作成される画像ファイル名
		}
		else
		{
			$newFpath = $_srcImgPath; //作成されなかった時は元画像
		}
		
		
		/* 画像処理 */
		
		if(!file_exists($newFpath) && ($imgExt == 'jpg' || $imgExt == 'gif' || $imgExt == 'png'))
		{
			switch($imgExt)	//拡張子により処理を分岐
			{
				case 'jpg':
					$tempImage = ImageCreateFromJPEG($_srcImgPath);
					break;
					
				case 'gif':
					$tempImage = ImageCreateFromGIF($_srcImgPath);
					break;
					
				case 'png':
					$tempImage = ImageCreateFromPNG($_srcImgPath);
					break;
			}
			
			// アップされた画像のサイズ
			
			$tempImageWidth = ImageSX($tempImage); //横幅（px）
			$tempImageHeight = ImageSY($tempImage); //縦幅（px）
			
			// リサイズ処理
			
			if($_type == 'ss') //縮小のみ
			{
				if(
					($tempImageWidth/$tempImageHeight) > ($_createImgWidth/$_createImgHeight) // 対象画像-case横長
					or
					$_createImgHeight == 'auto' // 縦AUTO（縮小のみ）
				)
				{
					$newWidth = $_createImgWidth;
					$rate = $newWidth / $tempImageWidth; //縦横比
					$newHeight = $rate * $tempImageHeight;
				}
				else // 対象画像-case縦長 or 横AUTO（縮小のみ）
				{
					$newHeight = $_createImgHeight;
					$rate = $newHeight / $tempImageHeight; //縦横比
					$newWidth = $rate * $tempImageWidth;
				}
				
				$x = 0;
				$y = 0;
			
				$newImage = ImageCreateTrueColor($newWidth,$newHeight); //空画像
			}
			elseif($_createImgWidth == 'auto') //横AUTO
			{
				$newHeight = $_createImgHeight;
				$rate = $newHeight / $tempImageHeight; //縦横比
				$newWidth = $rate * $tempImageWidth;
				
				$x = 0;
				$y = 0;
			
				$newImage = ImageCreateTrueColor($newWidth,$newHeight); //空画像
			}
			elseif($_createImgHeight == 'auto') //縦AUTO
			{
				$newWidth = $_createImgWidth;
				$rate = $newWidth / $tempImageWidth; //縦横比
				$newHeight = $rate * $tempImageHeight;
				
				$x = 0;
				$y = 0;
			
				$newImage = ImageCreateTrueColor($newWidth,$newHeight); //空画像
			}
			elseif(($tempImageWidth/$tempImageHeight) > ($_createImgWidth/$_createImgHeight)) //対象画像-case横長
			{
				$newHeight = $_createImgHeight;
				$rate = $newHeight / $tempImageHeight; //縦横比
				$newWidth = $rate * $tempImageWidth;
				
				$x = ($newWidth - $_createImgWidth)/1;
				$y = 0;
			
				$newImage = ImageCreateTrueColor($_createImgWidth,$_createImgHeight); //空画像
			}
			else //対象画像-case縦長or同
			{
				$newWidth = $_createImgWidth;
				$rate = $newWidth / $tempImageWidth; //縦横比
				$newHeight = $rate * $tempImageHeight;
				
				$x = 0;
				$y = ($newHeight - $_createImgHeight)/1;
			
				$newImage = ImageCreateTrueColor($_createImgWidth,$_createImgHeight); //空画像
			}
			
			ImageCopyResized($newImage,$tempImage,0,0,$x,$y,$newWidth,$newHeight,$tempImageWidth,$tempImageHeight);
		
			ImageJPEG($newImage, $newFpath, 100); //3rd引数:クオリティー（0-100）
		
			imagedestroy($tempImage);
			imagedestroy($newImage);
		}
		
		return $newFpath;
		
	}
}

?>

