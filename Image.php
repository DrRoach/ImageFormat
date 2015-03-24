<?php

class Image
{
    private static $BACKGROUND;
    private static $IMAGE;
    private static $TOPTRIM;
    private static $RIGHTTRIM;
    private static $BOTTOMTRIM;
    private static $LEFTTRIM;

    private static $PADDING = 1;

    public static function parseImage()
    {
        ini_set('xdebug.max_nesting_level', 500);
        ini_set('memory_limit', '-1');
        //Create a resource of the colour
        self::$IMAGE = self::createResource(Data::$IMAGE);
        //Begin by working out the background colour
        self::$BACKGROUND = self::detectBackgroundColour();
        //Find the beginning of the product
        self::findProduct();
        //Trim the image
        Thumbnail::trim(self::$IMAGE, self::$LEFTTRIM, self::$TOPTRIM, self::$RIGHTTRIM, self::$BOTTOMTRIM);
        //Create new resource of trimmed image
        self::$IMAGE = self::createResource(Data::$NAME . '.png');
        //Change all of the pixel colours
        $test = imagecolorallocate(self::$IMAGE, hexdec(substr(Data::$BACKGROUND,0, 2)), hexdec(substr(Data::$BACKGROUND, 2, 2)), hexdec(substr(Data::$BACKGROUND, 4, 2)));
        self::switchBackground($test);
        //Save the new image
        Thumbnail::save(self::$IMAGE);
    }

    private static function switchBackground($colour)
    {
        $x = 0;
        $y = 0;
        while($x < imagesx(self::$IMAGE)) {
            while($y < imagesy(self::$IMAGE)) {
                //Get the current pixel colour
                $pixel = self::getPixelColour($x, $y);
                /**
                 * BEST IF STATEMENT I'VE EVER WRITTEN
                 */
                if($pixel['r'] + 5 > self::$BACKGROUND['r'] && $pixel['r'] - 5 < self::$BACKGROUND['r'] && $pixel['g'] + 5 > self::$BACKGROUND['g'] && $pixel['g'] - 5 < self::$BACKGROUND['g'] && $pixel['b'] + 5 > self::$BACKGROUND['b'] && $pixel['b'] - 5 < self::$BACKGROUND['b']) {
                    imagesetpixel(self::$IMAGE, $x, $y, $colour);
                }
                $y++;
            }
            $y = 0;
            $x++;
        }
    }

    private static function findProduct()
    {
        self::$TOPTRIM = self::scanTop(0);
        self::$BOTTOMTRIM = self::scanBottom(imagesy(self::$IMAGE)-1);
        self::$RIGHTTRIM = self::scanRight(imagesx(self::$IMAGE)-1);
        self::$LEFTTRIM = self::scanLeft(0);
    }

    private static function scanTop($pos)
    {
        $foundProduct = false;
        //Get the colour of each line of pixels
        $x = 0;
        $y = $pos;
        while($x < imagesx(self::$IMAGE) && $foundProduct == false) {
            //Get the colour of the pixel
            $colours = self::getPixelColour($x, $y);
            if(self::$BACKGROUND['r'] > $colours['r'] + 40 || self::$BACKGROUND < $colours['r'] - 40) {
                $foundProduct = true;
            }
            if(self::$BACKGROUND['g'] > $colours['g'] + 40 || self::$BACKGROUND < $colours['g'] - 40) {
                $foundProduct = true;
            }
            if(self::$BACKGROUND['b'] > $colours['b'] + 40 || self::$BACKGROUND < $colours['b'] - 40) {
                $foundProduct = true;
            }
            $x++;
        }
        if($foundProduct == false) {
            return self::scanTop((int)$y + 1);
        } else {
            return $y;
        }
    }

    private static function scanBottom($pos)
    {
        $foundProduct = false;
        //Get the colour of each line of pixels
        $x = 0;
        $y = $pos;
        while($x < imagesx(self::$IMAGE) && $foundProduct == false) {
            //Get the colour of the pixel
            $colours = self::getPixelColour($x, $y);

            if(self::$BACKGROUND['r'] > $colours['r'] + 40 || self::$BACKGROUND < $colours['r'] - 40) {
                $foundProduct = true;
            }
            if(self::$BACKGROUND['g'] > $colours['g'] + 40 || self::$BACKGROUND < $colours['g'] - 40) {
                $foundProduct = true;
            }
            if(self::$BACKGROUND['b'] > $colours['b'] + 40 || self::$BACKGROUND < $colours['b'] - 40) {
                $foundProduct = true;
            }
            $x++;
        }
        if($foundProduct == false) {
            return self::scanBottom($y - 1);
        } else {
            return $y;
        }
    }

    private static function scanRight($pos)
    {
        $foundProduct = false;
        //Get the colour of each line of pixels
        $x = $pos;
        $y = 0;
        while($y < imagesy(self::$IMAGE) && $foundProduct == false) {
            //Get the colour of the pixel
            $colours = self::getPixelColour($x, $y);

            if(self::$BACKGROUND['r'] > $colours['r'] + Data::$ACCURACY || self::$BACKGROUND < $colours['r'] - Data::$ACCURACY) {
                $foundProduct = true;
            }
            if(self::$BACKGROUND['g'] > $colours['g'] + Data::$ACCURACY || self::$BACKGROUND < $colours['g'] - Data::$ACCURACY) {
                $foundProduct = true;
            }
            if(self::$BACKGROUND['b'] > $colours['b'] + Data::$ACCURACY || self::$BACKGROUND < $colours['b'] - Data::$ACCURACY) {
                $foundProduct = true;
            }
            $y++;
        }
        if($foundProduct == false) {
            return self::scanRight($x - 1);
        } else {
            return $x;
        }
    }

    private static function scanLeft($pos)
    {
        $foundProduct = false;
        //Get the colour of each line of pixels
        $x = $pos;
        $y = 0;
        while($y < imagesy(self::$IMAGE) && $foundProduct == false) {
            //Get the colour of the pixel
            $colours = self::getPixelColour($x, $y);

            if(self::$BACKGROUND['r'] > $colours['r'] + Data::$ACCURACY || self::$BACKGROUND < $colours['r'] - Data::$ACCURACY) {
                $foundProduct = true;
            }
            if(self::$BACKGROUND['g'] > $colours['g'] + Data::$ACCURACY || self::$BACKGROUND < $colours['g'] - Data::$ACCURACY) {
                $foundProduct = true;
            }
            if(self::$BACKGROUND['b'] > $colours['b'] + Data::$ACCURACY || self::$BACKGROUND < $colours['b'] - Data::$ACCURACY) {
                $foundProduct = true;
            }
            $y++;
        }
        if($foundProduct == false) {
            return self::scanLeft($x + 1);
        } else {
            return $x;
        }
    }

    private static function detectBackgroundColour()
    {
        //Get the four corner pixel colours
        $corners = self::getCornerColours();

        $r = null;
        $g = null;
        $b = null;
        $match = true;
        foreach($corners as $c) {
            empty($r) ? $r = $c['r'] : null;
            empty($g) ? $g = $c['g'] : null;
            empty($b) ? $b = $c['b'] : null;

            //Colours don't match, break
            if($r != $c['r'] || $g != $c['g'] || $b != $c['b']) {
                $match = false;
                break;
            }
        }

        //No match, increase the padding and check again
        if(!$match) {
            self::$PADDING++;
            self::detectBackgroundColour();
        }

        return $corners['topLeft'];
    }

    private static function getCornerColours()
    {
        return [
            'topLeft' => self::getPixelColour(self::$PADDING, self::$PADDING),
            'topRight' => self::getPixelColour(imagesx(self::$IMAGE) - self::$PADDING, self::$PADDING),
            'bottomLeft' => self::getPixelColour(self::$PADDING, imagesy(self::$IMAGE) - self::$PADDING),
            'bottomRight' => self::getPixelColour(imagesx(self::$IMAGE) - self::$PADDING, imagesy(self::$IMAGE) - self::$PADDING)
        ];
    }

    private static function createResource($image)
    {
        /**
         * Get the Image file extension
         */
        $extension = substr($image, strripos($image, '.') + 1);

        /**
         * Create the image resource depending on the extension
         */
        switch ($extension) {
            case 'png':
                return imagecreatefrompng($image);
                break;
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($image);
                break;
        }
    }

    private static function getPixelColour($x, $y)
    {
        $rgb = imagecolorat(self::$IMAGE, $x, $y);
        $r = ($rgb >> 16) & 0xff;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }
}