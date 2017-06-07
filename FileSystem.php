<?php

namespace IrishDan\ResponsiveImageBundle;

use League\Flysystem\FilesystemInterface;

/**
 * This is just a wrapper around flysytem API
 *
 * @see     http://flysystem.thephpleague.com/api/
 * @package ResponsiveImageBundle
 */
class FileSystem
{
    private $fileSystem;
    private $uploadPath;
    private $stylePath;

    public function __construct(array $imageConfigs, FilesystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
        $this->uploadPath = $imageConfigs['image_directory'];
        $this->stylePath = $imageConfigs['image_directory'] . '/' . $imageConfigs['image_styles_directory'];
    }

    public function getFileSystem()
    {
        return $this->fileSystem;
    }

    public function setFileSystem($fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function write($path, $contents)
    {
        $this->fileSystem->write($path, $contents);
    }

    public function update($path, $contents)
    {
        $this->fileSystem->update($path, $contents);
    }

    public function put($path, $contents)
    {
        $this->fileSystem->put($path, $contents);
    }

    public function read($path)
    {
        return $this->fileSystem->read($path);
    }

    public function has($path)
    {
        return $this->fileSystem->has($path);
    }

    public function delete($path)
    {
        $this->fileSystem->delete($path);
    }

    public function readAndDelete($path)
    {
        return $this->fileSystem->readAndDelete($path);
    }

    public function rename($currentPath, $newPath)
    {
        $this->fileSystem->rename($currentPath, $newPath);
    }

    public function copy($currentPath, $duplicatePath)
    {
        $this->fileSystem->copy($currentPath, $duplicatePath);
    }

    public function getMimetype($path)
    {
        return $this->fileSystem->getMimetype($path);
    }

    public function thing($path)
    {
        return $this->fileSystem->getTimestamp($path);
    }

    public function getSize($path)
    {
        return $this->fileSystem->getSize($path);
    }

    public function createDir($path)
    {
        $this->fileSystem->createDir($path);
    }

    public function deleteDir($path)
    {
        $this->fileSystem->deleteDir($path);
    }
}