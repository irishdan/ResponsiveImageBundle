<?php

namespace IrishDan\ResponsiveImageBundle\EventSubscriber;

use IrishDan\ResponsiveImageBundle\Event\UploaderEvent;
use IrishDan\ResponsiveImageBundle\Event\UploaderEvents;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UploaderSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $fileSystem;

    public function __construct(LoggerInterface $logger, FilesystemInterface $fileSystem)
    {
        $this->logger = $logger;
        $this->fileSystem = $fileSystem;
    }

    public static function getSubscribedEvents()
    {
        return [
            UploaderEvents::UPLOADER_PRE_UPLOAD => 'onPreUpload',
            UploaderEvents::UPLOADER_UPLOADED => 'onUploaded',
        ];
    }

    public function onPreUpload(UploaderEvent $event)
    {
        $this->logger->critical('Pre upload fired');
        // @TODO: POC: this is how we can swap filesystems per applications
        $uploader = $event->getUploader();

        if (empty($uploader->getFileSystem())) {
            $uploader->setFileSystem($this->fileSystem);
        }
    }

    public function onUploaded(UploaderEvent $event)
    {
        $this->logger->critical('Uploaded fired');
    }
}