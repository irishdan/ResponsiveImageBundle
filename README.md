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

## Quick and basic setup

### 1: [Install](../blob/master/LICENSE) the bundle and [Install](../blob/master/LICENSE) a [ResponsiveImage entity](../blob/master/LICENSE) and it's CRUD.

```php
php bin/console responsive_image:generate:entity
php bin/console responsive_image:generate:crud
```
With the generated entity and CRUD you can now, create and upload images, apply 'Art Direction' to images.

### 2: Define some image styles in your [configuration] file. (Usually config.yml)

```yml
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
You can now render a styled in your twig template like so:
 ```
 {{ styled_image(image, 'groovy_thumbnail_style') }}

 ```
### 3: Define some breakpoints and "picture sets"

```yml
breakpoints:
    base: 'min-width: 0px'
    mobile: 'min-width: 300px'
    desktop: 'min-width: 1100px'
groovey_picture_set:
    base: groovy_thumbnail_style_base
    mobile: groovy_thumbnail_style_mobile
    desktop: groovy_thumbnail_style_desktop

```
You can now render [responsive <picture> images] using and even render [responsive background image css]

### 4: Define some size sets


 7 - Display the picture
 ```
 {{ picture_image(image, 'groovey_picture_set') }}

 ```
 8 - Create a srssize set:

 ```
 ```

 9 - display

 10 - create a background image



## Documentation
- Setup
    - out of the box, local file system, generate entity, doctrine or no!!

- Styled & responsive images
    what are they, html structure, twig functions , overriding

- Filesystems
    out of the box/default, local, remote

- CRUD

- Uploading
    - form example
- Urls
    - encoding, methods

- Art Direction
    - crop focus rectangles, for widget, example
- Events
    - all powered by events

Attribution

- The standalone PHP Imagine Library is used by this bundle for image transformations.
- Flysystem..
- OneupFlysystemBundle
- Crop focus widget...



Features include:

- Image objects are stored via Doctrine ORM
- Handles uploading images to a configurable directory or an s3 bucket. 
- Allows for images styles to be defined in configuration.
- Allows breakpoints and pictures sets to be configured
- Handles creation of styled images on the fly (as they are viewed) or viw events listeners
- Includes a widget to define an images crop and focus areas giving art direction to styled images.

Image styles can be defined easily:

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
        greyscale: TRUE
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
    <img srcset="/uploads/documents/styles/thumb_picture-base/example.jpg" alt="Your alt text" title="Your title">
</picture>
```

4: Configuration
---------------------------

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

Most Browsers do not yet support the <picture> tag. Therefore a polyfil is needed. This is available here http://scottjehl.github.io/picturefill
and is also included in the bundle at Resources/public/js/vendor/picturefill.js.


5: Usage
---------------------------

Generator is now included

For image objects you can use your own entity, as long as it implements the ResponsiveImageInterface
```
ResponsiveImageBundle\ResponsiveImageInterface.
```
don't for get jquery for the edit widget

```
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
```

When creating a new image the responsive_image.uploader service handles uploading and saving the image file to the server.
```
$this->get('responsive_image.uploader')->upload($image);
```

for exmaple:
```
class ResponsiveImageController extends Controller
{
    ...
    ...

    /**
     * Creates a new responsiveImage entity.
     *
     * @Route("/new", name="image_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $responsiveImage = new Responsiveimage();
        $form = $this->createForm('ResponsiveImageBundle\Form\ResponsiveImageType', $responsiveImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('responsive_image.uploader')->upload($responsiveImage);

            $em = $this->getDoctrine()->getManager();
            $em->persist($responsiveImage);
            $em->flush();

            return $this->redirectToRoute('image_show', ['id' => $responsiveImage->getId()]);
        }

        return $this->render('responsiveimage/new.html.twig', [
            'responsiveImage' => $responsiveImage,
            'form' => $form->createView(),
        ]);
    }

```

The easiest way tot generate styled images is to use the twig extensions in your templates.
```
{{ styled_image(image, 'image_style_name') }}
{{ picture_image(image, 'picture_set_name') }}
```
You can also generate background image css with media queries for each brak point in a picture set.
```
<style>
   {{ background_responsive_image(image, 'picture_set_name', '#header') }}
</style>
```

To generate a styled image tag elsewhere, simply set the image style using the responsive_image.style_manager service.
```
$this->get('responsive_image')->setImageStyle($image, 'image_style_name');
```

To generate a picture element the style manager service is used.
```
$this->get('responsive_image')->setPictureSet($image, 'picture_set_name');
```
Again, printing the object will generate the picture element html. If the style and the picture properties are both set the picture takes precedence.

To set the crop and focus areas of an image in your edit form use the the CropFocusType in the form builder.
```
$form->add('crop_coordinates', CropFocusType::class, array(
    'data' => $image
));
```

If not using AWS are generated on the fly if an image url is visited the and the styled file is not present. Visiting the url below will generate the image the first time.
On subsequent visits the file is served.

www.example.com/uploads/images/styles/thumb/example.jpg

note: 'uploads/images' can be set as image_directory in your configuration and 'styles' can be set as image_styles_directory in your configuration.

Events listeners are provided to allow image generation with your CRUD logic. 
For example if you wanted to generate all of the styled images after an edit form is submitted, you could use an event dispatcher:

```
// Dispatch style generate event to the listeners.
$event = new ImageEvent($image);
$this->dispatcher->dispatch(
    ImageEvents::IMAGE_GENERATE_STYLED,
    $event
);
```

Available listeners are:
```
// Generate styled images.
IMAGE_GENERATE_STYLED = 'responsive_image.image_generate_styled';

// Delete original and styled images for a given image object.
IMAGE_DELETE_ALL = 'responsive_image.image_delete_all';

//Delete original and styled images for a given image object.
IMAGE_DELETE_ORIGINAL = 'responsive_image.image_delete_original';

// Delete styled images for a given image object.
IMAGE_DELETE_STYLED = 'responsive_image.image_delete_styled';

```