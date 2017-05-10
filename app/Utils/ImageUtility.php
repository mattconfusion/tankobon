<?php

namespace Utils;

class ImageUtility {

	/**
	 * Check if the page is double and beyond a treshold, split it and name the splitted images according to the reading direction
	 * @param  string $sourceFilePath    [description]
	 * @param  string $destFolderPath      [description]
	 * @param  int $pageWidthTreshold [description]
	 * @param  string $direction reading direction, use 'ltr' or 'rtl'
	 * @return array of new images, empty if no images created
	 * */
	public static function splitPageIfDouble($sourceFilePath, $destFolderPath, $pageWidthTreshold, $readDirection){
		$source = @imagecreatefromjpeg( $sourceFilePath);
		$source_width = imagesx( $source );
		$source_height = imagesy( $source );
		$newImages = array();
		$filename = pathinfo($sourceFilePath, PATHINFO_FILENAME);

		if((source_width > source_height) && ($source_width <= $pageWidthTreshold)){
			$width = $source_width/2;
			$height = source_height;
			$partsCounter = 1;
			for( $col = 0; $col < $source_width / $width; $col++){
				for( $row = 0; $row < $source_height / $height; $row++){
					//$fn = sprintf( "img%02d_%02d.jpg", $col, $row );
					//echo( "$fn\n" );
					$fn = "{$filename}_$partsCounter.jpg";
					$im = @imagecreatetruecolor( $width, $height );
					imagecopyresized( $im, $source, 0, 0,
					                 $col * $width, $row * $height, $width, $height,
					                 $width, $height );
					imagejpeg( $im, $fn );
					imagedestroy( $im );
					$newImages[] = $fn;
					++$partsCounter;
					unset($fn,$im);
				}
			}

			unset($width,$height,$partsCounter);
		} 
		unset($source_width,$source_height,$source);
		return $newImages;
	}


}