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

use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class FileSystemEvent
 *
 * @package IrishDan\ResponsiveImageBundle\Event
 */
class FileSystemEvent extends Event
{
    /**
     * @var PrimaryFileSystemWrapper
     */
    protected $PrimaryFileSystemWrapper;

    /**
     * FileSystemEvent constructor.
     *
     * @param PrimaryFileSystemWrapper $PrimaryFileSystemWrapper
     */
    public function __construct(PrimaryFileSystemWrapper $PrimaryFileSystemWrapper)
    {
        $this->PrimaryFileSystemWrapper = $PrimaryFileSystemWrapper;
    }

    /**
     * @return PrimaryFileSystemWrapper
     */
    public function getPrimaryFileSystemWrapper()
    {
        return $this->PrimaryFileSystemWrapper;
    }
}