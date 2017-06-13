<?php

namespace IrishDan\ResponsiveImageBundle\Tests\Entity;

use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ResponsiveImageBundle\Repository\ImageRepository")
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
    /**
     * @var
     */
    private $style;
    /**
     * @var
     */
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

    // @TODO: Only used during upload review if needed after move to flysystem
    public function setFile(UploadedFile $file)
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