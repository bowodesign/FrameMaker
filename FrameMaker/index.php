<?php 
error_reporting(E_ALL ^ E_NOTICE);
$sending = $_POST["sending"];
$frame = $_POST["frameset"];

if ($frame=="") {$frame="01.png";}

if($sending=="yes" or $downpic==""){
	$lokasi_file = $_FILES['upfile']['tmp_name'];
	$nama_file   = $_FILES['upfile']['name'];
		
	if(!empty($nama_file)){	
		// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
		// of $_FILES.
		try {					
			// Undefined | Multiple Files | $_FILES Corruption Attack
			// If this request falls under any of them, treat it invalid.
			if (
				!isset($_FILES['upfile']['error']) ||
				is_array($_FILES['upfile']['error'])
			) {
				throw new RuntimeException('Invalid parameters.');
			}

			// Check $_FILES['upfile']['error'] value.
			switch ($_FILES['upfile']['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit.');
				default:
					throw new RuntimeException('Unknown errors.');
			}

			// You should also check filesize here. 
			if ($_FILES['upfile']['size'] > 1000000) {
				throw new RuntimeException('Exceeded filesize limit.');
			}

			// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
			// Check MIME Type by yourself.
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			if (false === $ext = array_search(
				$finfo->file($_FILES['upfile']['tmp_name']),
				array(
					'jpg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
				),
				true
			)) {
				throw new RuntimeException('Invalid file format.');
			}

			// You should name it uniquely.
			// DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
			// On this example, obtain safe unique name from its binary data.
				
			$picname = sha1_file($_FILES['upfile']['tmp_name']);
			if (!move_uploaded_file(
				$_FILES['upfile']['tmp_name'],
				sprintf('./imgs/%s.%s',
					sha1_file($_FILES['upfile']['tmp_name']),
					$ext
				)
			)) {
				throw new RuntimeException('Failed to move uploaded file.');
			}					
		} catch (RuntimeException $e) {
			echo $e->getMessage();
			exit;
		}
	} else {
		$picname = 'image.png';
	}

	function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){		
		list($width, $height) = getimagesize($source_file);
		$imgsize = getimagesize($source_file);
		$mime = $imgsize['mime'];				
		switch($mime){
			case 'image/gif':
				$image_create = "imagecreatefromgif";
				$image = "imagegif";
				break;
					
			case 'image/png':
				$image_create = "imagecreatefrompng";
				$image = "imagepng";
				$quality = 7;
				break;
			 
			case 'image/jpeg':
				$image_create = "imagecreatefromjpeg";
				$image = "imagejpeg";
				$quality = 80;
				break;
			 
			default:
				return false;
				break;
		}
				 
		$dst_img = imagecreatetruecolor($max_width, $max_height);
		$src_img = $image_create($source_file);
				 
		$width_new = $height * $max_width / $max_height;
		$height_new = $width * $max_height / $max_width;
		//if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
		if($width_new > $width){
			//cut point by height
			$h_point = (($height - $height_new) / 2);
			//copy image
			imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
		}else{
			//cut point by width
			$w_point = (($width - $width_new) / 2);
			imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
		}
				 
		$image($dst_img, $dst_dir, $quality);
		 
		if($dst_img)imagedestroy($dst_img);
		if($src_img)imagedestroy($src_img);
	}
	//usage example
	if ($picname=='image.png') {
		$uppic   = $picname;
		$downpic = 'image-down.png';
		$downlast= 'image-down-last.png';
	} else {
		$uppic   = $picname.'.'.$ext;
		$downpic = $picname.'-down.'.$ext;
		$downlast= $picname.'-down-last.'.$ext;
	}
			
	resize_crop_image(640, 360, './imgs/'.$uppic, './imgs/'.$downpic);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>:: Frame Maker ::</title>
<link href="./bootstrap-3.3.7/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link rel="stylesheet" type="text/css" href="./image-picker/image-picker.css">
</script><script src="./jquery-3.1.1.min.js" type="text/javascript"></script>
<script src="./image-picker/image-picker.js" type="text/javascript"></script>
<style>
Body {padding-top: 50px;
	padding-bottom: 50px;
}
select {
    width: 220px;
    background-color: #ffffff;
    border: 1px solid #cccccc;
}
select, input[type="file"] {
    height: 40px;
    line-height: 30px;
}
select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
    display: inline-block;
    height: 40px;
    padding: 4px 6px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 20px;
    color: #555555;
    vertical-align: middle;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}
#about {
  float: left;
  width: 100%;
  overflowx: auto;    
}
img {
	opacity: 1.0;
    filter: gray;
	-webkit-filter: grayscale(0); /* Google Chrome, Safari 6+ & Opera 15+ */
    -webkit-box-shadow: 0px 2px 6px 2px rgba(0,0,0,0.75);
    -moz-box-shadow: 0px 2px 6px 2px rgba(0,0,0,0.75);
    box-shadow: 0px 2px 6px 2px rgba(0,0,0,0.75);
}
</style>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-sm-5">
			<form name="myForm" action="" method="post" enctype="multipart/form-data" class="contactoFormulario">
				<div class="picker">
					<select name="frameset" id="frameset" class="image-picker show-html">
					   <option data-img-src="./frames/01.png" data-img-class="first" data-img-alt="Frame 1" value="01.png">Frame 1</option>
					   <option data-img-src="./frames/02.png" data-img-alt="Frame 2" height="120px" value="02.png">Frame 2</option>
					   <option data-img-src="./frames/03.png" data-img-alt="Frame 3" value="03.png">Frame 3</option>
					   <option data-img-src="./frames/04.png" data-img-alt="Frame 4" value="04.png">Frame 4</option>
					   <option data-img-src="./frames/05.png" data-img-alt="Frame 5" value="05.png">Frame 5</option>
					   <option data-img-src="./frames/06.png" data-img-alt="Frame 6" value="06.png">Frame 6</option>
					   <option data-img-src="./frames/07.png" data-img-alt="Frame 7" value="07.png">Frame 7</option>
					   <option data-img-src="./frames/08.png" data-img-alt="Frame 8" data-img-class="last" value="08.png">Smiley 8</option>
					</select>
				</div>
				<h4>Your Image</h4>
				<div class="form-inline">
					<div class="form-group">
						<input type="file" class="form-control" name="upfile" size="10">
					</div>
					<button type="submit" class="btn btn-sm btn-primary" id="js-upload-submit">Upload files</button>
					<input type="hidden" name="sending" class="btn" value="yes" />
				</div>
			</form>
		</div>
		<div class="col-sm-7">		
			<p>
			<div id="start">
				<?php echo '<img src="ShowImage.php?image='.$downpic.'&frame='.$frame.'" class="img-thumbnail" alt="Frame Maker" width="640" height="360"/>'; ?>
			</div>
			</p>		
			<div style="margin-right:15px">
			<a download="<?php echo $downlast; ?>" href="<?php echo './imgs/'.$downlast; ?>" title="ImageName" class="btn btn-success btn-default btn-xs pull-right">
				<span class="glyphicon glyphicon-download"></span> Download
			</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">	
    jQuery("select.image-picker").imagepicker({
      hide_select:  false,
    });

    jQuery("select.image-picker.show-labels").imagepicker({
      hide_select:  false,
      show_label:   true,
    });

    jQuery("select.image-picker.limit_callback").imagepicker({
      limit_reached:  function(){alert('We are full!')},
      hide_select:    false
    });

    var container = jQuery("select.image-picker.masonry").next("ul.thumbnails");
    container.imagesLoaded(function(){
      container.masonry({
        itemSelector:   "li",
      });
    });
</script>
<script type="text/javascript">	
	$(document).ready(function(){
	   $("#frameset").change(function(){
		    <?php
			 echo "var phpVariable = '{$downpic}';";
			?>
			$('#start').html('<img id="imgFinal" src="ShowImage.php?image='+phpVariable+'&frame='+this.value+'" class="img-thumbnail" alt="we love Gaby .." width="640" height="360"/>');		
		});
	});
</script>
</body>
</html>