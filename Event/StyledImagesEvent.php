<?php

namespace IrishDan\ResponsiveImageBundle\Event;

use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use Symfony\Component\EventDispatcher\Event;

class StyledImagesEvent extends Event
{
    protected $image;
    protected $styleImageLocationArray = [];

    public function __construct(ResponsiveImageInterface $image = null, array $styleImageLocationArray = [])
    {
        $this->image = $image;
        $this->styleImageLocationArray = $styleImageLocationArray;
    }

    public function getStyleImageLocationArray()
    {
        return $this->styleImageLocationArray;
    }

    public function getImage()
    {
        return $this->image;
    }
}