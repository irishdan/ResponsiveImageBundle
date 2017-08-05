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

use Symfony\Component\HttpKernel\Config\FileLocator;

class ImageEntityClassLocator
{
    const RESPONSIVE_IMAGE_INTERFACE = 'IrishDan\ResponsiveImageBundle\ResponsiveImageInterface';
    protected $className = null;
    protected $fileLocator;

    public function __construct($bundles, FileLocator $fileLocator)
    {
        // @TODO: Perhaps use image entity parameter if its set just return that.

        $this->fileLocator = $fileLocator;

        // The aim is to find entity class which implements ResponsiveImageInterface

        // If doctrine enabled, use declared entities

        // Scan Bundle directories for entity directory
        if (empty($this->className)) {
            foreach ($bundles as $key => $namespace) {

                $path = $this->fileLocator->locate('@' . $key);
                $path .= 'Entity';
                if (file_exists($path)) {
                    // Remove the final part of the namespace
                    $namespaceParts = explode('\\', $namespace);
                    array_pop($namespaceParts);
                    $namespaceParts[] = 'Entity';
                    $namespace        = implode('\\', $namespaceParts);

                    $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
                    $phpFiles = new \RegexIterator($allFiles, '/\.php$/');
                    foreach ($phpFiles as $phpFile) {
                        $fileName  = $phpFile->getFileName();
                        $className = substr($fileName, 0, -4);
                        $FQCN      = $namespace . '\\' . $className;

                        // Try to load thew file as a class and determine if it implements the interface
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
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }
}
