<?php

namespace IrishDan\ResponsiveImageBundle;

use League\Flysystem\FilesystemInterface;

interface UploaderInterface
{
    public function upload(ResponsiveImageInterface $image);

    public function setFileSystem(FilesystemInterface $fileSystem);

    public function getFileSystem();
}