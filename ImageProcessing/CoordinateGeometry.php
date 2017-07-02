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

use Intervention\Image\Size;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class CoordinateGeometry
 *
 * @package IrishDan\ResponsiveImageBundle
 */
class CoordinateGeometry
{
    private $x1;
    private $y1;
    private $x2;
    private $y2;
    private $aspectRatio;
    private $propertyAccessor;

    public function __construct($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0)
    {
        $this->setPoints($x1, $y1, $x2, $y2);
        $this->setAspectRatio();
    }

    public function getAspectRatio()
    {
        return $this->aspectRatio;
    }

    public function scaleSize($scaleX, $scaleY)
    {
        $width  = $this->axisLength('x');
        $height = $this->axisLength('y');

        $size = new Size($width, $height);

        $size->resize(
            $scaleX,
            $scaleY,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        );

        return [
            'width'  => $size->width,
            'height' => $size->height,
        ];
    }

    public function setPoints($x1 = 0, $y1 = 0, $x2 = 100, $y2 = 100)
    {
        $this->x1 = $x1;
        $this->y1 = $y1;
        $this->x2 = $x2;
        $this->y2 = $y2;
    }

    protected function setAspectRatio()
    {
        $this->aspectRatio = $this->aspectRatio();
    }

    public function axisLength($axis = 'x')
    {
        $axis = strtolower($axis);
        if ($axis == 'x') {
            return $this->x2 - $this->x1;
        }
        else {
            return $this->y2 - $this->y1;
        }
    }

    public function aspectRatio($width = null, $length = null)
    {
        $l = empty($width) ? $this->axisLength('x') : $width;
        $w = empty($length) ? $this->axisLength('y') : $length;

        return $w / $l;
    }

    public function compareWithAspectRatios($width, $height)
    {
        $aspectRatio = $this->aspectRatio($width, $height);

        return $this->aspectRatio / $aspectRatio;
    }

    protected function shouldScaleAxis($x, $y)
    {
        $styleAspect = $this->aspectRatio($x, $y);
        if (empty($x) || $styleAspect >= $this->aspectRatio) {
            return 'x';
        }

        if (empty($y) || $styleAspect < $this->aspectRatio) {
            return 'y';
        }

        return 'y';
    }

    public function isInside($x1, $y1, $x2, $y2)
    {
        $inside = true;
        if ($x1 < $this->x1) {
            $inside = false;
        }

        if ($y1 < $this->y1) {
            $inside = false;
        }

        if ($x2 > $this->x2) {
            $inside = false;
        }

        if ($y2 > $this->y2) {
            $inside = false;
        }

        return $inside;
    }

    public function roundAll(array $data)
    {
        if (empty($this->propertyAccessor)) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        foreach ($data as $property => $value) {
            $this->propertyAccessor->setValue($data, $property, round($value));
        }

        return $data;
    }
}