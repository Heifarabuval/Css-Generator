<?php
/* Globals */
$imagesObject=array();
$imagesPathsArray=array();
static $positionsx;
static $size;
$padding=0;
 $imageName="sprite.png";
 $cssName="style.css";



if (in_array("man",$argv)&&$argc<3) {
    echo "
      \n\n\t\t\t  |¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯|
            \t\t  | SPRITESHEET & CSS GENERATOR : MAN... |
            \t\t  |______________________________________|\n\n                        
\t[SYNOPSIS]
\tcss_generator [OPTIONS]. . . assets_folder\n\n
\t[DESCRIPTION]
\tConcatenate all images inside a folder in one sprite and write a style sheet ready to use. Mandatory arguments to long options are mandatory 
\tfor short options too.\n
\t-r, --recursive
\tLook for images into the assets_folder passed as argument and all of its subdirectories.\n
\t-i, --output-image=IMAGE
\tName of the generated image. If blank, the default name is « sprite.png ».\n
\t-s, --output-style=STYLE
\tName of the generated stylesheet. If blank, the default name is « style.css ».\n\n
\t[BONUS OPTIONS]
\t-p, --padding=NUMBER
\tAdd padding between images of NUMBER pixels.\n
\t-o, --override-size=SIZE
\tForce each images of the sprite to fit a size of SIZExSIZE pixels.\n
\t-c, --columns_number=NUMBER
\tThe maximum number of elements to be generated horizontally.\n";
}else {
    /* Get the passed arguments */
    foreach ($argv as $key => $argument) {

        /* -i parameter */
        if (preg_match("~^-i$~", $argument)) {
            global $imageName;
            echo PHP_EOL . "-I" . PHP_EOL;
            $imageName = $argv[$key + 1];
            if (strlen($imageName) < 1) {
                $imageName = "sprite.png";
            }
            echo PHP_EOL . $imageName . PHP_EOL;
        }

        /* --output-images parameter */
        if (preg_match("~--output-image=~", $argument)) {
            global $imageName;
            echo PHP_EOL . "-I out" . PHP_EOL;
            $imageName = explode("=", $argument)[1];
            if (strlen($imageName) < 1) {
                $imageName = "sprite.png";
            }
            echo PHP_EOL . $imageName . PHP_EOL;
        }
        /* -s parameter */
        if (preg_match("~^-s$~", $argument)) {
            echo PHP_EOL . "-S" . PHP_EOL;
            $cssName = $argv[$key + 1];
            if (strlen($cssName) < 1) {
                $imageName = "style.css";
            }
            echo PHP_EOL . $cssName . PHP_EOL;
        }
        /* --output-style parameter */
        if (preg_match("~--output-style=~", $argument)) {
            global $cssName;
            echo PHP_EOL . "-I" . PHP_EOL;
            $cssName = explode("=", $argument)[1];
            if (strlen($cssName) < 1) {
                $imageName = "style.css";
            }
            echo PHP_EOL . $imageName . PHP_EOL;
        }


        /* -p parameter */
        if (preg_match("~^-p$~", $argument)) {
            global $padding;
            echo PHP_EOL . "-P" . PHP_EOL;
            $padding = $argv[$key + 1];
            echo PHP_EOL . $padding . PHP_EOL;
        }


        /* -o parameter */
        if (preg_match("~^-o$~", $argument)) {
            global $size;
            echo PHP_EOL . "-O" . PHP_EOL;
            $size = $argv[$key + 1];
            echo PHP_EOL . $size . PHP_EOL;
        }

    }

    /* Recursivity parameter */
    if (in_array("-r", $argv)) {
        echo PHP_EOL . "-R" . PHP_EOL;
        start_recursivity();
    } else {
        start();
    }

}

/* Normal start */
function start(){
    global $imagesPathsArray,$imagesObject,$argv;
    scan_dir($argv[1]);
    $imagesObject=createImagesObj($imagesPathsArray);
    copyImagesOnBackground();
    generateCss();
}

    /* Start recursive mode */
function start_recursivity(){
    global $imagesPathsArray,$imagesObject,$argv;
    scan_dir_recursivity($argv[1]);
    $imagesObject=createImagesObj($imagesPathsArray);
    copyImagesOnBackground();
    generateCss();
}



/* transform path to objects img */
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
    global $imagesObject,$size;
    foreach ($imagesObject as $key => $image){
        if (!isset($size)){
            $maxHeight[$key]=imagesy($image);
        }else{
            $maxHeight[$key]=$size;
            var_dump($maxHeight);
        }
    }
    return  max($maxHeight);
}

/* Add all widths to set background width */
function setBackgroundWidth(){
    global $imagesObject,$padding,$size;
    static $finalHeight;
    foreach ($imagesObject as $image){
        if (!isset($size)){
            $finalHeight+=imagesx($image)+$padding;
        }else{
            $finalHeight+=$size+$padding;
        }

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
    global $imagesObject,$positionsx,$imageName,$padding,$size;

     $positionx=0;
    foreach ($imagesObject as $image){
        $positionsx[]= $positionx;
        if (!isset($size)){
            echo $size;
            imagecopy($background,$image,$positionx,0,0,0,imagesx($image),imagesy($image));
            $positionx+=(imagesx($image))+$padding;
        }else {
            echo $size;
            imagecopyresized($background, $image, $positionx, 0, 0, 0, $size, $size, imagesx($image), imagesy($image));
            $positionx+=($size)+$padding;
        }

    }
    imagepng($background,$imageName);
    echo PHP_EOL."==>$imageName generated !".PHP_EOL;
}


/* GetImages Names */
function getNames($images)
{
    return explode(" ", trim(str_replace(".png", " ", implode($images))));

}

/* Generate Css */
function generateCss(){
    global $positionsx,$imagesPathsArray,$cssName,$size;
    $handle = fopen($cssName, "w+");
    fwrite($handle,
        ".sprite { 
    background-image: url(sprite.png);     
    background-repeat: no-repeat;
    display: block;
    } \n\n");

    foreach ($imagesPathsArray as $key => $file) {
        $imageSizes = getimagesize($file);
        $position=-$positionsx[$key];
        if (!isset($size)) {
            fwrite($handle, "#sprite-" . basename(getNames($imagesPathsArray)[$key]) . "{" .
                "\n" . "width:" . strval($imageSizes[0]) . "px;" .
                "\n" . "height:" . strval($imageSizes[1]) . "px;" .
                "\nbackground-position: " . strval($position . "px 0px;} \n\n"));
        }else{
            fwrite($handle, "#sprite-" . basename(getNames($imagesPathsArray)[$key]) . "{" .
                "\n" . "width:" . strval($size) . "px;" .
                "\n" . "height:" . strval($size) . "px;" .
                "\nbackground-position: " . strval($position . "px 0px;} \n\n"));
        }
    }
echo PHP_EOL."==>$cssName generated !".PHP_EOL;
}


/*Scan dir func with recursivity*/
function scan_dir_recursivity($dirPath){
    global $imagesPathsArray;
    static $imagesPath;
    if ($handle = opendir($dirPath)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if (preg_match("~\.~",$entry)){
                    if (preg_match("~\.png~",$entry)&&basename($entry!="sprite.png")){
                        $imagesPath[]="$dirPath/$entry";}
                }else{
                    scan_dir_recursivity($dirPath."/".$entry);
                }
            }
        }
        closedir($handle);
    }
    $imagesPathsArray=$imagesPath;
}

/* Scan dir without recursivity*/
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
