<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle;

use Symfony\Component\HttpFoundation\File\File;
// use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     *
     * @return mixed
     */
    public function setPath($path);

    /**
     * @return mixed
     */
    public function getTitle();

    /**
     * @param $title
     *
     * @return mixed
     */
    public function setTitle($title);

    /**
     * @return mixed
     */
    public function getAlt();

    /**
     * @param $alt
     *
     * @return mixed
     */
    public function setAlt($alt);

    /**
     * @return mixed
     */
    public function getWidth();

    /**
     * @param $width
     *
     * @return mixed
     */
    public function setWidth(int $width):void;

    /**
     * @return mixed
     */
    public function getHeight();

    /**
     * @param $height
     *
     * @return mixed
     */
    public function setHeight(int $height): void;

    /**
     * @return mixed
     */
    public function getCropCoordinates();

    /**
     * @param File $file
     *
     * @return mixed
     */
    public function setFile(?File $file): void;

    /**
     * @return mixed
     */
    public function getFile();

    /**
     * @param $cords
     *
     * @return mixed
     */
    public function setCropCoordinates(?string $cords) : void;

    /**
     * @param $src
     *
     * @return mixed
     */
    public function setSrc(string $src): void;

    /**
     * @return mixed
     */
    public function getSrc();
}
