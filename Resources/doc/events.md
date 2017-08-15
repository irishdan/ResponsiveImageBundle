# Events

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