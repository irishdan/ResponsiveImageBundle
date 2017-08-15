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

use IrishDan\ResponsiveImageBundle\Event\UploaderEvent;
use IrishDan\ResponsiveImageBundle\Event\UploaderEvents;
use League\Flysystem\FilesystemInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UploaderSubscriber
 *
 * @package IrishDan\ResponsiveImageBundle\EventSubscriber
 */
class UploaderSubscriber implements EventSubscriberInterface
{
    /**
     * @var FilesystemInterface
     */
    private $fileSystem;

    /**
     * UploaderSubscriber constructor.
     *
     * @param FilesystemInterface $fileSystem
     */
    public function __construct(FilesystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UploaderEvents::UPLOADER_PRE_UPLOAD => 'onPreUpload',
            UploaderEvents::UPLOADER_UPLOADED   => 'onUploaded',
        ];
    }

    /**
     * @param UploaderEvent $event
     */
    public function onPreUpload(UploaderEvent $event)
    {
        // This is how we can swap filesystems.
        $uploader = $event->getUploader();

        if (empty($uploader->getFileSystem())) {
            $uploader->setFileSystem($this->fileSystem);
        }
    }

    /**
     * @param UploaderEvent $event
     */
    public function onUploaded(UploaderEvent $event)
    {
        // @TODO: Implement
    }
}