# ResponsiveImageBundle

For those familiar with Drupal, this bundle combines Drupal's image styles, picture module's and image field focus module's functionality 
into a single bundle.

For those not familiar with Drupal, this bundle allows you to easily configure image formats and generate styled images and responsive images. For example an image format called 'thumbnail'
can easily be created in config.yml like so: 

```
thumbnail:
    effect: crop
    width: 180
    height: 180
```

Image styles can be grouped into 'picture sets', which are used to generate responsive images using the html5 '<picture>' element.
A picture set is a group of image styles with a breakpoint associated with each style.

First breakpoints are defined like so:

```
breakpoints:
    base: 'min-width: 0px'
    mobile: 'min-width: 300px'
    phablet: 'min-width: 480px'
    tablet: 'min-width: 768px'
    desktop: 'min-width: 1100px'
    tv: 'min-width: 1800px'
```

Then picture sets are defined using some or all of the defined breakpoints with defined styles or new styles:

```
thumb_picture:
    base:
        effect: crop
        width: 300
        height: 600
    mobile:
        effect: crop
        width: 480
        height: 300
    phablet:
        effect: crop
        width: 400
        height: 700
    tablet:
        effect: crop
        width: 180
        height: 380
    # to use a pre-defined style just use its name as below
    desktop: thumb
    tv:
        effect: crop
        width: 300
        height: 500
```
The generated picture element would be like:

```
<picture>
    <source srcset="/uploads/documents/styles/thumb_picture-tv/example.jpg" media="(min-width: 1800px)">
    <source srcset="/uploads/documents/styles/thumb/example.jpg" media="(min-width: 1100px)">
    <source srcset="/uploads/documents/styles/thumb_picture-phablet/example.jpg" media="(min-width: 768px)">
    <source srcset="/uploads/documents/styles/thumb_picture-tablet/example.jpg" media="(min-width: 480px)">
    <source srcset="/uploads/documents/styles/thumb_picture-mobile/example.jpg" media="(min-width: 300px)">
    <source srcset="/uploads/documents/styles/thumb_picture-base/example.jpg" media="(min-width: 0px)">
    <img srcset="/uploads/documents/styles/thumb_picture-base/example.jpg">
</picture>
```

A custom formType is included which creates a 'crop and focus widget'. This widget allows users to select an area which is always cropped out of the image, and a focus area which is always included in the image.

<img src="/docs/images/cropfocuswidget.jpg" />

The black area will always be cropped out for all image styles. The inner rectangle will always be fully included in styled images. 
There are some combinations of styles dimensions and focus dimensions where its just not possible include the whole focus rectangle. 
In this case the largest possible portion of the focus rectangle is included.


1: Installation
---------------------------

Clone the bundle repo to your src directory.
```
git clone https://github.com/irishdan/ResponsiveImageBundle.git
```

The bundle utilises the intervention image library http://image.intervention.io/. Add it as a requirement to composer.json
```
{
    "require": {
        "intervention/image": "^2.3",
    }
}
```

To use AWS the, the AWS PHP sdk is also required.

```
{
    "require": {
        "aws/aws-sdk-php": "^3.18",
    }
}
```

2: Enable the Bundle
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


3: Configuration
---------------------------

All of the available configuration:
```
responsive_image:
    debug: FALSE                        # If true debug info is printed on generated images
    image_compression: 80               # The compression quality of the generated images
    image_directory:                    # '%uploads_directory%' # The directory where uploaded images are saved
    image_styles_directory: 'styles'    # The directory within the uploads directory where generated images are saved
    image_entity_class: [ 'ResponsiveImageBundle:Image' ] # The image entity
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
            enabled: TRUE               # Enable or disable AWS support
            keep_local_files: NONE      # NONE ALL ORIGINAL
            move_to_bucket: ALL         # ALL STYLED_ONLY
            temp_directory: '/tmp'      # NULL
            protocol: 'http'            # The protocol used for S3 images 'http' or 'https'
            bucket: 'bucketname'        # The S3 bucket name
            region: 'eu-west-1'         # The S3 region
            version: 'latest'           # The sdk version
            directory: 'buck-folder'    # The directory top store images on the S3 bucket
            access_key_id: KEY_ID
            secret_access_key: ACCESS_SECRET
```

Most Browser do not yet support the <picture> tag. Therefore a polyfil is needed. This is available here http://scottjehl.github.io/picturefill
and is also included in the bundle at Resources/public/js/vendor/picturefill.js.


4:Usage
---------------------------

For image objects you can use your own entity, as long as it implements the ResponsiveImageInterface
```
ResponsiveImageBundle\Utils\ResponsiveImageInterface.
```
There's also a working image object included, Image.php, that you can use directly or modify.
```
ResponsiveImageBundle\Entity\Image.php
```

When creating a new image the responsive_image.uploader service handles uploading and saving the image file to the server.
```
$this->get('responsive_image.uploader')->upload($image);
```

To generate a styled image tag, simply set the image style using the responsive_image.style_manager service.
```
$this->get('responsive_image.style_manager')->setImageStyle($image, 'thumb');
```
Or you can simply use the setStyle method on the $image object directly. In your template file, printing invokes the _toString method to generate the img tag.

```
{{ image }}
```

To generate a picture element the style manager service is used.
```
$this->get('responsive_image.style_manager')->generatePictureImage($image, 'thumb_picture');
```
Again, printing the object will generate the picture element html. If the style and the picture properties are both set the picture takes precedence.

After editing an image it may be useful to delete all of the styled images so that they will be regenerated.
In your CRUD logic:
```
$this->get('responsive_image.style_manager')->deleteImageFile($image->getPath());
```

To set the crop and focus areas of an image in your edit form use the the CropFocusType in the form builder.
```
$form->add('crop_coordinates', CropFocusType::class, array(
    'data' => $image
));
```