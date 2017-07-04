<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Tests\File;

use IrishDan\ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class FileValidatorTest extends ResponsiveImageTestCase
{
    private $image;
    private $fileManager;
    private $styleManager;

    public function setUp()
    {
        // $this->image = new TestImage();
        // $this->image->setPath('dummy.jpg');
        // $this->fileManager  = $this->getService('responsive_image.file_manager');
        // $this->styleManager = $this->getService('responsive_image.style_manager');
    }

    public function testValidate()
    {
    }
}
