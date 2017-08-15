<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\EventSubscriber;


use IrishDan\ResponsiveImageBundle\Event\ImageEvent;
use IrishDan\ResponsiveImageBundle\Event\ImageEvents;
use IrishDan\ResponsiveImageBundle\ImageProcessing\ImageManager;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ImageSubscriber
 *
 * @package IrishDan\ResponsiveImageBundle\EventSubscriber
 */
class ImageSubscriber implements EventSubscriberInterface
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * ImageSubscriber constructor.
     *
     * @param ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ImageEvents::IMAGE_CREATED => 'onImageCreated',
            ImageEvents::IMAGE_UPDATED => 'onImageUpdated',
            ImageEvents::IMAGE_DELETED => 'onImageDeleted',
        ];
    }

    /**
     * @param ImageEvent $event
     */
    public function onImageCreated(ImageEvent $event)
    {
        // Generate all styled images.
        $image = $event->getImage();
        $this->imageManager->createAllStyledImages($image);
    }

    /**
     * @param ImageEvent $event
     */
    public function onImageUpdated(ImageEvent $event)
    {
        // Re-generate all styled images.
        // @TODO: Check for updated fields possible?? (crop focus, path or file)
        $image = $event->getImage();
        $this->imageManager->createAllStyledImages($image);
    }

    /**
     * @param ImageEvent $event
     */
    public function onImageDeleted(ImageEvent $event)
    {
        // Delete all styled images
        $image = $event->getImage();
        $this->imageManager->deleteAllImages($image);
    }
}