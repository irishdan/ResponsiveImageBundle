<?php

namespace IrishDan\ResponsiveImageBundle;

/**
 * Class ResponsiveImageManager
 *
 * This is wrapper service.
 * Its provides a convenient for accessing image information without using the twig functions.
 * Its allow for easy uploading of images
 *
 * @package ResponsiveImageBundle
 */
class ResponsiveImageManager
{
    /**
     * @var
     */
    private $config;
    private $styleManager;
    private $uploader;

    public function __construct(StyleManager $styleManager, array $config, $uploader)
    {
        // @TODO: Decide if this staying or going
        // GOING

        $this->styleManager = $styleManager;
        $this->config       = $config;
        $this->uploader     = $uploader;
    }

    public function createStyledImages(ResponsiveImageInterface $image, $stylename = null)
    {
    }

    public function deleteStyleFiles(array $styles)
    {
    }

    public function setImageStyle(ResponsiveImageInterface $image, $styleName)
    {
    }

    public function setPictureSet(ResponsiveImageInterface $image, $pictureSet)
    {
    }

    public function uploadImage(ResponsiveImageInterface $image)
    {
    }
}