<?php

namespace IrishDan\ResponsiveImageBundle\Repository;

use Doctrine\ORM\EntityRepository;
use IrishDan\ResponsiveImageBundle\ResponsiveImageRepositoryInterface;

class ImageRepository extends EntityRepository implements ResponsiveImageRepositoryInterface
{
    public function findImageFromFilename($filename)
    {
        return $this->findOneByPath($filename);
    }
}
