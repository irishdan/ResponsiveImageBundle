<?php

namespace IrishDan\ResponsiveImageBundle;

use IrishDan\ResponsiveImageBundle\Event\UploaderEvent;
use IrishDan\ResponsiveImageBundle\Event\UploaderEvents;
use IrishDan\ResponsiveImageBundle\File\FilenameTransliteratorInterface;
use IrishDan\ResponsiveImageBundle\File\FileValidatorInterface;
use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Uploader
 *
 * @package ResponsiveImageBundle
 */
class Uploader implements UploaderInterface
{
    /**
     * @var FilesystemInterface $fileSystem
     */
    protected $fileSystem;
    /**
     * @var UploadedFile $file
     */
    protected $file;
    /**
     * @var
     */
    protected $error;
    /**
     * @var FilenameTransliteratorInterface
     */
    protected $transliterator;
    /**
     * @var FileValidatorInterface
     */
    protected $fileValidator;

    /**
     * Uploader constructor.
     *
     * @param EventDispatcherInterface             $eventDispatcher
     * @param FilenameTransliteratorInterface|null $transliterator
     * @param FileValidatorInterface|null          $fileValidator
     */
    public function __construct(
        //EventDispatcherInterface $eventDispatcher,
        PrimaryFileSystemWrapper $PrimaryFileSystemWrapper,
        FilenameTransliteratorInterface $transliterator = null,
        FileValidatorInterface $fileValidator = null
    )
    {
        // $this->eventDispatcher = $eventDispatcher;
        $this->transliterator = $transliterator;
        $this->fileValidator = $fileValidator;
        $this->fileSystem = $PrimaryFileSystemWrapper->getFileSystem();
    }

    // public function setFilesystem(FilesystemInterface $fileSystem)
    // {
    //     $this->fileSystem = $fileSystem;
    // }
//
    // public function getFilesystem()
    // {
    //     return $this->fileSystem;
    // }

    public function upload(ResponsiveImageInterface $image)
    {
        // $uploaderEvent = new UploaderEvent($this);
        // Dispatch pre-upload event
        // $this->eventDispatcher->dispatch(UploaderEvents::UPLOADER_PRE_UPLOAD, $uploaderEvent);

        $this->file = $image->getFile();

        // Use UploadedFile's inbuilt validation and allow
        // implementation specific custom checks on uploaded file
        if ($this->file->isValid() && $this->isValid()) {
            // Alter name for uniqueness
            $path = $this->formatPath();

            $info = getimagesize($this->file);
            list($length, $height) = $info;

            // Save the actual file to the filesystem.
            $stream = fopen($this->file->getRealPath(), 'r+');
            $this->fileSystem->writeStream($path, $stream);
            fclose($stream);

            $image->setPath($path);
            $image->setHeight($length);
            $image->setWidth($height);

            // @TODO: if it is configured to do so
            $storageData = $this->getStorageDataFormFileSystem();
            if (!empty($storageData)) {
                $image->setFileSystem(serialize($storageData));
            }

            // Clean up the file property as you won't need it anymore.
            $this->file = null;
            $image->setFile(null);

            // Dispatch uploaded event
            // $this->eventDispatcher->dispatch(UploaderEvents::UPLOADER_UPLOADED, $uploaderEvent);
        } else {
            $error = empty($this->error) ? $this->file->getErrorMessage() : $this->error;
            throw new FileException(
                $error
            );
        }
    }

    protected function getStorageDataFormFileSystem()
    {
        $adapter = $this->fileSystem->getAdapter();
        $adapterType = $this->getAdapterType($adapter);

        switch ($adapterType) {
            case 'AwsS3Adapter':
                $prefix = $adapter->getPathPrefix();
                $bucket = $adapter->getBucket();
                $region = $adapter->getClient()->getRegion();

                return [
                    'adapter' => $adapterType,
                    'prefix' => $prefix,
                    'bucket' => $bucket,
                    'region' => $region,
                ];

                break;

            case 'Local':
                return [
                    'adapter' => $adapterType,
                    'prefix' => 'test/images',
                ];

                break;
        }

        return [];
    }

    protected function getAdapterType(AdapterInterface $adapter)
    {
        $class = get_class($adapter);
        $namespaceArray = explode("\\", $class);

        return array_pop($namespaceArray);
    }

    protected function formatPath()
    {
        $path = $this->file->getClientOriginalName();
        if ($this->transliterator instanceof FilenameTransliteratorInterface) {
            $path = $this->transliterator->transliterate($path);
        }

        return $path;
    }

    protected function isValid()
    {
        if ($this->fileValidator instanceof FileValidatorInterface) {
            return $this->fileValidator->validate($this->file);
        }

        return true;
    }
}
