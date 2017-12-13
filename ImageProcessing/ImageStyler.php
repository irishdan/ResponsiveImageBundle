<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\ImageProcessing;

use Intervention\Image\ImageManager as Intervention;

/**
 * Class ImageStyler
 *
 * @package ResponsiveImageBundle
 */
class ImageStyler
{
    /**
     * @var
     */
    private $compression = 90;
    /**
     * @var
     */
    private $cropCoordinates = [];
    /**
     * @var
     */
    private $driver = 'gd';
    /**
     * @var
     */
    private $focusCoordinates = [];
    /**
     * @var \Intervention\Image\Image
     */
    private $image;
    /**
     * @var
     */
    private $manager;
    /**
     * @var array
     */
    private $styleData = [];

    /**
     * ImageStyler constructor.
     *
     * @param array $responsiveImageConfig
     */
    public function __construct($responsiveImageConfig = [])
    {
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

    /**
     * @param $width
     * @param $height
     */
    protected function scaleImage($width, $height)
    {
        $this->image->resize(
            $width,
            $height,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        );
    }

    /**
     * @param        $source
     * @param string $driver
     */
    protected function setImage($source, $driver = 'gd')
    {
        if (empty($this->manager)) {
            $this->manager = new Intervention(['driver' => $driver]);
        }
        $this->image = $this->manager->make($source);
    }

    /**
     * Performs the image manipulation using current style information
     * and user defined crop and focus rectangles.
     *
     * @param       $source
     * @param       $destination
     * @param array $style
     * @param null $cropFocusCoords
     *
     * @return string
     */
    public function createImage($source, $destination, array $style = [], $cropFocusCoords = null)
    {
        $this->setImage($source, $this->driver);

        if (!empty($style)) {
            $this->setStyleData($style);
        }

        if (!empty($cropFocusCoords)) {
            $this->setCoordinateGroups($cropFocusCoords);
        }

        if (!empty($this->styleData)) {
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
                    else {

                        $focusOffsetFinder = new FocusCropDataCalculator(
                            $this->getCoordinates('crop'),
                            $this->getCoordinates('focus'),
                            $this->styleData['width'],
                            $this->styleData['height']
                        );

                        $focusCropData = $focusOffsetFinder->getFocusCropData();
                        if (!empty($focusCropData)) {
                            $this->cropImage(
                                $focusCropData['width'],
                                $focusCropData['height'],
                                $focusCropData['x'],
                                $focusCropData['y']
                            );
                        }
                    }

                    $this->image->fit(
                        $this->styleData['width'],
                        $this->styleData['height'],
                        function ($constraint) {
                            $constraint->upsize();
                        }
                    );

                    break;
            }

            // Do greyscale.
            if (!empty($this->styleData['greyscale'])) {
                $this->image->greyscale();
            }
        }

        return $this->saveImage($destination);
    }

    /**
     * @param $width
     * @param $height
     * @param $xOffset
     * @param $yOffset
     */
    protected function cropImage($width, $height, $xOffset, $yOffset)
    {
        try {
            $this->image->crop(
                round($width),
                round($height),
                round($xOffset),
                round($yOffset)
            );
        } catch (Exception $exception) {
            throw new ImageProcessingException($e->getMessage);
        }
    }

    /**
     *  Crops out defined crop area.
     */
    protected function doCropRectangle()
    {
        // Get the offset.
        $cropCoords = $this->getCoordinates('crop');
        if (!empty($cropCoords)) {
            $geometry = new CoordinateGeometry($cropCoords[0], $cropCoords[1], $cropCoords[2], $cropCoords[3]);

            // Get the lengths.
            $newWidth = $geometry->axisLength('x');
            $newHeight = $geometry->axisLength('y');

            // Do the initial crop.
            $this->cropImage($newWidth, $newHeight, $cropCoords[0], $cropCoords[1]);
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function getCoordinates($type = 'crop')
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
     * @param $destination
     *
     * @return mixed
     */
    protected function saveImage($destination)
    {
        $this->image->save($destination, $this->compression);

        return $destination;
    }
}