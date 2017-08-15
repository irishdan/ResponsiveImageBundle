# Configuration

All of the available configuration:

```
responsive_image:
    debug: FALSE                        # If true debug info is printed on generated images
    image_compression: 80               # The compression quality of the generated images
    image_directory: 'uploads/images'   # '%uploads_directory%' # The directory where uploaded images are saved
    image_styles_directory: 'styles'    # The directory within the uploads directory where generated images are saved
    image_entity_class: 'ResponsiveImageBundle\Entity\ResponsiveImage' # The image entity
    image_driver: gd                    # The php image library
    breakpoints:                        # Breakpoint definitions
        base: 'min-width: 0px'
        mobile: 'min-width: 300px'
        tablet: 'min-width: 480px'
        phablet: 'min-width: 768px'
        desktop: 'min-width: 1100px'
        tv: 'min-width: 1800px'
    image_styles:                       # Image style definitions
        full:                           # Style name
            effect: scale               # Style effect (scale or crop)
            height: 200
        thumb:
            effect: crop
            width: 180
            height: 180
        project_full:
            effect: scale
            width: 940
        project_thumb:
            effect: crop
            width: 540
            height: 400
    picture_sets:                       # Picture set definitions
        thumb_picture:
            base:                       # Breakpoint name
                effect: crop            # Style effect
                width: 300              
                height: 600
            mobile:
                effect: crop
                width: 480
                height: 300
            tablet:
                effect: crop
                width: 400
                height: 700
            phablet:
                effect: crop
                width: 180
                height: 380
            desktop: thumb              # To use a pre-defined style just use its name
            tv:
                effect: crop
                width: 300
                height: 500
    crop_focus_widget:                  # Crop focus widget settings
        include_js_css: TRUE        # If true widget js css is included in the field html. Otherwise add it manually.
        display_coordinates: TRUE   # Toggles between a text field or hidden field.
    aws_s3:
        enabled: FALSE
        remote_file_policy: STYLED_ONLY # STYLED_ONLY, ALL
        temp_directory: 'tmp/' # will be created within the symfony directory
        protocol: 'http'
        bucket: 'bucket_name'
        region: 'eu-west-1'
        version: 'latest'
        directory: 'directory_name'
        access_key_id: KEY_ID
        secret_access_key: ACCESS_SECRET
```