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

use IrishDan\ResponsiveImageBundle\UploaderInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class UploaderEvent
 *
 * @package IrishDan\ResponsiveImageBundle\Event
 */
class UploaderEvent extends Event
{
    /**
     * @var UploaderInterface
     */
    protected $uploader;

    /**
     * UploaderEvent constructor.
     *
     * @param UploaderInterface $uploader
     */
    public function __construct(UploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @return UploaderInterface
     */
    public function getUploader()
    {
        return $this->uploader;
    }
}