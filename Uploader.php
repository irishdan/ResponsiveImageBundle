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
    protected $eventDispatcher;

    public function getFileSystem()
    {
        return $this->fileSystem;
    }

    public function setFileSystem($filesystem)
    {
        $this->fileSystem = $filesystem;
    }

    /**
     * Uploader constructor.
     *
     * @param PrimaryFileSystemWrapper             $PrimaryFileSystemWrapper
     * @param FilenameTransliteratorInterface|null $transliterator
     * @param FileValidatorInterface|null          $fileValidator
     * @param EventDispatcherInterface|null        $eventDispatcher
     */
    public function __construct(
        PrimaryFileSystemWrapper $PrimaryFileSystemWrapper,
        FilenameTransliteratorInterface $transliterator = null,
        FileValidatorInterface $fileValidator = null,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->fileSystem      = $PrimaryFileSystemWrapper->getFileSystem();
        $this->transliterator  = $transliterator;
        $this->fileValidator   = $fileValidator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function upload(ResponsiveImageInterface $image)
    {
        // Dispatch pre-upload event.
        if (!empty($this->eventDispatcher)) {
            $uploaderEvent = new UploaderEvent($this);
            $this->eventDispatcher->dispatch(UploaderEvents::UPLOADER_PRE_UPLOAD, $uploaderEvent);
        }

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

            // If the image has a setFileSystem method set the filesystem data.
            if (method_exists($image, 'setFileSystem')) {
                $storageData = $this->getStorageDataFormFileSystem();
                if (!empty($storageData)) {
                    $image->setFileSystem(serialize($storageData));
                }
            }

            // Clean up the file property as you won't need it anymore.
            $this->file = null;
            $image->setFile(null);

            // Dispatch uploaded event
            if (!empty($uploaderEvent)) {
                $this->eventDispatcher->dispatch(UploaderEvents::UPLOADER_UPLOADED, $uploaderEvent);
            }
        }
        else {
            $error = empty($this->error) ? $this->file->getErrorMessage() : $this->error;
            throw new FileException(
                $error
            );
        }
    }

    protected function getStorageDataFormFileSystem()
    {
        $adapter     = $this->fileSystem->getAdapter();
        $adapterType = $this->getAdapterType($adapter);

        // @TODO: This should be part of the urlEncoders
        switch ($adapterType) {
            case 'AwsS3Adapter':
                $prefix = $adapter->getPathPrefix();
                $bucket = $adapter->getBucket();
                $region = $adapter->getClient()->getRegion();

                return [
                    'adapter' => $adapterType,
                    'prefix'  => $prefix,
                    'bucket'  => $bucket,
                    'region'  => $region,
                ];

                break;

            case 'Local':
                return [
                    'adapter' => $adapterType,
                    'prefix'  => 'test/images', // @TODO: what is this?
                ];

                break;
        }

        return [];
    }

    protected function getAdapterType(AdapterInterface $adapter)
    {
        $class          = get_class($adapter);
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
