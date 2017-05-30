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
     * Separates the crop and focus coordinates from the image object and stores them.
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

    protected function scaleImage($width, $height)
    {
        $this->img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
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
        $this->setStyleData($style);

        if (!empty($cropFocusCoords)) {
            $this->setCoordinateGroups($cropFocusCoords);
        }

        switch ($this->styleData['effect']) {
            case 'scale':
                // Do the crop rectangle first
                // then scale the image
                $this->doCropRectangle();
                $this->scaleImage($this->styleData['width'], $this->styleData['height']);
                break;

            case 'crop':
                // If there's no focus rectangle,
                // just cut out the crop rectangle.
                if (empty($this->getCoordinates('focus'))) {
                    $this->doCropRectangle();
                }
                // If there is a crop rectangle,
                // We have the image shape (or crop rectangle),
                // and we have the the final image style rectangle.
                // 
                // The image style shape should be as large as possible so,
                // there are three possibilities:
                // 1: The style rectangle fits inside the crop rectangle vertically.
                //    The sides of the image will be cropped.
                // 2: The style rectangle fits inside the crop rectangle horizontally.
                //    The top and bottom of the image will be cropped.
                // 3: The style rectangle fits inside the crop rectangle exactly.
                //    no cropping in required
                //
                // To determine which type of cropping should be used, the aspect-ratio of the image/crop rectangle ($imageAspectRatio)
                // and the aspect-ratio of the style ($styleAspectRatio) are compared.
                // 1: $imageAspectRatio > $styleAspectRatio
                // 2: $imageAspectRatio < $styleAspectRatio
                // 3: $imageAspectRatio === $styleAspectRatio
                else {
                    $doCrop = true;

                    $cropCoords = $this->getCoordinates('crop');
                    $newWidth = $this->getLength('x', $cropCoords);
                    $newHeight = $this->getLength('y', $cropCoords);
                    $styleWidth = $this->styleData['width'];
                    $styleHeight = $this->styleData['height'];

                    // Find out what type of style crop we are dealing with.
                    $imageAspectRatio = $newWidth / $newHeight;
                    $styleAspectRatio = $styleWidth / $styleHeight;

                    if ($imageAspectRatio > $styleAspectRatio) {
                        // Inner height is 100% of the outer.
                        // Width is scaled.
                        $cropHeight = $newHeight;
                        $cropYOffset = $cropCoords[1];
                        $cropWidth = $newHeight * $styleAspectRatio;

                        // The X position should be offset to include the focus rectangle.
                        $cropXOffset = $this->findFocusOffset('x', $cropWidth);

                        // The initial crop was not performed so add that to the offset.
                        $cropXOffset = $cropCoords[0] + $cropXOffset;
                    } else {
                        if ($imageAspectRatio < $styleAspectRatio) {
                            // Inner width is 100% of the outer.
                            // Height is scaled.
                            $cropWidth = $newWidth;
                            $cropXOffset = $cropCoords[0];
                            $cropHeight = $newWidth / $styleAspectRatio;

                            // The Y position should be offset to include the focus rectangle.
                            $cropYOffset = $this->findFocusOffset('y', $cropHeight);

                            // The initial crop was not performed so aff that to the offset.
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

    public function getFocusPointForAccess($axis, $type = 'near')
    {
        $cropCoords = $this->getCoordinates('crop');
        $focusCoords = $this->getCoordinates('focus');

        $cropX1 = $cropCoords[0];
        $cropY1 = $cropCoords[1];
        $cropX2 = $cropCoords[2];
        $cropY2 = $cropCoords[3];

        $focusX1 = $focusCoords[0];
        $focusY1 = $focusCoords[1];
        $focusX2 = $focusCoords[2];
        $focusY2 = $focusCoords[3];

        if ($type == 'near') {
            $point = ${'focus' . $axis . '1'} - ${'crop' . $axis . '1'};
        } else {
            $point = ${'focus' . $axis . '2'} - ${'crop' . $axis . '1'};
        }

        return $point;
    }

    /**
     * Calculates the offset needed to keep focus rectangle in view with optimal position.
     *
     * @param string $axis
     * @param        $cropLength
     * @return mixed
     */
    public function findFocusOffset($axis = 'x', $cropLength)
    {
        $axis = ucfirst($axis);

        // Get the crop and focus information,
        // and the length.
        $cropCoords = $this->getCoordinates('crop');
        $focusCoords = $this->getCoordinates('focus');
        $imageLength = $this->getLength($axis, $cropCoords);

        // If there are no focus coordinates the image should be cropped from center.
        if (empty($focusCoords)) {
            return ($imageLength - $cropLength) / 2;
        }

        // Offsetting on either the x or the y axis.
        // Subtract the crop rectangle.
        $focusNear = $this->getFocusPointForAccess($axis, 'near');
        $focusFar = $this->getFocusPointForAccess($axis, 'far');

        $focusLength = $focusFar - $focusNear;
        $focusCenter = round(($focusNear + $focusFar) / 2);

        // There are two possibilities.
        // 1: The focus area is longer then the desired crop length.
        //    In this case we simple center ont he crop area on the the center of the focus area.
        //    Both sides of the image will be clipped, and both sides of the crop area will be missing a piece.
        // 2: In most cases the focus rectangle will fit nicely within the crop area.
        //    We must find the optimal position to crop from.
        $focusType = $focusLength >= $cropLength ? 'in' : 'out';

        // First case.
        if ($focusType == 'in') {
            return $focusCenter - ($cropLength / 2);
        } // Second case.
        else {
            // We will find a range of valid offsets,
            $validOffsets = $this->getValidOffsets($focusNear, $focusFar, $cropLength, $imageLength);

            // Return the most optimal offset.
            return $this->getOptimalOffset($validOffsets);
        }
    }

    public function getOptimalOffset(array $validOffsets)
    {
        $offset = 0;

        if (!empty($validOffsets)) {
            asort($validOffsets);
            $offsets = array_keys($validOffsets);
            $offset = reset($offsets);
        }

        return $offset;
    }

    public function getValidOffsets($focusNear, $focusFar, $cropLength, $imageLength)
    {
        $nearGap = $focusNear;
        $farGap = $imageLength - $focusFar;
        $offFactor = $nearGap / $farGap;

        // Will need the maximum and minimum offset also.
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
                    $optimalFactor = ($near / $far) / $offFactor;
                    $optimalFactor = abs($optimalFactor);

                    $theTest = abs($optimalFactor - 1);
                    $validOffsets[$i] = $theTest;
                }
            }
        }

        return $validOffsets;
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