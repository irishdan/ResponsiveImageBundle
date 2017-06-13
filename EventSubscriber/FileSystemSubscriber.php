<?php

namespace IrishDan\ResponsiveImageBundle\EventSubscriber;

use IrishDan\ResponsiveImageBundle\Event\FileSystemEvent;
use IrishDan\ResponsiveImageBundle\Event\FileSystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FileSystemSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            FileSystemEvents::FILE_SYSTEM_FACTORY_GET => 'onFileSystemGet',
            FileSystemEvents::FILE_SYSTEM_FACTORY_SET => 'onFileSystemSet',
        ];
    }

    public function onFileSystemSet(FileSystemEvent $event)
    {
        if ($this->logger) {
            $this->logger->info('File System subscriber onFileSystemSet');
        }
    }

    public function onFileSystemGet(FileSystemEvent $event)
    {
        $this->logger->info('File System subscriber onFileSystemSet');
    }
}