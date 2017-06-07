<?php

namespace IrishDan\ResponsiveImageBundle;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Uploader
 *
 * @package ResponsiveImageBundle
 */
class Uploader
{
    /**
     * @var array
     */
    protected $allowedTypes = [
        'jpg',
        'jpeg',
        'png',
    ];
    /**
     * @var FileManager
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
     * Uploader constructor.
     *
     * @param FilesystemInterface $fileSystem
     */
    public function __construct(FilesystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return null|string
     */
    protected function formatPath()
    {
        // @TODO: The could be adjusted in any number of ways depending on implementation.
        $path = $this->file->getClientOriginalName();

        return $path;
    }

    protected function isValid()
    {
        // @TODO: Implement.
        // $this->error = '';
        return true;
    }

    public function upload(ResponsiveImageInterface $image)
    {
        $this->file = $image->getFile();

        // Use UploadedFile's inbuild validation and allow
        // implementation specific custom checks on uploaded file
        if ($this->file->isValid() && $this->isValid()) {
            // Alter name for uniqueness
            $path = $this->formatPath();

            $info = getimagesize($this->file);
            list($x, $y) = $info;

            // Save the actual file to the filesystem.
            $stream = fopen($this->file->getRealPath(), 'r+');
            $this->fileSystem->writeStream($path, $stream);
            fclose($stream);

            $image->setPath($path);
            $image->setHeight($x);
            $image->setWidth($y);

            // var_dump($this->fileSystem->getMetadata($path));
            // var_dump($info);
            // var_dump($x);
            // var_dump($y);

            // Clean up the file property as you won't need it anymore.
            $this->file = null;
            $image->setFile(null);
        } else {
            $error = empty($this->error) ? $this->file->getErrorMessage() : $this->error;
            throw new FileException(
                $error
            );
        }
    }
}
