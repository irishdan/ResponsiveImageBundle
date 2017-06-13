<?php

namespace IrishDan\ResponsiveImageBundle;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface ResponsiveImageInterface
 *
 * @package ResponsiveImageBundle
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

    // public function setPicture($picture);

    // public function setStyle($style);

    // public function getStyle();

    // public function getPicture();

    /**
     * @param UploadedFile $file
     * @return mixed
     */
    public function setFile(UploadedFile $file);

    /**
     * @return mixed
     */
    public function getFile();

    public function setCropCoordinates($cords);

    public function setSrc($src);

    public function getSrc();
}