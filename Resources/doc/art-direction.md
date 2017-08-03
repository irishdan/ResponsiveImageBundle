# Art Direction

## Crop Focus Widget

A custom formType is included which creates a 'crop and focus widget'. This widget allows users to select an area which is always cropped out of the image, and a focus area which is always included in the image.

<img src="../images/cropfocuswidget.jpg" />

The black area will always be cropped out for all image styles. The inner rectangle will always be fully included in styled images. 
There are some combinations of styles dimensions and focus dimensions where its just not possible include the whole focus rectangle. 
In this case the largest possible portion of the focus rectangle is included.

For example the image below has a crop and focus applied to it using the widget:

<img src="../images/gougou-widget.jpg" />

Images that have been cropped and scaled with various styles might look like this:

<img src="../images/gougou-focus-cropped.jpg" />

If no focus or cropped were applied the images would be like this:

<img src="../images/gougou-nocrop-focus.jpg" />