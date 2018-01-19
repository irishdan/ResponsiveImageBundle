# Filesystems

This bundle requires the [OneUpFlysystem bundle](https://github.com/1up-lab/OneupFlysystemBundle) which uses [FlySystem](http://flysystem.thephpleague.com/) library under the hood.

This allows images to be stored on multiple filesystems which can be local or remote eg on amazons S3

## What is a filesystem?

A filesystem is simply a place to store files. The place could be Local (on the same server as your symfony project)
or remote, perhaps on an amazon S3 bucket.

[FlySystem](http://flysystem.thephpleague.com/) lets us define multiple filesystems with handy methods for access files on that filesystem.
Each Filesystem needs an adapter. 
[FlySystem](http://flysystem.thephpleague.com/) Includes adapters for most of the common storage solutions.

## Configuration

Currently the system requires a temporary local filesystem and a default filesystem to be configured.

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

### Switching Filesystems at run time