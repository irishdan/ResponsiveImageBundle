<?php

namespace IrishDan\ResponsiveImageBundle\Event;

use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use Symfony\Component\EventDispatcher\Event;


class FileSystemEvent extends Event
{
    protected $PrimaryFileSystemWrapper;

    public function __construct(PrimaryFileSystemWrapper $PrimaryFileSystemWrapper)
    {
        $this->PrimaryFileSystemWrapper = $PrimaryFileSystemWrapper;
    }

    public function getPrimaryFileSystemWrapper()
    {
        return $this->PrimaryFileSystemWrapper;
    }
}