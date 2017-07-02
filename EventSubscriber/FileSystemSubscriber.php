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

use IrishDan\ResponsiveImageBundle\Event\FileSystemEvent;
use IrishDan\ResponsiveImageBundle\Event\FileSystemEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FileSystemSubscriber
 *
 * @package IrishDan\ResponsiveImageBundle\EventSubscriber
 */
class FileSystemSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FileSystemSubscriber constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FileSystemEvents::FILE_SYSTEM_FACTORY_GET => 'onFileSystemGet',
            FileSystemEvents::FILE_SYSTEM_FACTORY_SET => 'onFileSystemSet',
        ];
    }

    /**
     * @param FileSystemEvent $event
     */
    public function onFileSystemSet(FileSystemEvent $event)
    {
        if ($this->logger) {
            $this->logger->info('File System subscriber onFileSystemSet');
        }
    }

    /**
     * @param FileSystemEvent $event
     */
    public function onFileSystemGet(FileSystemEvent $event)
    {
        $this->logger->info('File System subscriber onFileSystemSet');
    }
}