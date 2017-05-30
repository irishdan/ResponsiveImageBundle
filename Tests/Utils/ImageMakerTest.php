<?php

namespace ResponsiveImageBundle\Tests\Utils;

use ResponsiveImageBundle\Tests\ResponsiveImageTestCase;
use ResponsiveImageBundle\Utils\FileSystem;
use ResponsiveImageBundle\Utils\ImageMaker;

class ImageMakerTest extends ResponsiveImageTestCase
{
    private $imager;
    private $coordinates = [
        '280, 396, 3719, 2290:1977, 1311, 2470, 1717',
    ];

    public function setUp()
    {
        // $filesystem = New FileSystem('root_directory', $this->parameters);
        // $this->imager = new ImageMaker($filesystem, $this->parameters);

        // $this->imager->setCoordinateGroups($this->coordinates[0]);
    }

    public function testGetLength()
    {
        // $coords = $this->imager->getCoordinates();

        // $xLength = $this->imager->getLength('x', $coords);
        // $this->assertEquals(3439.0, $xLength);

        // $yLength = $this->imager->getLength('y', $coords);
        // $this->assertEquals(1894.0, $yLength);
    }
}
