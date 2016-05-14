<?php

namespace ResponsiveImageBundle\Event;

use ResponsiveImageBundle\Event\ImageEvent;

/**
 * Class ImageListener
 *
 * @package ResponsiveImageBundle\Event
 */
class ImageListener {
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
     */
    public function __construct($config, $imageManager)
    {
        $this->config = $config;
        $this->imageManager = $imageManager;
    }

    /**
     * @param \ResponsiveImageBundle\Event\ImageEvent $event
     */
    public function onImageGenerated(ImageEvent $event)
    {
        if (!empty($this->config['aws_s3'])) {
            // $this->imageManager->transferToS3($event);
        }

    }

    /**
     * @param \ResponsiveImageBundle\Event\ImageEvent $event
     */
    public function onImageCreated(ImageEvent $event) {
        $image = $event->getImage();
        $this->imageManager->createAllStyledImages($image);

        $this->imageManager->transferToS3();
    }

    /**
     * @param \ResponsiveImageBundle\Event\ImageEvent $event
     */
    public function onImageUpdated(ImageEvent $event) {
        $image = $event->getImage();
        $this->imageManager->deleteAllStyledImages($image);
        $this->imageManager->createAllStyledImages($image);

        $this->imageManager->transferToS3();
    }
}