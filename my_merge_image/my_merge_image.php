<?php
function my_merge_image($first_img_path,$second_img_path){
    header('Content-Type: image/png');
    //transform image to objects
    $first_img=imagecreatefrompng($first_img_path);
    $second_img=imagecreatefrompng($second_img_path);
    //first img size
    $height_first=imagesy($first_img);
    $width_first=imagesx($first_img);
    //second img size
    $height_second=imagesy($second_img);
    $width_second=imagesx($second_img);


    $height=(($height_first+$height_second)/2)+10;
    $width=$width_first+$width_second+20;

    $files=array($first_img,$second_img);

    $img = imagecreatetruecolor($width, $height);
    $background = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $background);
    imagealphablending($img, false);
    imagesavealpha($img, true);

    $pos=0;
    foreach ($files as $file){
        imagecopy($img,$file,$pos,5,0,0,$width_first,$height_first);
        $pos+=(5+$width_first);
    }
    imagepng($img,"sprite.png");
    imagedestroy($first_img);
    imagedestroy($second_img);

}
my_merge_image("img1.png","img2.png");

