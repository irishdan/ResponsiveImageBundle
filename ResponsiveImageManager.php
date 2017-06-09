<?php

namespace IrishDan\ResponsiveImageBundle;

/**
 * Class ResponsiveImageManager
 *
 * @package ResponsiveImageBundle
 */
class ResponsiveImageManager
{
    /**
     * @var
     */
    private $config;
    /**
     * @var array
     */
    private $images = [];
    /**
     * @var
     */
    private $styleManager;
    /**
     * @var
     */
    private $uploader;

    public function __construct(StyleManager $styleManager, array $config, $uploader)
    {
        $this->styleManager = $styleManager;
        $this->config = $config;
        $this->uploader = $uploader;
    }

    /**
     * Cleans out any temp files if needed after image generation.
     */
    // private function cleanUp()
    // {
    //     $s3Enabled = $this->s3enabled();
    //     if ($s3Enabled) {
    //         $remote_file_policy = empty($this->config['aws_S3']['remote_file_policy']) ? 'ALL' : $this->config['aws_S3']['remote_file_policy'];
    //         if ($remote_file_policy == 'ALL') {
    //             if (!empty($this->images[0])) {
    //                 unset ($this->images[0]);
    //             }
    //         }
    //         foreach ($this->images as $key => $pathArray) {
    //             $this->system->deleteFile($pathArray[0]);
    //         }
    //     }
    // }

    /**
     * Creates styled images for an image object.
     * Handles generation from the controller or the form.
     *
     * @param $imageObject
     * @param $styleName
     * @return mixed
     */
    private function createImageDerivative($imageObject, $styleName = null)
    {
        $this->styleManager->createStyledImage($imageObject, $styleName);

        // $paths = $this->images;
        // $original = $paths[0];
        // $filePath = $original[0];
        // $crop = empty($imageObject) ? null : $imageObject->getCropCoordinates();
//
        // if (!empty($styleName)) {
        //     // $paths = [];
        //     $paths = [$styleName => $paths[$styleName]];
        // } else {
        //     unset($paths[0]);
        // }
//
        // foreach ($paths as $styleKey => $files) {
        //     $style = $this->styleManager->getStyle($styleKey);
        //     $stylePath = $this->system->getStorageDirectory('styled', null, $styleKey);
        //     $image = $this->imager->createImage($filePath, $stylePath, $style, $crop);
        // }
//
        // return $image;
    }

    /**
     * Creates all styled images for a given image object.
     * If optional stylename if given only that style will be created.
     *
     * @param ResponsiveImageInterface $image
     * @paran string $stylename
     * @return image
     */
    public function createStyledImages(ResponsiveImageInterface $image, $stylename = null)
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
     * @param                          bool
     * @param                          bool
     */
    public function deleteImageFiles(ResponsiveImageInterface $image, $deleteOriginal = true, $deleteStyled = true)
    {
        // Create an array of images to work,
        // $this->setImages($image, $deleteOriginal, $deleteStyled);
//
        // // Delete the local files.
        // foreach ($this->images as $paths) {
        //     $this->system->deleteFile($paths[0]);
        // }
//
        // // Delete S3 files.
        // if ($this->belongsOnS3()) {
        //     $paths = $this->getS3ObjectKeys();
        //     if (!empty($paths)) {
        //         $this->s3->setPaths($paths);
        //         $this->s3->removeFromS3();
        //     }
        // }
    }

    /**
     * Delete an images styled derivatives.
     *
     * @param array $styles
     */
    public function deleteStyleFiles(array $styles)
    {
        // @TODO: at yet implemented.
    }

    /**
     * Generates CSS for a background image with media queries.
     *
     * @param ResponsiveImageInterface $image
     * @param                          $pictureSet
     * @param                          $selector
     * @return string
     */
    public function createCSS(ResponsiveImageInterface $image, $pictureSet, $selector)
    {
        return $this->styleManager->createBackgroundImageCSS($image, $pictureSet, $selector);
    }

    /**
     * Builds an array of image paths needed for image creation, deletion and transferring.
     *
     * @param      $image
     * @param bool $original
     * @param bool $styled
     */
    private function setImages($image, $original = true, $styled = true)
    {
        // $filename = $image->getPath();
        // $styles = $this->styleManager->getAllStyles();
//
        // // This adds the orginal path and style tree to the $images array.
        // if ($original) {
        //     $this->findSourceFile($image);
        // }
//
        // // Create an array of paths and styles trees
        // if (!empty($filename) && $styled) {
        //     foreach ($styles as $stylename => $style) {
        //         $stylePath = $this->system->getStorageDirectory('styled', null, $stylename);
        //         $styleTree = $this->system->getStyleTree($stylename);
        //         $this->images[$stylename] = [$stylePath . $filename, $styleTree . '/' . $filename];
        //     }
        // }
    }

    /**
     * Sets the image style for image rendering.
     *
     * @param ResponsiveImageInterface $image
     * @param                          $styleName
     * @return ResponsiveImageInterface
     */
    public function setImageStyle(ResponsiveImageInterface $image, $styleName)
    {
        $this->styleManager->setImageStyle($image, $styleName);

        return $image;
    }

    /**
     * Sets the picture set for image rendering.
     *
     * @param ResponsiveImageInterface $image
     * @param                          $pictureSet
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

        return $image;
    }
}