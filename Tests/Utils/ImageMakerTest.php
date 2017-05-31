<?php

namespace ResponsiveImageBundle\Tests\Utils;

use ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class ImageMakerTest extends ResponsiveImageTestCase
{
    private $imager;
    private $testImagePath = __DIR__ . '/../Resources/dummy.jpg';
    private $generatedDirectory = __DIR__ . '/../Resources/generated/';
    private $coordinates = '100, 100, 900, 900:400, 500, 700, 800';

    public function setUp()
    {
        $this->imager = $this->getService('responsive_image.image_maker');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->deleteDirectory($this->generatedDirectory);
    }

    public function testCreateImage()
    {
        $style = [
            'effect' => 'scale',
            'width' => 200,
        ];

        $this->imager->createImage($this->testImagePath, $this->generatedDirectory, $style);

        $generateImage = $this->generatedDirectory . 'dummy.jpg';

        $this->assertFileExists($generateImage);
        $this->assertEquals("image/jpeg", mime_content_type($generateImage));
    }

    public function testCreateImageWithScale()
    {
        $style = [
            'effect' => 'scale',
            'width' => 200,
        ];

        $this->imager->createImage($this->testImagePath, $this->generatedDirectory, $style);

        $generateImage = $this->generatedDirectory . 'dummy.jpg';

        $this->assertFileExists($generateImage);
        $this->assertEquals("image/jpeg", mime_content_type($generateImage));
        $this->assertTrue(filesize($this->testImagePath) > filesize($generateImage));
    }

    public function testCreateImageWithCrop()
    {
        $style = [
            'effect' => 'crop',
            'width' => 200,
            'height' => 200,
        ];

        $this->imager->createImage($this->testImagePath, $this->generatedDirectory, $style);

        $generateImage = $this->generatedDirectory . 'dummy.jpg';

        $this->assertFileExists($generateImage);
        $this->assertEquals("image/jpeg", mime_content_type($generateImage));
        $this->assertTrue(filesize($this->testImagePath) > filesize($generateImage));
    }

    public function testCreateImageWithCoordinates()
    {
        $style = [
            'effect' => 'crop',
            'width' => 200,
            'height' => 200,
        ];

        $this->imager->createImage($this->testImagePath, $this->generatedDirectory, $style, $this->coordinates);

        $generateImage = $this->generatedDirectory . 'dummy.jpg';

        $this->assertFileExists($generateImage);
        $this->assertEquals("image/jpeg", mime_content_type($generateImage));
        $this->assertTrue(filesize($this->testImagePath) > filesize($generateImage));
    }
}
