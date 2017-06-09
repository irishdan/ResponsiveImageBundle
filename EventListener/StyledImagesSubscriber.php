<?php

namespace IrishDan\ResponsiveImageBundle\EventListener;

use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvent;
use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvents;
use IrishDan\ResponsiveImageBundle\FileSystem\FileSystemFactory;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StyledImagesSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $temporaryFileSystem;

    public function __construct(LoggerInterface $logger, FileSystemFactory $fileSystemFactory, FilesystemInterface $temporaryFileSystem)
    {
        $this->logger = $logger;
        $this->temporaryFileSystem = $temporaryFileSystem;
        $this->primaryFileSystem = $fileSystemFactory->getFileSystem();
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

        $generateImages = $event->getStyleImageLocationArray();
        foreach ($generateImages as $style => $relativePath) {
            $contents = $this->temporaryFileSystem->read($relativePath);
            $this->primaryFileSystem->put($relativePath, $contents);

            $this->temporaryFileSystem->delete($relativePath);
        }

        $this->logger->critical('StyledImages subscriber generated');
    }

    public function onImagesDeleted(StyledImagesEvent $event)
    {
        $this->logger->critical('StyledImages subscriber deleted');
    }
}