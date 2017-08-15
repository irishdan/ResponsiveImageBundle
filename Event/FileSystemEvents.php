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
 * Class FileSystemEvents
 *
 * @package IrishDan\ResponsiveImageBundle\Event
 */
final class FileSystemEvents
{
    const FILE_SYSTEM_FACTORY_GET = 'file_system_factory.get';
    const FILE_SYSTEM_FACTORY_SET = 'file_system_factory.set';
}