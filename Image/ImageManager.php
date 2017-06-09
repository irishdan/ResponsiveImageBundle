<?php

namespace IrishDan\ResponsiveImageBundle\Image;

use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvent;
use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvents;
use IrishDan\ResponsiveImageBundle\FileSystem\FileSystemFactory;
use IrishDan\ResponsiveImageBundle\ImageMaker;
use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\StyleManager;
use League\Flysystem\FilesystemInterface;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImageManager
{
    private $styleManager;
    private $imageMaker;
    private $fileSystem;
    private $temporaryFileSystem;
    private $eventDispatcher;
    private $generatedImages = [];

    public function __construct(
        StyleManager $styleManager,
        ImageMaker $imageMaker,
        FileSystemFactory $fileSystem,
        FilesystemInterface $temporaryFileSystem,
        EventDispatcherInterface $eventDispatcher = null
    )
    {
        $this->styleManager = $styleManager;
        $this->imageMaker = $imageMaker;
        $this->fileSystem = $fileSystem;
        $this->temporaryFileSystem = $temporaryFileSystem;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createStyledImages(ResponsiveImageInterface $image, array $styles = [])
    {
        // Copy the image from its current filesystem onto the filesystem used by intervention.
        $contents = $this->fileSystem->read($image->getPath());
        $this->temporaryFileSystem->put($image->getPath(), $contents);

        // Generate all of the required files
        foreach ($styles as $style) {
            // @TODO: Use the relative part instead of the full path!!!
            $this->createStyledImage($image, $style);
        }

        // Dispatch an event.
        if (!empty($this->eventDispatcher)) {
            $imagesGeneratedEvent = new StyledImagesEvent($image, $this->generatedImages);
            $this->eventDispatcher->dispatch(StyledImagesEvents::STYLED_IMAGES_GENERATED, $imagesGeneratedEvent);
        }

        var_dump($this->generatedImages);

        return $this->generatedImages;
    }

    protected function createStyledImage(ResponsiveImageInterface $image, $style)
    {
        $styleData = $this->styleManager->getStyle($style);
        $directory = $this->temporaryFileSystem->getAdapter()->getPathPrefix();
        $source = $directory . $image->getPath();

        if (!empty($styleData)) {
            $cropFocusData = $image->getCropCoordinates();
            $relativeStylePath = $this->styleManager->getStylePath($image, $style);

            $destination = $directory . $relativeStylePath;

            // Intervention needs directories to exist prior to creating images.
            $this->createStyleDirectory($relativeStylePath);

            try {
                $this->imageMaker->createImage($source, $destination, $styleData, $cropFocusData);
                $this->generatedImages[$style] = $relativeStylePath;
            } catch (\Exception $e) {
                // @TODO: Throw exception
            }
        } else {
            // throw InvalidArgumentException::
        }
    }

    public function deleteStyledImages(ResponsiveImageInterface $image, array $styles = [])
    {
        if (empty($styles)) {
            $styles = $this->styleManager->getAllStyles();
        }

        foreach ($styles as $style) {
            $this->deleteImage($image, $style);
        }
    }

    public function deleteImage(ResponsiveImageInterface $image, $style = '')
    {
        // @TODO: Implement
    }

    protected function createStyleDirectory($destination)
    {
        $filename = basename($destination);
        $directory = explode($filename, $destination)[0];

        if (!$this->temporaryFileSystem->has($directory)) {
            $this->temporaryFileSystem->createDir($directory);
        }
    }
}