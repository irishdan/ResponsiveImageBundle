<?php

namespace IrishDan\ResponsiveImageBundle\Tests\File;

use IrishDan\ResponsiveImageBundle\File\FileValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileValidatorTest extends TestCase
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
}
