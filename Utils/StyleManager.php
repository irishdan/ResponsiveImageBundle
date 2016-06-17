<?php

namespace ResponsiveImageBundle\Utils;

/**
 * Class StyleManager
 *
 * @package ResponsiveImageBundle\Utils
 */
/**
 * Class StyleManager
 * @package ResponsiveImageBundle\Utils
 */
class StyleManager
{
    /**
     * @var array
     */
    private $breakpoints = [];

    /**
     * @var
     */
    private $displayPathPrefix = '/';

    /**
     * @var
     */
    private $remoteFilePolicy;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * @var array
     */
    private $pictureSets = [];

    /**
     * @var array
     */
    private $styles = [];

    /**
     * StyleManager constructor.
     *
     * @param \ResponsiveImageBundle\Utils\FileSystem $system
     * @param $parameters
     */
    public function __construct(FileSystem $system, $parameters)
    {
        $this->fileSystem = $system;

        // Set the styles array.
        if (!empty($parameters['image_styles'])) {
            $this->styles = $parameters['image_styles'];
        }

        // Set the picture sets array
        if (!empty($parameters['picture_sets'])) {
            $this->pictureSets = $parameters['picture_sets'];
            // Get the any picture set styles and incorporate into the configured styles array.
            foreach ($parameters['picture_sets'] as $pictureSetName => $picture_set) {
                foreach ($picture_set as $breakpoint => $set_style) {
                    if (is_array($set_style)) {
                        $this->styles[$pictureSetName . '-' . $breakpoint] = $set_style;
                    }
                }
            }
        }

        // Set the breakpoints array.
        if (!empty($parameters['breakpoints'])) {
            $this->breakpoints = $parameters['breakpoints'];
        }

        // Set the prefix.
        if (!empty($parameters['path_prefix'])) {
            $this->displayPathPrefix = $parameters['path_prefix'];
        }

        if (!empty($parameters['aws_s3'])) {
           if (!empty($parameters['aws_s3']['remote_file_policy'])) {
               $this->remoteFilePolicy = $parameters['aws_s3']['remote_file_policy'];
           }
        }
    }

    // public function createPathsArray($filename) {
    //     // @TODO: This is similar to another function.
    //     $styles = $this->getAllStyles();
    //     $systemLocation = $this->fileSystem->uploadedFilePath($filename);
    //     $styledLocation = $this->fileSystem->uploadedFileWebPath($filename);
//
    //     // Add the original file to the array.
    //     $paths = [$systemLocation => $styledLocation];
//
    //     foreach ($styles as $stylename => $style) {
    //         $systemLocation = $this->fileSystem->styleFilePath($stylename, $filename);
    //         $styledLocation = $this->fileSystem->styledFileWebPath($stylename, $filename);
//
    //         $paths[$systemLocation] = $styledLocation;
    //     }
//
    //     return $paths;
    // }

    /**
     * @param $filename
     */
    // public function deleteImageStyledFiles($filename) {
    //     // For all styles append the style to the filename eg 'stylename/filename.jpg';
    //     foreach ($this->styles as $styleName => $styleData) {
    //         $styles_path = $this->fileSystem->getSystemStylesPath();
    //         $path = $styles_path . '/' . $styleName . '/' . $filename;
    //         $this->fileSystem->deleteFile($path);
    //     }
    // }

    /**
     * Deletes a file.
     *
     * @param $filename
     */
    public function deleteImageFile($filename) {
        $system_upload_path = $this->fileSystem->getSystemUploadPath();
        $path = $system_upload_path . '/' . $filename;
        // Delete the source file.
        $this->fileSystem->deleteFile($path);
        // Delete the styled files.
        $this->deleteImageStyledFiles($filename);
    }

    /**
     * Deletes all of the files in an image style folder.
     *
     * @param array $styles
     */
    public function deleteStyledImages(array $styles) {
        foreach ($styles as $style) {
            $system_styles_path = $this->fileSystem->getSystemStylesPath();
            $path = $system_styles_path . '/' . $style;
            $this->fileSystem->deleteDirectory($path);
        }
    }

    /**
     * Checks if a given style name is a defined style.
     *
     * @param $styleName
     *
     * @return bool
     */
    public function styleExists($styleName) {
        $style = $this->getStyle($styleName);

        return !empty($style);
    }

    /**
     * Sets the pictureTag property of a image object.
     *
     * @param ResponsiveImageInterface $image
     * @param $pictureSetName
     * @return ResponsiveImageInterface
     */
    public function setPictureImage(ResponsiveImageInterface $image, $pictureSetName) {
        $filename = $image->getPath();
        $picture = $this->pictureTag($pictureSetName, $filename);
        $image->setPicture($picture);

        return $image;
    }

    /**
     * @param mixed $displayPathPrefix
     */
    public function setDisplayPathPrefix($displayPathPrefix)
    {
        $this->displayPathPrefix = $displayPathPrefix;
    }

    /**
     * @param mixed $remoteFilePolicy
     */
    public function setRemoteFilePolicy($remoteFilePolicy)
    {
        $this->remoteFilePolicy = $remoteFilePolicy;
    }

    /**
     * @return array
     */
    public function getAllStyles() {
        return $this->styles;
    }

    /**
     * Returns a style information array.
     *
     * @param $stylename
     * @return bool
     */
    public function getStyle($stylename) {
        if (!in_array($stylename, array_keys($this->styles))) {
            return FALSE;
        }
        else {
            // var_dump($stylename);
            // var_dump($this->styles[$stylename]);
            return $this->styles[$stylename];
        }
    }

    /**
     * Prefixes url string with the displayPathPrefix string, if the style and the config require it.
     *
     * @param $url
     * @param $style
     * @return string
     */
    public function prefixPath($url , $style = NULL) {
        // Remote fle policy values ALL, STYLED_ONLY.
        if (!empty($this->displayPathPrefix) && $style !== NULL) {
            $url = $this->displayPathPrefix . $url;
        }
        else if ($this->remoteFilePolicy != 'STYLED_ONLY' && $style == NULL) {
            $url = $this->displayPathPrefix . $url;
        }
        else if ($this->remoteFilePolicy == 'STYLED_ONLY' && $style == NULL) {
            $url = '/' . $url;
        }
        else {
            $url = '/' . $url;
        }
        
        return $url;
    }

    /**
     * Generates a picture tag for a given picture set and filename.
     *
     * @param $pictureSetName
     * @param $filename
     * @return string
     */
    public function pictureTag($pictureSetName, $filename) {
        if (!empty($this->pictureSets[$pictureSetName])) {
            $set = $this->pictureSets[$pictureSetName];

            $picture = '<picture>';
            foreach (array_reverse($set) as $break => $style) {
                if (is_array($style)) {
                    $stylename = $pictureSetName . '-' . $break;
                } else {
                    $stylename = $style;
                }
                $styles_directory = $this->fileSystem->getStylesDir();
                $path = $styles_directory . '/' . $stylename . '/' . $filename;
                $path = $this->prefixPath($path, $stylename);

                $picture .= '<source srcset="' . $path . '" media="(' . $this->breakpoints[$break] . ')">';
            }

            $picture .= '<img srcset="' . $path . '">';
            $picture .= '</picture>';

            return $picture;
        }
        else {
            return FALSE;
        }
    }

    /**
     * Sets the path of a an Image object to the full styled image path.
     *
     * @param ResponsiveImageInterface $image
     * @param null $styleName
     * @return ResponsiveImageInterface
     */
    public function setImageStyle(ResponsiveImageInterface $image, $styleName = null) {
        $filename = $image->getPath();
        if (!empty($styleName)) {
            $stylePath = $this->fileSystem->styleWebPath($styleName);
        }
        else {
            $stylePath = $this->fileSystem->getUploadsDir();
        }
        $webPath = $stylePath . '/' . $filename;
        $webPath = $this->prefixPath($webPath, $styleName);
        $image->setStyle($webPath);

        return $image;
    }
}