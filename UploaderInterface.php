<?php

namespace IrishDan\ResponsiveImageBundle;

/**
 * Interface UploaderInterface
 *
 * @package IrishDan\ResponsiveImageBundle
 */
interface UploaderInterface
{
    /**
     * @param ResponsiveImageInterface $image
     *
     * @return mixed
     */
    public function upload(ResponsiveImageInterface $image);

    public function getFileSystem();

    public function setFileSystem($filesystem);
}
