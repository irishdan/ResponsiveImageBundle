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
    /**
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function validate(UploadedFile $file)
    {
        return true;
    }
}