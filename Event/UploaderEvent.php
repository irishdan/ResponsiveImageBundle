<?php

namespace IrishDan\ResponsiveImageBundle\Event;

use IrishDan\ResponsiveImageBundle\UploaderInterface;
use Symfony\Component\EventDispatcher\Event;


class UploaderEvent extends Event
{
    protected $uploader;

    public function __construct(UploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function getUploader()
    {
        return $this->uploader;
    }
}