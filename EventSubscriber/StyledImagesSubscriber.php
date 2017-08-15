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

use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvent;
use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvents;
use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class StyledImagesSubscriber
 *
 * @package IrishDan\ResponsiveImageBundle\EventSubscriber
 */
class StyledImagesSubscriber implements EventSubscriberInterface
{
    /**
     * @var FilesystemInterface
     */
    private $temporaryFileSystem;
    /**
     * @var FilesystemInterface|null
     */
    private $primaryFileSystem;

    /**
     * StyledImagesSubscriber constructor.
     *
     * @param PrimaryFileSystemWrapper $PrimaryFileSystemWrapper
     * @param FilesystemInterface|null $temporaryFileSystem
     */
    public function __construct(PrimaryFileSystemWrapper $PrimaryFileSystemWrapper, FilesystemInterface $temporaryFileSystem = null)
    {
        $this->temporaryFileSystem = $temporaryFileSystem;
        $this->primaryFileSystem   = $PrimaryFileSystemWrapper->getFileSystem();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            StyledImagesEvents::STYLED_IMAGES_GENERATED => 'onImagesGenerated',
            StyledImagesEvents::STYLED_IMAGES_DELETED   => 'onImagesDeleted',
        ];
    }

    /**
     * @param StyledImagesEvent $event
     */
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
    }

    /**
     * @param StyledImagesEvent $event
     */
    public function onImagesDeleted(StyledImagesEvent $event)
    {
        // @TODO: Implement.
    }
}