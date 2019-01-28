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


class StyleManagerTest extends ResponsiveImageTestCase
{
    private $image;
    private $styleManager;

    public function setUp()
    {
        $this->image = new TestImage();
        $this->image->setPath('dummy.jpg');

        $this->styleManager = $this->getService('responsive_image.style_manager');
    }

    public function testSetImageAttributes()
    {
        // Test with cropped styles
        $this->styleManager->setImageAttributes($this->image, 'thumb', 'src/dummy.jpg');

        $this->assertEquals(180, $this->image->getWidth());
        $this->assertEquals(180, $this->image->getHeight());
        $this->assertEquals('src/dummy.jpg', $this->image->getSrc());

        // Test with scaled styled
        $this->styleManager->setImageAttributes($this->image, 'test_scale');

        $this->assertEquals(344, $this->image->getWidth());
        $this->assertEquals(800, $this->image->getHeight());
    }

    public function testGetImagesSizesData()
    {
        $sizesData = $this->styleManager->getImageSizesData($this->image, 'blog_sizes');

        // Check the returned array structure.
        $this->assertArrayHasKey('fallback', $sizesData);
        $this->assertArrayHasKey('sizes', $sizesData);
        $this->assertArrayHasKey('srcsets', $sizesData);

        $this->assertEquals('(min-width: 1100px) 10vw', $sizesData['sizes'][0]);
        $this->assertEquals(180, $sizesData['srcsets']['styles/thumb/dummy.jpg']);
        $this->assertEquals(300, $sizesData['srcsets']['styles/big_thumb/dummy.jpg']);
    }

    public function testGetPictureData()
    {
        $mq = $this->styleManager->getPictureData($this->image, 'thumb_picture');

        // Check the returned array structure.
        $this->assertArrayHasKey('fallback', $mq);
        $this->assertArrayHasKey('sources', $mq);

        // Check the data.
        $this->assertEquals('styles/thumb/dummy.jpg', $mq['fallback']);
        $this->assertArrayHasKey('min-width: 0px', $mq['sources']);
        $this->assertArrayHasKey('min-width: 1100px', $mq['sources']);
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

        // Test a custom styles.
        $style = $this->styleManager->getStyleData('custom_scale_30_100');
        $this->assertArrayHasKey('effect', $style);
        $this->assertArrayHasKey('width', $style);
        $this->assertArrayHasKey('height', $style);
    }
}
