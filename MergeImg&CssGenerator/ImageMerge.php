<?php




/* Globals */

$images=array();
$imagesPathsArray=array();
static $positionsx;
static $imageName;

$answer=readline();
if (trim($answer)=="css_generator"){

    start();
}

if (preg_match("~-i~",$answer)){
    echo PHP_EOL."-I".PHP_EOL;
   preg_filter( "~i~","",explode("-",$answer))[0];


}
if (preg_match("~-s~",$answer)){
    echo PHP_EOL."-S".PHP_EOL;
}

if (preg_match("~-r~",$answer)){
    echo PHP_EOL."-R".PHP_EOL;

    start_recursivity();
}


/*

switch ($answer){
    case "css_generator -s" :
        echo "\nanswer : -s\n";
        break;
    case "css_generator -r":
        echo "\nanswer : -r\n";
        break;
    case "css_generator -i":
        echo "\nanswer : -i\n";
        break;
}
*/


function start(){
    global $imagesPathsArray;
    global $images;
    scan_dir("/home/profchen/Documents/Epitech/ModuleCss/Bootstrap/Css-Generator/MergeImg&CssGenerator");
    $images=createImagesObj($imagesPathsArray);
    copyImagesOnBackground();
    generateCss();
}

function start_recursivity(){
    global $imagesPathsArray;
    global $images;
    scan_dir_recursivity("/home/profchen/Documents/Epitech/ModuleCss/Bootstrap/Css-Generator/MergeImg&CssGenerator");
    $images=createImagesObj($imagesPathsArray);
    copyImagesOnBackground();
    generateCss();
}



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
function copyImagesOnBackground($name="sprite.png"){
    $background=createBackground();
    global $images;
    global $positionsx;
     $positionx=0;
    foreach ($images as $image){
        $positionsx[]= $positionx;
        imagecopy($background,$image,$positionx,0,0,0,imagesx($image),imagesy($image));
        $positionx+=(imagesx($image));
    }
    imagepng($background,$name);
    echo PHP_EOL."==>$name generated !".PHP_EOL;
}

//Css

/* GetImages Names */
function getNames($images)
{
    return explode(" ", trim(str_replace(".png", " ", implode($images))));

}

/* Generate Css */
function generateCss($name="style.css"){
    global $positionsx;
    global $imagesPathsArray;
    $handle = fopen($name, "w+");
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
echo PHP_EOL."==>$name generated !".PHP_EOL;
}

function scan_dir_recursivity($dirPath){
    global $imagesPathsArray;
    static $images;
    if ($handle = opendir($dirPath)) {

        while (false !== ($entry = readdir($handle))) {

            if ($entry != "." && $entry != "..") {
                if (preg_match("~\.~",$entry)){
                    if (preg_match("~\.png~",$entry)&&basename($entry!="sprite.png")){
                        $images[]="$dirPath/$entry";}
                }else{
                    scan_dir_recursivity($dirPath."/".$entry);
                }

            }
        }

        closedir($handle);
    }
    $imagesPathsArray=$images;

}

function scan_dir($dirPath){
    global $imagesPathsArray;
    static $images;
    if ($handle = opendir($dirPath)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                    if (preg_match("~\.png~",$entry)&&basename($entry!="sprite.png")){
                        $images[]="$dirPath/$entry";}


            }
        }

        closedir($handle);
    }
    $imagesPathsArray=$images;

}
