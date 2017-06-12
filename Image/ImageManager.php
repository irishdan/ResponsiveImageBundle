<?php

namespace IrishDan\ResponsiveImageBundle\Image;

use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvent;
use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvents;
use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use IrishDan\ResponsiveImageBundle\ImageStyler;
use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\StyleManager;
use League\Flysystem\FilesystemInterface;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ImageManager
 *
 * @package IrishDan\ResponsiveImageBundle\Image
 */
class ImageManager
{
    protected $styleManager;
    protected $ImageStyler;
    protected $fileSystem;
    protected $temporaryFileSystem;
    protected $eventDispatcher;
    protected $generatedImages = [];

    public function __construct(
        StyleManager $styleManager,
        ImageStyler $imageStyler,
        PrimaryFileSystemWrapper $fileSystem,
        FilesystemInterface $temporaryFileSystem = null,
        EventDispatcherInterface $eventDispatcher = null
    )
    {
        $this->styleManager = $styleManager;
        $this->ImageStyler = $imageStyler;
        $this->fileSystem = $fileSystem->getFileSystem();
        $this->temporaryFileSystem = $temporaryFileSystem;
        $this->eventDispatcher = $eventDispatcher;

        // @TODO: In terms of configuration for the temporary directory, change the name, and..
        // @TODO: If there's only on local directory configured then intervention will simply use that
        // @TODO: That should be out of the box configuration

        // @TODO: Filestore info save will be enabled simply if the getter exists. Add to generated image entity, commented out
    }

    public function createAllStyledImages(ResponsiveImageInterface $image)
    {
        $styles = $this->styleManager->getAllStylesNames();

        return $this->createStyledImages($image, $styles);
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

        return $this->generatedImages;
    }

    protected function createStyledImage(ResponsiveImageInterface $image, $style)
    {
        $styleData = $this->styleManager->getStyle($style);

        // @TODO: Rename temporaryFileSystem
        // @TODO: Use primary as a fallback
        // @TODO: Ensure that file system is Local

        $directory = $this->temporaryFileSystem->getAdapter()->getPathPrefix();
        $source = $directory . $image->getPath();

        if (!empty($styleData)) {
            $cropFocusData = $image->getCropCoordinates();
            $relativeStylePath = $this->styleManager->getStylePath($image, $style);

            $destination = $directory . $relativeStylePath;

            // Intervention needs directories to exist prior to creating images.
            $this->createStyleDirectory($relativeStylePath);

            try {
                $this->ImageStyler->createImage($source, $destination, $styleData, $cropFocusData);
                $this->generatedImages[$style] = $relativeStylePath;
            } catch (\Exception $e) {
                // @TODO: Throw exception
            }
        } else {
            // throw InvalidArgumentException::
        }
    }

    public function deleteAllImages(ResponsiveImageInterface $image)
    {
        $this->deleteImage($image);
        $this->deleteStyledImages($image);
    }

    public function deleteStyledImages(ResponsiveImageInterface $image, array $styles = [])
    {
        if (empty($styles)) {
            $styles = $this->styleManager->getAllStylesNames();
        }

        foreach ($styles as $style) {
            $this->deleteImage($image, $style);
        }
    }

    public function deleteImage(ResponsiveImageInterface $image, $style = '')
    {
        if (!empty($style)) {
            $path = $this->styleManager->getStylePath($image, $style);
        } else {
            $path = $image->getPath();
        }
        $this->fileSystem->delete($path);
    }

    protected function createStyleDirectory($destination)
    {
        $filename = basename($destination);
        $directory = explode($filename, $destination)[0];

        if (!$this->temporaryFileSystem->has($directory)) {
            $this->temporaryFileSystem->createDir($directory);
        }
    }

    public function imageExists($path)
    {
        return $this->fileSystem->has($path);
    }

    public function createCustomStyledImage(ResponsiveImageInterface $image, $customStyleString, $forceGenerate = false)
    {
        // check is it exists, using the string
        $stylePath = $this->styleManager->getStylePath($image, $customStyleString);

        $exists = $this->imageExists($stylePath);
        if (!$exists || $forceGenerate) {
            // create the style array and add to the existing styles

            $styleData = explode('_', $customStyleString);

            list($custom, $effect, $width, $height) = $styleData;

            $style = [
                'effect' => $effect,
                'width' => $width,
                'height' => $height,
            ];

            // Add this style to the styles array
            $this->styleManager->addStyle($customStyleString, $style);

            // generate the image
            $this->createStyledImages($image, [$style]);
        }
    }

    public function deleteCustomStyledImages(ResponsiveImageInterface $image)
    {
        // @TODO: Implement
    }
}