<?php

class Thumbnail
{
    public static function trim($image, $startx, $starty, $endx, $endy)
    {
        $thumb = imagecreatetruecolor(Data::$WIDTH, Data::$HEIGHT);
        imagecopyresampled($thumb, $image, 0, 0, $startx, $starty, Data::$WIDTH, Data::$HEIGHT, $endx - $startx, $endy - $starty);
        imagepng($thumb, Data::$NAME.'.png');
    }

    public static function save($image)
    {
        $thumb = imagecreatetruecolor(Data::$WIDTH, Data::$HEIGHT);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, Data::$WIDTH, Data::$HEIGHT, Data::$WIDTH, Data::$HEIGHT);
        imagepng($thumb, Data::$NAME.'.png');
    }
}