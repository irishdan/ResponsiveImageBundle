# Commands

The bundle includes two commands to help you get started. 
Both commands require the [symfony generator bundle](http://symfony.com/doc/master/bundles/SensioGeneratorBundle/index.html) to be enabled.

## Generate Image entity

Generates a Doctrine image [entity](entity.md) you can use and/or modify for your own needs. 

```php
php bin/console responsive_image:generate:entity
```

## Generate Image Entity crud

Generates CRUD for the entity above.

```php
php bin/console responsive_image:generate:crud
```

Generated code includes:
- [Uploading](uploading.md) of image file during Create action
- [CropFocus widget](art-direction.md) on image edit form
- [Event dispatching](events.md) to generate styled images in Edit action
