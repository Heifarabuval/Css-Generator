<?php
/* Globals */
static $positionsx,$size,$folderArray,$imagesPathsArray,$imagesObject;
/* Default values */
$padding=0;
$imageName="sprite.png";
$cssName="style.css";

if (in_array("man",$argv)&&$argc<3) {
    displayMan();
    sleep(1);
    exit();
}else {
    /* Get the passed arguments */
    foreach ($argv as $key => $argument) {

        /* -i parameter */
        if (preg_match("~^-i$~", $argument)) {
            if (isset($argv[$key + 1])){
            $imageName = $argv[$key + 1];}
            if (!preg_match("~[A-Za-z0-9_.]\.png~",$imageName)) {
                $imageName = "sprite.png";
            }
            echo "\n\e[35m spriteSheet name: $imageName  \e[0m \n";

        }
        /* --output-images parameter */
        if (preg_match("~--output-image=~", $argument)) {
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
            $cssName = explode("=", $argument)[1];
            if (!preg_match("~[A-Za-z0-9_.]\.css~",$cssName)) {
                $cssName = "style.css";
            }
            echo "\n\e[35m Css name: $cssName  \e[0m \n";
        }


        /* -p parameter */
        if (preg_match("~^-p$~", $argument)) {
            if (isset($argv[$key+1])){
            $padding = $argv[$key + 1];}
            if (!is_numeric($padding)) {
                $padding = 0;
            }
            echo "\n\e[35m Padding: $padding px \e[0m \n";
        }
        if (preg_match("~--padding=~", $argument)) {
            $padding = explode("=", $argument)[1];
            if (!is_numeric($padding)) {
                $padding = 0;
            }
            echo "\n\e[35m Padding: $padding  \e[0m \n";
        }


        /* -o parameter */
        if (preg_match("~^-o$~", $argument)) {
            if (isset($argv[$key+1])){
            $size = $argv[$key + 1];}
            if (!is_numeric($size)||$size<=0) {
                $size =null;
            }else{
                echo "\n\e[35m Size changed =>  Width: $size px | Height: $size px \e[0m \n";
            }
        }
        /* --oversize-image parameter */
        if (preg_match("~--override-size=~", $argument)) {
            $size = explode("=", $argument)[1];
            if (!is_numeric($size)) {
                $size = null;
            }
            echo "\n\e[35m Size changed =>  Width: $size px | Height: $size px \e[0m \n";
        }

    }
}


/**----------------------------------------------------------------------------------------------------------------------------------
 * Launching !
 * ----------------------------------------------------------------------------------------------------------------------------------
 */
start();


/* Normal start */
function start(){
    global $imagesPathsArray,$imagesObject,$argv,$folderArray;
    if (isset($argv[1])){
        $dir=$argv[1];
    if (!is_dir($dir)){
        echo "\n\t\t\e[31mErreur: Veuillez indiquer le chemin vers votre dossier. Redirection vers le MAN en cours...\e[0m \n";
        sleep(2);
        displayMan();
    }else{
        /* Recursivity parameter */
        if (in_array("-r", $argv)||in_array("--recursive",$argv)) {
            scan_dir_recursivity($dir);
        } else {
            scan_dir($dir);
        }

    $imagesObject=createImagesObj($imagesPathsArray);
    copyImagesOnBackground();
    generateCss();
    generateHtml();
    deleteImages($dir);
}}else{echo "\n\t\t\e[31m Erreur lors du passage de paramètres: Redirection vers le MAN en cours...\e[0m \n";
        sleep(2);
        displayMan();}}



/* transform path to objects img */
function createImagesObj($imagesPaths){
    global $imagesPathsArray;
    static $objectsImages;
    $imagesPathsArray=$imagesPaths;
    if (!isset($imagesPathsArray)){
        echo "\t\e[31mErreur: Aucune image de type .png dans ce dossier. Veuillez relancer le programme en passant un dossier contenant des images .png\e[0m \n";
        sleep(3);
        exit();
    }
    foreach ($imagesPaths as $key => $imagePath){
       $objectsImages[$key]=imagecreatefrompng($imagePath);
       imagecreatefrompng($imagePath);
    }
    return $objectsImages;
}


/* get max height to set background height */
function setBackgroundHeightAndWidth(){
    global $imagesObject,$size,$padding;
    static $finalWidth,$maxHeight;
    foreach ($imagesObject as $key => $image){
        if (!isset($size)){
            $maxHeight[$key]=imagesy($image);
            $finalWidth+=imagesx($image)+$padding;
        }else{
            $maxHeight[$key]=$size;
            $finalWidth+=$size+$padding;
        }
    }
    return $widthAndHeight=array( "height" => max($maxHeight),"width" =>$finalWidth);
}

/* Create the background image */
function createBackground(){
    $img = imagecreatetruecolor(setBackgroundHeightAndWidth()["width"], setBackgroundHeightAndWidth()["height"]);
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
    echo "\n\e[33m $imageName generated \e[0m \n";

}


/* GetImages Names */
function getNames($images)
{
    return explode(" ", trim(str_replace(".png", " ", implode($images))));
}

/* Generate Css */
function generateCss(){
    global $positionsx,$imagesPathsArray,$cssName,$size,$imageName;
    $handle = fopen($cssName, "w+");
    fwrite($handle,
        ".sprite { 
    background-image: url($imageName);     
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

function generateHtml(){
    global $imagesPathsArray,$cssName;
    $handle = fopen("test2.html", "w+");
    fwrite($handle,
        "<!DOCTYPE html>
        <html lang=\"en\">
        <head>
            <meta charset=\"UTF-8\">
            <title>Title</title>
            <link rel=\"stylesheet\" href=\"$cssName\">
        </head>
     <body>\n \n");
    foreach ($imagesPathsArray as $key => $file) {
    $imgName=basename(getNames($imagesPathsArray)[$key]);
            fwrite($handle,
            " <p>$imgName</p>
            <div class=\"sprite\" id=\"sprite-".$imgName."\"></div>\n");
    }
        fwrite($handle,  "</body>
</html>");
    echo "\n\e[33m HTML generated  \e[0m \n\n";
}

/*Scan dir func with recursivity*/
function scan_dir_recursivity($dirPath){
    global $imagesPathsArray,$folderArray;
    static $imagesPath;
    if ($handle = opendir($dirPath)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if (preg_match("~\.~",$entry)){
                    if (preg_match("~\.png~",$entry)&&basename($entry!="sprite.png")){
                        $imagesPath[]="$dirPath/$entry";}
                }else{
                    $folderArray[]=$dirPath."/".$entry;
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
function deleteImages($dirPath){
        shell_exec("rm -fr $dirPath");

}
function displayMan(){
    system('clear');
    echo   <<< EOF
\e[33m
   _____            _ _        _____ _               _      _____            
  / ____|          (_) |      / ____| |             | |    / ____|           
 | (___  _ __  _ __ _| |_ ___| (___ | |__   ___  ___| |_  | |  __  ___ _ __  
  \___ \| '_ \| '__| | __/ _ \\___  \| '_ \ / _ \/ _ \ __| | | |_ |/ _ \ '_ \ 
  ____) | |_) | |  | | ||  __/____) | | | |  __/  __/ |_  | |__| |  __/ | | |
 |_____/| .__/|_|  |_|\__\___|_____/|_| |_|\___|\___|\__|  \_____|\___|_| |_|
        | |                                                                  
        |_|                                                                  

 
\e[0m 
                            
 \e[33m[SYNOPSIS]\e[0m 
 css_generator [OPTIONS]. . . assets_folder
 
 
 \e[33m[SYNOPSIS]\e[0m 
 php ImageMerge.php assets_folder  [OPTIONS]
 As example: php ImageMerge.php /home/laptop/PngImagesFolder -r --output-image=spriteSheet.png -o 100
 Here recursive mode enabled to scan /home/laptop/PngImagesFolder, output name of the sprite sheet set as "spriteSheet.png" and images resized 100px*100px
 
 
 \e[33m[DESCRIPTION]\e[0m
 Concatenate all images inside a folder in one sprite and write a style sheet ready to use. Mandatory arguments to long options are mandatory 
 for short options too. Html page also generated and all given images are deleted.
 
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
 
EOF;
}

