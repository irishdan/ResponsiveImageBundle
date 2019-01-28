<?php

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
        $this->classLocator = $classLocator;
        $this->imageEntityParameter = $imageEntityParameter;
    }

    public function classExists()
    {
        $class = $this->getClassName();
        $locatedClass = $this->classLocator->getClassName();

        return ($class === $locatedClass) ? true : false;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        // If it's set here just return that.
        if (!empty($this->className)) {
            return $this->className;
        }

        // Use class name parameter if its been set
        if (!empty($this->imageEntityParameter)) {
            $this->className = $this->imageEntityParameter;

            return $this->className;
        }

        // Load it from cached data if it exists
        // as a last resort use the Class locator service.
        if (empty($this->cache)) {
            $this->cache = new FilesystemCache();
        }

        $cached = $this->cache->has(self::CACHE_KEY);
        if ($cached) {
            $classname = $this->cache->get(self::CACHE_KEY);
        }

        // LAt resort use the Class locator service
        if (empty($classname)) {
            $classname = $this->classLocator->getClassName();
        }

        // Set the value in as a property in this class
        // and set as a cached value for next time.
        if (!empty($classname)) {
            $this->className = $classname;

            if (!$cached) {
                // @TODO: Should set cache expiration
                $this->cache->set(self::CACHE_KEY, $classname);
            }

            return $this->className;
        }

        return false;
    }

    public function getCache()
    {
        return $this->cache;
    }
}
