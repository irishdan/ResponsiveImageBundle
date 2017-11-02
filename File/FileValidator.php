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

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileValidator
 *
 * @package IrishDan\ResponsiveImageBundle\File
 */
class FileValidator implements FileValidatorInterface
{
    protected $errors;
    protected $allowedTypes = [
        'jpeg',
        'jpg',
        'png',
        'gif',
    ];

    /**
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function validate(UploadedFile $file)
    {
        // Check allowed types.
        $fileExtension        = strtolower($file->getClientOriginalExtension());
        $guessedFileExtension = strtolower($file->guessExtension());

        if (!in_array($guessedFileExtension, $this->allowedTypes)) {
          $this->errors[] = 'Files of "' . $guessedFileExtension . '" type are not allowed';

          return false;
        }

        if (!in_array($fileExtension, $this->allowedTypes)) {
            $this->errors[] = 'Files of "' . $fileExtension . '" type are not allowed';

            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return $this->allowedTypes;
    }

    /**
     * @param array $allowedTypes
     */
    public function setAllowedTypes($allowedTypes)
    {
        $this->allowedTypes = $allowedTypes;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }
}