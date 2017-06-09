<?php

namespace IrishDan\ResponsiveImageBundle\Event;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\Event;


class FileSystemEvent extends Event
{
    protected $fileSystemFactory;

    public function __construct($fileSystemFactory)
    {
        $this->fileSystemFactory = $fileSystemFactory;
    }

    public function getFileSystemFactory()
    {
        return $this->fileSystemFactory;
    }
}