<?php

class CaptchaCircle
{
    /** @var null|int */
    protected $_captchaId = NULL;

    /** @var null|int  */
    protected $_centerX = NULL;

    /** @var null|int */
    protected $_centerY = NULL;

    /** @var null|int */
    protected $_diameter = NULL;

    /** @var int */
    protected $_sizeX = 0;

    /** @var int */
    protected $_sizeY = 0;

    /**
     * Instance
     * @param int $captchaId
     * @param int $sizeX
     * @param int $sizeY
     */
    public function __construct($captchaId, $sizeX, $sizeY)
    {
        $this->_captchaId = (int)$captchaId;
        $this->_sizeX = max(100, (int)$sizeX);
        $this->_sizeY = max(100, (int)$sizeY);
        $this->_generateCoords();
    }

    /**
     * Generates a new captcha id
     * @return string
     */
    public static function generateId()
    {
        return mt_rand(0, time());
    }

    /**
     * Calculates the radius
     */
    protected function _generateDiameter()
    {
        mt_srand($this->_captchaId);
        $size = min($this->_sizeX, $this->_sizeY);
        $this->_diameter = mt_rand($size/10, $size/3*2);
    }

    /**
     * Calculates the center coordinates
     */
    protected function _generateCoords()
    {
        $this->_generateDiameter();
        mt_srand($this->_captchaId);
        $this->_centerX = mt_rand($this->_diameter/2, $this->_sizeX-$this->_diameter/2);
        $this->_centerY = mt_rand($this->_diameter/2, $this->_sizeY-$this->_diameter/2);
    }

    /**
     * Validates if the user has clicked on a valid coord of the captcha
     * @param int $coordX
     * @param int $coordY
     * @return bool
     */
    public function isValid($coordX, $coordY)
    {
        $dist = sqrt(pow($coordX-$this->_centerX, 2) + pow($coordY-$this->_centerY, 2));
        if ($dist <= $this->_diameter/2) {
            return true;
        }

        return false;
    }

    /**
     * Shows the captcha
     */
    public function show()
    {
        // Check for GD library
        if( !function_exists('gd_info') ) {
            throw new Exception('Required GD library is missing');
        }

        mt_srand($this->_captchaId);
        $img = imageCreateTrueColor($this->_sizeX, $this->_sizeY);

        $bgColor = imageColorAllocate($img, 0, 0, 0);
        $fgColor = imageColorAllocate($img, 90, 90, 90);
        $fgColorRed = imageColorAllocate($img, 255, 0, 0);

        imagefill($img , 0, 0, $bgColor);

        // draw distraction circles
        $numCircles = mt_rand(15, 30);
        for($i=0; $i<$numCircles; $i++) {
            $x = mt_rand(0, $this->_sizeX);
            $y = mt_rand(0, $this->_sizeY);
            $size = min($this->_sizeX, $this->_sizeY);
            $rad = mt_rand($size/10, $size*2/3);
            imagearc($img, $x, $y, $rad, $rad,  0, 360, $fgColor);
        }

        // draw checking circle (opened)
        $rotateRand = mt_rand(0, 360);
        imagearc(
            $img,
            $this->_centerX,
            $this->_centerY,
            $this->_diameter,
            $this->_diameter,
            0 + $rotateRand,
            300 + $rotateRand,
            $fgColor
        );

        header('Expires: '.GMDate("D, d M Y H:i:s", time()).' GMT');
        header('Last-Modified: '.GMDate("D, d M Y H:i:s", time()).' GMT');
        header('Content-type: image/png');
        header('Content-Disposition: inline; filename=captcha'.$this->_captchaId);
        imagePNG($img);
    }
}