<?php

namespace ResponsiveImageBundle\Tests\Utils;


use ResponsiveImageBundle\Utils\FileSystem;

/**
 * Class FileSystemTest
 * @package ResponsiveImageBundle\Tests\Utils
 */
class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    use \ResponsiveImageBundle\Tests\Traits\Parameters;

    /**
     * @var
     */
    private $fileSystem;

    /**
     *
     */
    public function setUp() {
        $this->fileSystem = New FileSystem('root_directory', $this->parameters);
    }

    /**
     *
     */
    public function testUploadedFilePath()
    {
        $uploadedFilePath = $this->fileSystem->uploadedFilePath('test_path');

        $this->assertEquals('root_direc/web/uploads/documents/test_path', $uploadedFilePath);
    }
}
