<?php

namespace ResponsiveImageBundle\Tests\Entity;

class TestImage implements \ResponsiveImageBundle\Utils\ResponsiveImageInterface
{
    private $title = 'Test image';
    private $path = 'dummy.jpg';
    private $alt = 'Test image alt text';
    private $width = 1000;
    private $height = 1600;
    private $file;
    private $cropCoordinates = '200, 3, 800, 1400:310, 145, 750, 617';
    private $style;
    private $picture;

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
        // TODO: Looks at implementation
        return $this->picture;
    }

    public function setPicture($picture)
    {
        return $this->picture;
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
    // @TODO: Look at using twig template to generate the html
}