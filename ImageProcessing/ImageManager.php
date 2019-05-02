<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\ImageProcessing;

use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvent;
use IrishDan\ResponsiveImageBundle\Event\StyledImagesEvents;
use IrishDan\ResponsiveImageBundle\Exception\ImageFilesystemException;
use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\StyleManager;
use League\Flysystem\FilesystemInterface;
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

    /**
     * ImageManager constructor.
     * @param StyleManager $styleManager
     * @param ImageStyler $imageStyler
     * @param PrimaryFileSystemWrapper $fileSystem
     * @param FilesystemInterface|null $temporaryFileSystem
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(
        StyleManager $styleManager,
        ImageStyler $imageStyler,
        PrimaryFileSystemWrapper $fileSystem,
        FilesystemInterface $temporaryFileSystem = null,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->styleManager        = $styleManager;
        $this->ImageStyler         = $imageStyler;
        $this->fileSystem          = $fileSystem->getFileSystem();
        $this->temporaryFileSystem = $temporaryFileSystem;
        $this->eventDispatcher     = $eventDispatcher;
    }

    /**
     * @param ResponsiveImageInterface $image
     * @return array
     */
    public function createAllStyledImages(ResponsiveImageInterface $image)
    {
        $styles = $this->styleManager->getAllStylesNames();

        return $this->createStyledImages($image, $styles);
    }

    /**
     * @param ResponsiveImageInterface $image
     * @param array $styles
     * @return array
     */
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

    /**
     * @param ResponsiveImageInterface $image
     * @param $style
     */
    protected function createStyledImage(ResponsiveImageInterface $image, $style)
    {
        $styleData = $this->styleManager->getStyleData($style);

        $directory = $this->temporaryFileSystem->getAdapter()->getPathPrefix();
        $source    = $directory . $image->getPath();

        if (!empty($styleData)) {
            $cropFocusData     = $image->getCropCoordinates();
            $relativeStylePath = $this->styleManager->getStylePath($image, $style);

            $destination = $directory . $relativeStylePath;

            // Intervention needs directories to exist prior to creating images.
            $this->createStyleDirectory($relativeStylePath);

            try {
                $this->ImageStyler->createImage($source, $destination, $styleData, $cropFocusData);
                $this->generatedImages[$style] = $relativeStylePath;
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @param ResponsiveImageInterface $image
     */
    public function deleteAllImages(ResponsiveImageInterface $image)
    {
        $this->deleteImage($image);
        $this->deleteStyledImages($image);
    }

    /**
     * @param ResponsiveImageInterface $image
     * @param array $styles
     */
    public function deleteStyledImages(ResponsiveImageInterface $image, array $styles = [])
    {
        if (empty($styles)) {
            $styles = $this->styleManager->getAllStylesNames();
        }

        foreach ($styles as $style) {
            $this->deleteImage($image, $style);
        }
    }

    /**
     * @param ResponsiveImageInterface $image
     * @param string $style
     */
    public function deleteImage(ResponsiveImageInterface $image, $style = '')
    {
        if (!empty($style)) {
            $path = $this->styleManager->getStylePath($image, $style);
        }
        else {
            $path = $image->getPath();
        }
        $this->fileSystem->delete($path);
    }

    /**
     * Creates a directory for storing styled images.
     *
     * @param $destination
     * @throws ImageFilesystemException
     */
    protected function createStyleDirectory($destination)
    {
        $filename  = basename($destination);
        $directory = explode($filename, $destination)[0];

        if (!$this->temporaryFileSystem->has($directory)) {
            try {
                $this->temporaryFileSystem->createDir($directory);
            }
            catch (\Exception $e) {
                throw new ImageFilesystemException($e->getMessage());
            }
        }
    }

    /**
     * Checks is an image file exists in the filesystem.
     *
     * @param $path
     * @return bool
     */
    public function imageExists($path)
    {
        return $this->fileSystem->has($path);
    }

    /**
     * @param ResponsiveImageInterface $image
     * @param $customStyleString
     * @param bool $forceGenerate
     */
    public function createCustomStyledImage(ResponsiveImageInterface $image, $customStyleString, $forceGenerate = false)
    {
        // @TODO: To avoid creating images, that already exist, check if it exists, need a way to disable this checking

        // check is it exists, using the string
        $stylePath = $this->styleManager->getStylePath($image, $customStyleString);

        $exists = $this->imageExists($stylePath);
        if (!$exists || $forceGenerate) {
            // Create the style array and add to the existing styles
            $style = $this->styleManager->styleDataFromCustomStyleString($customStyleString);
            $this->styleManager->addStyle($customStyleString, $style);

            // generate the image
            $this->createStyledImages($image, [$customStyleString]);
        }
    }

    /**
     * @param ResponsiveImageInterface $image
     */
    public function deleteCustomStyledImages(ResponsiveImageInterface $image)
    {
        // @TODO: Implement
    }
}