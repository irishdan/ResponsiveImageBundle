# Rendering images

The Twig/ResponsiveImageExtension twig extension provides all of the functions needed to render images  in your twig templates.

## Rendering a styled image

The render a styled image use:
```twig
{{ styled_image(image, 'groovy_thumbnail_style') }}
```
The this will render, using the img.html.twig file, as a normal <img> tag.
```html
<img src="/images/styles/groovy_thumbnail_style/sample_167576.jpg" alt ="Your alt text" title="Image title" width="180" height="180" />

```

## Render responsive image with <picture> tag

To generate a responsive image use:
```twig
{{ picture_image(image, 'groovey_picture_set') }}

```
The 

```html
<picture>
    <source srcset="/images/styles/groovy_thumbnail_style_base/sample_167576.jpg" media="(min-width: 0px)">
    <source srcset="/images/styles/groovy_thumbnail_style_mobile/sample_167576.jpg" media="(min-width: 300px)">
    <source srcset="/images/styles/thumb_picture-desktop/sample_167576.jpg" media="(min-width: 1100px)">
    <img srcset="/images/styles/groovy_thumbnail_style/sample_167576.jpg" alt="Your alt text" title="Image title" width="180" height="180">
</picture>
```

### Retina/High definition images

High definition images are supported and are automatically rendering if a pixel density style exists.

First define retina styles. 
A retina style has a pixel density suffix eg: 

```yml
responsive_image:
    image_styles:
        groovy_thumbnail_style_base:
            effect: scale
            width: 240
        groovy_thumbnail_style_base_1.5x:
            effect: scale
            height: 360
        groovy_thumbnail_style_base_2x:
            effect: scale
            height: 480
```
When <picture> tags are being generated, each style is checked to see if it has any corresponding retina styles.
If they exist they are included in the rendered html. 

```html
<picture>
    <source srcset="/images/styles/groovy_thumbnail_style_base/sample_167576.jpg" media="(min-width: 0px) and ()">
    <source srcset="/images/styles/groovy_thumbnail_style_mobile/sample_167576.jpg" media="(min-width: 300px)">
    <source srcset="/images/styles/thumb_picture-desktop/sample_167576.jpg" media="(min-width: 1100px)">
    <img srcset="/images/styles/groovy_thumbnail_style/sample_167576.jpg" alt="Your alt text" title="Image title" width="180" height="180">
</picture>
```

## Render responsive image with <img> tag, sizes and srcset

## Render background css

## Overriding the html