<?php

namespace IrishDan\ResponsiveImageBundle\FileSystem;

use IrishDan\ResponsiveImageBundle\Event\FileSystemEvent;
use IrishDan\ResponsiveImageBundle\Event\FileSystemEvents;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FileSystemFactory
{
    private $fileSystem;
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher = null, FilesystemInterface $fileSystem = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->fileSystem = $fileSystem;
    }

    public function getFileSystem()
    {
        if (!empty($this->eventDispatcher)) {
            $fileSystemEvent = new FileSystemEvent($this);
            $this->eventDispatcher->dispatch(FileSystemEvents::FILE_SYSTEM_FACTORY_GET, $fileSystemEvent);
        }

        return $this->fileSystem;
    }

    public function setFileSystem($fileSystem)
    {
        // if (!empty($this->eventDispatcher)) {
        //     $fileSystemEvent = new FileSystemEvent($this);
        //     $this->eventDispatcher->dispatch(FileSystemEvents::FILE_SYSTEM_FACTORY_SET, $fileSystemEvent);
        // }
//
        // $this->fileSystem = $fileSystem;
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