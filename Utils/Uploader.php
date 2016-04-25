<?php

namespace ResponsiveImageBundle\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Uploader
 * @package AppBundle\Utils
 */
class Uploader {

    /**
     * @var array
     */
    public $allowedTypes = array(
        'jpg',
        'jpeg',
        'png',
    );

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * @var
     */
    private $file;

    /**
     * @var
     */
    private $uploadOk = FALSE;

    /**
     * Uploader constructor.
     * @param FileSystem $system
     */
    public function __construct(FileSystem $system)
    {
        $this->fileSystem = $system;
    }

    /**
     * Sets file. XX
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file. XX
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param $str
     * @return mixed
     */
    private function createFilename($str) {
        // Sanitize and transliterate
        $str = strtolower($str);
        $str = strip_tags($str);
        $safeName = preg_replace('/[^a-z0-9-_\.]/','', $str);

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
     * @param $name
     * @return bool
     */
    private function isUniqueFilename($name) {
        $filePath = $this->fileSystem->getSystemUploadDirectory() . '/' . $name;
        if ($this->fileSystem->directoryExists($filePath)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    /**
     * @return bool
     */
    public function isAllowedType() {
        $extension = $this->getFile()->guessExtension();
        if (in_array($extension, $this->allowedTypes)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    /**
     * Deals with image after uploading it.
     *
     * @param ResponsiveImageInterface $image
     * @return ResponsiveImageInterface
     */
    public function upload(ResponsiveImageInterface $image)
    {
        // The file property can be empty if the field is not required.
        if (null === $image->getFile()) {
            return FALSE;
        }

        $this->setFile($image->getFile());
        $messages = array();
        $this->uploadOk = TRUE;
        $uploadMaxSize = ini_get('upload_max_filesize');

        if (!$this->file instanceof UploadedFile && !empty($image->getFile()->getError())) {
            $messages[] = 'Uploaded file should be an instance of \'UploadedFile\'';
            $this->uploadOk = FALSE;
        }
        elseif ($this->file->getSize() > $uploadMaxSize) {
            $messages[] = 'File size cannot be larger than ' . $uploadMaxSize;
            $this->uploadOk = FALSE;
        }
        elseif (!$this->isAllowedType()) {
            $messages[] = 'File type is not allowed';
            $this->uploadOk = FALSE;
        }
        else {
            // Sanitize it at least to avoid any security issues.
            $fileName = $this->file->getClientOriginalName();
            $newFileName = $this->createFilename($fileName);

            // Move takes the target directory and then the target filename to move to.
            $this->file->move(
                $this->fileSystem->getSystemUploadDirectory(),
                $newFileName
            );

            // Set the path property to the filename where you've saved the file.
            $image->setpath($newFileName);

            // Set the image dimensions.
            $imageData = getimagesize('/' . $this->fileSystem->getSystemUploadDirectory() . '/' . $newFileName);
            $image->setWidth($imageData[0]);
            $image->setHeight($imageData[1]);

            // Clean up the file property as you won't need it anymore.
            $this->file = null;
            $image->setFile(null);
        }

        if ($this->uploadOk) {
            return $image;
        }
        else {
            print_r($messages);
            die;
        }
    }
}
