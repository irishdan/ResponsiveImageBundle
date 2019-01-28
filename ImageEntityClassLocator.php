<?php

namespace IrishDan\ResponsiveImageBundle;

use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Class ImageEntityClassLocator
 *
 * The aim is to find entity class which implements ResponsiveImageInterface
 *
 * @property array bundles
 * @package IrishDan\ResponsiveImageBundle
 */
class ImageEntityClassLocator
{
    const RESPONSIVE_IMAGE_INTERFACE = 'IrishDan\ResponsiveImageBundle\ResponsiveImageInterface';
    /**
     * @var null|string
     */
    protected $className = null;
    /**
     * @var FileLocator
     */
    protected $fileLocator;
    /**
     * @var string
     */
    protected $entityDirectory = 'Entity';

    /**
     * ImageEntityClassLocator constructor.
     *
     * @param             $bundles
     * @param FileLocator $fileLocator
     */
    public function __construct($bundles = [], FileLocator $fileLocator)
    {
        $this->bundles     = $bundles;
        $this->fileLocator = $fileLocator;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        // Scan Bundle directories for entity directory
        if (empty($this->className)) {
            foreach ($this->bundles as $key => $namespace) {

                $path = $this->fileLocator->locate('@' . $key);
                $path .= $this->entityDirectory;

                if (file_exists($path)) {
                    // Remove the final part of the namespace
                    $namespaceParts = explode('\\', $namespace);
                    array_pop($namespaceParts);
                    $namespaceParts[] = $this->entityDirectory;
                    $namespace        = implode('\\', $namespaceParts);

                    // switch any /'s into \'s for namespaces
                    $namespace = str_replace('/', '\\', $namespace);

                    $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
                    $phpFiles = new \RegexIterator($allFiles, '/\.php$/');
                    foreach ($phpFiles as $phpFile) {
                        $fileName  = $phpFile->getFileName();
                        $className = substr($fileName, 0, -4);
                        $FQCN      = $namespace . '\\' . $className;

                        // Try to load the file as a class and determine if it implements the interface
                        try {
                            $reflect = new \ReflectionClass($FQCN);
                            if ($reflect->implementsInterface(self::RESPONSIVE_IMAGE_INTERFACE)) {
                                $this->className = $FQCN;
                                break;
                            }
                        } catch (\ReflectionException $e) {
                            // No action required
                        }
                    }
                }
            }
        }

        return $this->className;
    }

    /**
     * @param string $entityDirectory
     */
    public function setEntityDirectory($entityDirectory)
    {
        $this->entityDirectory = $entityDirectory;
    }
}
