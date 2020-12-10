<?php
/* Globals */
$imagesObject=array();
$imagesPathsArray=array();
static $positionsx;
static $size;
/* Default values */
$padding=0;
$imageName="sprite.png";
$cssName="style.css";

if (in_array("man",$argv)&&$argc<3) {
    displayMan();
}else {
    /* Get the passed arguments */
    foreach ($argv as $key => $argument) {

        /* -i parameter */
        if (preg_match("~^-i$~", $argument)) {
            global $imageName;
            if (isset($argv[$key + 1])){
            $imageName = $argv[$key + 1];}
            if (!preg_match("~[A-Za-z0-9_.]\.png~",$imageName)) {
                $imageName = "sprite.png";
            }
            echo "\n\e[35m spriteSheet name: $imageName  \e[0m \n";

        }
        /* --output-images parameter */
        if (preg_match("~--output-image=~", $argument)) {
            global $imageName;
            $imageName = explode("=", $argument)[1];
            if (!preg_match("~[A-Za-z0-9_.]\.png~",$imageName)) {
                $imageName = "sprite.png";
            }
            echo "\n\e[35m spriteSheet name: $imageName  \e[0m \n";
        }
        /* -s parameter */
        if (preg_match("~^-s$~", $argument)) {
            if (isset($argv[$key + 1])){
            $cssName = $argv[$key + 1];}
            if (!preg_match("~[A-Za-z0-9_.]\.css~",$cssName)) {
                $cssName = "style.css";
            }
            echo "\n\e[35m Css name: $cssName  \e[0m \n";
        }
        /* --output-style parameter */
        if (preg_match("~--output-style=~", $argument)) {
            global $cssName;
            $cssName = explode("=", $argument)[1];
            if (!preg_match("~[A-Za-z0-9_.]\.css~",$cssName)) {
                $cssName = "style.css";
            }
            echo "\n\e[35m Css name: $cssName  \e[0m \n";
        }


        /* -p parameter */
        if (preg_match("~^-p$~", $argument)) {
            global $padding;
            if (isset($argv[$key+1])){
            $padding = $argv[$key + 1];}
            if (!is_numeric($padding)) {
                $padding = 0;
            }
            echo "\n\e[35m Padding: $padding px \e[0m \n";
        }


        /* -o parameter */
        if (preg_match("~^-o$~", $argument)) {
            global $size;
            if (isset($argv[$key+1])){
            $size = $argv[$key + 1];}
            if (!is_numeric($size)) {
                $size =null;
            }else{
                echo "\n\e[35m Size changed =>  Width: $size px | Height: $size px \e[0m \n";
            }
        }

    }

    /* Recursivity parameter */
    if (in_array("-r", $argv)||in_array("--recursive",$argv)) {
        echo PHP_EOL . "-R" . PHP_EOL;
        start_recursivity();
    } else {
        start();
    }

}

/* Normal start */
function start(){
    global $imagesPathsArray,$imagesObject,$argv,$argc;

    if (!is_dir($argv[1])){
        echo "\n\t\t\e[31mErreur: Veuillez indiquer le chemin vers votre dossier. Redirection vers le MAN en cours...\e[0m \n";
        sleep(2);
        ncurses_clear();
        displayMan();
    }else{
    scan_dir($argv[1]);
    $imagesObject=createImagesObj($imagesPathsArray);
    copyImagesOnBackground();
    generateCss();
}}

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
        }
    }
    return  max($maxHeight);
}

/* Add all widths to set background width */
function setBackgroundWidth(){
    global $imagesObject,$padding,$size;
    static $finalWidth;
    foreach ($imagesObject as $image){
        if (!isset($size)){
            $finalWidth+=imagesx($image)+$padding;
        }else{
            $finalWidth+=$size+$padding;
        }

    }return $finalWidth;

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
            imagecopy($background,$image,$positionx,0,0,0,imagesx($image),imagesy($image));
            $positionx+=(imagesx($image))+$padding;
        }else {
            imagecopyresized($background, $image, $positionx, 0, 0, 0, $size, $size, imagesx($image), imagesy($image));
            $positionx+=($size)+$padding;
        }

    }
    imagepng($background,$imageName);
    echo "\n\e[33m $imageName generated  \e[0m \n";
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
                "\nbackground-position: " . strval($position)."px 0px;} \n\n");
        }
    }
    echo "\n\e[33m $cssName generated  \e[0m \n\n";
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

function displayMan(){
    echo   <<< EOF
\e[33m
                          |¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯|
                          | SPRITESHEET & CSS GENERATOR : MAN... |
                          |______________________________________|\e[0m 
                            
 \e[33m[SYNOPSIS]\e[0m 
 css_generator [OPTIONS]. . . assets_folder
 
 
 \e[33m[DESCRIPTION]\e[0m
 Concatenate all images inside a folder in one sprite and write a style sheet ready to use. Mandatory arguments to long options are mandatory 
 for short options too.
 
 \e[34m-r, --recursive\e[0m
 Look for images into the assets_folder passed as argument and all of its subdirectories.
 
 \e[34m-i, --output-image=IMAGE\e[0m
 Name of the generated image. If blank, the default name is « sprite.png ».
 
 \e[34m-s, --output-style=STYLE\e[0m
 Name of the generated stylesheet. If blank, the default name is « style.css ».
 
 
 \e[33m[BONUS OPTIONS]\e[0m
 \e[34m-p, --padding=NUMBER\e[0m
 Add padding between images of NUMBER pixels.
 
 \e[34m-o, --override-size=SIZE\e[0m
 Force each images of the sprite to fit a size of SIZExSIZE pixels.
 
 \e[34m-c, --columns_number=NUMBER\e[0m
 The maximum number of elements to be generated horizontally.
 
 
EOF;
}



