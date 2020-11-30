<?php
function my_merge_image($first_img_path,$second_img_path){
    $first_img=imagecreatefrompng($first_img_path);
    $second_img=imagecreatefrompng($second_img_path);
    $height_first=imagesy($first_img);
    $width_first=imagesx($first_img);
    $height_second=imagesy($second_img);
    $width_second=imagesx($second_img);

    //Find the middle of first image
   echo "first X : ".$first_img_x=($width_second+120-$width_first)/2;
   echo "first Y : ".$first_img_y=($height_second+120-$height_first)/2;

    //Fusion des images
    imagecopymerge($first_img,$second_img,0,0,$first_img_x,$first_img_y,$width_first,$height_first,30);
    /** For test :
     *imagepng($first_img,"img3.png");
     */
    return imagepng($first_img);



}
echo my_merge_image("img1.png","img2.png");

