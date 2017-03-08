<?php

$image_src = isset($_GET["image"]) ? $_GET["image"] : '';
$frame_src = isset($_GET["frame"]) ? $_GET["frame"] : '';

$img = new WaterMarkThis($image_src, $frame_src);
$img->show();

class WaterMarkThis{
	public function __construct($imageSrc, $frame_src){    	
    	$this->imagePath = $imageSrc;
    	$this->FramePath = $frame_src;
    }
	
	public function show(){
		$imgsize = getimagesize('./imgs/'.$this->imagePath);
		$width = $imgsize[0];
		$height = $imgsize[1];
		$mime = $imgsize['mime'];
			
		switch($mime){
			case 'image/gif':
				$image = "imagegif";
				$format = "GIF";
				$ext = ".gif";
				break;
			 
			case 'image/png':
				$image = "imagepng";
				$format = "PNG";
				$quality = 7;
				$ext = ".png";
				break;
					 
			case 'image/jpeg':
				$image = "imagejpeg";
				$format = "JPG";
				$quality = 85;
				$ext = ".jpg";
				break;
				
			default:
				return false;
				break;
		}
			
		/*
		 * PHP GD
		 * adding watermark to an image with GD library
		 */

		// Load the watermark and the photo to apply the watermark to
		$stamp = @imagecreatefrompng('./frames/'.$this->FramePath);
		
		// Create image
		if($format=="JPG"){
			$im = @imagecreatefromjpeg('./imgs/'.$this->imagePath);
		}
		else if($format=="PNG"){
			$im = @imagecreatefrompng('./imgs/'.$this->imagePath);
		}
		else if ($format=="GIF"){
			$im = @imagecreatefromgif('./imgs/'.$this->imagePath);
		}else{
			echo "Not Supported File";
			exit();
		}

		// Set the margins for the stamp and get the height/width of the stamp image
		$marge_right = 0;
		$marge_bottom = 0;
		$sx = imagesx($stamp);
		$sy = imagesy($stamp);

		// Copy the stamp image onto our photo using the margin offsets and the photo 
		// width to calculate positioning of the stamp. 
		imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

		$lastname = basename($this->imagePath, $ext).'-last'.$ext;

		// Output and free memory
		header('Content-type: image/'.$format);
		switch ($format){
			case "JPEG":
				imagejpeg($im,'./imgs/'.$lastname,85);
				imagejpeg($im,"",85);
				break;
			case "PNG":
				imagepng($im,'./imgs/'.$lastname,7);
				imagepng($im);
				break;
			case "GIF":
				imagegif($im,'./imgs/'.$lastname);
				imagegif($im);
				break;
			default:
				imagepng($im,'./imgs/'.$lastname,7);
				imagepng($im);
		}
		imagedestroy($im);
	}
}
?>