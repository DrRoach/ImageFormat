<?php

class Data
{
    public static $IMAGE;
    public static $WIDTH;
    public static $HEIGHT;
    public static $BACKGROUND;
    public static $ACCURACY = 40;
    public static $NAME;

    public static function parseHex()
    {
        if(substr(self::$BACKGROUND, 0, 1) == '#') {
            self::$BACKGROUND = substr(self::$BACKGROUND, 1);
        }
    }
}