<?php

namespace IrishDan\ResponsiveImageBundle\Event;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\Event;


class FileSystemEvent extends Event
{
    protected $PrimaryFileSystemWrapper;

    public function __construct($PrimaryFileSystemWrapper)
    {
        $this->PrimaryFileSystemWrapper = $PrimaryFileSystemWrapper;
    }

    public function getPrimaryFileSystemWrapper()
    {
        return $this->PrimaryFileSystemWrapper;
    }
}