<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\File;


/**
 * Class FilenameTransliterator
 *
 * @package IrishDan\ResponsiveImageBundle\File
 */
class FilenameTransliterator implements FilenameTransliteratorInterface
{
    /**
     * @var FileToObject
     */
    protected $fileToObject;

    /**
     * FilenameTransliterator constructor.
     *
     * @param \IrishDan\ResponsiveImageBundle\File\FileToObject $fileToObject
     */
    public function __construct(FileToObject $fileToObject)
    {
        $this->fileToObject = $fileToObject;
    }

    /**
     * @param $filename
     *
     * @return mixed|string
     */
    public function transliterate($filename)
    {
        // Sanitize and transliterate
        $str      = strtolower($filename);
        $str      = strip_tags($str);
        $safeName = preg_replace('/[^a-z0-9-_\.]/', '', $str);

        // Create unique filename.
        $i         = 1;
        $nameArray = explode('.', $safeName);

        while (!$this->isUniqueFilename($safeName)) {
            $parts           = $nameArray;
            $secondLastIndex = count($parts) - 2;

            // Add an incremented suffix to the second last part.
            $parts[$secondLastIndex] = $parts[$secondLastIndex] . '_' . $i;
            // Stick it all back together
            $safeName = implode('.', $parts);

            $i++;
        }

        return $safeName;
    }

    /**
     * @param $filename
     *
     * @return bool
     */
    protected function isUniqueFilename($filename)
    {
        $entity = $this->fileToObject->getObjectFromFilename($filename);

        return empty($entity) ? true : false;
    }
}