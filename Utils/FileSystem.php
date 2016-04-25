<?php

namespace ResponsiveImageBundle\Utils;

/**
 * Class FileSystem
 * @package ResponsiveImageBundle\Utils
 */
class FileSystem
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var
     */
    private $uploadsDir;

    /**
     * @var string
     */
    private $stylesDir;

    /**
     * @var string
     */
    private $systemPath;

    /**
     * @var string
     */
    private $systemUploadPath;

    /**
     * @var string
     */
    private $systemStylesPath;

    /**
     * @var
     */
    private $webDirectory;

    /**
     * @var
     */
    private $webStylesDirectory;

    /**
     * FileSystem constructor.
     * @param $uploads_dir
     * @param $styles_dir
     */
    public function __construct($rootDir, $uploadsDir, $stylesDir) {
        $symfonyDir = substr($rootDir, 0, -4);

        $this->setRootDir($symfonyDir);
        $this->setUploadsDir($uploadsDir);
        $this->setStylesDir($uploadsDir . '/' . $stylesDir['image_styles_directory']);
        $this->setSystemPath($symfonyDir . '/web');
        $this->setSystemUploadPath($this->systemPath . '/' . $this->uploadsDir);
        $this->setSystemStylesPath($this->systemUploadPath . '/' . $stylesDir['image_styles_directory']);
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return mixed
     */
    public function getWebStylesDirectory()
    {
        return $this->webStylesDirectory;
    }

    /**
     * @param mixed $webStylesDirectory
     */
    public function setWebStylesDirectory($webStylesDirectory)
    {
        $this->webStylesDirectory = $webStylesDirectory;
    }

    /**
     * @return mixed
     */
    public function getWebDirectory()
    {
        return $this->webDirectory;
    }

    /**
     * @param mixed $webDirectory
     */
    public function setWebDirectory($webDirectory)
    {
        $this->webDirectory = $webDirectory;
    }

    /**
     * @return string
     */
    public function getSystemStylesPath()
    {
        return $this->systemStylesPath;
    }

    /**
     * @param string $systemStylesPath
     */
    public function setSystemStylesPath($systemStylesPath)
    {
        $this->systemStylesPath = $systemStylesPath;
    }

    /**
     * @return string
     */
    public function getSystemUploadPath()
    {
        return $this->systemUploadPath;
    }

    /**
     * @param string $systemUploadPath
     */
    public function setSystemUploadPath($systemUploadPath)
    {
        $this->systemUploadPath = $systemUploadPath;
    }

    /**
     * @return string
     */
    public function getSystemPath()
    {
        return $this->systemPath;
    }

    /**
     * @param string $systemPath
     */
    public function setSystemPath($systemPath)
    {
        $this->systemPath = $systemPath;
    }

    /**
     * @return string
     */
    public function getStylesDir()
    {
        return $this->stylesDir;
    }

    /**
     * @param string $stylesDir
     */
    public function setStylesDir($stylesDir)
    {
        $this->stylesDir = $stylesDir;
    }

    /**
     * @return mixed
     */
    public function getUploadsDir()
    {
        return $this->uploadsDir;
    }

    /**
     * @param mixed $uploadsDir
     */
    public function setUploadsDir($uploadsDir)
    {
        $this->uploadsDir = $uploadsDir;
    }

    /**
     * @return string
     */
    public function getSystemUploadDirectory() {
        return $this->systemUploadPath;
    }

    /**
     * @param $directory
     * @param bool $create
     * @return bool
     */
    public function directoryExists($directory, $create = FALSE) {
        if (file_exists($directory)) {
            return TRUE;
        }
        elseif (!file_exists($directory) && $create) {
            return mkdir($directory, 0775, TRUE);
        }
        else {
            return FALSE;
        }
    }

    /**
     * Daletes a directory and its contents or a file.
     *
     * @param $directory
     * @return bool
     */
    public function deleteDirectory($target) {
        if(is_dir($target)){
            $files = glob($target . '/*');
            foreach($files as $file)
            {
                $this->deleteFile($file);
            }
            rmdir($target);
        }
        elseif (is_file($target)) {
            $this->deleteFile($target);
        }
    }

    /**
     * @param $filename
     * @param null $directory
     * @return bool
     */
    public function deleteFile($path) {
        // If path exists delete the file.
        if ($this->directoryExists($path)) {
            unlink($path);
        }
    }

    /**
     * @param $filename
     * @return string
     */
    public function uploadedFilePath($filename) {
        return $this->systemUploadPath . '/' . $filename;
    }

    /**
     * @param $stylename
     * @return string
     */
    public function styleDirectoryPath($stylename) {
        return $this->systemStylesPath . '/' . $stylename;
    }

    /**
     * @param $stylename
     * @param $filename
     * @return string
     */
    public function styleFilePath($stylename, $filename) {
        return $this->styleDirectoryPath($stylename) . '/' . $filename;
    }

    /**
     * @param $path
     * @return string
     */
    public function getFilenameFromPath($path) {
        return basename($path);
    }

    /**
     * @param $stylename
     * @return string
     */
    public function styleWebPath($stylename) {
        $stylesDirectory = $this->stylesDir;
        $path = $stylesDirectory . '/' . $stylename;

        return $path;
    }
}