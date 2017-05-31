<?php

namespace ResponsiveImageBundle\Tests\Entity;

use ResponsiveImageBundle\Utils\ResponsiveImageInterface;

class TestImage implements ResponsiveImageInterface
{
    private $id = 1;
    private $title = 'Test image';
    private $path = 'dummy.jpg';
    private $alt = 'Test image alt text';
    private $width = 1000;
    private $height = 1600;
    private $file;
    private $cropCoordinates = '200, 3, 800, 1400:310, 145, 750, 617';
    private $style;
    private $picture;

    public function getId()
    {
        return $this->id;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getStyle()
    {
        return $this->style;
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getCropCoordinates()
    {
        return $this->cropCoordinates;
    }

    public function setCropCoordinates($cords)
    {
        $this->cropCoordinates = $cords;
    }

    // @TODO: Only used during upload review if needed after move to flysystem
    public function setFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * Generates an <img> tag for a given style.
     *
     * @param null $style
     * @return string
     */
    public function img()
    {
        // @TODO: Look at using twig template to generate the html. this way users can override.

        if (!empty($this->style)) {
            $src = $this->style;
        } else {
            $src = $this->getPath();
        }

        // @TODO: Use cache_bust config.
        // $updated = $this->getUpdated();
        // if (!empty($updated)) {
        //     $src = $this->path . '?' . $updated->getTimestamp();
        // }

        $title = $this->title;
        $alt = $this->alt;

        // @TODO: If image style is used height and width should be transposed.
        $height = $this->height;
        $width = $this->width;

        return '<img src="' . $src . '" height="' . $height . '" width="' . $width . '" title="' . $title . '" alt="' . $alt . '"/>';
    }

    /**
     *  Returns an <img> tag string if the object is printed directly.
     */
    public function __toString()
    {
        if (empty($this->picture)) {
            return $this->img();
        }

        return $this->picture;
    }
}