<?php

namespace ResponsiveImageBundle\Tests\Utils;


use ResponsiveImageBundle\Utils\FileSystem;
use ResponsiveImageBundle\Utils\Uploader;

class UploaderTest extends \PHPUnit_Framework_TestCase
{
    use \ResponsiveImageBundle\Tests\Traits\Parameters;

    private $fileSystem;

    private $uploader;

    public function setUp()
    {
        $this->fileSystem = New FileSystem('root_directory', $this->parameters);
        $this->uploader = New Uploader($this->fileSystem);
    }

    public function testMToBytes()
    {
        $fileSizes = [
            '128M' => 131072,
            '128k' => 131072,
            '128g' => 131072,
        ];
        foreach ($fileSizes as $input => $expected) {
            $bytes = $this->uploader->mToBytes($input);
            $this->assertEquals($expected, $bytes);
        }
    }
}
