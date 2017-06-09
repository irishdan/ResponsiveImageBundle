<?php

namespace IrishDan\ResponsiveImageBundle\EventListener;

use IrishDan\ResponsiveImageBundle\Event\FileSystemEvent;
use IrishDan\ResponsiveImageBundle\Event\FileSystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FileSystemSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        // @TODO: Currently we don;y evce need this. this is for implementers to switch filesystem
        return [
            FileSystemEvents::FILE_SYSTEM_FACTORY_GET => 'onFileSystemGet',
            FileSystemEvents::FILE_SYSTEM_FACTORY_SET => 'onFileSystemSet',
        ];
    }

    public function onFileSystemSet(FileSystemEvent $event)
    {
        $this->logger->critical('File System subscriber onFileSystemSet');
    }

    public function onFileSystemGet(FileSystemEvent $event)
    {
        $this->logger->critical('File System subscriber onFileSystemSet');
    }
}