<?php

namespace IrishDan\ResponsiveImageBundle\EventSubscriber;

use IrishDan\ResponsiveImageBundle\Event\FileSystemEvent;
use IrishDan\ResponsiveImageBundle\Event\FileSystemEvents;
use IrishDan\ResponsiveImageBundle\Event\ImageEvent;
use IrishDan\ResponsiveImageBundle\Event\ImageEvents;
use IrishDan\ResponsiveImageBundle\Image\ImageManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ImageSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $imageManager;

    public function __construct(ImageManager $imageManager, LoggerInterface $logger = null)
    {
        $this->imageManager = $imageManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            ImageEvents::IMAGE_CREATED => 'onImageCreated',
            ImageEvents::IMAGE_UPDATED => 'onImageUpdated',
            ImageEvents::IMAGE_DELETED => 'onImageDeleted',
        ];
    }

    public function onImageCreated(ImageEvent $event)
    {
        // Generate all styled images.
        $image = $event->getImage();
        $this->imageManager->createAllStyledImages($image);

        if (!empty($this->logger)) {
            $this->logger->debug('Image ' . $image->getId() . ' onImageCreated called');
        }
    }

    public function onImageUpdated(ImageEvent $event)
    {
        // Re-generate all styled images.
        // @TODO: Check for updated fields possible?? (crop focus, path or file)
        $image = $event->getImage();
        $this->imageManager->createAllStyledImages($image);

        if (!empty($this->logger)) {
            $this->logger->debug('Image ' . $image->getId() . ' onImageUpdated called');
        }
    }

    public function onImageDeleted(ImageEvent $event)
    {
        // Delete all styled images
        $image = $event->getImage();
        $this->imageManager->deleteAllImages($image);

        if (!empty($this->logger)) {
            $this->logger->debug('Image ' . $image->getId() . ' onImageDeleted called');
        }
    }
}