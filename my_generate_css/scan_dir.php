<?php
function scan_dir($dirPath){

    if ($handle = opendir($dirPath)) {

        while (false !== ($entry = readdir($handle))) {

            if ($entry != "." && $entry != "..") {
            if (preg_match("~\.~",$entry)){
                if (preg_match("~\.png~",$entry)){
                    $images[]="$dirPath/$entry";}
            }else{
                scan_dir($dirPath."/".$entry);
            }

            }
        }

        closedir($handle);
    }
    var_dump($images);
}

scan_dir("/home/profchen/Documents/Epitech/ModuleCss/CSS Generator/class");