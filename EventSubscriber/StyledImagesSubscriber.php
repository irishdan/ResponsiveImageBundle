<?php

namespace IrishDan\ResponsiveImageBundle\EventSubscriber;

use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvent;
use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvents;
use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StyledImagesSubscriber implements EventSubscriberInterface
{
    private $temporaryFileSystem;
    private $primaryFileSystem;
    private $logger;

    public function __construct(
        PrimaryFileSystemWrapper $PrimaryFileSystemWrapper,
        FilesystemInterface $temporaryFileSystem = null,
        LoggerInterface $logger = null
    )
    {
        $this->temporaryFileSystem = $temporaryFileSystem;
        $this->primaryFileSystem = $PrimaryFileSystemWrapper->getFileSystem();
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            StyledImagesEvents::STYLED_IMAGES_GENERATED => 'onImagesGenerated',
            StyledImagesEvents::STYLED_IMAGES_DELETED => 'onImagesDeleted',
        ];
    }

    public function onImagesGenerated(StyledImagesEvent $event)
    {
        $image = $event->getImage();
        $this->temporaryFileSystem->delete($image->getPath());

        $generatedImages = $event->getStyleImageLocationArray();

        foreach ($generatedImages as $style => $relativePath) {
            $contents = $this->temporaryFileSystem->read($relativePath);
            $this->primaryFileSystem->put($relativePath, $contents);

            $this->temporaryFileSystem->delete($relativePath);
        }

        $this->logger->critical('StyledImages subscriber generated');
    }

    public function onImagesDeleted(StyledImagesEvent $event)
    {
        // @TODO: Do we need this
        $this->logger->critical('StyledImages subscriber deleted');
    }
}