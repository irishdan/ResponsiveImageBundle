<?php

namespace ResponsiveImageBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ResponsiveImageBundle\Utils\ResponsiveImageRepositoryInterface;

class ImageRepository extends EntityRepository implements ResponsiveImageRepositoryInterface
{
}
