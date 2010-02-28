<?PHP
include("../01acp/system/functions.php");
define('ACP_TB_WIDTH', 40);


$sourcefile = "1220623539.png";
$resize = 453;


$split = split('[.]', strtolower($sourcefile));
$filename = $split[0];
$fileType = $split[1];

// Thumbnail ausgeben, wenn vorhanden
if(file_exists($filename."_tb_".ACP_TB_WIDTH.".".$fileType) && $resize == ACP_TB_WIDTH || file_exists($filename."_tb_".ACP_TB_WIDTH200.".".$fileType) && $resize == ACP_TB_WIDTH200){
	switch($fileType){
	  case('png'):
		$sourcefile_id = imagecreatefrompng($filename."_tb_".$resize.".".$fileType);
		header("Content-type: image/png");
		imagepng($sourcefile_id);
	  break;
	  default:
		$sourcefile_id = imagecreatefromjpeg($filename."_tb_".$resize.".".$fileType);
		header("Content-type: image/jpg");
		imagejpeg($sourcefile_id);
	  }
	}
else{

	$info = getimagesize($sourcefile);

	// Resize images
	if($info[0] >= $info[1]){ $bigside = $info[0]; }
	else{ $bigside = $info[1]; }

	if($bigside > $resize){
		$k = $bigside/$resize;
		$picwidth = $info[0]/$k;
		$picheight = $info[1]/$k;
		}
	else{
		$picwidth = $info[0];
		$picheight = $info[1];
		}

	$echofile_id = imagecreatetruecolor($picwidth, $picheight);

	switch($fileType){
	  case('png'):
		$sourcefile_id = imagecreatefrompng($sourcefile);
	  break;
	  default:
		$sourcefile_id = imagecreatefromjpeg($sourcefile);
	  }

	// Get the sizes of pic
	$sourcefile_width = imageSX($sourcefile_id);
	$sourcefile_height = imageSY($sourcefile_id);

	// Create a jpeg out of the modified picture
	switch($fileType){
		// remember we don't need gif any more, so we use only png or jpeg.
		// See the upsaple code immediately above to see how we handle gifs
	  case('png'):
		header("Content-type: image/png");
		imagecopyresampled($echofile_id, $sourcefile_id, 0, 0, 0, 0, $picwidth, $picheight, $info[0], $info[1]);
		if($resize == ACP_TB_WIDTH || $resize == ACP_TB_WIDTH200)
			imagepng($echofile_id,$filename."_tb_".$resize.".".$fileType);
		imagepng($echofile_id);
	  break;
	  default:
		header("Content-type: image/jpg");
		imagecopyresampled($echofile_id, $sourcefile_id, 0, 0, 0, 0, $picwidth, $picheight, $info[0], $info[1]);
		if($resize == ACP_TB_WIDTH || $resize == ACP_TB_WIDTH200)
			imagejpeg($echofile_id,$filename."_tb_".$resize.".".$fileType,80);
		imagejpeg($echofile_id);
	  }

	imagedestroy($sourcefile_id);
	imagedestroy($echofile_id);
	}
?>