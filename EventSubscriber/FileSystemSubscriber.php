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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FileSystemSubscriber
 *
 * @package IrishDan\ResponsiveImageBundle\EventSubscriber
 */
class FileSystemSubscriber implements EventSubscriberInterface
{
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
     * @param FileSystemEvent $fileSystemEvent
     */
    public function onFileSystemSet(FileSystemEvent $fileSystemEvent)
    {
        // @TODO: Implement
    }

    /**
     * @param FileSystemEvent $fileSystemEvent
     */
    public function onFileSystemGet(FileSystemEvent $fileSystemEvent)
    {
        // @TODO: Implement
    }
}