<?php

namespace IrishDan\ResponsiveImageBundle;

/**
 * Class StyleManager
 * This class is responsible for image style information,
 * and translating styles into realatove style paths.
 *
 * @package ResponsiveImageBundle
 */
class StyleManager
{
    private $breakpoints = [];
    private $pictureSets = [];
    private $styles = [];
    private $styleDirectory = 'styles';

    public function __construct(array $configuration)
    {
        // @TODO: Anything in here should only ever return a relative path

        // Set the styles directory;
        if (!empty($configuration['image_styles_directory'])) {
            $this->styleDirectory = $configuration['image_styles_directory'];
        }

        // Set the styles array.
        if (!empty($configuration['image_styles'])) {
            $this->styles = $configuration['image_styles'];
        }

        // Set the picture sets array
        if (!empty($configuration['picture_sets'])) {
            $this->pictureSets = $configuration['picture_sets'];
            // Get the any picture set styles and incorporate into the configured styles array.
            foreach ($configuration['picture_sets'] as $pictureSetName => $picture_set) {
                foreach ($picture_set as $breakpoint => $set_style) {
                    if (is_array($set_style)) {
                        $this->styles[$pictureSetName . '-' . $breakpoint] = $set_style;
                    }
                }
            }
        }

        // Set the breakpoints array.
        if (!empty($configuration['breakpoints'])) {
            $this->breakpoints = $configuration['breakpoints'];
        }
    }

    public function styleExists($styleName)
    {
        $style = $this->getStyle($styleName);

        return !empty($style);
    }

    public function getAllStyles()
    {
        return $this->styles;
    }

    public function getAllStylesNames()
    {
        $styles = $this->getAllStyles();

        return array_keys($styles);
    }

    public function getStyle($stylename)
    {
        if (!in_array($stylename, array_keys($this->styles))) {
            return false;
        } else {
            return $this->styles[$stylename];
        }
    }

    public function getMediaQuerySourceMappings(ResponsiveImageInterface $image, $pictureSetName)
    {
        // @TODO: Is this the best place for this
        $mappings = [];
        $filename = $image->getPath();

        // First mapping is the default image.
        $mappings[] = $image->getPath();

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
        // @TODO: Add some formatting.
        $path = $this->styleDirectory . '/' . $styleName . '/' . $fileName;

        return $path;
    }

    public function getStylePath(ResponsiveImageInterface $image, $styleName = null)
    {
        $filename = $image->getPath();

        if (!empty($styleName)) {
            $stylePath = $this->buildStylePath($styleName, $filename);
        } else {
            $stylePath = $filename;
        }

        return $stylePath;
    }

    public function setImageStyle(ResponsiveImageInterface $image, $styleName = null)
    {
        // @TODO: perhaps should be setSrc

        if ($styleName !== null && empty($this->getStyle($styleName))) {
            return $image;
        }

        $stylePath = $this->getStylePath($image, $styleName);

        $image->setStyle($stylePath);

        return $image;
    }
}