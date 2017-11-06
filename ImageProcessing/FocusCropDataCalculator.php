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

/**
 * Class FocusCropDataCalculator
 *
 * @package IrishDan\ResponsiveImageBundle\ImageProcessing
 */
class FocusCropDataCalculator
{
    private $cropCoordinates;
    private $focusCoordinates;
    private $styleWidth;
    private $styleHeight;
    private $geometry;

    /**
     * FocusCropDataCalculator constructor.
     *
     * @param $cropCoordinates
     * @param $focusCoordinates
     * @param $styleWidth
     * @param $styleHeight
     */
    public function __construct($cropCoordinates, $focusCoordinates, $styleWidth, $styleHeight)
    {
        $this->cropCoordinates = $cropCoordinates;
        $this->focusCoordinates = $focusCoordinates;
        $this->styleWidth = $styleWidth;
        $this->styleHeight = $styleHeight;
    }

    /**
     * @return array
     */
    public function getFocusCropData()
    {
        // If there is a focus rectangle,
        // We have the image shape (or crop rectangle),
        // and we have the the final image style rectangle.
        //
        // The image style shape should be as large as possible so,
        // there are three possibilities:
        // 1: The style rectangle fits inside the crop rectangle vertically.
        //    The sides of the image will be cropped.
        // 2: The style rectangle fits inside the crop rectangle horizontally.
        //    The top and bottom of the image will be cropped.
        // 3: The style rectangle fits inside the crop rectangle perfectly.
        //    no cropping in required
        //
        // To determine which type of cropping should be used, the aspect-ratio of the image/crop rectangle ($imageAspectRatio)
        // and the aspect-ratio of the style ($styleAspectRatio) are compared.
        // 1: $imageAspectRatio > $styleAspectRatio
        // 2: $imageAspectRatio < $styleAspectRatio
        // 3: $imageAspectRatio === $styleAspectRatio

        list($x1, $y1, $x2, $y2) = $this->cropCoordinates;
        $this->geometry = new CoordinateGeometry($x1, $y1, $x2, $y2);

        $newWidth = $this->geometry->axisLength('x');
        $newHeight = $this->geometry->axisLength('y');

        // Find out what type of style crop we are dealing with.
        // @TODO: Checkout the geometry calculation.
        // $imageAspectRatio = $this->geometry->getAspectRatio();
        $imageAspectRatio = $newWidth / $newHeight;
        $styleAspectRatio = $this->styleWidth / $this->styleHeight;

        if ($imageAspectRatio > $styleAspectRatio) {
            $axis = 'x';
        }
        else if ($imageAspectRatio < $styleAspectRatio) {
            $axis = 'y';
        }
        else {
            return [
                'width'  => $newWidth,
                'height' => $newHeight,
                'x'      => $this->cropCoordinates[0],
                'y'      => $this->cropCoordinates[1],
            ];
        }

        return $this->calculateAxisCropData($axis, $this->cropCoordinates, $styleAspectRatio, $newWidth, $newHeight);
    }

    /**
     * @param string $axis
     * @param        $cropCoordinates
     * @param        $aspectRatio
     * @param        $newWidth
     * @param        $newHeight
     *
     * @return array
     */
    protected function calculateAxisCropData($axis = 'x', $cropCoordinates, $aspectRatio, $newWidth, $newHeight)
    {
        if ($axis !== 'x' && $axis !== 'y') {
            throw new \InvalidArgumentException('$axis can only have a value of x or y. ' . $axis . ' given');
        }

        if ($axis == 'x') {
            $cropHeight = $newHeight;

            // How many times the style height goes into the new height
            $scaleFactor = $newHeight / $this->styleHeight;
            $cropWidth = $this->styleWidth * $scaleFactor;
        }
        else {
            $cropWidth = $newWidth;

            // How many times the style height goes into the new height
            $scaleFactor = $newWidth / $this->styleWidth;
            $cropHeight = $this->styleHeight * $scaleFactor;
        }
        $data['scale_factor'] = $scaleFactor;

        $cropXOffset = ($axis == 'y') ? $cropCoordinates[0] : $this->getFloatingOffset(
            'x',
            $cropWidth,
            $cropCoordinates[0]
        );
        $cropYOffset = ($axis == 'y') ? $this->getFloatingOffset(
            'y',
            $cropHeight,
            $cropCoordinates[1]
        ) : $cropCoordinates[1];

        return [
            'width'  => $cropWidth,
            'height' => $cropHeight,
            'x'      => $cropXOffset,
            'y'      => $cropYOffset,
        ];
    }

    /**
     * @param string $axis
     * @param        $point
     * @param        $start
     *
     * @return mixed
     */
    protected function getFloatingOffset($axis = 'y', $point, $start)
    {
        $offset = $this->findFocusOffset($axis, $point);
        $offset = $offset + $start;

        return $offset;
    }

    /**
     * @param        $axis
     * @param string $type
     *
     * @return mixed
     */
    protected function getFocusPointForAxis($axis, $type = 'near')
    {
        $cropX1 = $this->cropCoordinates[0];
        $cropY1 = $this->cropCoordinates[1];
        $cropX2 = $this->cropCoordinates[2];
        $cropY2 = $this->cropCoordinates[3];

        $focusX1 = $this->focusCoordinates[0];
        $focusY1 = $this->focusCoordinates[1];
        $focusX2 = $this->focusCoordinates[2];
        $focusY2 = $this->focusCoordinates[3];

        if ($type == 'near') {
            $point = ${'focus' . $axis . '1'} - ${'crop' . $axis . '1'};
        }
        else {
            $point = ${'focus' . $axis . '2'} - ${'crop' . $axis . '1'};
        }

        return $point;
    }

    /**
     * Calculates the offset needed to keep focus rectangle in view with optimal position.
     *
     * @param string $axis
     * @param        $cropLength
     *
     * @return mixed
     */
    protected function findFocusOffset($axis = 'x', $cropLength)
    {
        $axis = ucfirst($axis);

        // Get the crop and focus information, sudo iptables -A INPUT -s 142.54.166.218 -j REJECT
        // and the length.
        $imageLength = $this->geometry->axisLength($axis);

        // If there are no focus coordinates the image should be cropped from center.
        if (empty($this->focusCoordinates)) {
            return ($imageLength - $cropLength) / 2;
        }

        // Offsetting on either the x or the y axis.
        // Subtract the crop rectangle.
        $focusNear = $this->getFocusPointForAxis($axis, 'near');
        $focusFar = $this->getFocusPointForAxis($axis, 'far');

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

    /**
     * @param array $validOffsets
     *
     * @return int|mixed
     */
    protected function getOptimalOffset(array $validOffsets)
    {
        $offset = 0;

        if (!empty($validOffsets)) {
            asort($validOffsets);
            $offsets = array_keys($validOffsets);
            $offset = reset($offsets);
        }

        return $offset;
    }

    /**
     * @param $focusNear
     * @param $focusFar
     * @param $cropLength
     * @param $imageLength
     *
     * @return array
     */
    protected function getValidOffsets($focusNear, $focusFar, $cropLength, $imageLength)
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
     *
     * @return bool
     */
    protected function isInBounds($point, $cropLength, $imageLength, $focusNear, $focusFar)
    {
        $inBounds = false;
        if ($point + $cropLength <= $imageLength && $point <= $focusNear && $point + $cropLength >= $focusFar) {
            $inBounds = true;
        }

        return $inBounds;
    }
}