<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Tests\ImageProcessing;


use IrishDan\ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class ImageStylerTest extends ResponsiveImageTestCase
{
    private $imager;
    private $testImagePath = __DIR__ . '/../Resources/dummy.jpg';
    private $generatedDirectory = __DIR__ . '/../Resources/generated/';
    private $coordinates = '100, 100, 900, 900:400, 500, 700, 800';

    public function setUp()
    {
        $this->imager = $this->getService('responsive_image.image_styler');
        $this->createDirectory($this->generatedDirectory);
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
            'width'  => 200,
        ];

        $destination = $this->generatedDirectory . 'dummy.jpg';
        $this->imager->createImage($this->testImagePath, $destination, $style);

        $this->assertFileExists($destination);
        $this->assertEquals("image/jpeg", mime_content_type($destination));
    }

    public function testCreateImageWithScale()
    {
        $style = [
            'effect' => 'scale',
            'width'  => 200,
        ];

        $destination = $this->generatedDirectory . 'dummy.jpg';
        $this->imager->createImage($this->testImagePath, $destination, $style);

        $this->assertFileExists($destination);
        $this->assertEquals("image/jpeg", mime_content_type($destination));
        $this->assertTrue(filesize($this->testImagePath) > filesize($destination));
    }

    public function testCreateImageWithCrop()
    {
        $style = [
            'effect' => 'crop',
            'width'  => 200,
            'height' => 200,
        ];

        $destination = $this->generatedDirectory . 'dummy.jpg';
        $this->imager->createImage($this->testImagePath, $destination, $style);

        $this->assertFileExists($destination);
        $this->assertEquals("image/jpeg", mime_content_type($destination));
        $this->assertTrue(filesize($this->testImagePath) > filesize($destination));
    }

    public function testCreateImageWithCoordinates()
    {
        $style = [
            'effect' => 'crop',
            'width'  => 200,
            'height' => 200,
        ];

        $destination = $this->generatedDirectory . 'dummy.jpg';
        $this->imager->createImage($this->testImagePath, $destination, $style, $this->coordinates);

        $this->assertFileExists($destination);
        $this->assertEquals("image/jpeg", mime_content_type($destination));
        $this->assertTrue(filesize($this->testImagePath) > filesize($destination));
    }
}
