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
 * Interface FilenameTransliteratorInterface
 *
 * @package IrishDan\ResponsiveImageBundle\File
 */
interface FilenameTransliteratorInterface
{
    /**
     * @param $filename
     *
     * @return mixed
     */
    public function transliterate($filename);
}