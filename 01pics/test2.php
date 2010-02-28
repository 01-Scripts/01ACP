<?PHP
function createthumb($name, $newname, $new_w, $new_h, $border=false, $transparency=true, $base64=false) {
    if(file_exists($newname))
        @unlink($newname);
    if(!file_exists($name))
        return false;
    $arr = split("\.",$name);
    $ext = $arr[count($arr)-1];

    if($ext=="jpeg" || $ext=="jpg"){
        $img = @imagecreatefromjpeg($name);
    } elseif($ext=="png"){
        $img = @imagecreatefrompng($name);
    } elseif($ext=="gif") {
        $img = @imagecreatefromgif($name);
    }
    if(!$img)
        return false;
    $old_x = imageSX($img);
    $old_y = imageSY($img);
    if($old_x < $new_w && $old_y < $new_h) {
        $thumb_w = $old_x;
        $thumb_h = $old_y;
    } elseif ($old_x > $old_y) {
        $thumb_w = $new_w;
        $thumb_h = floor(($old_y*($new_h/$old_x)));
    } elseif ($old_x < $old_y) {
        $thumb_w = floor($old_x*($new_w/$old_y));
        $thumb_h = $new_h;
    } elseif ($old_x == $old_y) {
        $thumb_w = $new_w;
        $thumb_h = $new_h;
    }
    $thumb_w = ($thumb_w<1) ? 1 : $thumb_w;
    $thumb_h = ($thumb_h<1) ? 1 : $thumb_h;
    $new_img = ImageCreateTrueColor($thumb_w, $thumb_h);
    
    if($transparency) {
        if($ext=="png") {
            imagealphablending($new_img, false);
            $colorTransparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
            imagefill($new_img, 0, 0, $colorTransparent);
            imagesavealpha($new_img, true);
        } elseif($ext=="gif") {
            $trnprt_indx = imagecolortransparent($img);
            if ($trnprt_indx >= 0) {
                //its transparent
                $trnprt_color = imagecolorsforindex($img, $trnprt_indx);
                $trnprt_indx = imagecolorallocate($new_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                imagefill($new_img, 0, 0, $trnprt_indx);
                imagecolortransparent($new_img, $trnprt_indx);
            }
        }
    } else {
        Imagefill($new_img, 0, 0, imagecolorallocate($new_img, 255, 255, 255));
    }
    
    imagecopyresampled($new_img, $img, 0,0,0,0, $thumb_w, $thumb_h, $old_x, $old_y); 
    if($border) {
        $black = imagecolorallocate($new_img, 0, 0, 0);
        imagerectangle($new_img,0,0, $thumb_w, $thumb_h, $black);
    }
    if($base64) {
        ob_start();
        imagepng($new_img);
        $img = ob_get_contents();
        ob_end_clean();
        $return = base64_encode($img);
    } else {
        if($ext=="jpeg" || $ext=="jpg"){
            imagejpeg($new_img, $newname);
            $return = true;
        } elseif($ext=="png"){
            imagepng($new_img, $newname);
            $return = true;
        } elseif($ext=="gif") {
            imagegif($new_img, $newname);
            $return = true;
        }
    }
    imagedestroy($new_img); 
    imagedestroy($img); 
    return $return;
}
//example useage
createthumb("36.gif", "36_tb.gif", 100,100,false, true, false);
?>
<body bgcolor="#00FF00";>
<img src="36_tb.gif" />