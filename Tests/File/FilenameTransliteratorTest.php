<?php

namespace IrishDan\ResponsiveImageBundle\Tests\File;

use IrishDan\ResponsiveImageBundle\File\FilenameTransliterator;
use IrishDan\ResponsiveImageBundle\File\FileToObject;
use IrishDan\ResponsiveImageBundle\Tests\Entity\TestImage;
use PHPUnit\Framework\TestCase;

class FilenameTransliteratorTest extends TestCase
{
    public function testTransliterate()
    {
        // Mock the repository so it returns the mock of the Image repository.
        $fileToObject = $this->getMockBuilder(FileToObject::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $fileToObject->expects($this->any())
                     ->method('getObjectFromFilename')
                     ->will(
                         $this->returnCallback(
                             function ($filename) {
                                 $existingFilenames = [
                                     'test.jpg',
                                     'test_1.jpg',
                                 ];
                                 if (in_array($filename, $existingFilenames)) {
                                     $image = new TestImage();

                                     return $image;
                                 }
                                 else {
                                     return null;
                                 }
                             }
                         )
                     );

        $transliterator = new FilenameTransliterator($fileToObject);

        // Test that characters are replaced
        $filename = $transliterator->transliterate('a file name-with £disallowed characters.jpg');
        $this->assertEquals('a_file_name_with_disallowed_characters.jpg', $filename);

        // Test that filename that already exist are appended with a number.
        $filename = $transliterator->transliterate('test.jpg');
        $this->assertEquals('test_2.jpg', $filename);
    }
}
