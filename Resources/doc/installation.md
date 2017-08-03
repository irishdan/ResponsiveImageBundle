# Installation and Setup
---------------------------

With composer
```
composer require irishdan/responsive-image-bundle
```

3: Enable the Bundle
-------------------------

Enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new ResponsiveImageBundle\ResponsiveImageBundle(),
        );

        // ...
    }

    // ...
}
```

Import the service definitions in to your config.yml file.
```php
    - { resource: "@ResponsiveImageBundle/Resources/config/services.yml" }
```

Import the routing in to routing.yml file.
```php
responsive_image:
    resource: "@ResponsiveImageBundle/Resources/config/routing.yml"
    prefix:   /
```
