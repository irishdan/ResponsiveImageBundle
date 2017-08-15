<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates a Notification inside a bundle.
 */
class ImageEntityGenerator extends Generator
{
    /** @var Filesystem */
    private $filesystem;
    private $overwrite;

    /**
     * NotificationGenerator constructor.
     *
     * @param Filesystem $filesystem
     * @param bool       $overwrite
     */
    public function __construct(Filesystem $filesystem, $overwrite = false)
    {
        $this->filesystem = $filesystem;
        $this->overwrite  = $overwrite;
    }

    /**
     * @param BundleInterface $bundle
     * @param                 $name
     */
    public function generate(BundleInterface $bundle, $name)
    {
        $bundleDir = $bundle->getPath();
        $imageDir  = $bundleDir . '/Entity';
        self::mkdir($imageDir);

        $imageClassName = $name;
        $imageFile      = $imageDir . '/' . $imageClassName . '.php';

        $parameters = [
            'namespace'  => $bundle->getNamespace(),
            'class_name' => $imageClassName,
            'name'       => $name,
            'table'      => strtolower($name), // @TODO: Use the tablize function
        ];

        // Build an array of files to be created
        $filesArray   = [];
        $filesArray[] = [
            'entity/Image.php.twig',
            $imageFile,
            $parameters,
        ];

        if (!empty($filesArray)) {
            $this->generateFiles($filesArray);
        }
    }

    /**
     * @param array $files
     */
    protected function generateFiles(array $files)
    {
        // Set generator to look in correct directory for notifications template.
        $path = __DIR__ . '/../Resources/skeleton';
        $this->setSkeletonDirs([$path]);

        // Check that each file does not already exist
        foreach ($files as $file) {
            if ($this->filesystem->exists($file[1]) && empty($this->overwrite)) {
                throw new \RuntimeException(sprintf('"%s" already exists', $file[1]));
            }
        }

        // Generate each file
        foreach ($files as $file) {
            // Template, destination, params
            $this->renderFile($file[0], $file[1], $file[2]);
        }
    }
}