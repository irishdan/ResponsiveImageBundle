<?php

namespace ResponsiveImageBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ResponsiveImageBundle\Utils\ResponsiveImageInterface;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\HasLifecycleCallbacks()
 */
class ResponsiveImage implements ResponsiveImageInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, unique=true)
     */
    private $path;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="crop_coordinations", type="string", nullable=true)
     */
    private $cropCoordinates;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @var null
     */
    private $style = null;

    /**
     * @var null
     */
    public $picture = null;

    /**
     * @return mixed
     */
    public function getCropCoordinates()
    {
        return $this->cropCoordinates;
    }

    /**
     * @param mixed $cropCoordinates
     */
    public function setCropCoordinates($cropCoordinates)
    {
        $this->cropCoordinates = $cropCoordinates;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Image
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return Image
     */
    public function setWidth($width)
    {
        // Set from uploaded image.
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Image
     */
    public function setHeight($height)
    {
        // Set from uploaded image.
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @ORM\prePersist
     */
    public function prePersist() {
        $this->created = new \DateTime();
    }

    /**
     * Set weight
     *
     * @param $path
     * @return Image
     * @internal param string $alt
     *
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get weight
     *
     * @return int
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set weight
     *
     * @param $weight
     * @return Image
     * @internal param string $alt
     *
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }


    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param $style
     */
    function setStyle($style) {
        $this->style = $style;
    }

    function getStyle() {
        return $this->style;
    }

    /**
     * @param null $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    public function getPicture() {
        return $this->picture;
    }

    /**
     * Generates an <img> tag for a given style.
     * @return string
     * @internal param null $style
     */
    public function img() {
        if (!empty($this->style)) {
            $src = $this->style;
        }
        else {
            $src = $this->getPath();
        }

        $title = empty($this->title) ? '' : $this->title;
        $alt = empty($this->alt) ? '' : $this->alt;
        $height = empty($this->height) ? '' : $this->height;
        $width = empty($this->width) ? '' : $this->width;

        return '<img src="' . $src . '" height="' . $height . '" width="' . $width . '" title="' . $title . '" alt="' . $alt . '"/>';
    }

    /**
     *  Returns an <img> or <picture> tag string if the object is printed directly.
     */
    public function __toString() {
        if (empty($this->picture)) {
            return $this->img();
        }
        return $this->picture;
    }
}
