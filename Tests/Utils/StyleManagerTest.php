<?php

namespace ResponsiveImageBundle\Tests\Utils;


use ResponsiveImageBundle\Entity\Image;
use ResponsiveImageBundle\Utils\FileSystem;
use ResponsiveImageBundle\Utils\StyleManager;

class StyleManagerTest extends \PHPUnit_Framework_TestCase
{
    
    use \ResponsiveImageBundle\Tests\Traits\Parameters;
    
    private $image;

    private $fileSystem;

    private $styleManager;

    public function setUp()
    {
        $this->image = new Image();
        $this->image->setPath('dummy.jpg');

        $this->fileSystem = New FileSystem('root_directory', $this->parameters);

        $this->styleManager = New StyleManager($this->fileSystem, $this->parameters);
    }

    public function testSetImageStyle()
    {
        $this->image = $this->styleManager->setImageStyle($this->image , 'thumb');

        // Assert that the web path is correct
        $expectedPath = '/uploads/documents/styles/thumb/dummy.jpg';
        $this->assertEquals($expectedPath, $this->image->getStyle());
    }

    public function testSetPictureImage()
    {
        $this->styleManager->setPictureImage($this->image, 'thumb_picture');
        $picture = $this->image->getPicture();

        $this->assertContains('<picture>', $picture);
        $this->assertContains('</picture>', $picture);
        $this->assertContains('<source srcset="/uploads/documents/styles/thumb/dummy.jpg" media="(min-width: 1100px)">', $picture);
        $this->assertContains('<source srcset="/uploads/documents/styles/thumb_picture-base/dummy.jpg" media="(min-width: 0px)">', $picture);
        $this->assertContains('<img srcset="/uploads/documents/styles/thumb_picture-base/dummy.jpg">', $picture);
    }

    public function testPictureTag()
    {
        $pictureTag = $this->styleManager->pictureTag('thumb_picture', 'dummy.jpg');

        $this->assertContains('<picture>', $pictureTag);
        $this->assertContains('</picture>', $pictureTag);
        $this->assertContains('<source srcset="/uploads/documents/styles/thumb/dummy.jpg" media="(min-width: 1100px)">', $pictureTag);
        $this->assertContains('<source srcset="/uploads/documents/styles/thumb_picture-base/dummy.jpg" media="(min-width: 0px)">', $pictureTag);
        $this->assertContains('<img srcset="/uploads/documents/styles/thumb_picture-base/dummy.jpg">', $pictureTag);
    }

    public function testGetStyle()
    {
        // Non existant style returns FALSE.
        $style = $this->styleManager->getStyle('nonExistingStyle');
        $this->assertFalse($style);

        // Existing array returns array.
        $style = $this->styleManager->getStyle('thumb');
        $this->assertTrue(is_array($style));

        // Style info.
        $expected = $this->parameters['image_styles']['thumb'];
        $this->assertEquals($expected, $style);
    }

    public function prefixProvider() {
        return array(
            array('path', 'style', 'http://prefix.', 'ALL', 'http://prefix.path'),
            array('path', 'style', '', 'ALL', '/path'),
            array('path', 'style', '', 'STYLED_ONLY', '/path'),
            array('path', NULL, 'http://prefix.', 'ALL', 'http://prefix.path'),
            array('path', NULL, 'http://prefix.', 'STYLED_ONLY', '/path'),
        );
    }

    /**
     * @dataProvider prefixProvider
     */
    public function testPrefixPath($path, $style, $prefix, $policy, $expected) {
        $this->styleManager->setDisplayPathPrefix($prefix);
        $this->styleManager->setRemoteFilePolicy($policy);

        $result = $this->styleManager->prefixPath($path, $style);

        $this->assertEquals($expected, $result);
    }
}
