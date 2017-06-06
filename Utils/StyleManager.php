<?php

namespace IrishDan\ResponsiveImageBundle\Utils;

/**
 * Class StyleManager
 *
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
     * @var FileManager
     */
    private $fileManager;
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
     * @param \ResponsiveImageBundle\Utils\FileManager $system
     * @param array                                    $parameters
     */
    public function __construct(FileManager $system, array $parameters)
    {
        $this->fileManager = $system;

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

    /**
     * Deletes a file.
     *
     * @param $filename
     */
    public function deleteImageFile($filename)
    {
        $system_upload_path = $this->fileManager->getSystemUploadPath();
        $path = $system_upload_path . '/' . $filename;
        // Delete the source file.
        $this->fileManager->deleteFile($path);
        // Delete the styled files.
        $this->deleteImageStyledFiles($filename);
    }

    /**
     * Deletes all of the files in an image style folder.
     *
     * @param array $styles
     */
    public function deleteStyledImages(array $styles)
    {
        foreach ($styles as $style) {
            $system_styles_path = $this->fileManager->getSystemStylesPath();
            $path = $system_styles_path . '/' . $style;
            $this->fileManager->deleteDirectory($path);
        }
    }

    /**
     * Checks if a given style name is a defined style.
     *
     * @param $styleName
     * @return bool
     */
    public function styleExists($styleName)
    {
        $style = $this->getStyle($styleName);

        return !empty($style);
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
    public function getAllStyles()
    {
        return $this->styles;
    }

    /**
     * Returns a style information array.
     *
     * @param $stylename
     * @return bool
     */
    public function getStyle($stylename)
    {
        if (!in_array($stylename, array_keys($this->styles))) {
            return false;
        } else {
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
    protected function prefixPath($url, $style = null)
    {
        // Remote fle policy values ALL, STYLED_ONLY.
        if (!empty($this->displayPathPrefix) && $style !== null) {
            $url = $this->displayPathPrefix . $url;
        } else {
            if ($this->remoteFilePolicy != 'STYLED_ONLY' && $style == null) {
                $url = $this->displayPathPrefix . $url;
            } else {
                if ($this->remoteFilePolicy == 'STYLED_ONLY' && $style == null) {
                    $url = '/' . $url;
                } else {
                    $url = '/' . $url;
                }
            }
        }

        return $url;
    }

    public function getMediaQuerySourceMappings(ResponsiveImageInterface $image, $pictureSetName)
    {
        $mappings = [];
        $filename = $image->getPath();

        // First mapping is the default image.
        $mappings[] = $image->getStyle();

        if (!empty($this->pictureSets[$pictureSetName])) {
            $set = $this->pictureSets[$pictureSetName];

            foreach ($set as $break => $style) {
                if (is_array($style)) {
                    $styleName = $pictureSetName . '-' . $break;
                } else {
                    $styleName = $style;
                }
                $path = $this->buildStylePath($styleName, $filename);

                $mappings[$this->breakpoints[$break]] = $path;
            }
        }

        return $mappings;
    }

    protected function buildStylePath($styleName, $fileName)
    {
        $styles_directory = $this->fileManager->getStylesDirectory();
        $path = $styles_directory . '/' . $styleName . '/' . $fileName;
        $path = $this->prefixPath($path, $styleName);

        return $path;
    }

    /**
     * Sets the path of a an Image object to the full styled image path.
     *
     * @param ResponsiveImageInterface $image
     * @param null                     $styleName
     * @return ResponsiveImageInterface
     */
    public function setImageStyle(ResponsiveImageInterface $image, $styleName = null)
    {
        // @TODO: Hack to avoid looping over itself and string paths together.
        if ($styleName !== null && !in_array($styleName, $this->styles)) {
            return $image;
        }

        $filename = $image->getPath();

        if (!empty($styleName)) {
            $stylePath = $this->fileManager->styleWebPath($styleName);
        } else {
            $stylePath = $this->fileManager->getUploadsDirectory();
        }

        $webPath = $stylePath . '/' . $filename;
        $webPath = $this->prefixPath($webPath, $styleName);

        $image->setStyle($webPath);

        return $image;
    }
}