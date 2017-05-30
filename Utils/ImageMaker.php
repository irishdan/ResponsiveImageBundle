<?php

namespace ResponsiveImageBundle\Utils;

use Intervention\Image\ImageManager;

/**
 * Class ImageMaker
 *
 * @package ResponsiveImageBundle\Utils
 */
class ImageMaker
{
    /**
     * @var
     */
    private $compression;
    /**
     * @var
     */
    private $cropCoordinates = [];
    /**
     * @var bool
     */
    private $debug = false;
    /**
     * @var array
     */
    private $debugData = [];
    /**
     * @var
     */
    private $driver;
    /**
     * @var FileManager
     */
    private $fileManager;
    /**
     * @var
     */
    private $focusCoordinates = [];
    /**
     * @var
     */
    private $img;
    /**
     * @var
     */
    private $manager;
    /**
     * @var array
     */
    private $styleData = [];

    /**
     * Imager constructor.
     *
     * @param FileManager $system
     * @param array       $responsiveImageConfig
     * @internal param $driver
     * @internal param $compression
     */
    public function __construct(FileManager $system, $responsiveImageConfig = [])
    {
        $this->fileManager = $system;
        if (!empty($responsiveImageConfig['debug'])) {
            $this->debug = $responsiveImageConfig['debug'];
        }
        if (!empty($responsiveImageConfig['image_driver'])) {
            $this->driver = $responsiveImageConfig['image_driver'];
        }
        if (!empty($responsiveImageConfig['image_compression'])) {
            $this->compression = $responsiveImageConfig['image_compression'];
        }
    }

    /**
     *  Adds the data stored in debugData array to the image as text for debugging.
     */
    public function addDebugInfo()
    {
        $y = 10;

        foreach ($this->debugData as $key => $value) {
            $x = 10;

            if (!is_array($value)) {
                $text = $key . ': ' . $value;
                $this->img->text($text, $x, $y);
                $y = $y + 10;
            } else {
                $text = $key;
                $this->img->text($text, $x, $y);
                $y = $y + 10;
                foreach ($value as $key1 => $value1) {
                    if (!is_array($value1)) {
                        $x = 20;
                        $text = $key1 . ': ' . $value1;
                        $this->img->text($text, $x, $y);
                        $y = $y + 10;
                    }
                }
            }
        }
    }

    /**
     * Separates the crop and focus cordinates from the image object and stores them.
     *
     * @param $cropFocusCoords
     */
    public function setCoordinateGroups($cropFocusCoords)
    {
        // x1, y1, x2, y2:x3, y3, x4, y4
        $coordsSets = explode(':', $cropFocusCoords);
        $this->cropCoordinates = explode(', ', $coordsSets[0]);
        $this->focusCoordinates = explode(', ', $coordsSets[1]);
    }

    /**
     * Returns the style information of a defined style.
     *
     * @param array $style
     */
    public function setStyleData($style = [])
    {
        $this->styleData['effect'] = empty($style['effect']) ? null : $style['effect'];
        $this->styleData['width'] = empty($style['width']) ? null : $style['width'];
        $this->styleData['height'] = empty($style['height']) ? null : $style['height'];
        $this->styleData['greyscale'] = empty($style['greyscale']) ? null : $style['greyscale'];
    }

    public function setImg($source, $driver = 'gd')
    {
        if (empty($this->manager)) {
            $this->manager = new ImageManager(['driver' => $driver]);
        }
        $this->img = $this->manager->make($source);
    }

    /**
     * Performs the image manipulation using current style information
     * and user defined crop and focus rectangles.
     *
     * @param       $source
     * @param       $destination
     * @param array $style
     * @param null  $cropFocusCoords
     * @return string
     */
    public function createImage($source, $destination, array $style = [], $cropFocusCoords = null)
    {
        $this->setImg($source, $this->driver);

        // Set style data.
        $this->setStyleData($style);

        // Set Crop and focus Co-ordinates.
        if (!empty($cropFocusCoords)) {
            $this->setCoordinateGroups($cropFocusCoords);
        }

        if (empty($this->getCoordinates('focus')) || $style['effect'] == 'scale') {
            $this->doCropRectangle();
        }

        switch ($this->styleData['effect']) {
            case 'scale':
                // Simply scale the according to style data.
                $this->img->resize($this->styleData['width'], $this->styleData['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });

                break;
            case 'crop':
                // For cropped images the least amount of area should be cropped out.
                // The top and bottom can be cropped, or the left and right sides can be cropped.
                // To determine which type of cropping should be used, the aspect ratios of the image ($outerAspectRatio)
                // and the style ($innerAspectRatio) are compared.
                if (!empty($this->getCoordinates('focus'))) {
                    $doCrop = true;

                    $cropCoords = $this->getCoordinates('crop');
                    $newWidth = $this->getLength('x', $cropCoords);
                    $newHeight = $this->getLength('y', $cropCoords);
                    $styleWidth = $this->styleData['width'];
                    $styleHeight = $this->styleData['height'];

                    // Find out what type of style crop we are dealing with.
                    $outerAspectRatio = $newWidth / $newHeight;
                    $innerAspectRatio = $styleWidth / $styleHeight;

                    if ($outerAspectRatio > $innerAspectRatio) {
                        // Inner height is 100% of the outer.
                        // Width is scaled.
                        $cropHeight = $newHeight;
                        $cropYOffset = $cropCoords[1];

                        $cropWidth = $newHeight * $innerAspectRatio;
                        // The X position should be offset to include the focus rectangle.

                        $cropXOffset = $this->findFocusOffset('x', $cropWidth);
                        // The initial crop was not performed so and that to the offset.
                        $cropXOffset = $cropCoords[0] + $cropXOffset;
                    } else {
                        if ($outerAspectRatio < $innerAspectRatio) {
                            // Inner width is 100% of the outer.
                            // Height is scaled.
                            $cropWidth = $newWidth;
                            $cropXOffset = $cropCoords[0];

                            $cropHeight = $newWidth / $innerAspectRatio;
                            // The Y position should be offset to include the focus rectangle.
                            $cropYOffset = $this->findFocusOffset('y', $cropHeight);
                            // The initial crop was not performed so and that to the offset.
                            $cropYOffset = $cropCoords[1] + $cropYOffset;
                        } else {
                            // Aspect ratios match, do nothing.
                            $doCrop = false;
                        }
                    }

                    if ($doCrop) {
                        $this->img->crop(
                            round($cropWidth),
                            round($cropHeight),
                            round($cropXOffset),
                            round($cropYOffset)
                        );
                    }
                }

                $this->img->fit($this->styleData['width'], $this->styleData['height'], function ($constraint) {
                    $constraint->upsize();
                });
                break;
        }

        // Do greyscale.
        if (!empty($this->styleData['greyscale'])) {
            $this->img->greyscale();
        }

        // Add debug info.
        if (!empty($this->debug) && !empty($this->debugData)) {
            $this->addDebugInfo();
        }

        return $this->saveImage($destination, $source);
    }

    /**
     *  Crops out defined crop area.
     */
    public function doCropRectangle()
    {
        // Get the offset.
        $cropCoords = $this->getCoordinates('crop');
        if (!empty($cropCoords)) {
            $x1 = $cropCoords[0];
            $y1 = $cropCoords[1];

            // Get the lengths.
            $newWidth = $this->getLength('x', $cropCoords);
            $newHeight = $this->getLength('y', $cropCoords);

            // Do the initial crop.
            $this->img->crop($newWidth, $newHeight, $x1, $y1);
        }
    }

    /**
     * Calculates the offset needed to keep focus rectangle in view with opimal position.
     *
     * @param string $axis
     * @param        $cropLength
     * @return mixed
     */
    public function findFocusOffset($axis = 'x', $cropLength)
    {
        $axis = ucfirst($axis);

        // Get the crop information.
        $cropCoords = $this->getCoordinates('crop');
        $imageLength = $this->getLength($axis, $cropCoords);

        // Get the focus information.
        $focusCoords = $this->getCoordinates('focus');
        if (empty($focusCoords)) {
            // There are no focus coords so image should be cropped from center.
            return ($imageLength - $cropLength) / 2;
        }

        $cropX1 = $cropCoords[0];
        $cropY1 = $cropCoords[1];
        $cropX2 = $cropCoords[2];
        $cropY2 = $cropCoords[3];

        $focusX1 = $focusCoords[0];
        $focusY1 = $focusCoords[1];
        $focusX2 = $focusCoords[2];
        $focusY2 = $focusCoords[3];

        // Offsetting on either the x or the y axis.
        // Subtract the crop rectangle.
        $focusNear = ${'focus' . $axis . '1'} - ${'crop' . $axis . '1'};
        $focusFar = ${'focus' . $axis . '2'} - ${'crop' . $axis . '1'};
        $focusLength = $focusFar - $focusNear;
        $focusCenter = round(($focusNear + $focusFar) / 2);

        // Is some cases keeping 100% of the focus rectangle is view is just not possible.
        $focusType = $focusLength >= $cropLength ? 'in' : 'out';
        if ($focusType == 'in') {
            // Its not possible to keep focus rectangle fully in view.
            // In this case center the crop area.
            $offset = $focusCenter - ($cropLength / 2);
        } else {
            // The entire focus rectangle can be preserved.
            $nearGap = $focusNear;
            $farGap = $imageLength - $focusFar;
            $offFactor = $nearGap / $farGap;

            // Find the maximum and minimum offset.
            $maxOffset = $imageLength - $cropLength;
            $minOffset = 0;

            $validOffsets = [];
            for ($i = $minOffset; $i <= $maxOffset; $i++) {
                if ($this->isInBounds($i, $cropLength, $imageLength, $focusNear, $focusFar)) {
                    // Need a factor of near / far to compare to offFactor.
                    // Closest to that wins.
                    $near = $focusNear - $i;
                    $far = ($i + $cropLength) - $focusFar;
                    if ($near != 0 && $far != 0) {
                        $theShizzleFactor = ($near / $far) / $offFactor;
                        $theShizzleFactor = abs($theShizzleFactor);

                        $theTest = abs($theShizzleFactor - 1);
                        $validOffsets[$i] = $theTest;
                    }
                }
            }

            if (!empty($validOffsets)) {
                asort($validOffsets);
                $offsets = array_keys($validOffsets);
                $offset = reset($offsets);
            } else {
                $offset = 0;
            }
        }

        return $offset;
    }

    /**
     * Tests if a given offset is valid.
     * Valid offsets cropped images will include the focus rectangle and will not fall outside of the original image.
     *
     * @param $point
     * @param $cropLength
     * @param $imageLength
     * @param $focusNear
     * @param $focusFar
     * @return bool
     */
    public function isInBounds($point, $cropLength, $imageLength, $focusNear, $focusFar)
    {
        $inBounds = false;
        if ($point + $cropLength <= $imageLength && $point <= $focusNear && $point + $cropLength >= $focusFar) {
            $inBounds = true;
        }

        return $inBounds;
    }

    /**
     * Returns either the crop or focus rectangle coordinates.
     *
     * @param string $type
     * @return mixed
     */
    public function getCoordinates($type = 'crop')
    {
        $coords = $this->{$type . 'Coordinates'};
        $valid = 0;
        foreach ($coords as $id => $coord) {
            if ($coord > 0) {
                $valid++;
            }
            $coords[$id] = round($coord);
        }

        if ($valid == 0) {
            return false;
        }

        return $coords;
    }

    /**
     * Gets vertical or horizontal length between two coordinates (x, y, x2, y2).
     *
     * @param string $type
     * @param array  $coords
     * @return mixed
     */
    public function getLength($type = 'x', array $coords)
    {
        $type = strtolower($type);
        if ($type == 'x') {
            return $coords[2] - $coords[0];
        } else {
            return $coords[3] - $coords[1];
        }
    }

    /**
     * Saves the new image.
     *
     * @param $destination
     * @param $source
     * @return string
     */
    public function saveImage($destination, $source)
    {
        // @TODO: Allow for the destination to be overridden, eg for temporary directory.

        // Check if directory exists and if not create it.
        $this->fileManager->directoryExists($destination, true);

        // Get the file name from source path.
        $filename = $this->fileManager->getFilenameFromPath($source);
        $fullPath = $destination . '/' . $filename;

        $this->img->save($fullPath, $this->compression);

        return $fullPath;
    }
}