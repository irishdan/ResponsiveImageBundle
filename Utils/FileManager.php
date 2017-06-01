<?php

namespace ResponsiveImageBundle\Utils;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FileSystem
 *
 * @package ResponsiveImageBundle\Utils
 */
class FileManager
{
    /**
     * @var $config
     */
    private $awsConfig;
    /**
     * @var string
     */
    private $rootDirectory;
    /**
     * @var string
     */
    private $stylesDirectory;
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
    private $tempDirectory = null;
    /**
     * @var
     */
    private $uploadsDirectory;
    /**
     * @var
     */
    private $webDirectory;
    /**
     * @var
     */
    private $webStylesDirectory;
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * FileManager constructor.
     *
     * @param            $rootDirectory
     * @param array      $imageConfigs
     * @param Filesystem $fileSystem
     */
    public function __construct($rootDirectory, array $imageConfigs, FileSystem $fileSystem)
    {
        // @TODO: Replace with flysystem.
        $this->fileSystem = $fileSystem;

        $uploadsDirectory = $imageConfigs['image_directory'];
        $stylesDirectory = $imageConfigs['image_styles_directory'];
        $symfonyDirectory = substr($rootDirectory, 0, -4);

        $this->rootDirectory = $symfonyDirectory;
        $this->uploadsDirectory = $uploadsDirectory;
        $this->stylesDirectory = $uploadsDirectory . '/' . $stylesDirectory;
        $this->systemPath = $symfonyDirectory . '/web';
        $this->systemUploadPath = $this->systemPath . '/' . $this->uploadsDirectory;
        $this->systemStylesPath = $this->systemUploadPath . '/' . $stylesDirectory;

        // Set the temp directory if aws is enabled.
        if (!empty($imageConfigs['aws_s3'])) {
            if (!empty($imageConfigs['aws_s3']['enabled'])) {
                $this->awsConfig = $imageConfigs['aws_s3'];
                if (!empty($this->awsConfig['temp_directory'])) {
                    $this->tempDirectory = $symfonyDirectory . '/' . $this->awsConfig['temp_directory'];
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }

    /**
     * @return mixed
     */
    public function getWebStylesDirectory()
    {
        return $this->webStylesDirectory;
    }

    /**
     * @return mixed
     */
    public function getWebDirectory()
    {
        return $this->webDirectory;
    }

    /**
     * @return string
     */
    public function getSystemStylesPath()
    {
        return $this->systemStylesPath;
    }

    /**
     * @return string
     */
    public function getSystemUploadPath()
    {
        return $this->systemUploadPath;
    }

    /**
     * @return string
     */
    public function getSystemPath()
    {
        return $this->systemPath;
    }

    /**
     * @return string
     */
    public function getStylesDirectory()
    {
        return $this->stylesDirectory;
    }

    /**
     * @return mixed
     */
    public function getUploadsDirectory()
    {
        return $this->uploadsDirectory;
    }

    /**
     * @param $stylename
     * @return string
     */
    public function getStyleTree($stylename)
    {
        return $this->stylesDirectory . '/' . $stylename;
    }

    /**
     * @return string
     */
    public function getSystemUploadDirectory()
    {
        return $this->systemUploadPath;
    }

    /**
     * Check if a directory exists in the system and optionally create it if it doesn't
     *
     * @param      $directory
     * @param bool $create
     * @return bool
     */
    public function directoryExists($directory, $create = false)
    {
        if (file_exists($directory)) {
            return true;
        } elseif (!file_exists($directory) && $create) {
            return mkdir($directory, 0775, true);
        } else {
            return false;
        }
    }

    /**
     * @param $fileName
     * @return mixed
     */
    public function fileExists($fileName)
    {
        $originalPath = $this->getStorageDirectory('original', $fileName);

        return file_exists($originalPath);
    }

    /**
     * Deletes a directory and its contents or a file.
     *
     * @param $target
     * @return bool
     */
    public function deleteDirectory($target)
    {
        if (is_dir($target)) {
            $files = glob($target . '/*');
            foreach ($files as $file) {
                $this->deleteFile($file);
            }
            rmdir($target);
        } elseif (is_file($target)) {
            $this->deleteFile($target);
        }
    }

    /**
     *
     */
    public function clearTemporaryFiles()
    {
        $temp = $this->getTempDirectory();
        $uploadsFolder = $this->getUploadsDirectory();
        $this->deleteDirectory($temp . $uploadsFolder);
    }

    /**
     * @param $path
     * @return bool
     */
    public function deleteFile($path)
    {
        // If path exists delete the file.
        if ($this->directoryExists($path)) {
            unlink($path);
        }
    }

    /**
     * @param $filename
     * @return string
     */
    public function uploadedFilePath($filename)
    {
        return $this->systemUploadPath . '/' . $filename;
    }

    /**
     * @param $stylename
     * @return string
     */
    public function styleDirectoryPath($stylename)
    {
        return $this->systemStylesPath . '/' . $stylename;
    }

    /**
     * @param $filename
     * @return string
     */
    public function uploadedFileWebPath($filename)
    {
        return $this->uploadsDir . '/' . $filename;
    }

    /**
     * @param $stylename
     * @param $filename
     * @return string
     */
    public function styleFilePath($stylename, $filename)
    {
        return $this->styleDirectoryPath($stylename) . '/' . $filename;
    }

    /**
     * @param $path
     * @return string
     */
    public function getFilenameFromPath($path)
    {
        return basename($path);
    }

    /**
     * @param $stylename
     * @return string
     */
    public function styleWebPath($stylename)
    {
        $stylesDirectory = $this->stylesDirectory;
        $path = $stylesDirectory . '/' . $stylename;

        return $path;
    }

    /**
     * Returns the web accessible styled file path.
     *
     * @param $stylename
     * @return string
     */
    public function styledFileWebPath($stylename, $filename)
    {
        $stylesDirectory = $this->stylesDirectory;
        $path = $stylesDirectory . '/' . $stylename . '/' . $filename;

        return $path;
    }

    /**
     * Return the temporary directory if it has been set.
     *
     * @return bool|string
     */
    public function getTempDirectory()
    {
        if ($this->tempDirectory != null) {
            $this->directoryExists($this->tempDirectory, true);

            return $this->tempDirectory;
        } else {
            return sys_get_temp_dir();
        }
    }

    /**
     * Returns the appropriate local storage directory based on the current operation and config parameters.
     *
     * @param $operation
     * @return string
     */
    public function getStorageDirectory($operation = 'original', $filename = null, $stylename = null)
    {
        // If AWS is not enabled the directory is the image_directory directory
        if (!empty($this->awsConfig) && !empty($this->awsConfig['enabled'])) {
            $remote_file_policy = $this->awsConfig['remote_file_policy'];
            switch ($operation) {
                case 'original':
                    if ($remote_file_policy == 'ALL') {
                        $directory = $this->getTempDirectory();
                    } else {
                        $directory = $this->getSystemUploadDirectory();
                    }
                    break;
                case 'styled':
                    $directory = $this->getTempDirectory();
                    break;

                case 'temporary':
                    // Use the temporary directory.
                    $directory = $this->getTempDirectory();
                    break;

                default:
                    // Use the web directory by default.
                    $directory = $this->getSystemUploadDirectory();
                    break;
            }
        } else {
            if (empty($stylename)) {
                $directory = $this->getSystemUploadDirectory();
            } else {
                $directory = $this->getSystemPath() . '/';
            }
        }

        if ($stylename !== null) {
            $styleTree = $this->getStyleTree($stylename);
            $directory .= $styleTree . '/';
        }

        // Check the trailing slash.
        $directory = $this->trailingSlash($directory, true);

        // Add the filename on the end.
        if (!empty($filename)) {
            $directory .= $filename;
        }

        return $directory;
    }

    /**
     * Ensures that a string has a trailing slash or not.
     *
     * @param      $path
     * @param bool $slash
     * @return string
     */
    protected function trailingSlash($path, $slash = true)
    {
        $path = rtrim($path, '/');
        if ($slash) {
            $path .= '/';
        }

        return $path;
    }
}