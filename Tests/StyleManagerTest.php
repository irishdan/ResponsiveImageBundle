<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Tests;

use IrishDan\ResponsiveImageBundle\Tests\Entity\TestImage;
use IrishDan\ResponsiveImageBundle\File\FileManager;


class StyleManagerTest extends ResponsiveImageTestCase
{
    private $image;
    private $fileManager;
    private $styleManager;

    public function setUp()
    {
        $this->image = new TestImage();
        $this->image->setPath('dummy.jpg');

        $this->fileManager  = $this->getService('responsive_image.file_manager');
        $this->styleManager = $this->getService('responsive_image.style_manager');
    }

    public function testSetImageStyle()
    {
        $this->image = $this->styleManager->setImageStyle($this->image, 'thumb');

        // Assert that the web path is correct
        $expectedPath = '/test/images/styles/thumb/dummy.jpg';
        $this->assertEquals($expectedPath, $this->image->getStyleData());
    }

    public function testGetMediaQuerySourceMappings()
    {
        $this->styleManager->setImageStyle($this->image, 'thumb');
        $mq = $this->styleManager->getPictureData($this->image, 'thumb_picture');

        $this->assertArrayHasKey(0, $mq);
        $this->assertArrayHasKey('min-width: 0px', $mq);
        $this->assertArrayHasKey('min-width: 1100px', $mq);

        $this->assertEquals('/test/images/styles/thumb/dummy.jpg', $mq[0]);
        $this->assertEquals('/test/images/styles/thumb_picture-base/dummy.jpg', $mq['min-width: 0px']);
        $this->assertEquals('/test/images/styles/thumb/dummy.jpg', $mq['min-width: 1100px']);
    }

    public function testGetStyleData()
    {
        // Non existant style returns FALSE.
        $style = $this->styleManager->getStyleData('nonExistingStyle');
        $this->assertFalse($style);

        // Existing array returns array.
        $style = $this->styleManager->getStyleData('thumb');
        $this->assertTrue(is_array($style));

        // Style info.
        $parameters = $this->getParameters('responsive_image');

        $expected = $parameters['image_styles']['thumb'];
        $this->assertEquals($expected, $style);
    }
}
