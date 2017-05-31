<?php

namespace ResponsiveImageBundle\Utils;

use Doctrine\ORM\EntityManager;

/**
 * Class FileToObject
 *
 * @package ResponsiveImageBundle\Utils
 */
class FileToObject
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * FileToObject constructor.
     *
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Fetches and returns the image object based on the file name.
     *
     * @param $filename
     * @param $entityClassName
     * @return mixed
     */
    public function getObjectFromFilename($filename, $entityClassName)
    {
        /** @var ResponsiveImageRepositoryInterface $repository */
        $repository = $this->manager->getRepository($entityClassName);

        if ($repository instanceof ResponsiveImageRepositoryInterface) {
            $fileObject = $repository->findImageFromFilename($filename);

            return $fileObject;
        }

        return null;
    }
}