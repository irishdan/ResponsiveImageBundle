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
    private $sizeSets = [];
    private $styles = [];
    private $styleDirectory = 'styles';

    /**
     * StyleManager constructor.
     *
     * @param array $configuration
     */
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

        // Set the picture sets array.
        if (!empty($configuration['picture_sets'])) {
            $this->pictureSets = $configuration['picture_sets'];
        }

        // Set the size sets array.
        if (!empty($configuration['size_sets'])) {
            $this->sizeSets = $configuration['size_sets'];
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
                        $scaledDimensions = $this->getScaledDimensions($image, $styleData);

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

    protected function getScaledDimensions(ResponsiveImageInterface $image, array $styleData)
    {
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

        return $geometry->scaleSize($styleData['width'], $styleData['height']);
    }

    public function styleExists($styleName)
    {
        // If its's a custom style, grab the data and add the styles array.
        if (0 === strpos($styleName, 'custom_')) {
            $styleData = $this->styleDataFromCustomStyleString($styleName);
            $this->addStyle($styleName, $styleData);
        }

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
            if (strpos($styleName, 'custom_') === 0) {
                return $this->styleDataFromCustomStyleString($styleName);
            }
        }
        else {
            return $this->styles[$styleName];
        }

        return false;
    }

    public function getPictureData(ResponsiveImageInterface $image, $pictureSetName)
    {
        $mappings = [
            'fallback' => '',
            'sources'  => [],
        ];
        $filename = $image->getPath();

        if (!empty($this->pictureSets[$pictureSetName])) {
            $set = $this->pictureSets[$pictureSetName];

            foreach ($set['sources'] as $break => $styleName) {
                $paths   = [];
                $paths[] = $this->buildStylePath($styleName, $filename);

                // Check to for multiplier styles
                $multiplierStyles = $this->findMultiplierStyles($styleName);
                if (!empty($multiplierStyles)) {
                    foreach ($multiplierStyles as $multiplier => $style) {
                        $paths[] = $this->buildStylePath($style, $filename) . ' ' . $multiplier;
                    }
                }

                // Mappings should be in 'media_query' => '/path/to/image'
                if ($this->breakpoints[$break]) {
                    $mediaQuery                       = $this->breakpoints[$break]['media_query'];
                    $mappings['sources'][$mediaQuery] = implode(', ', $paths);
                }
            }

            // Set the fallback image path.
            if (isset($set['fallback'])) {
                $mappings['fallback'] = $this->getStylePath($image, $set['fallback']);
            }
        }

        return $mappings;
    }

    protected function findMultiplierStyles($styleName)
    {
        $multiplierStyles = [];
        foreach ($this->styles as $style => $styleData) {
            // ^thumb_[0-9]+([.][0-9])?x$
            $regex = '/^' . $styleName . '_([0-9]+([.][0-9])?x$)/';
            preg_match($regex, $style, $matches);

            if ($matches) {
                $multiplierStyles[$matches[1]] = $style;
            }
        }

        return $multiplierStyles;
    }

    public function getImageSizesData(ResponsiveImageInterface $image, $imageSizesSetName)
    {
        $mappings = [
            'fallback' => '',
            'sizes'    => [],
            'srcsets'  => [],
        ];
        $sizeData = $this->getSizesSet($imageSizesSetName);

        if ($sizeData) {
            // Sort out the sizes data.
            $mappings['sizes'] = [];
            foreach ($sizeData['sizes'] as $vw => $mediaQuery) {
                // Get the media query from the breakpoint data.
                $breakpoint = $this->breakpoints[$mediaQuery['breakpoint']];

                if ($breakpoint) {
                    // $mappings['sizes'][$vw] = $breakpoint['media_query'];
                    $mappings['sizes'][] = '(' . $breakpoint['media_query'] . ') ' . $vw;
                }
            }

            // Get the image paths and widths.
            // In most case the width will be apart of the style (crop or scale)
            // If it's not we can need to derive it.
            foreach ($sizeData['srcsets'] as $styleName) {
                $styleData = $this->getStyleData($styleName);
                if ($styleData) {
                    if (empty($styleData['width'])) {
                        // We need to derive the width.
                        $scaledDimensions = $this->getScaledDimensions($image, $styleData);
                        $width            = $scaledDimensions['width'];
                    }
                    else {
                        $width = $styleData['width'];
                    }

                    $path = $this->getStylePath($image, $styleName);
                    // Stick it into that array there.
                    $mappings['srcsets'][$path] = $width;
                }
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

    public function getSizesSet($setName)
    {
        return isset($this->sizeSets[$setName]) ? $this->sizeSets[$setName] : false;
    }
}
