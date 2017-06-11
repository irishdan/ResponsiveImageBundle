<?php

namespace IrishDan\ResponsiveImageBundle\File;

use IrishDan\ResponsiveImageBundle\File\FileToObject;

class FilenameTransliterator implements FilenameTransliteratorInterface
{
    protected $fileToObject;

    /**
     * FilenameTransliterator constructor.
     */
    public function __construct(FileToObject $fileToObject)
    {
        $this->fileToObject = $fileToObject;
    }

    public function transliterate($filename)
    {
        // Sanitize and transliterate
        $str = strtolower($filename);
        $str = strip_tags($str);
        $safeName = preg_replace('/[^a-z0-9-_\.]/', '', $str);

        // Create unique filename.
        $i = 1;
        $nameArray = explode('.', $safeName);

        while (!$this->isUniqueFilename($safeName)) {
            $parts = $nameArray;
            $secondLastIndex = count($parts) - 2;

            // Add an incremented suffix to the second last part.
            $parts[$secondLastIndex] = $parts[$secondLastIndex] . '_' . $i;
            // Stick it all back together
            $safeName = implode('.', $parts);

            $i++;
        }

        return $safeName;
    }

    protected function isUniqueFilename($filename)
    {
        $entity = $this->fileToObject->getObjectFromFilename($filename);

        return empty($entity) ? true : false;
    }
}