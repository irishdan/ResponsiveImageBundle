<?php

namespace ResponsiveImageBundle\Utils;
use Intervention\Image\ImageManager;


/**
 * Class Imager
 * @package ResponsiveImageBundle\Utils
 */
class Imager
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
    private $debug = FALSE;

    /**
     * @var array
     */
    private $debugData = [];

    /**
     * @var
     */
    private $driver;

    /**
     * @var FileSystem
     */
    private $fileSystem;

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
     * @param FileSystem $system
     * @param $driver
     * @param $compression
     */
    public function __construct(FileSystem $system, $responsiveImageConfig = []) {
        $this->fileSystem = $system;
        if (!empty($responsiveImageConfig['debug'])) {
            $this->debug = $responsiveImageConfig['debug'];
        }
        if (!empty($responsiveImageConfig['image_driver'])) {
            $this->driver = $responsiveImageConfig['image_driver'];
        }
        if (!empty($responsiveImageConfig['image_compression'])) {
            $this->compression =  $responsiveImageConfig['image_compression'];
        }
    }

    /**
     *  Adds the data stored in debugData array to the image as text for debugging.
     */
    public function addDebugInfo() {
        $y = 10;

        foreach ($this->debugData as $key => $value) {
            $text = '';
            $x = 10;

            if (!is_array($value)) {
                $text = $key . ': ' . $value;
                $this->img->text($text, $x, $y);
                $y = $y + 10;
            }
            else {
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

    public function setCoordinateGroups($cropFocusCoords) {
        // x1, y1, x2, y2:x3, y3, x4, y4
        $coordsSets = explode(':', $cropFocusCoords);
        $this->cropCoordinates = explode(', ', $coordsSets[0]);
        $this->focusCoordinates = explode(', ', $coordsSets[1]);
    }

    /**
     * Performs the image manipulation for the image using current style information
     * and user defined crop and focus rectangles.
     *
     * @param $source
     * @param $destination
     * @param array $style
     * @return string
     */
    public function createImage($source, $destination, array $style = [], $cropFocusCoords = NULL) {
        $this->manager = new ImageManager(array('driver' => $this->driver));
        $this->img = $this->manager->make($source);

        // Set style data.
        $this->styleData['width'] =  empty($style['width']) ? null : $style['width'];
        $this->styleData['height'] =  empty($style['height']) ? null : $style['height'];

        // Set Crop and focus Co-ordinates.
        if (!empty($cropFocusCoords)) {
            $this->setCoordinateGroups($cropFocusCoords);
        }

        if (empty($this->getCoordinates('focus')) || $style['effect'] == 'scale') {
            $this->doCropRectangle();
        }

        switch($style['effect']) {
            case 'scale':
                // Simply scale the according to style data.
                $this->img->resize($this->styleData['width'], $this->styleData['height'], function($constraint) {
                    $constraint->aspectRatio();
                });

                break;
            case 'crop':
                // For cropped images the least amount of area should be cropped out.
                // The top and bottom can be cropped, or the left and right sides can be cropped.
                // To determine which type of cropping should be used, the aspect ratios of the image ($outerAspectRatio)
                // and the style ($innerAspectRatio) are compared.
                if (!empty($this->getCoordinates('focus'))) {
                    $doCrop = TRUE;

                    $cropCoords = $this->getCoordinates('crop');
                    $newWidth = $this->getLength('x', $cropCoords);
                    $newHeight = $this->getLength('y', $cropCoords);
                    $styleWidth =  $this->styleData['width'];
                    $styleHeight = $this->styleData['height'];

                    // Find out what type of style crop we are dealing with.
                    $outerAspectRatio = $newWidth/$newHeight;
                    $innerAspectRatio = $styleWidth/$styleHeight;

                    if ($outerAspectRatio > $innerAspectRatio) {
                        // Inner height is 100% of the outer.
                        // Width is scaled.
                        $cropHeight = $newHeight;
                        $cropYOffset = $cropCoords[1];

                        $cropWidth = $newHeight * $innerAspectRatio;
                        // The X position should be offset to include the focus rectangle.

                        $cropXOffset = $this->findFocusOffset('x', $cropWidth);
                        // The initial crop was not performed so and that to the offset.
                        $cropXOffset =$cropCoords[0] + $cropXOffset;
                    }
                    else if ($outerAspectRatio < $innerAspectRatio) {
                        // Inner width is 100% of the outer.
                        // Height is scaled.
                        $cropWidth = $newWidth;
                        $cropXOffset = $cropCoords[0];

                        $cropHeight = $newWidth / $innerAspectRatio;
                        // The Y position should be offset to include the focus rectangle.
                        $cropYOffset = $this->findFocusOffset('y', $cropHeight);
                        // The initial crop was not performed so and that to the offset.
                        $cropYOffset = $cropCoords[1] + $cropYOffset;
                    }
                    else {
                        // Aspect ratios match, do nothing.
                        $doCrop = FALSE;
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

                // @TODO: Is it possible to do all of these manipulations at once.
                $this->img->fit($this->styleData['width'], $this->styleData['height'], function ($constraint) {
                    $constraint->upsize();
                });
                break;
        }

        // Add debug info.
        if (!empty($this->debug) && !empty($this->debugData)) {
            $this->addDebugInfo();
        }

        return $this->saveImage($destination, $source);
    }

    /**
     *  Crops out unneeded area.
     */
    public function doCropRectangle() {
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
     * @param $cropLength
     * @return mixed
     */
    public function findFocusOffset($axis = 'x', $cropLength) {
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
        }
        else {
            // The entire focus rectangle can be preserved.
            $nearGap = $focusNear;
            $farGap = $imageLength - $focusFar;
            $offFactor = $nearGap / $farGap;

            // Find the maximum and minimum offset.
            $maxOffset = $imageLength - $cropLength;
            $minOffset = 0;

            $validOffsets = [];
            for ($i = $minOffset; $i <= $maxOffset; $i++) {
                if ($i + $cropLength <= $imageLength &&
                    $i <= $focusNear &&
                    $i + $cropLength >= $focusFar) {
                    // Need a factor of near / far to compare to offFactor.
                    // Closest to that wins.
                    $near =  $focusNear - $i;
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
            }
            else {
                $offset = 0;
            }
        }

        return $offset;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function getCoordinates($type = 'crop') {
        $coords = $this->{$type . 'Coordinates'};
        $valid = 0;
        foreach ($coords as $id => $coord) {
            if ($coord > 0) {
                $valid++;
            }
            $coords[$id] = round($coord);
        }

        if ($valid == 0) {
            return FALSE;
        }

        return $coords;
    }

    /**
     * Get the lengths based on coordinate (x, y, x2, y2)
     *
     * @param string $type
     * @param array $coords
     * @return mixed
     */
    public function getLength($type = 'x', array $coords) {
        $type = strtolower($type);
        if ($type == 'x') {
            return $coords[2] - $coords[0];
        }
        else {
            return $coords[3] - $coords[1];
        }
    }

    public function saveImage($destination, $source) {
        // Check if directory exists and if not create it.
        $this->fileSystem->directoryExists($destination, TRUE);

        // Get the file name from source path.
        $filename = $this->fileSystem->getFilenameFromPath($source);
        $fullPath = $destination . '/' . $filename;

        $this->img->save($fullPath, $this->compression);

        return $fullPath;
    }
}