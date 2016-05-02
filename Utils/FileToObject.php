<?php

namespace ResponsiveImageBundle\Utils;

use Doctrine\ORM\EntityManager;

/**
 * Class FileToObject
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
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager) {
        $this->manager = $manager;
    }

    /**
     * Fetches and returns the image object based on the file name.
     *
     * @param $filename
     * @param $entityClassName
     * @param string $property
     * @return mixed
     */
    public function getObjectFromFilename($filename, $entityClassName, $property = 'path') {
        $methodName = 'findOneBy' . ucfirst($property);

        $em = $this->manager;
        $fileObject = $em->getRepository($entityClassName)->{$methodName}($filename);

        return $fileObject;
    }
}