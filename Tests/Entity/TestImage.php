<?php

namespace IrishDan\ResponsiveImageBundle\Tests\Entity;

use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="IrishDan\ResponsiveImageBundle\Repository\ImageRepository")
 * @ORM\Table(name="image")
 * @ORM\HasLifecycleCallbacks()
 */
class TestImage implements ResponsiveImageInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id = 1;
    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title = 'Test image';
    /**
     * @ORM\Column(name="path", type="string", length=255, unique=true)
     */
    private $path = 'dummy.jpg';
    /**
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt = 'Test image alt text';
    /**
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width = 1000;
    /**
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height = 1600;
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
    /**
     * @ORM\Column(name="crop_coordinations", type="string", nullable=true)
     */
    private $cropCoordinates = '200, 3, 800, 1400:310, 145, 750, 617';
    private $src;

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

    public function getStyleData()
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

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setSrc($src)
    {
        $this->src = $src;
    }

    public function getSrc()
    {
        return $this->src;
    }
}