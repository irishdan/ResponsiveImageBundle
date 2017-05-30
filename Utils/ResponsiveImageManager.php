<?php

namespace ResponsiveImageBundle\Utils;

/**
 * Class ResponsiveImageManager
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
     * @var array
     */
    private $images = [];

    /**
     * @var array
     */
    private $s3;

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
    private $uploader;

    /**
     * ImageManager constructor.
     *
     * @param $imager
     * @param $config
     */
    public function __construct($imager, $styleManager, $config, $system, $s3, $uploader)
    {
        $this->imager = $imager;
        $this->styleManager = $styleManager;
        $this->config = $config;
        $this->system = $system;
        $this->s3 = $s3;
        $this->uploader = $uploader;
    }

    /**
     * Cleans out any temp files if needed after image generation.
     */
    private function cleanUp() {
        $s3Enabled = $this->s3enabled();
        if ($s3Enabled) {
            $remote_file_policy = empty($this->config['aws_S3']['remote_file_policy']) ? 'ALL' : $this->config['aws_S3']['remote_file_policy'];
            if ($remote_file_policy == 'ALL') {
                if (!empty($this->images[0])) {
                    unset ($this->images[0]);
                }
            }
            foreach ($this->images as $key => $pathArray) {
                $this->system->deleteFile($pathArray[0]);
            }

        }
    }

    /**
     * Creates styled images for an image object.
     * Handles generation from the controller or the form.
     *
     * @param $imageObject
     * @param $styleName
     * @return mixed
     */
    private function createImageDerivative($imageObject, $styleName = NULL)
    {
        $paths = $this->images;
        $original = $paths[0];
        $filePath = $original[0];
        $crop = empty($imageObject) ? null : $imageObject->getCropCoordinates();

        if (!empty($styleName)) {
            // $paths = [];
            $paths = [$styleName => $paths[$styleName]];
        }
        else {
            unset($paths[0]);
        }

        foreach ($paths as $styleKey => $files) {
            $style = $this->styleManager->getStyle($styleKey);
            $stylePath = $this->system->getStorageDirectory('styled', NULL, $styleKey);
            $image = $this->imager->createImage($filePath, $stylePath, $style, $crop);
        }

        return $image;
    }

    /**
     * Creates all styled images for a given image object.
     * If optional stylename if given only that style will be created.
     *
     * @param ResponsiveImageInterface $image
     * @paran string $stylename
     * @return image
     */
    public function createStyledImages(ResponsiveImageInterface $image, $stylename = NULL)
    {
        $this->setImages($image);
        $image = $this->createImageDerivative($image, $stylename);

        // Do the the transfer if required.
        if ($this->belongsOnS3('styled')) {
            if (!$this->belongsOnS3('original')) {
                if (!empty($this->images[0])) {
                    unset($this->images[0]);
                }
            }
            $this->doS3Transfer();
        }

        // Cleanup any temp files.
        $this->cleanUp();

        return $image;
    }

    /**
     * Deletes images files associated with an image object
     *
     * @param ResponsiveImageInterface $image
     * @param bool
     * @param bool
     */
    public function deleteImageFiles(ResponsiveImageInterface $image, $deleteOriginal = TRUE, $deleteStyled = TRUE) {
        // Create an array of images to work,
        $this->setImages($image, $deleteOriginal, $deleteStyled);

        // Delete the local files.
        foreach ($this->images as $paths) {
            $this->system->deleteFile($paths[0]);
        }

        // Delete S3 files.
        if ($this->belongsOnS3()) {
            $paths = $this->getS3ObjectKeys();
            if (!empty($paths)) {
                $this->s3->setPaths($paths);
                $this->s3->removeFromS3();
            }
        }
    }

    /**
     * Delete an images styled derivatives.
     *
     * @param array $styles
     */
    public function deleteStyleFiles(array $styles)
    {
        // @TODO: at yet implemented.
        if (empty($styles)) {
            // Delete all styled files.
        }
        else {
            // Delete files for the given style.
        }
    }

    /**
     * Transfer files in the $images array to the configured S3 bucket.
     */
    private function doS3Transfer()
    {
        $paths = $this->getS3ObjectKeys();
        if (!empty($paths)) {
            $this->s3->setPaths($paths);
            $this->s3->uploadToS3();
        }

        // Delete temp files.
        $this->cleanUp();
    }

    /**
     * Returns an array of keys and locations for S3 transfers.
     */
    private function getS3ObjectKeys() {
        $keys = [];
        foreach ($this->images as $style => $locations) {
            $keys[$locations[0]] = $locations[1];
        }

        return $keys;
    }

    /**
     * Returns the location fo the original source file and fetches if it's stored remotely.
     *
     * @param $image
     * @return string
     */
    private function findSourceFile($image) {
        $filename = $image->getPath();
        $fetchFromS3 = FALSE;
        if (!empty($this->images[0])) {
            return $this->images[0][0];
        }
        else {
            // The original file is in difference places depending on the local file policy.
            $directory = $this->system->getStorageDirectory('original');
            $path = $directory . $filename;
            
            // Check if the file exists on the server.
            if (!$this->system->fileExists($filename)) {
                $fetchFromS3 =  TRUE;
            }
            $tree = $this->system->getUploadsDirectory() . '/' . $filename;
            // If the policy was set to keep no files locally, then original should be downloaded from s3.
            if (!empty($fetchFromS3)) {
                $s3key = empty($this->config['aws_s3']['directory']) ? $tree :  $this->config['aws_s3']['directory'] . '/' . $tree;
                $this->system->directoryExists($directory , TRUE);
                $this->s3->fetchS3Object($path, $s3key);
            }

            $this->images[0] = [$path, $tree];
        }

        return $path;
    }

    /**
     * @return bool
     */
    private function s3enabled() {
        $enabled = FALSE;
        if (!empty($this->config['aws_s3'])) {
            $enabled = empty($this->config['aws_s3']['enabled']) ? FALSE : TRUE;
        }

        return $enabled;
    }

    /**
     * Checks if files should be transferred to S3 bucket or not.
     *
     * @Param string
     * @return bool
     */
    private function belongsOnS3($imageType = 'styled')
    {
        $enabled = $this->s3enabled();
        if ($enabled) {
            $aws_config = $this->config['aws_s3'];
            $remoteFilePolicy = empty($aws_config['remote_file_policy']) ? 'ALL': $aws_config['remote_file_policy'];

            // If AWS is enabled.
            if ($enabled) {
                // Styled images are always transferred.
                if ($imageType == 'styled') {
                    return TRUE;
                }
                // Originals are only transferred if remote_file_policy is set to ALL.
                else if ($imageType == 'original') {
                    if ($remoteFilePolicy == 'ALL') {
                        return TRUE;
                    }
                }
            }
        }
        return FALSE;
    }

    /**
     * Generates CSS for a background image with media queries.
     *
     * @param ResponsiveImageInterface $image
     * @param $pictureSet
     * @param $selector
     * @return string
     */
    public function createCSS(ResponsiveImageInterface $image, $pictureSet, $selector) {
        return $this->styleManager->createBackgroundImageCSS($image, $pictureSet, $selector);
    }

    /**
     * Builds an array of image paths needed for image creation, deletion and transferring.
     *
     * @param $image
     * @param bool $original
     * @param bool $styled
     */
    private function setImages($image, $original = TRUE, $styled = TRUE) {
        $filename = $image->getPath();
        $styles = $this->styleManager->getAllStyles();

        // This adds the orginal path and style tree to the $images array.
        if ($original) {
            $this->findSourceFile($image);
        }

        // Create an array of paths and styles trees
        if (!empty($filename) && $styled) {
            foreach ($styles as $stylename => $style) {
                $stylePath = $this->system->getStorageDirectory('styled', NULL, $stylename);
                $styleTree = $this->system->getStyleTree($stylename);
                $this->images[$stylename] = [$stylePath . $filename, $styleTree . '/' . $filename];
            }
        }
    }

    /**
     * Sets the image style for image rendering.
     *
     * @param ResponsiveImageInterface $image
     * @param $styleName
     * @return ResponsiveImageInterface
     */
    public function setImageStyle(ResponsiveImageInterface $image, $styleName) {
        $this->styleManager->setImageStyle($image, $styleName);
        return $image;
    }

    /**
     * Sets the picture set for image rendering.
     *
     * @param ResponsiveImageInterface $image
     * @param $pictureSet
     * @return ResponsiveImageInterface
     */
    public function setPictureSet(ResponsiveImageInterface $image, $pictureSet)
    {
        $this->styleManager->setPictureImage($image, $pictureSet);
        return $image;
    }

    /**
     * Uploads an image file
     *
     * @param ResponsiveImageInterface $image
     * @return ResponsiveImageInterface
     */
    public function uploadImage(ResponsiveImageInterface $image)
    {
        $image = $this->uploader->upload($image);

        // Transfer to S3 if needed.
        if ($this->belongsOnS3('original')) {
            $this->setImages($image, TRUE, FALSE);
            $this->doS3Transfer();
        }

        $this->images = array();

        return $image;
    }
}