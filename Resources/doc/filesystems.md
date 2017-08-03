# Filesystems

Thhe bundle uses the OneUpFlysystem

```yml
oneup_flysystem:
    adapters:
        nomad.local:
            local:
                directory: '%kernel.root_dir%/../web/%responsive_image.image_directory%' # %kernel.root_dir%/cache/
        nomad.temp:
            local:
                directory: '%kernel.root_dir%/../var/cache/resp_img'
        nomad.aws:
            awss3v3:
                client: nomad.s3_client
                bucket: 'danbyrnebucket'
                prefix: 'oneup'
                options: { visibility: 'public-read', protocol: 'https' }
    filesystems:
        nomad.local:
            adapter: nomad.local
            alias: responsive_image_filesystem
        nomad.temp:
            adapter: nomad.temp
            alias: responsive_image_temp
        nomad.aws:
            adapter: nomad.aws
            # alias: responsive_image_filesystem
```
