<?php
function my_generate_css($image1,$image2)
{
    $array = array($image1, $image2);
    $styleName = "style.css";
    $handle = fopen($styleName, "w+");

    //TODO change sprite.png by name
    fwrite($handle,
        ".sprite { 
    background-image: url(sprite.png);     
    background-repeat: no-repeat;
    display: block;
    } \n\n");

    foreach ($array as $key => $file) {
        $dataImg = getimagesize($file);
        static $backPosWidth=5;

        $backPosWidth=strval($backPosWidth);
        fwrite($handle,".sprite-".get_name($array)[$key]."{".
            "\n"."width:".strval( $dataImg[0])."px;".
            "\n"."height:". strval($dataImg[1]) ."px;".
            "\nbackground-position: -".$backPosWidth."px -5px;} \n\n");
        $backPosWidth+=$dataImg[0]+10;
    }


}


    function get_name($array)
    {
        $newarr = explode(" ", trim(str_replace(".png", " ", implode($array))));
        return $newarr;
    }

    my_generate_css("img1.png", "img2.png");


/**WHAT WE LOOKING FOR :
.sprite {
    background-image: url(spritesheet.png);
    background-repeat: no-repeat;
    display: block;
}

.sprite-github {
    width: 30px;
    height: 30px;
    background-position: -5px -5px;
}

.sprite-gmail {
    width: 30px;
    height: 30px;
    background-position: -45px -5px;
}
*/