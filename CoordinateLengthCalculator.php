<?php

namespace IrishDan\ResponsiveImageBundle;


trait CoordinateLengthCalculator
{
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
}