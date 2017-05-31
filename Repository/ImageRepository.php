<?php

namespace ResponsiveImageBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ResponsiveImageBundle\Utils\ResponsiveImageRepositoryInterface;

class ImageRepository extends EntityRepository implements ResponsiveImageRepositoryInterface
{
    public function findImageFromFilename($filename)
    {
        return $this->findOneByPath($filename);
    }
}
