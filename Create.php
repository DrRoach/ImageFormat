<?php

class Create
{
    public function __construct(array $params = [])
    {
        /**
         * Load in the autoloader
         */
        spl_autoload_register('self::autoload');

        /**
         * Check to make sure that all parameters are added
         */
        $die = self::validateParams($params);

        /**
         * If $die has been set, data is missing. Throw error and give message
         */
        if ($die !== false) {
            switch ($die) {
                case 'image':
                    throw new Exception('You must provide a image', 400);
                case 'background':
                    throw new Exception('You must provide a desired background colour', 400);
                case 'width':
                    throw new Exception('You must provide a thumbnail width', 400);
                case 'height':
                    throw new Exception('You must provide a thumbnail height', 400);
                case 'name':
                    throw new Exception('You must provide a thumbnail name', 400);
            }
        }

        Data::parseHex();

        Image::parseImage();
    }

    /**
     * @param $params
     * @return false|string
     * @throws Exception
     */
    private static function validateParams($params)
    {
        $die = false;
        empty($params['image']) ? $die = 'image' : Data::$IMAGE = $params['image'];
        empty($params['width']) ? $die = 'width' : Data::$WIDTH = $params['width'];
        empty($params['height']) ? $die = 'height' : Data::$HEIGHT = $params['height'];
        empty($params['background']) ? $die = 'background' : Data::$BACKGROUND = $params['background'];
        empty($params['accuracy']) ? null : Data::$ACCURACY = $params['accuracy'];
        empty($params['name']) ? $die = 'name' : Data::$NAME = $params['name'];

        if (!file_exists(Data::$IMAGE)) {
            throw new Exception('The image you provided couldn\'t be found', 400);
        }

        return $die;
    }

    public function autoload($class)
    {
        require_once $class.'.php';
    }
}
