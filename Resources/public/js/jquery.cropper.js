/*
 A jquery widget for selecting area and a focus area of an image.
 It's based of the widget here:

 http://code.tutsplus.com/tutorials/how-to-create-a-jquery-image-cropping-plugin-from-scratch-part-i--net-20994
 http://code.tutsplus.com/tutorials/how-to-create-a-jquery-image-cropping-plugin-from-scratch-part-ii--net-21092
 */
(function ($) {
    $.imageCrop = function (object, customOptions) {
        cropper = {
            defaultOptions: {
                aspectRatio: 0,
                displaySizeHint: false,
                minSelect: [0, 0],
                minSize: [0, 0],
                maxSize: [0, 0],
                outlineOpacity: 0.5,
                overlayOpacity: 0.5,
                previewBoundary: 90,
                previewFadeOnBlur: 1,
                previewFadeOnFocus: 0.35,
                selectionPosition: [0, 0],
                selectionWidth: 0,
                selectionHeight: 0,
                focusPosition: [0, 0],
                focusWidth: 0,
                focusHeight: 0,
                cropInputselector: '.crop-focus-coordinates input',
                onChange: function () {
                },
                onSelect: function () {
                }
            },

            $trigger: '',
            $outline: '',
            $overlay: '',
            $selection: '',
            $image: '',
            $holder: '',
            $nwCropResizer: '',
            $neCropResizer: '',
            $swCropResizer: '',
            $seCropResizer: '',
            $nwFocusResizer: '',
            $neFocusResizer: '',
            $swFocusResizer: '',
            $seFocusResizer: '',
            $focusDestroyer: '',

            // Options array.
            options: [],

            // Initialize global variables.
            naturalWidth: 0,
            naturalHeight: 0,
            resizeHorizontally: true,
            resizeVertically: true,
            selectionExists: '',
            selectionOffset: [0, 0],
            selectionOrigin: [0, 0],
            focusExists: '',
            focusOrigin: [0, 0],

            init: function (object, customOptions) {
                // Set options to default.
                this.options = this.defaultOptions;

                // And merge them with the custom options
                setOptions(customOptions);
                // Merge current options with the custom option.
                function setOptions(customOptions) {
                    this.options = $.extend(this.options, customOptions);
                };

                // Initialize the image
                this.$image = $(object);
                // Initialize an image holder
                this.$holder = $('<div></div>')
                    .css({
                        position: 'relative'
                    })
                    .width(this.$image.width())
                    .height(this.$image.height());

                // Wrap the holder around the image
                this.$image.wrap(this.$holder)
                    .css({
                        position: 'absolute'
                    });

                // Initialize an overlay layer and place it above the image
                this.$overlay = $('<div id="image-crop-overlay"></div>')
                    .css({
                        opacity: this.options.overlayOpacity,

                    })
                    .width(this.$image.width())
                    .height(this.$image.height())
                    .insertAfter(this.$image);

                // Initialize a trigger layer and place it above the overlay layer
                this.$trigger = $('<div></div>')
                    .css({
                        backgroundColor: '#000000',
                        opacity: 0,
                        position: 'absolute'
                    })
                    .width(this.$image.width())
                    .height(this.$image.height())
                    .insertAfter(this.$overlay);

                // Initialize an outline layer and place it above the trigger layer
                this.$outline = $('<div id="image-crop-outline"></div>')
                    .css({
                        opacity: this.options.outlineOpacity,
                    })
                    .insertAfter(this.$trigger);

                // Initialize a selection layer and place it above the outline layer
                this.$selection = $('<div></div>')
                    .css({
                        background: 'url(' + this.$image.attr('src') + ') no-repeat',
                        backgroundSize: this.$image.width() + 'px auto',
                        position: 'absolute'
                    })
                    .insertAfter(this.$outline);

                // Initialize a resize handlers and place in the inferface.
                this.$nwCropResizer = $('<div class="image-crop-resize-handler" id="image-crop-nw-resize-handler"></div>')
                    .insertAfter(this.$selection);

                this.$neCropResizer = $('<div class="image-crop-resize-handler" id="image-crop-ne-resize-handler"></div>')
                    .insertAfter(this.$selection);

                this.$swCropResizer = $('<div class="image-crop-resize-handler" id="image-crop-sw-resize-handler"></div>')
                    .insertAfter(this.$selection);

                this.$seCropResizer = $('<div class="image-crop-resize-handler" id="image-crop-se-resize-handler"></div>')
                    .insertAfter(this.$selection);

                // Add the elements for focus selection
                // Initialize a selection layer and place it above the outline layer
                this.$focusSelection = $('<div></div>')
                    .css({
                        // backgroundSize: '970px auto',
                        position: 'absolute'
                    })
                    .addClass('image-focus-rectangle')
                    .insertAfter(this.$seCropResizer)
                    .append('<a class="focus-destroyer">x</a>');

                this.$focusDestroyer = $('.focus-destroyer');

                // Initialize a north/west resize handler and place it above the selection layer
                this.$nwFocusResizer = $('<div class="image-focus-resize-handler" id="image-focus-nw-resize-handler"></div>')
                    .insertAfter(this.$focusSelection);

                // Initialize a north/east resize handler and place it above the selection layer
                this.$neFocusResizer = $('<div class="image-focus-resize-handler" id="image-focus-ne-resize-handler"></div>')
                    .insertAfter(this.$focusSelection);

                // Initialize a south/west resize handler and place it above the selection layer
                this.$swFocusResizer = $('<div class="image-focus-resize-handler" id="image-focus-sw-resize-handler"></div>')
                    .insertAfter(this.$focusSelection);


                // Initialize a south/east resize handler and place it above the selection layer
                this.$seFocusResizer = $('<div class="image-focus-resize-handler" id="image-focus-se-resize-handler"></div>')
                    .insertAfter(this.$focusSelection);

                // Verify if the selection size is bigger than the minimum accepted
                // and set the selection existence accordingly
                if (this.options.selectionWidth > this.options.minSelect[0] &&
                    this.options.selectionHeight > this.options.minSelect[1])
                    this.selectionExists = true;
                else
                    this.selectionExists = false;


                // initialize the plug-in interface
                this.setNaturalDimensions();
                this.addRectangles();
                this.updateInterface();

                this.$trigger.mousedown(this.setCrop);
                this.$selection.mousedown(this.pickSelection);
                $('div.image-crop-resize-handler, div.image-focus-resize-handler').mousedown(this.pickResizeHandler);

                // Add click event to the focus destroyer.
                this.$focusDestroyer.click(function () {
                    cropper.removeFocus();
                    return false;
                });
            },

            setNaturalDimensions: function () {
                var theImage = new Image();
                theImage.src = this.$image.attr("src");

                this.naturalWidth = theImage.width;
                this.naturalHeight = theImage.height;
            },

            // Get the current offset of an element
            getElementOffset: function (object) {
                var offset = $(object).offset();

                return [offset.left, offset.top];
            },

            // Update the overlay layer
            updateOverlayLayer: function () {
                this.$overlay.css({
                    display: this.selectionExists ? 'block' : 'none'
                });
            },

            // Get the current mouse position relative to the image position
            getMousePosition: function (event) {
                var imageOffset = this.getElementOffset(this.$image);

                var x = event.pageX - imageOffset[0],
                    y = event.pageY - imageOffset[1];

                x = (x < 0) ? 0 : (x > this.$image.width()) ? this.$image.width() : x;
                y = (y < 0) ? 0 : (y > this.$image.height()) ? this.$image.height() : y;

                return [x, y];
            },

            // Update the trigger layer
            updateTriggerLayer: function () {
                this.$trigger.css({
                    cursor: 'crosshair',
                });
            },

            // Update the selection
            updateCrop: function () {
                // Update the outline layer
                this.$outline.css({
                    cursor: 'default',
                    display: this.selectionExists ? 'block' : 'none',
                    left: this.options.selectionPosition[0],
                    top: this.options.selectionPosition[1]
                })
                    .width(this.options.selectionWidth)
                    .height(this.options.selectionHeight);

                // Update the selection layer
                this.$selection.css({
                    backgroundPosition: ( -this.options.selectionPosition[0] - 1) + 'px ' + ( -this.options.selectionPosition[1] - 1) + 'px',
                    cursor: 'move',
                    display: this.selectionExists ? 'block' : 'none',
                    left: this.options.selectionPosition[0] + 1,
                    top: this.options.selectionPosition[1] + 1
                })
                    .width((this.options.selectionWidth - 2 > 0) ? (this.options.selectionWidth - 2) : 0)
                    .height((this.options.selectionHeight - 2 > 0) ? (this.options.selectionHeight - 2) : 0);

                // Update the forcus rectangle
                this.$focusSelection.css({
                    backgroundPosition: ( -this.options.focusPosition[0] - 1) + 'px ' + ( -this.options.focusPosition[1] - 1) + 'px',
                    cursor: 'move',
                    display: this.focusExists ? 'block' : 'none',
                    left: this.options.focusPosition[0] + 1,
                    top: this.options.focusPosition[1] + 1
                })
                    .width((this.options.focusWidth - 2 > 0) ? (this.options.focusWidth - 2) : 0)
                    .height((this.options.focusHeight - 2 > 0) ? (this.options.focusHeight - 2) : 0);

            },

            // Update the cursor type
            updateCursor: function (cursorType) {
                this.$trigger.css({
                    cursor: cursorType
                });

                this.$outline.css({
                    cursor: cursorType
                });

                this.$selection.css({
                    cursor: cursorType
                });
            },

            // Update the plug-in's interface
            updateInterface: function (sender) {
                switch (sender) {
                    case 'addRectangles':
                        this.updateOverlayLayer();
                        break;

                    case 'setCrop' :
                        this.updateOverlayLayer();
                        this.updateCrop();
                        this.updateResizeHandlers('hide-all');
                        break;

                    case 'setFocus' :
                        this.updateCrop();
                        this.updateResizeHandlers('hide-all');
                        break;

                    case 'pickCrop' :
                        this.updateResizeHandlers('hide-all');
                        break;

                    case 'pickResizeHandler' :
                        this.updateResizeHandlers('hide-all');
                        break;

                    case 'resizeCrop' :
                        this.updateCrop();
                        this.updateResizeHandlers('hide-all');
                        this.updateCursor('crosshair');
                        break;

                    case 'releaseCrop' :
                        this.updateTriggerLayer();
                        this.updateOverlayLayer();
                        this.updateCrop();
                        this.updateResizeHandlers();
                        break;

                    default :
                        this.updateTriggerLayer();
                        this.updateOverlayLayer();
                        this.updateCrop();
                        this.updateResizeHandlers();
                }
            },

            // Set a new selection
            setCrop: function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (cropper.selectionExists !== true) {
                    $(document).mousemove(cropper.resizeCrop).mouseup(cropper.releaseCrop);
                    cropper.selectionExists = true;
                }

                // Reset the selection size
                cropper.options.selectionWidth = 0;
                cropper.options.selectionHeight = 0;

                // Get the selection origin
                cropper.selectionOrigin = cropper.getMousePosition(event);

                // And set its position
                cropper.options.selectionPosition[0] = cropper.selectionOrigin[0];
                cropper.options.selectionPosition[1] = cropper.selectionOrigin[1];

                cropper.updateInterface('setCrop');
            },

            setFocus: function (event) {
                event.preventDefault();
                event.stopPropagation();

                // Switch from cropper to focuser
                // Bind an event handler to the 'mousemove' and 'mouseup' events
                $(document).mousemove(cropper.resizeFocus).mouseup(cropper.releaseFocus);

                // Notify that a selection exists
                cropper.focusExists = true;

                // Reset the selection size
                cropper.options.focusWidth = 0;
                cropper.options.focusHeight = 0;

                // Get the selection origin
                cropper.focusOrigin = cropper.getMousePosition(event);

                // And set its position
                cropper.options.focusPosition[0] = cropper.focusOrigin[0];
                cropper.options.focusPosition[1] = cropper.focusOrigin[1];

                cropper.updateInterface('setCrop');
            },

            isValidPosition: function (mousePosition, area) {
                var mouseX = mousePosition[0],
                    mouseY = mousePosition[1],
                    valid = true;

                if (area == 'crop') {
                    if ((mouseX >= cropper.focusOrigin[0] && mouseX <= (cropper.focusOrigin[0] + cropper.options.focusWidth)) ||
                        (mouseY <= (cropper.focusOrigin[1] + cropper.options.focusHeight) && mouseY >= cropper.focusOrigin[1])) {
                        valid = false;
                    }
                }
                else {
                    if (mouseX <= cropper.selectionOrigin[0] ||
                        mouseY <= cropper.selectionOrigin[1] ||
                        mouseX >= (cropper.selectionOrigin[0] + cropper.options.selectionWidth) ||
                        mouseY >= (cropper.selectionOrigin[1] + cropper.options.selectionHeight)) {
                        valid = false;
                    }
                }

                return valid;
            },

            // Resize the crop area
            resizeCrop: function (event) {
                event.preventDefault();
                event.stopPropagation();
                var mousePosition = cropper.getMousePosition(event),
                    validPosition = cropper.isValidPosition(mousePosition, 'crop');

                if (validPosition) {
                    // Get the selection size
                    cropper.options.selectionWidth = mousePosition[0] - cropper.selectionOrigin[0];
                    cropper.options.selectionHeight = mousePosition[1] - cropper.selectionOrigin[1];
                    if (cropper.options.selectionWidth < 0) {
                        cropper.options.selectionWidth = Math.abs(cropper.options.selectionWidth);
                        cropper.options.selectionPosition[0] = cropper.selectionOrigin[0] - cropper.options.selectionWidth;
                    } else
                        cropper.options.selectionPosition[0] = cropper.selectionOrigin[0];
                    if (cropper.options.selectionHeight < 0) {
                        cropper.options.selectionHeight = Math.abs(cropper.options.selectionHeight);
                        cropper.options.selectionPosition[1] = cropper.selectionOrigin[1] - cropper.options.selectionHeight;
                    } else {
                        cropper.options.selectionPosition[1] = cropper.selectionOrigin[1];
                    }
                    cropper.updateInterface('resizeCrop');
                }
            },

            resizeFocus: function (event) {
                event.preventDefault();
                event.stopPropagation();
                var mousePosition = cropper.getMousePosition(event),
                    validPosition = cropper.isValidPosition(mousePosition, 'focus');

                if (validPosition) {
                    // Get the focus size
                    cropper.options.focusWidth = mousePosition[0] - cropper.focusOrigin[0];
                    cropper.options.focusHeight = mousePosition[1] - cropper.focusOrigin[1];
                    if (cropper.options.focusWidth < 0) {
                        cropper.options.focusWidth = Math.abs(cropper.options.focusWidth);
                        cropper.options.focusPosition[0] = cropper.focusOrigin[0] - cropper.options.focusWidth;
                    } else
                        cropper.options.focusPosition[0] = cropper.focusOrigin[0];
                    if (cropper.options.focusHeight < 0) {
                        cropper.options.focusHeight = Math.abs(cropper.options.focusHeight);
                        cropper.options.focusPosition[1] = cropper.focusOrigin[1] - cropper.options.focusHeight;
                    } else {
                        cropper.options.focusPosition[1] = cropper.focusOrigin[1];
                    }
                    cropper.updateInterface('resizeFocus');
                }
            },

            // Release the current selection
            releaseCrop: function (event) {
                event.preventDefault();
                event.stopPropagation();

                // Unbind the event handler to the 'mousemove' event
                $(document).unbind('mousemove');
                $(document).unbind('mouseup');

                // Update the selection origin
                cropper.selectionOrigin[0] = cropper.options.selectionPosition[0];
                cropper.selectionOrigin[1] = cropper.options.selectionPosition[1];

                // Reset the resize constraints
                resizeHorizontally = true;
                resizeVertically = true;

                // Verify if the selection size is bigger than the minimum accepted
                // and set the selection existence accordingly
                if (cropper.options.selectionWidth > cropper.options.minSelect[0] &&
                    cropper.options.selectionHeight > cropper.options.minSelect[1])
                    selectionExists = true;
                else
                    selectionExists = false;

                // Trigger the 'onSelect' event when the selection is made
                cropper.options.onSelect(cropper.getCropData());

                // If the selection doesn't exist
                if (!selectionExists) {
                    cropper.$previewHolder.unbind('mouseenter');
                    cropper.$previewHolder.unbind('mouseleave');
                }

                cropper.updateInterface('releaseCrop');
            },

            releaseFocus: function (event) {
                event.preventDefault();
                event.stopPropagation();

                // Unbind the event handler to the 'mousemove' event
                $(document).unbind('mousemove');
                $(document).unbind('mouseup');

                // Update the focus origin
                cropper.focusOrigin[0] = cropper.options.focusPosition[0];
                cropper.focusOrigin[1] = cropper.options.focusPosition[1];

                // Reset the resize constraints
                resizeHorizontally = true;
                resizeVertically = true;

                // Verify if the focus size is bigger than the minimum accepted
                // and set the focus existence accordingly
                if (cropper.options.focusWidth > cropper.options.minSelect[0] &&
                    cropper.options.focusHeight > cropper.options.minSelect[1])
                    focusExists = true;
                else
                    focusExists = false;

                // Trigger the 'onSelect' event when the focus is made
                cropper.options.onSelect(cropper.getCropData());

                // If the focus doesn't exist
                if (!focusExists) {
                    cropper.$previewHolder.unbind('mouseenter');
                    cropper.$previewHolder.unbind('mouseleave');
                }

                cropper.updateInterface('releaseCrop');
            },

            removeFocus: function () {
                // Remove the rectangle.
                cropper.$focusSelection.css({
                    top: 0,
                    left: 0,
                    height: 0,
                    width: 0,
                });

                // Reset the values.
                cropper.focusExists = false;
                cropper.focusOrigin = [0, 0];
                cropper.options.focusPosition = [0, 0];
                cropper.options.focusHeight = 0;
                cropper.options.focusWidth = 0;

                // Move the handlers.
                cropper.updateResizeHandlers();

                // Update the data.
                cropper.getCropData();
            },

            // Update the resize handlers
            updateResizeHandlers: function (action) {
                switch (action) {
                    case 'hide-all' :
                        $('.image-crop-resize-handler, .image-focus-resize-handler').each(function () {
                            $(this).css({
                                display: 'none'
                            });
                        });

                        break;
                    default :
                        var display = (cropper.selectionExists) ? 'block' : 'none';
                        // Crop rectangle.
                        cropper.$nwCropResizer.css({
                            cursor: 'nw-resize',
                            display: display,
                            left: cropper.options.selectionPosition[0] - Math.round(cropper.$nwCropResizer.width() / 2),
                            top: cropper.options.selectionPosition[1] - Math.round(cropper.$nwCropResizer.height() / 2)
                        });

                        cropper.$neCropResizer.css({
                            cursor: 'ne-resize',
                            display: display,
                            left: cropper.options.selectionPosition[0] + cropper.options.selectionWidth - Math.round(cropper.$neCropResizer.width() / 2) - 1,
                            top: cropper.options.selectionPosition[1] - Math.round(cropper.$neCropResizer.height() / 2)
                        });

                        cropper.$swCropResizer.css({
                            cursor: 'sw-resize',
                            display: display,
                            left: cropper.options.selectionPosition[0] - Math.round(cropper.$swCropResizer.width() / 2),
                            top: cropper.options.selectionPosition[1] + cropper.options.selectionHeight - Math.round(cropper.$swCropResizer.height() / 2) - 1
                        });

                        cropper.$seCropResizer.css({
                            cursor: 'se-resize',
                            display: display,
                            left: cropper.options.selectionPosition[0] + cropper.options.selectionWidth - Math.round(cropper.$seCropResizer.width() / 2) - 1,
                            top: cropper.options.selectionPosition[1] + cropper.options.selectionHeight - Math.round(cropper.$seCropResizer.height() / 2) - 1
                        });

                        // Focus Rectangle.
                        cropper.$nwFocusResizer.css({
                            cursor: 'nw-resize',
                            display: display,
                            left: cropper.options.focusPosition[0] - Math.round(cropper.$nwFocusResizer.width() / 2),
                            top: cropper.options.focusPosition[1] - Math.round(cropper.$nwFocusResizer.height() / 2)
                        });

                        cropper.$neFocusResizer.css({
                            cursor: 'ne-resize',
                            display: display,
                            left: cropper.options.focusPosition[0] + cropper.options.focusWidth - Math.round(cropper.$neFocusResizer.width() / 2) - 1,
                            top: cropper.options.focusPosition[1] - Math.round(cropper.$neFocusResizer.height() / 2)
                        });

                        cropper.$swFocusResizer.css({
                            cursor: 'sw-resize',
                            display: display,
                            left: cropper.options.focusPosition[0] - Math.round(cropper.$swFocusResizer.width() / 2),
                            top: cropper.options.focusPosition[1] + cropper.options.focusHeight - Math.round(cropper.$swFocusResizer.height() / 2) - 1
                        });

                        cropper.$seFocusResizer.css({
                            cursor: 'se-resize',
                            display: display,
                            left: cropper.options.focusPosition[0] + cropper.options.focusWidth - Math.round(cropper.$seFocusResizer.width() / 2) - 1,
                            top: cropper.options.focusPosition[1] + cropper.options.focusHeight - Math.round(cropper.$seFocusResizer.height() / 2) - 1
                        });
                }
            },

            // Pick the current selection
            pickSelection: function (event) {
                event.preventDefault();
                event.stopPropagation();

                cropper.setFocus(event);
            },

            // Pick one of the resize handlers
            pickResizeHandler: function (event) {
                var rectangle = 'Crop';

                event.preventDefault();
                event.stopPropagation();

                switch (event.target.id) {
                    case 'image-crop-nw-resize-handler':
                        cropper.selectionOrigin[0] += cropper.options.selectionWidth;
                        cropper.selectionOrigin[1] += cropper.options.selectionHeight;
                        cropper.options.selectionPosition[0] = cropper.selectionOrigin[0] - cropper.options.selectionWidth;
                        cropper.options.selectionPosition[1] = cropper.selectionOrigin[1] - cropper.options.selectionHeight;
                        break;

                    case 'image-crop-ne-resize-handler':
                        cropper.selectionOrigin[1] += cropper.options.selectionHeight;
                        cropper.options.selectionPosition[1] = cropper.selectionOrigin[1] - cropper.options.selectionHeight;
                        break;

                    case 'image-crop-sw-resize-handler':
                        cropper.selectionOrigin[0] += cropper.options.selectionWidth;
                        cropper.options.selectionPosition[0] = cropper.selectionOrigin[0] - cropper.options.selectionWidth;
                        break;

                    case 'image-crop-se-resize-handler':
                        cropper.selectionOrigin[0] = cropper.options.selectionPosition[0];
                        cropper.options.selectionHeight = cropper.options.selectionPosition[1] - cropper.selectionOrigin[1];
                        break;

                    case 'image-focus-nw-resize-handler':
                        rectangle = 'Focus';
                        cropper.focusOrigin[0] += cropper.options.focusWidth;
                        cropper.focusOrigin[1] += cropper.options.focusHeight;
                        cropper.options.focusPosition[0] = cropper.focusOrigin[0] - cropper.options.focusWidth;
                        cropper.options.focusPosition[1] = cropper.focusOrigin[1] - cropper.options.focusHeight;
                        break;

                    case 'image-focus-ne-resize-handler':
                        rectangle = 'Focus';
                        cropper.focusOrigin[1] += cropper.options.focusHeight;
                        cropper.options.focusPosition[1] = cropper.focusOrigin[1] - cropper.options.focusHeight;
                        break;

                    case 'image-focus-sw-resize-handler' :
                        rectangle = 'Focus';
                        cropper.focusOrigin[0] += cropper.options.focusWidth;
                        cropper.options.focusPosition[0] = cropper.focusOrigin[0] - cropper.options.focusWidth;
                        break;

                    case 'image-focus-se-resize-handler':
                        rectangle = 'Focus';
                        cropper.focusOrigin[0] = cropper.options.focusPosition[0];
                        cropper.options.focusHeight = cropper.options.focusPosition[1] - cropper.focusOrigin[1];
                        break;

                }

                $(document).mousemove(cropper['resize' + rectangle]);
                $(document).mouseup(cropper['release' + rectangle]);

                cropper.updateInterface('pickResizeHandler');
            },

            addRectangles: function () {
                // Set the values if they already exist.
                var $input = $(cropper.options.cropInputselector),
                    valueString = $input.val();

                if (valueString != '') {
                    var valueArray = valueString.split(':'),
                        cropString = valueArray[0],
                        focusString = valueArray[1],
                        cropCoords = cropString.split(', '),
                        focusCoords = focusString.split(', '),
                        widthScale = cropper.naturalWidth / cropper.$image.width(),
                        heightScale = cropper.naturalHeight / cropper.$image.height();

                    cropper.options.selectionPosition[0] = parseInt(cropCoords[0]) / widthScale;
                    cropper.options.selectionPosition[1] = parseInt(cropCoords[1]) / heightScale;

                    cropper.options.selectionWidth = (parseInt(cropCoords[2]) - parseInt(cropCoords[0])) / widthScale;
                    cropper.options.selectionHeight = (parseInt(cropCoords[3]) - parseInt(cropCoords[1])) / heightScale;

                    cropper.options.focusPosition[0] = parseInt(focusCoords[0]) / widthScale;
                    cropper.options.focusPosition[1] = parseInt(focusCoords[1]) / heightScale;

                    cropper.options.focusWidth = (parseInt(focusCoords[2]) - parseInt(focusCoords[0])) / widthScale;
                    cropper.options.focusHeight = (parseInt(focusCoords[3]) - parseInt(focusCoords[1])) / heightScale;

                    // Set the origin variable.
                    cropper.selectionOrigin[0] = cropper.options.selectionPosition[0];
                    cropper.selectionOrigin[1] = cropper.options.selectionPosition[1];
                    cropper.focusOrigin[0] = cropper.options.focusPosition[0];
                    cropper.focusOrigin[1] = cropper.options.focusPosition[1];

                    cropper.selectionExists = true;
                    cropper.focusExists = true;

                    cropper.updateInterface('addrectangles');
                }
            },

            // Return an object containing information about the plug-in state
            getCropData: function () {
                var data = cropper.options;

                var widthScale = cropper.naturalWidth / cropper.$image.width();
                var heightScale = cropper.naturalHeight / cropper.$image.height();

                var cx = Math.floor(data.selectionPosition[0] * widthScale),
                    cy = Math.floor(data.selectionPosition[1] * heightScale),
                    cw = Math.floor(data.selectionWidth * widthScale),
                    ch = Math.floor(data.selectionHeight * heightScale),
                    fx = Math.floor(data.focusPosition[0] * widthScale),
                    fy = Math.floor(data.focusPosition[1] * heightScale),
                    fw = Math.floor(data.focusWidth * widthScale),
                    fh = Math.floor(data.focusHeight * heightScale);

                var output = cx + ', ' + cy + ', ' + (cx + cw) + ', ' + (cy + ch) + ':' + fx + ', ' + fy + ', ' + (fx + fw) + ', ' + (fy + fh);

                $(data.cropInputselector).val(output);
            }
        };

        cropper.init(object, customOptions);
    };

    $.fn.imageCrop = function (customOptions) {
        this.each(function () {
            var currentObject = this,
                image = new Image();

            // And attach imageCrop when the object is loaded
            image.onload = function () {
                $.imageCrop(currentObject, customOptions);
            };

            // Reset the src because cached images don't fire load sometimes
            image.src = currentObject.src;
        });

        return this;
    };
})(jQuery);

jQuery(window).load(function ($) {
    jQuery('.crop-focus-image img').imageCrop();
});