# Rendering images

1: Twiggy wiggy
---------------------------

## Render styled image

## Render responsive image with picture tag

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

### Retina images

## Render responsive image with img tag, sixes and srcset

## Render background css

## Overriding the html