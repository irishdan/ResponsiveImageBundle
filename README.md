# ResponsiveImageBundle

[![Build Status](https://travis-ci.org/irishdan/ResponsiveImageBundle.svg?branch=master)](https://travis-ci.org/irishdan/ResponsiveImageBundle)

## Overview:

The ResponsiveImageBundle adds the ability to easily created styled responsive images (scaled, cropped, greyscale) in Symfony3.
This bundle allows for the management and presentation of images in various styles (scaled, cropped, grey scale etc etc)
and sizes.
Art directed responsive images, with picture or sizes/srcset, can also be generated.
Define break points, map them to images styles to create responsive images and css.

The bundle uses flysystem filesystem abstraction layer giving you control over where images are stored.
Eventas are used to dirvie the system, giving more flexibiltiy and extensibility, can control when images are generated, eg perhaps this should be queued
Images can be created from predefined styles or on the fly
supports retina 2x 1.5x images

ResponsiveImageBundle adds the ability to easily created styled responsive images (scaled, cropped, greyscale) in Symfony3.

## Features

- Image objects are stored via Doctrine ORM
- Handles uploading images to a configurable directory or an s3 bucket. 
- Allows for images styles to be defined in configuration.
- Allows breakpoints and pictures sets to be configured
- Handles creation of styled images on the fly (as they are viewed) or viw events listeners
- Includes a widget to define an images crop and focus areas giving art direction to styled images.

## Quick and basic setup

Out of the box, ResponsiveImage bundle should work with minimal configuration.

### 1: [Install](Resources/doc/installation.md) the bundle and [generate](Resources/doc/installation.md) a [ResponsiveImage entity](../blob/master/LICENSE) and it's CRUD.

```php
php bin/console responsive_image:generate:entity
php bin/console responsive_image:generate:crud
```
With the generated entity and CRUD you can now, create and upload images, apply 'Art Direction' to images.

### 2: Define some image styles in your [configuration](../blob/master/LICENSE) file. (Usually config.yml)

```yml
responsive_image:
    image_styles:
        groovy_thumbnail_style:
            effect: crop
            width: 180
            height: 180
        groovy_thumbnail_style_base:
            effect: scale
            width: 240
        groovy_thumbnail_style_mobile:
            effect: scale
            height:480
        groovy_thumbnail_style_mobile:
            effect: crop
            width: 200
            height: 300
        greyscale: true

```
You can now [render](../blob/master/LICENSE) a styled in your twig template like so:
 ```
    {{ styled_image(image, 'groovy_thumbnail_style') }}

 ```
### 3: Define some breakpoints and "picture sets"

```yml
responsive_image:
    breakpoints:
        base: 
            media_query: 'min-width: 0px'
        mobile: 
            media_query: 'min-width: 300px'
        desktop: 
            media_query: 'min-width: 1100px'
    groovey_picture_set:
        base: groovy_thumbnail_style_base
        mobile: groovy_thumbnail_style_mobile
        desktop: groovy_thumbnail_style_desktop

```
You can now render [responsive <picture> images]() using and even render [responsive background image css]() in twig templates

```
<head>
    {{ background_responsive_image(image, 'picture_set_name', '#header') }}
</head>
<body>
    {{ picture_image(image, 'groovey_picture_set') }}
</body>
```

### 4: Define some size sets

```yml
responsive_image:
    size_sets:
        blog_sizes:
            fallback: 'groovy_thumbnail_style_base'
            sizes:
                10vw:
                    breakpoint: 'mobile'
            srcsets: [ 'groovy_thumbnail_style_mobile', 'groovy_thumbnail_style_desktop' ]

```
You can now render [responsiveimages]() using srcset ans image sizes in twig templates.

```
    {{ sizes_image(image, 'blog_sizes') }}
```

## Documentation

- [Installation and Setup]()
- [Filesystems]()
- [Image entities]()
- [Generators]()
- [Image rendering]()
- [Art Direction]()
- [Configuration]()
- [Uploading]()
- [Urls]()
- [Events]()
    
## Attribution

- [Intervention](http://image.intervention.io/) is the standalone PHP Imagine Library is used by this bundle for image transformations
- [OneupFlysystemBundle](https://github.com/1up-lab/OneupFlysystemBundle) which used [Flysystem](https://flysystem.thephpleague.com/) filesystem astraction library, is required by this bundle
- The CropFocus art direction widget javascript was created by following this [TutsPlus tutorial](http://code.tutsplus.com/tutorials/how-to-create-a-jquery-image-cropping-plugin-from-scratch-part-i--net-20994)