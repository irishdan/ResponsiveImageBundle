<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\FileSystem;

use IrishDan\ResponsiveImageBundle\Event\FileSystemEvent;
use IrishDan\ResponsiveImageBundle\Event\FileSystemEvents;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PrimaryFileSystemWrapper
 *
 * This is a wrapper around a flysystem filesystem.
 * It includes some helper methods for easy access to flysystem.
 * If event dispatcher is injected it will fire events whenerever the filesystem is set is gotten.
 * This is to allow for swapping of filesystem.
 *
 * @package IrishDan\ResponsiveImageBundle\FileSystem
 */
class PrimaryFileSystemWrapper
{
    /**
     * @var FilesystemInterface
     */
    private $fileSystem;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * PrimaryFileSystemWrapper constructor.
     *
     * @param EventDispatcherInterface|null $eventDispatcher
     * @param FilesystemInterface|null      $fileSystem
     */
    public function __construct(EventDispatcherInterface $eventDispatcher = null, FilesystemInterface $fileSystem = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->fileSystem      = $fileSystem;
    }

    /**
     * @return FilesystemInterface|null
     */
    public function getFileSystem()
    {
        if (!empty($this->eventDispatcher)) {
            $fileSystemEvent = new FileSystemEvent($this);
            $this->eventDispatcher->dispatch(FileSystemEvents::FILE_SYSTEM_FACTORY_GET, $fileSystemEvent);
        }

        return $this->fileSystem;
    }

    public function getAdapter()
    {
        if (!empty($this->fileSystem)) {
            return $this->fileSystem->getAdapter();
        }

        return null;
    }

    /**
     * @param FilesystemInterface $fileSystem
     */
    public function setFileSystem(FilesystemInterface $fileSystem)
    {
        if (!empty($this->eventDispatcher)) {
            $fileSystemEvent = new FileSystemEvent($this);
            $this->eventDispatcher->dispatch(FileSystemEvents::FILE_SYSTEM_FACTORY_SET, $fileSystemEvent);
        }
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param $path
     * @param $contents
     */
    public function write($path, $contents)
    {
        $this->fileSystem->write($path, $contents);
    }

    /**
     * @param $path
     * @param $contents
     */
    public function update($path, $contents)
    {
        $this->fileSystem->update($path, $contents);
    }

    /**
     * @param $path
     * @param $contents
     */
    public function put($path, $contents)
    {
        $this->fileSystem->put($path, $contents);
    }

    /**
     * @param $path
     *
     * @return false|string
     */
    public function read($path)
    {
        return $this->fileSystem->read($path);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function has($path)
    {
        return $this->fileSystem->has($path);
    }

    /**
     * @param $path
     */
    public function delete($path)
    {
        $this->fileSystem->delete($path);
    }

    /**
     * @param $path
     *
     * @return false|string
     */
    public function readAndDelete($path)
    {
        return $this->fileSystem->readAndDelete($path);
    }

    /**
     * @param $currentPath
     * @param $newPath
     */
    public function rename($currentPath, $newPath)
    {
        $this->fileSystem->rename($currentPath, $newPath);
    }

    /**
     * @param $currentPath
     * @param $duplicatePath
     */
    public function copy($currentPath, $duplicatePath)
    {
        $this->fileSystem->copy($currentPath, $duplicatePath);
    }

    /**
     * @param $path
     *
     * @return false|string
     */
    public function getMimetype($path)
    {
        return $this->fileSystem->getMimetype($path);
    }

    /**
     * @param $path
     *
     * @return false|string
     */
    public function getTimeStamp($path)
    {
        return $this->fileSystem->getTimestamp($path);
    }

    /**
     * @param $path
     *
     * @return false|int
     */
    public function getSize($path)
    {
        return $this->fileSystem->getSize($path);
    }

    /**
     * @param $path
     */
    public function createDir($path)
    {
        $this->fileSystem->createDir($path);
    }

    /**
     * @param $path
     */
    public function deleteDir($path)
    {
        $this->fileSystem->deleteDir($path);
    }
}