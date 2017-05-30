<?php

namespace ResponsiveImageBundle\Tests\Utils;

use ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class ImageMakerTest extends ResponsiveImageTestCase
{
    private $imager;
    private $generatedDirectory = __DIR__ . '/../Resources/generated/';
    private $coordinates = [
        '280, 396, 3719, 2290:1977, 1311, 2470, 1717',
    ];

    public function setUp()
    {
        $this->imager = $this->getService('responsive_image.image_maker');
    }

    public function testGetLength()
    {
        $this->imager->setCoordinateGroups($this->coordinates[0]);

        $coords = $this->imager->getCoordinates();

        $xLength = $this->imager->getLength('x', $coords);
        $this->assertEquals(3439.0, $xLength);

        $yLength = $this->imager->getLength('y', $coords);
        $this->assertEquals(1894.0, $yLength);
    }

    public function testIsInBounds()
    {
        $point = 20;
        $cropLength = 80;
        $imageLength = 100;
        $focusNear = 40;
        $focusFar = 80;

        $valid = $this->imager->isInBounds($point, $cropLength, $imageLength, $focusNear, $focusFar);

        $this->assertTrue($valid);
    }

    public function testSaveImage()
    {
        $testImageSource = __DIR__ . '/../Resources/dummy.jpg';

        $this->imager->setImg($testImageSource);

        $this->imager->saveImage($this->generatedDirectory, $testImageSource);

        // Assert that image has been created.
        $this->assertFileExists($this->generatedDirectory . 'dummy.jpg');
        // @TODO: Assert more about file type etc etc.

        $this->deleteDirectory($this->generatedDirectory);
    }
}
