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

use Symfony\Component\Cache\Simple\FilesystemCache;


class ImageEntityNameResolver
{
    const CACHE_KEY = 'responsive_image.image_entity';
    protected $className = null;
    protected $classLocator;
    protected $cache;
    protected $imageEntityParameter;

    public function __construct(ImageEntityClassLocator $classLocator, $imageEntityParameter = '')
    {
        $this->classLocator         = $classLocator;
        $this->imageEntityParameter = $imageEntityParameter;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        if (!empty($this->className)) {
            return $this->className;
        }

        // Use class name if its been set
        if (empty($this->className)) {
            if (!empty($imageEntityParameter)) {
                $this->className = $imageEntityParameter;

                return $this->className;
            }
        }

        // Use the cached value.
        if (empty($this->cache)) {
            $this->cache = new FilesystemCache();
        }
        if (!$this->cache->has(self::CACHE_KEY)) {
            $classname = $this->classLocator->getClassName();
            $this->cache->set(self::CACHE_KEY, $classname);
        }
        else {
            $classname = $this->cache->get(self::CACHE_KEY);
        }

        if (!empty($classname)) {
            $this->className = $classname;

            return $this->className;
        }

        return false;
    }
}
