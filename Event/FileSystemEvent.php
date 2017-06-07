<?php

namespace IrishDan\ResponsiveImageBundle\Event;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\Event;


class FileSystemEvent extends Event
{
    /**
     * @var
     */
    protected $fileSystem;

    public function __construct(FilesystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return mixed
     */
    public function getFileSystem()
    {
        return $this->fileSystem;
    }
}