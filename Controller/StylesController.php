<?php

namespace ResponsiveImageBundle\Controller;

use ResponsiveImageBundle\Form\StylesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StylesController extends Controller
{
    public function indexAction(Request $request)
    {
        // Get all styles and create the form
        $styles = $this->get('responsive_image.style_manager')->getAllStyles();
        $form = $this->createForm(StylesType::class, $styles);

        // pass the form to the form handler.
        $formHandler = $this->get('responsive_image.styles_form_handler');
        if (!$formHandler->handle($form, $request)) {
            // Handle failed form submission.
        }

        // Get the image list from s3.
        $s3Images = $this->get('responsive_image.s3_bridge')->listImages();

        return $this->render('ResponsiveImageBundle:Settings:index.html.twig', array(
            'image_settings_form' => $form->createView(),
            's3_images' => $s3Images,
        ));

    }
}
