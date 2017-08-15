# Filesystems

This bundle reuires the [OneUpFlysystem bundle]() which uses [flysystem]() library under the hood.

This allows images to be stored on muliplte filesystems which can be local or remote eg on amazons S3

## What is a filesystem?

A filesystem is simply a place to store files. The place could be Local (on the same server as your symfony project)
or remote, perhaps on an amazon S3 bucket.

Flysystem lets us define multiple filesystems with handy methods for access files on that filesystem.

## Configuration

Basically a filesystem needs an adapter.

For more 

```yml
oneup_flysystem:
    adapters:
        image.local:
            local:
                directory: '%kernel.root_dir%/../web/%responsive_image.image_directory%' # %kernel.root_dir%/cache/
        image.temp:
            local:
                directory: '%kernel.root_dir%/../var/cache/resp_img'
        image.aws:
            awss3v3:
                client: nomad.s3_client
                bucket: 'danbyrnebucket'
                prefix: 'oneup'
                options: { visibility: 'public-read', protocol: 'https' }
    filesystems:
        nomad.local:
            adapter: image.local
            alias: responsive_image_filesystem
        nomad.temp:
            adapter: image.temp
            alias: responsive_image_temp
        nomad.aws:
            adapter: image.aws
            # alias: responsive_image_filesystem
```
