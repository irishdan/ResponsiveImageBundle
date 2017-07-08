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
 * Interface FileValidatorInterface
 *
 * @package IrishDan\ResponsiveImageBundle\File
 */
interface FileValidatorInterface
{
    /**
     * Validates the UploadedFile object.
     *
     * @param UploadedFile $file
     *
     * @return boolean
     */
    public function validate(UploadedFile $file);

    /**
     * Fetches any errors generated during validation
     *
     * @return array
     */
    public function getErrors();
}