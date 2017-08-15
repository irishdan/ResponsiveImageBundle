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

use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class EnquiryEvent
 *
 * @package ResponsiveImageBundle\Event
 */
class ImageEvent extends Event
{
    /**
     * @var
     */
    protected $image;

    /**
     * ImageEvent constructor.
     *
     * @param ResponsiveImageInterface|NULL $image
     */
    public function __construct(ResponsiveImageInterface $image = null)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }
}