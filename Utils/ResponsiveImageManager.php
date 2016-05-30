<?php

namespace ResponsiveImageBundle\Utils;

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

    public function alterImagesArray() {
        $local_file_policy = $this->config['aws_s3']['local_file_policy'];
        if ($local_file_policy != 'KEEP_NONE') {
            unset($this->images[0]);
        }
    }

    /**
     * Creates a single styled image for an image object.
     *
     * @param $imageObject
     * @param $styleName
     * @return mixed
     */
    public function createImageDerivative($imageObject, $styleName)
    {
        $system = $this->system;
        $filename = $imageObject->getPath();

        // Where's the original file? AWS or local?
        // If AWS fetch the file and store in temp directory if there is one, set $this->sourceFetched.
        $filePath = $this->findSourceFile($imageObject);

        // $stylePath = $system->styleDirectoryPath($styleName);
        $stylePath = $system->getStorageDirectory('styled', NULL, $styleName);
        $style = $this->styleManager->getStyle($styleName);
        $crop = empty($imageObject) ? null : $imageObject->getCropCoordinates();

        $image = $this->imager->createImage($filePath, $stylePath, $style, $crop);

        // Store this here for postprocessing.
        $styleTree = $system->getStyleTree($styleName);
        $this->images[$styleName] = [$stylePath .$filename, $styleTree . '/' . $filename];

        return $image;
    }

    /**
     * Creates all styled images for a given image object.
     * If optional stylename if given only that style will be created.
     *
     * @param ResponsiveImageInterface $image
     * @paran string $stylename
     */
    public function createStyledImages(ResponsiveImageInterface $image, $stylename = NULL)
    {
        $filename = $image->getPath();
        $styles = $this->styleManager->getAllStyles();
        var_dump($this->images);
        if (!empty($filename)) {
            foreach ($styles as $stylename => $style) {
                $this->createImageDerivative($image, $stylename, TRUE);
            }
        }
        var_dump($this->images);
        // @TODO: Here should check if needs to transfer.
        // $this->imageManager->alterImagesArray();
        // $this->imageManager->doS3Transfer();
    }

    /**
     * Deletes all image files associated with an image object
     *
     * @param ResponsiveImageInterface $image
     */
    public function deleteImageFiles(ResponsiveImageInterface $image) {
        // $this->get('responsive_image.style_manager')->deleteImageFile($image->getPath());
    }

    /**
     * Delete an images styled derivatives.
     *
     * @param ResponsiveImageInterface $image
     */
    public function deleteOrginalImage(ResponsiveImageInterface $image)
    {
        // $this->get('responsive_image.style_manager')->deleteImageFile($image->getPath());
    }

    /**
     * Delete an images styled derivatives.
     *
     * @param ResponsiveImageInterface $image
     */
    public function deleteStyledImages(ResponsiveImageInterface $image)
    {
        // $filename = $image->getPath();
        // if (!empty($filename)) {
        //     $this->styleManager->deleteImageStyledFiles($filename);
        // }
    }

    /**
     * Transfer files in the $images array to the configured S3 bucket.
     */
    public function doS3Transfer()
    {
        $paths = [];
        foreach ($this->images as $style => $locations) {
            $paths[$locations[0]] = $locations[1];
        }

        if (!empty($paths)) {
            $this->s3->setPaths($paths);
            $this->s3->uploadToS3();
        }

        // Delete local files
        $local_file_policy = $this->config['aws_s3']['local_file_policy'];
        if ($local_file_policy !== 'KEEP_ALL') {
            foreach ($paths as $path => $tree) {
                $this->system->deleteFile($path);
            }
        }
    }

    /**
     * Returns the location fo the original source file and fetches if it's stored remotely.
     *
     * @param $image
     * @return string
     */
    public function findSourceFile($image) {
        $filename = $image->getPath();
        $fetchFromS3 = FALSE;
        if (!empty($this->images[0])) {
            return $this->images[0][0];
        }
        else {
            // The original file is in difference places depending on the local file policy.
            $directory = $this->system->getStorageDirectory('original');
            $path = $directory . $filename;
            
            // @TODO: This check only checks the uploads directory.
            if (!$this->system->fileExists($filename)) {
                $fetchFromS3 =  TRUE;
            }

            // @TODO: This 'tree' thing is used a lot and would be useful as a function.
            $tree = $this->system->getUploadsDir() . '/' . $filename;

            // If the policy was set to keep no files, the original should be downloaded from s3.
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
     * Delete temporary files.
     *
     * @TODO: Is this needed?
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
     * Sets the image style for image rendering
     *
     * @param ResponsiveImageInterface $image
     * @param $stylename
     * @return ResponsiveImageInterface
     */
    public function setImageStyle(ResponsiveImageInterface $image, $stylename)
    {
        $this->styleManager->setImageStyle($image, $stylename);
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
     * @param ResponsiveImageInterface $image
     */
    public function transferSingleImageToS3(ResponsiveImageInterface $image) {
        $this->findSourceFile($image);
        $this->doS3Transfer();
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
        if (!empty($this->config['aws_s3'])) {
            if (!empty($this->config['aws_s3']['enabled'])) {
                // Check remote file policy to see if should be transferred to s3.
                $remoteFilePolicy = $this->config['aws_s3']['remote_file_policy'];
                if ($remoteFilePolicy != 'STYLED_ONLY') {
                    $this->transferSingleImageToS3($image);
                }

                // Check local file policy to see if it should be deleted.
                $localFilePolicy = $this->config['aws_s3']['local_file_policy'];
                if ($localFilePolicy != 'KEEP_NONE') {
                    // @TODO: Delete the original file
                }
            }
        }
        $this->images = array();

        return $image;
    }
}