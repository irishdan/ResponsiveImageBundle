<?php

namespace ResponsiveImageBundle\Utils;


use ResponsiveImageBundle\Event\ImageEvent;
use ResponsiveImageBundle\Event\ImageEvents;

/**
 * Class ImageManager
 * @package ResponsiveImageBundle\Utils
 */
class ResponsiveImageManager
{
    /**
     * @var
     */
    private $config;

    /**
     * @var
     */
    private $imager;

    /**
     * @var
     */
    private $styleManager;

    /**
     * @var
     */
    private $system;

    /**
     * @var
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $s3;

    /**
     * ImageManager constructor.
     * @param $imager
     * @param $config
     */
    public function __construct($imager, $styleManager, $config, $system, $dispatcher, $s3)
    {
        $this->imager = $imager;
        $this->styleManager = $styleManager;
        $this->config = $config;
        $this->system = $system;
        $this->dispatcher = $dispatcher;
        $this->s3 = $s3;
    }

    /**
     * Creates a single styled image for an image object.
     *
     * @param $imageObject
     * @param $styleName
     * @param bool $storePath
     * @param bool $tmp
     * @return mixed
     */
    public function createStyledImage($imageObject, $styleName, $storePath = FALSE, $tmp = FALSE)
    {
        $system = $this->system;
        $stylePath = $system->styleDirectoryPath($styleName);
        $originalPath = $system->uploadedFilePath($imageObject->getPath());

        $style = $this->styleManager->getStyle($styleName);

        $crop = empty($imageObject) ? null : $imageObject->getCropCoordinates();
        $image = $this->imager->createImage($originalPath, $stylePath, $style, $crop);

        // @TODO: for what?
        if ($storePath) {
            $this->imagePaths[] = $stylePath;
        }

        // Despatch event to any listeners.
        $event = new ImageEvent($imageObject, $style);
        $this->dispatcher->dispatch(
            ImageEvents::IMAGE_GENERATED,
            $event
        );

        return $image;
    }

    /**
     * Creates all styled images for a given image object.
     *
     * @param ResponsiveImageInterface $image
     */
    public function createAllStyledImages(ResponsiveImageInterface $image)
    {
        $this->imagePaths = [];
        $filename = $image->getPath();
        $styles = $this->styleManager->getAllStyles();
        if (!empty($filename)) {
            foreach ($styles as $stylename => $style) {
                $this->createStyledImage($image, $stylename, TRUE);
            }
        }
    }

    /**
     * @param ResponsiveImageInterface $image
     */
    public function deleteAllStyledImages(ResponsiveImageInterface $image)
    {
        $filename = $image->getPath();
        if (!empty($filename)) {
            $this->styleManager->deleteImageStyledFiles($filename);
        }
    }

    /**
     * Transfer files to S3 bucket
     *
     * @param ResponsiveImageInterface $image
     */
    public function transferToS3(ResponsiveImageInterface $image)
    {
        $file = $image->getPath();
        $paths = $this->styleManager->createPathsArray($file);

        $this->s3->setPaths($paths);
        $this->s3->uploadToS3();
    }

    /**
     * Delete temporary files
     *
     * @param ResponsiveImageInterface $image
     */
    public function removeFiles(ResponsiveImageInterface $image) {
        $file = $image->getPath();
        $paths = $this->styleManager->createPathsArray($file);
        
        $this->s3->setPaths($paths);
        $this->s3->removeFromS3();
    }

    /**
     * Delete temporary files
     *
     * @param ResponsiveImageInterface $image
     */
    public function deleteTempFiles(ResponsiveImageInterface $image = null) {
        
    }
}