<?php
function my_merge_image($first_img_path,$second_img_path,$third_image_path){
    header('Content-Type: image/png');

    //transform image to objects

    $first_img=imagecreatefrompng($first_img_path);
    $second_img=imagecreatefrompng($second_img_path);
    $third_img=imagecreatefrompng($third_image_path);
    $files=array($first_img,$second_img,$third_img);

    //first img size
    $height_first=imagesy($first_img);
    $width_first=imagesx($first_img);

    //second img size
    $height_second=imagesy($second_img);
    $width_second=imagesx($second_img);

    //get max height
    function getHeight($files){
        foreach ($files as $key=> $file){
            $arr[$key]=imagesy($file);
        }
        return  max($arr);
    }

    //get max width
    function getWidth($files){
        foreach ($files as $file){
            $arr=array(imagesx($file));
        }
        return  max($arr);
    }



    //set background size depends of images
    $height=getHeight($files)+10;
    $width=$width_first+$width_second+imagesx($third_img)+20;



    $img = imagecreatetruecolor($width, $height);
    $background = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $background);
    imagealphablending($img, false);
    imagesavealpha($img, true);





    $pos=0;
    foreach ($files as $file){
        echo $pos."\n";
        imagecopy($img,$file,$pos,0,0,0,imagesx($file),imagesy($file));
        $pos+=(5+imagesx($file));
    }

    imagepng($img,"sprite.png");
    imagedestroy($first_img);
    imagedestroy($second_img);





}




my_merge_image("img3.png","img4.png","img2.png");

