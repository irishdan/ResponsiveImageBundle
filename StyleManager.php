<?php

namespace IrishDan\ResponsiveImageBundle;

use IrishDan\ResponsiveImageBundle\ImageProcessing\CoordinateGeometry;

/**
 * Class StyleManager
 * This class is responsible for image style information,
 * and translating styles into relative style paths.
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

    public function setImageAttributes(ResponsiveImageInterface $image, $styleName = null, $src = null)
    {
        // Use the style data to figure out the width and height for this image
        // and then set hose attributes on the image.
        if (!empty($styleName)) {
            $styleData = $this->getStyleData($styleName);
            if (!empty($styleData) && !empty($styleData['effect'])) {
                switch ($styleData['effect']) {
                    case 'crop':
                        $image->setWidth($styleData['width']);
                        $image->setHeight($styleData['height']);
                        break;

                    case 'scale':
                        $coordinates = $image->getCropCoordinates();

                        if (empty($coordinates)) {
                            $geometry = new CoordinateGeometry(0, 0, $image->getWidth(), $image->getHeight());
                        }
                        else {
                            $cropCoordinates = explode(':', $coordinates)[0];
                            $points          = explode(',', $cropCoordinates);
                            $geometry        = new CoordinateGeometry(
                                trim($points[0]),
                                trim($points[1]),
                                trim($points[2]),
                                trim($points[3])
                            );
                        }

                        $scaledDimensions = $geometry->scaleSize($styleData['width'], $styleData['height']);

                        $image->setWidth($scaledDimensions['width']);
                        $image->setHeight($scaledDimensions['height']);

                        break;
                }
            }
        }

        // Set the src if value is provided
        if (!empty($src)) {
            $image->setSrc($src);
        }

        return $image;
    }

    public function styleExists($styleName)
    {
        // @TODO: Allow for custom styles. ie styles beginning with custom_scale or custom_scale
        $style = $this->getStyleData($styleName);

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

    public function getStyleData($styleName)
    {
        if (!in_array($styleName, array_keys($this->styles))) {
            // If is custom style string.
            if (strpos('custom', $styleName) == 0) {
                return $this->styleDataFromCustomStyleString($styleName);
            }
        }
        else {
            return $this->styles[$styleName];
        }

        return false;
    }

    public function getMediaQuerySourceMappings(ResponsiveImageInterface $image, $pictureSetName)
    {
        $mappings = [];
        $filename = $image->getPath();

        // First mapping is the default image.
        $mappings[] = $image->getPath();

        if (!empty($this->pictureSets[$pictureSetName])) {
            $set = $this->pictureSets[$pictureSetName];

            foreach ($set as $break => $style) {
                if (is_array($style)) {
                    $styleName = $pictureSetName . '-' . $break;
                }
                else {
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
        $path = $this->styleDirectory . '/' . $styleName . '/' . $fileName;

        return $path;
    }

    public function getStylePath(ResponsiveImageInterface $image, $styleName = null)
    {
        $filename = $image->getPath();

        if (!empty($styleName)) {
            $stylePath = $this->buildStylePath($styleName, $filename);
        }
        else {
            $stylePath = $filename;
        }

        return $stylePath;
    }

    public function addStyle($key, $styleData)
    {
        $this->styles[$key] = $styleData;
    }

    public function styleDataFromCustomStyleString($customStyleString)
    {
        $styleData = explode('_', $customStyleString);

        list($custom, $effect, $width, $height) = $styleData;

        return [
            'effect' => $effect,
            'width'  => $width,
            'height' => $height,
        ];
    }
}