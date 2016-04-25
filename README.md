# ResponsiveImageBundle

For those familiar with Drupal, this bundle combines Drupal's image styles, picture module's and image field focus module's functionality 
into a single bundle.

For those not familiar with Drupal, this bundle allows you to easily configure image formats. For example an image format called 'thumbnail'
can easily be created in config.yml like so: 

```
thumbnail:
    effect: crop
    width: 180
    height: 180
```

Currently scale and crop are supported.

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



Image styles, uploads directory, breakpoints, and picture sets are defined in config.yml.

A custom (widget) formType is included to allow users to select an area which is always cropped out of the image, and a focus area which is always included in the image.

Its also handles the uploading of images.

1: Installation
---------------------------

Clone the repo to your src directory.

Step 2: Enable the Bundle
-------------------------


3: Configuration
---------------------------


4: Usage
---------------------------



 