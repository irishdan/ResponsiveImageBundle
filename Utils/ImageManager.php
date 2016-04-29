<?php

namespace ResponsiveImageBundle\Utils;


/**
 * Class ImageManager
 * @package ResponsiveImageBundle\Utils
 */
class ImageManager
{
    /**
     * @var
     */
    private $config;

    /**
     * @var
     */
    private $imager;

    /**
     * @var
     */
    private $styleManager;

    /**
     * ImageManager constructor.
     * @param $imager
     * @param $config
     */
    public function __construct($imager, $styleManager, $config)
    {
        $this->imager = $imager;
        $this->styleManager = $styleManager;
        $this->config = $config;
    }

    public function createStyledImage($image, $style) {
        $crop = empty($imageObject) ? null : $image->getCropCoordinates();
        $this->get('responsive_image.imager')->createImage($originalPath, $stylePath, $style, $crop);
    }

    /**
     * @param ResponsiveImageInterface $image
     */
    public function createAllStyledImages(ResponsiveImageInterface $image) {

    }

    /**
     * @param ResponsiveImageInterface $image
     */
    public function deleteAllStyledImages(ResponsiveImageInterface $image) {

    }


}