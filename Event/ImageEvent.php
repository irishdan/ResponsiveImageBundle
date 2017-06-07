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
     * @var
     */
    protected $styles = [];

    /**
     * ImageEvent constructor.
     *
     * @param ResponsiveImageInterface|NULL $image
     * @param array|NULL                    $stylesArray
     */
    public function __construct(ResponsiveImageInterface $image = null, array $stylesArray = null)
    {
        if (!empty($image)) {
            $this->image = $image;
        }
        if (!empty($stylesArray)) {
            $this->styles = $stylesArray;
        }
    }

    /**
     * @return mixed
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }
}