<?php

namespace ResponsiveImageBundle\Utils;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Uploader
 *
 * @package ResponsiveImageBundle\Utils
 */
class Uploader
{
    /**
     * @var array
     */
    public $allowedTypes = [
        'jpg',
        'jpeg',
        'png',
    ];
    /**
     * @var FileManager
     */
    private $fileSystem;
    /**
     * @var
     */
    private $file;
    /**
     * @var
     */
    private $uploadOk = false;

    /**
     * Uploader constructor.
     *
     * @param FileManager $system
     */
    public function __construct(FileManager $system)
    {
        $this->fileSystem = $system;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sanitizes and cleans up filename
     *
     * @param $str
     * @return mixed
     */
    private function createFilename($str)
    {
        // Sanitize and transliterate
        $str = strtolower($str);
        $str = strip_tags($str);
        $safeName = preg_replace('/[^a-z0-9-_\.]/', '', $str);

        // Create unique filename.
        $i = 1;
        while (!$this->isUniqueFilename($safeName)) {
            $nameArray = explode('.', $safeName);
            $safeName = $nameArray[0] . '-' . $i . '.' . $nameArray[1];
            $i++;
        }

        return $safeName;
    }

    /**
     * Convert MB/K/G to bytesize
     *
     * @param $uploadMaxSize
     * @return int
     */
    public function mToBytes($uploadMaxSize)
    {
        $uploadMaxSize = trim($uploadMaxSize);
        $last = strtolower($uploadMaxSize[strlen($uploadMaxSize) - 1]);

        switch ($last) {
            case 'g':
                $uploadMaxSize *= 1024 * 1000 * 1000;
                break;

            case 'm':
                $uploadMaxSize *= 1024 * 1000;
                break;

            case 'k':
                $uploadMaxSize *= 1024;
                break;
        }

        return $uploadMaxSize;
    }

    /**
     * Checks to see if a file name is unique in the storage directory.
     *
     * @param $name
     * @return bool
     */
    private function isUniqueFilename($name)
    {
        $storageDirectory = $this->fileSystem->getStorageDirectory();
        $filePath = $storageDirectory . $name;
        if ($this->fileSystem->directoryExists($filePath)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Tests if mime type is allowed.
     *
     * @return bool
     */
    public function isAllowedType()
    {
        $extension = $this->getFile()->guessExtension();
        if (in_array($extension, $this->allowedTypes)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * After uploading, this function checks if the image is valid and if so moves it to an appropriate storage
     * location.
     *
     * @param ResponsiveImageInterface $image .
     * @return ResponsiveImageInterface
     */
    public function upload(ResponsiveImageInterface $image)
    {
        // The file property can be empty if the field is not required.
        if (null === $image->getFile()) {
            return false;
        }

        $this->setFile($image->getFile());
        $messages = [];
        $this->uploadOk = true;

        // Get max file upload in bytes.
        $uploadMaxSize = ini_get('upload_max_filesize');
        $uploadMaxSize = $this->mToBytes($uploadMaxSize);

        if (!$this->file instanceof UploadedFile && !empty($image->getFile()->getError())) {
            $messages[] = 'Uploaded file should be an instance of \'UploadedFile\'';
            $this->uploadOk = false;
        } elseif ($this->file->getSize() > $uploadMaxSize) {
            $messages[] = sprintf('%s: File size cannot be larger than %s', $this->file->getSize(), $uploadMaxSize);
            $this->uploadOk = false;
        } elseif (!$this->isAllowedType()) {
            $messages[] = 'File type is not allowed';
            $this->uploadOk = false;
        } else {
            // Sanitize it at least to avoid any security issues.
            $fileName = $this->file->getClientOriginalName();
            $newFileName = $this->createFilename($fileName);

            // Move takes the target directory and then the target filename to move to.
            $storageDirectory = $this->fileSystem->getStorageDirectory('original');
            $this->file->move(
                $storageDirectory,
                $newFileName
            );

            // Set the path property to the filename where you've saved the file.
            $image->setPath($newFileName);

            // Set the image dimensions.
            $imageData = getimagesize($storageDirectory . $newFileName);
            $image->setWidth($imageData[0]);
            $image->setHeight($imageData[1]);

            // Clean up the file property as you won't need it anymore.
            $this->file = null;
            $image->setFile(null);
        }

        if ($this->uploadOk) {
            return $image;
        } else {
            throw new FileException($messages[0]);
        }
    }
}
