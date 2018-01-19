# Configuration

The bundle includes default configuration for basic usage and getting started.
The system needs two filesystems configured to function, see [generated crud](filesystems.md) for details.

A sample File system configuration:

```yml
oneup_flysystem:
    adapters:
        images.local:
            local:
                directory: '%kernel.root_dir%/../web/%responsive_image.image_directory%' # %kernel.root_dir%/cache/
        images.temp:
            local:
                directory: '%kernel.root_dir%/../var/cache/resp_img'
        images.aws:
            awss3v3:
                client: images.s3_client
                bucket: 'your_bucket'
                prefix: 'oneup'
                options: { visibility: 'public-read', protocol: 'https' }
    filesystems:
        images.local:
            adapter: images.local
            alias: responsive_image_filesystem
        images.temp:
            adapter: images.temp
            alias: responsive_image_temp
        images.aws:
            adapter: images.aws

```
Sample configuration for the bundle:

```
#
# This is the default configuration for this bundle.
#
responsive_image:
    cache_bust: true
    image_compression: 90
    # image_entity_class: 'AppBundle\Entity\ResponsiveImage'
    image_directory: 'test/images'
    image_driver: 'gd'
    image_styles_directory: 'styles'
    crop_focus_widget:
        include_js_css: TRUE
        display_coordinates: TRUE
# Breakpoints
    breakpoints:
        base:
            media_query: 'min-width: 0px'
        desktop:
            media_query: 'min-width: 1100px'
        tv:
            media_query: 'min-width: 1800px'
        size_mq:
            media_query: 'min-width: 36em'
# Image styles
    image_styles:
        thumb:
            effect: 'crop'
            width: 180
            height: 180
        thumb_2x:
            effect: 'crop'
            width: 360
            height: 360
        picture_thumb_base:
            effect:  'crop'
            width:  300
            height:  600
        picture_thumb_tv:
            effect:  'scale'
            width:  800
            height:  600
            greyscale: true
# Sizesets
    size_sets:
        blog_sizes:
            fallback: 'thumb'
            sizes:
                10vw:
                    breakpoint: 'size_mq'
            srcsets: [ 'thumb', 'picture_thumb_base', 'picture_thumb_tv' ]
# Picture sets
    picture_sets:
        thumb_picture:
            fallback: 'thumb'
            sources:
                base: 'picture_thumb_base'
                desktop: 'thumb'
                tv: 'picture_thumb_tv'
        blog_picture:
            fallback: 'thumb'
            sources:
                base: 'picture_thumb_base'
                desktop: 'thumb'
                tv: 'picture_thumb_tv'
```