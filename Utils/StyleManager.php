<?php

namespace ResponsiveImageBundle\Utils;

/**
 * Class StyleManager
 * @package ResponsiveImageBundle\Utils
 */
class StyleManager
{
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
     * @var array
     */
    private $breakpoints = [];

    /**
     * StyleManager constructor.
     * @param \ResponsiveImageBundle\Utils\FileSystem $system
     * @param $parameters
     */
    public function __construct(FileSystem $system, $parameters)
    {
        $this->fileSystem = $system;

        if (!empty($parameters['image_styles'])) {
            $this->styles = $parameters['image_styles'];
        }

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

        $this->breakpoints = $parameters['breakpoints'];
    }

    /**
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
        $webPath = '/' . $stylePath . '/' . $filename;

        $image->setStyle($webPath);

        return $image;
    }

    /**
     * @param ResponsiveImageInterface $image
     * @param $pictureSetName
     * @return ResponsiveImageInterface
     */
    public function generatePictureImage(ResponsiveImageInterface $image, $pictureSetName) {
        $filename = $image->getPath();
        $picture = $this->generatePicture($pictureSetName, $filename);
        $image->setPicture($picture);

        return $image;
    }

    /**
     * @param $pictureSetName
     * @param $filename
     * @return string
     */
    public function generatePicture($pictureSetName, $filename) {
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
                $picture .= '<source srcset="/' . $path . '" media="(' . $this->breakpoints[$break] . ')">';
            }

            $picture .= '<img srcset="/' . $path . '">';
            $picture .= '</picture>';

            return $picture;
        }
        else {
            return FALSE;
        }
    }

    /**
     * @return array
     */
    public function getAllStyles() {
        return $this->styles;
    }

    /**
     * @param $stylename
     * @return bool
     */
    public function getStyle($stylename) {
        if (!in_array($stylename, array_keys($this->styles))) {
            return FALSE;
        }
        else {
            return $this->styles[$stylename];
        }
    }

    /**
     * @param $filename
     */
    public function deleteImageStyledFiles($filename) {
        // For all styles append the style to the filename eg 'stylename/filename.jpg';
        foreach ($this->styles as $styleName => $styleData) {
            $styles_path = $this->fileSystem->getSystemStylesPath();
            $path = $styles_path . '/' . $styleName . '/' . $filename;
            $this->fileSystem->deleteFile($path);
        }
    }

    /**
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
     * @param array $styles
     */
    public function deleteStyledImages(array $styles) {
        foreach ($styles as $style) {
            $system_styles_path = $this->fileSystem->getSystemStylesPath();
            $path = $system_styles_path . '/' . $style;
            $this->fileSystem->deleteDirectory($path);
        }
    }
}