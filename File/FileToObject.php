<?php

namespace IrishDan\ResponsiveImageBundle\File;

use Doctrine\ORM\EntityManager;
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

    /**
     * FileToObject constructor.
     *
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager, $entityClassName)
    {
        $this->manager = $manager;
        $this->entityClassName = $entityClassName;
    }

    /**
     * Fetches and returns the image object based on the file name.
     *
     * @param $filename
     * @param $entityClassName
     * @return mixed
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