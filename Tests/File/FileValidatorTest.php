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

use IrishDan\ResponsiveImageBundle\File\FileValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected function getMockFileUpload()
    {
        // Mock the repository so it returns the mock of the Image repository.
        return $this->getMockBuilder(UploadedFile::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    public function testValid()
    {
        $uploadedFile = $this->getMockFileUpload();
        $uploadedFile->expects($this->once())
                     ->method('getClientOriginalExtension')
                     ->will(
                         $this->returnValue('jpg')
                     );
        $uploadedFile->expects($this->once())
                     ->method('guessExtension')
                     ->will(
                         $this->returnValue('jpg')
                     );

        // Test the validator.
        $validator = new FileValidator();

        $valid = $validator->validate($uploadedFile);

        $this->assertTrue($valid);
    }

    public function testUnsupportedFileType()
    {
        $uploadedFile = $this->getMockFileUpload();
        $uploadedFile->expects($this->once())
                     ->method('getClientOriginalExtension')
                     ->will(
                         $this->returnValue('doc')
                     );
        $uploadedFile->expects($this->once())
                     ->method('guessExtension')
                     ->will(
                         $this->returnValue('doc')
                     );

        // Test the validator.
        $validator = new FileValidator();

        $valid = $validator->validate($uploadedFile);

        $this->assertFalse($valid);
    }

    public function testMimeAndExtensionDontMatch()
    {
        $uploadedFile = $this->getMockFileUpload();
        $uploadedFile->expects($this->once())
                     ->method('getClientOriginalExtension')
                     ->will(
                         $this->returnValue('jpg')
                     );
        $uploadedFile->expects($this->once())
                     ->method('guessExtension')
                     ->will(
                         $this->returnValue('png')
                     );

        // Test the validator.
        $validator = new FileValidator();

        $valid = $validator->validate($uploadedFile);

        $this->assertFalse($valid);
    }
}
