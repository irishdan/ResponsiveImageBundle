<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Event;

/**
 * Class UploaderEvents
 *
 * @package IrishDan\ResponsiveImageBundle\Event
 */
final class UploaderEvents
{
    const UPLOADER_PRE_UPLOAD = 'uploader.pre_upload';
    const UPLOADER_UPLOADED = 'uploader.uploaded';
}