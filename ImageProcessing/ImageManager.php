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
class ImageManager {

    protected $styleManager;

    protected $imageStyler;

    protected $fileSystem;

    protected $temporaryFileSystem;

    protected $eventDispatcher;

    protected $generatedImages = [];

    protected $temporaryFileCreated = false;

    /**
     * ImageManager constructor.
     *
     * @param StyleManager                  $styleManager
     * @param ImageStyler                   $imageStyler
     * @param PrimaryFileSystemWrapper      $fileSystem
     * @param FilesystemInterface|null      $temporaryFileSystem
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(
        StyleManager $styleManager,
        ImageStyler $imageStyler,
        PrimaryFileSystemWrapper $fileSystem,
        FilesystemInterface $temporaryFileSystem = NULL,
        EventDispatcherInterface $eventDispatcher = NULL
    ) {
        $this->styleManager        = $styleManager;
        $this->imageStyler         = $imageStyler;
        $this->fileSystem          = $fileSystem->getFileSystem();
        $this->temporaryFileSystem = $temporaryFileSystem;
        $this->eventDispatcher     = $eventDispatcher;
    }
    
    public function createAllStyledImages(ResponsiveImageInterface $image) {
        $styles = $this->styleManager->getAllStylesNames();

        return $this->createStyledImages($image, $styles);
    }

    protected function createTemporaryFile(ResponsiveImageInterface $image) {
        // Check if it exists and create if not..
        if (!$this->temporaryFileSystem->has($image->getPath())) {
            $contents = $this->fileSystem->read($image->getPath());
            $this->temporaryFileSystem->put($image->getPath(), $contents);
        }
    }

    protected function getTemporaryFileMimeType(ResponsiveImageInterface $image) {
        return $this->temporaryFileSystem->getMimetype($image->getPath());
    }
    
    public function createStyledImages(ResponsiveImageInterface $image, array $styles = []) {
        // Copy the image from its current filesystem onto the filesystem used by intervention.
        $this->createTemporaryFile($image);

        // Generate all of the required files
        foreach ($styles as $style) {
            $this->createStyledImage($image, $style);
        }

        // Dispatch an event.
        if (!empty($this->eventDispatcher)) {
            $imagesGeneratedEvent = new StyledImagesEvent($image, $this->generatedImages);
            $this->eventDispatcher->dispatch(StyledImagesEvents::STYLED_IMAGES_GENERATED, $imagesGeneratedEvent);
        }

        return $this->generatedImages;
    }

    public function createStyledImage(ResponsiveImageInterface $image, $style, $useCropfocus = TRUE, $stream = FALSE) {
        $this->createTemporaryFile($image);

        // If style is a string it is treated as a style name and the style data is 
        // retrieved from config.
        // Otherwise its an array of style data
        if (is_array($style)) {
            $styleData = $style;
        }
        else {
            $styleData = $this->styleManager->getStyleData($style);
        }

        $cropFocusData = $useCropfocus ? $image->getCropCoordinates() : FALSE;
        $directory     = $this->temporaryFileSystem->getAdapter()->getPathPrefix();
        $source        = $directory . $image->getPath();

        // If stream is not set then save images normally
        if (!$stream) {
            try {
                if (!empty($styleData)) {
                    $relativeStylePath = $this->styleManager->getStylePath($image, $style);
                    $destination       = $directory . $relativeStylePath;
                    $this->createStyleDirectory($relativeStylePath);

                    $this->imageStyler->createImage($source, $destination, $styleData, $cropFocusData, $this->getTemporaryFileMimeType($image));
                    $this->generatedImages[$style] = $relativeStylePath;

                    return $relativeStylePath;
                }
            } catch (\Exception $e) {
                // @TODO: Custom Exception..
            }
        }
        // Create an image stream
        // No images are saved..
        else {
            // Using falsy $destination returns a stream.
            return $this->imageStyler->createImage($source, null, $styleData, $cropFocusData, $this->getTemporaryFileMimeType($image));
        }
    }

    /**
     * @param ResponsiveImageInterface $image
     */
    public function deleteAllImages(ResponsiveImageInterface $image) {
        $this->deleteImage($image);
        $this->deleteStyledImages($image);
    }

    /**
     * @param ResponsiveImageInterface $image
     * @param array                    $styles
     */
    public function deleteStyledImages(ResponsiveImageInterface $image, array $styles = []) {
        if (empty($styles)) {
            $styles = $this->styleManager->getAllStylesNames();
        }

        foreach ($styles as $style) {
            $this->deleteImage($image, $style);
        }
    }

    public function deleteImage(ResponsiveImageInterface $image, $style = '') {
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
     *
     * @throws ImageFilesystemException
     */
    protected function createStyleDirectory($destination) {
        $filename  = basename($destination);
        $directory = explode($filename, $destination)[0];

        if (!$this->temporaryFileSystem->has($directory)) {
            try {
                $this->temporaryFileSystem->createDir($directory);
            } catch (\Exception $e) {
                throw new ImageFilesystemException($e->getMessage());
            }
        }
    }

    /**
     * Checks is an image file exists in the filesystem.
     *
     * @param $path
     *
     * @return bool
     */
    public function imageExists($path) {
        return $this->fileSystem->has($path);
    }

    /**
     * @param ResponsiveImageInterface $image
     * @param                          $customStyleString
     * @param bool                     $forceGenerate
     */
    public function createCustomStyledImage(ResponsiveImageInterface $image, $customStyleString, $forceGenerate = FALSE) {
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
    public function deleteCustomStyledImages(ResponsiveImageInterface $image) {
        // @TODO: Implement
    }
}
