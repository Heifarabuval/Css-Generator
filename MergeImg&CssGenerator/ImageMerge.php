<?php

/* Globals */
$images=array();
$imagesPathsArray=array();
static $positionsx;
scan_dir("/home/profchen/Documents/Epitech/ModuleCss/Bootstrap/Css-Generator/MergeImg&CssGenerator");
$images=createImagesObj($imagesPathsArray);


/* Start */
copyImagesOnBackground();
generateCss();


//transform path to objects img
function createImagesObj($imagesPaths){
    global $imagesPathsArray;
    $imagesPathsArray=$imagesPaths;
    foreach ($imagesPaths as $key => $imagePath){
       $objectsImages[$key]=imagecreatefrompng($imagePath);

    }
    return $objectsImages;
}


/* get max height to set background height */
function setBackgroundHeight(){
    global $images;
    foreach ($images as $key => $image){
        $maxHeight[$key]=imagesy($image);
    }
    return  max($maxHeight);

}

/* Add all widths to set background width */
function setBackgroundWidth(){
    global $images;
    static $finalHeight;
    foreach ($images as $image){
        $finalHeight+=imagesx($image);
    }return $finalHeight;

}

/* Create the background image */
function createBackground(){
    $img = imagecreatetruecolor(setBackgroundWidth(), setBackgroundHeight());
    $background = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $background);
    imagealphablending($img, false);
    imagesavealpha($img, true);
    return $img;
}


/* Creating the sprite sheet */
function copyImagesOnBackground(){
    $background=createBackground();
    global $images;
    global $positionsx;
     $positionx=0;
    foreach ($images as $image){
        $positionsx[]= $positionx;
        imagecopy($background,$image,$positionx,0,0,0,imagesx($image),imagesy($image));
        $positionx+=(imagesx($image));
    }
    imagepng($background,"sprite.png");
}

//Css

/* GetImages Names */
function getNames($images)
{
    return explode(" ", trim(str_replace(".png", " ", implode($images))));

}

/* Generate Css */
function generateCss(){
    global $positionsx;
    global $imagesPathsArray;
    $styleName = "style.css";
    $handle = fopen($styleName, "w+");
    fwrite($handle,
        ".sprite { 
    background-image: url(sprite.png);     
    background-repeat: no-repeat;
    display: block;
    } \n\n");

    foreach ($imagesPathsArray as $key => $file) {
        $imageSizes = getimagesize($file);
        $position=-$positionsx[$key];

        fwrite($handle,".sprite-".basename(getNames($imagesPathsArray)[$key])."{".
            "\n"."width:".strval( $imageSizes[0])."px;".
            "\n"."height:". strval($imageSizes[1]) ."px;".
            "\nbackground-position: ".strval($position."px 0px;} \n\n"));

    }

}

function scan_dir($dirPath){
    global $imagesPathsArray;
    static $images;
    if ($handle = opendir($dirPath)) {

        while (false !== ($entry = readdir($handle))) {

            if ($entry != "." && $entry != "..") {
                if (preg_match("~\.~",$entry)){
                    if (preg_match("~\.png~",$entry)&&basename($entry!="sprite.png")){
                        $images[]="$dirPath/$entry";}
                }else{
                    scan_dir($dirPath."/".$entry);
                }

            }
        }

        closedir($handle);
    }
    $imagesPathsArray=$images;

}
