<?php

namespace IrishDan\ResponsiveImageBundle\EventListener;

use IrishDan\ResponsiveImageBundle\Event\ImageEvent;
use IrishDan\ResponsiveImageBundle\ResponsiveImageManager;

/**
 * Class ImageListener
 *
 * @package ResponsiveImageBundle\Event
 */
class ImageListener
{
    /**
     * @var
     */
    private $config;
    /**
     * @var
     */
    private $imageManager;

    /**
     * ImageListener constructor.
     *
     * @param $config
     * @param $imageManager
     */
    public function __construct(array $config, ResponsiveImageManager $imageManager)
    {
        $this->config = $config;
        $this->imageManager = $imageManager;
    }

    public function imageGenerateStyled(ImageEvent $event)
    {
        $image = $event->getImage();
        $this->imageManager->createStyledImages($image);
    }

    public function imageDeleteAll(ImageEvent $event)
    {
        $image = $event->getImage();
        if (!empty($image)) {
            $this->imageManager->deleteImageFiles($image, true, true);
        }
    }

    public function imageDeleteOriginal(ImageEvent $event)
    {
        $image = $event->getImage();
        if (!empty($image)) {
            $this->imageManager->deleteImageFiles($image, true, false);
        }
    }

    public function imageDeleteStyled(ImageEvent $event)
    {
        $image = $event->getImage();
        if (!empty($image)) {
            $this->imageManager->deleteImageFiles($image, false, true);
        }
    }

    public function styleDeleteStyled(ImageEvent $event)
    {
        $styles = $event->getStyles();
        if (!empty($styles)) {
            $this->imageManager->deleteStyleFiles($styles);
        }
    }

    public function styleDeleteAll()
    {
        $this->imageManager->deleteStyleFiles();
    }
}