<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle;

/**
 * Interface UploaderInterface
 *
 * @package IrishDan\ResponsiveImageBundle
 */
interface UploaderInterface
{
    /**
     * @param ResponsiveImageInterface $image
     *
     * @return mixed
     */
    public function upload(ResponsiveImageInterface $image);
}