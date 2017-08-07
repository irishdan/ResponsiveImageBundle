<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\File;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use IrishDan\ResponsiveImageBundle\ImageEntityNameResolver;
use IrishDan\ResponsiveImageBundle\ResponsiveImageRepositoryInterface;

/**
 * Class FileToObject
 *
 * @package ResponsiveImageBundle
 */
class FileToObject
{
    /**
     * @var EntityManager
     */
    private $manager;
    private $entityClassName;

    public function __construct(EntityManagerInterface $manager, ImageEntityNameResolver $nameResolver)
    {
        $this->manager = $manager;

        $entityClassName       = $nameResolver->getClassName();
        $this->entityClassName = $entityClassName;
    }

    /**
     * Fetches and returns the image object based on the file name.
     *
     * @param $filename
     *
     * @return mixed
     * @internal param $entityClassName
     */
    public function getObjectFromFilename($filename)
    {
        /** @var ResponsiveImageRepositoryInterface $repository */
        $repository = $this->manager->getRepository($this->entityClassName);

        if ($repository instanceof ResponsiveImageRepositoryInterface) {
            $fileObject = $repository->findImageFromFilename($filename);

            return $fileObject;
        }

        return null;
    }
}