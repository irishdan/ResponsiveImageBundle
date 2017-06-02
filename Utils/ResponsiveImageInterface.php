<?php

namespace IrishDan\ResponsiveImageBundle\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface ResponsiveImageInterface
 *
 * @package ResponsiveImageBundle\Utils
 */
Interface ResponsiveImageInterface
{
    /**
     * @return mixed
     */
    public function getPath();

    /**
     * @param $path
     * @return mixed
     */
    public function setPath($path);

    /**
     * @return mixed
     */
    public function getTitle();

    /**
     * @param $title
     * @return mixed
     */
    public function setTitle($title);

    /**
     * @return mixed
     */
    public function getAlt();

    /**
     * @param $alt
     * @return mixed
     */
    public function setAlt($alt);

    /**
     * @return mixed
     */
    public function getWidth();

    /**
     * @param $width
     * @return mixed
     */
    public function setWidth($width);

    /**
     * @return mixed
     */
    public function getHeight();

    /**
     * @param $height
     * @return mixed
     */
    public function setHeight($height);

    /**
     * @return mixed
     */
    public function getCropCoordinates();

    /**
     * @param $picture
     * @return mixed
     */
    public function setPicture($picture);

    /**
     * @param $style
     * @return mixed
     */
    public function setStyle($style);

    public function getStyle();

    /**
     * @param UploadedFile $file
     * @return mixed
     */
    public function setFile(UploadedFile $file);

    /**
     * @return mixed
     */
    public function getFile();

    public function getPicture();

    public function setCropCoordinates($cords);
}