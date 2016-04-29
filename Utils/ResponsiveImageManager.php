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
    private $imagePaths = [];

    /**
     * ImageManager constructor.
     * @param $imager
     * @param $config
     */
    public function __construct($imager, $styleManager, $config, $system, $dispatcher)
    {
        $this->imager = $imager;
        $this->styleManager = $styleManager;
        $this->config = $config;
        $this->system = $system;
        $this->dispatcher = $dispatcher;
    }

    /**
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
     *  Transfer files to S3 bucket
     */
    public function transferToS3()
    {
        $commands = array();
        $commands[] = $s3Client->getCommand('PutObject', array(
            'Bucket' => 'SOME_BUCKET',
            'Key' => 'photos/photo01.jpg',
            'Body' => fopen('/tmp/photo01.jpg', 'r'),
        ));
        $commands[] = $s3Client->getCommand('PutObject', array(
            'Bucket' => 'SOME_BUCKET',
            'Key' => 'photos/photo02.jpg',
            'Body' => fopen('/tmp/photo02.jpg', 'r'),
        ));

        // Execute an array of command objects to do them in parallel
        $s3Client->execute($commands);

        // Loop over the commands, which have now all been executed
        foreach ($commands as $command) {
            $result = $command->getResult();
            // Do something with result
        }
    }
}