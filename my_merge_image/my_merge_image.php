<?php
function my_merge_image($first_img_path,$second_img_path){
    $first_img=imagecreatefrompng($first_img_path);
    $second_img=imagecreatefrompng($second_img_path);
    $height_first=imagesy($first_img);
    $width_first=imagesx($first_img);
    $height_second=imagesy($second_img);
    $width_second=imagesx($second_img);
   echo "first X : ".$first_img_x=$width_first."\n";
    echo "first Y : ".$first_img_y=$height_first."\n";
    echo "Second X : ".$second_img_x=$width_second."\n";
    echo "Second Y : ".$second_img_y=$height_second."\n";



    imagecopymerge($first_img,$second_img,0,0,-320,-320,$width_first,$height_first,30);
    imagepng($first_img,"img3.png");
    imagedestroy($first_img);
    imagedestroy($second_img);

}
my_merge_image("img1.png","img2.png");

