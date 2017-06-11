<?php

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