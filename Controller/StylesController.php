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
        $styles = $this->get('image.style_manager')->getAllStyles();
        $form = $this->createForm(StylesType::class, $styles);

        // pass the form to the form handler.
        $formHandler = $this->get('image.styles_form_handler');
        if (!$formHandler->handle($form, $request)) {
            // Handle failed form submission.
        }

        return $this->render('ResponsiveImageBundle:Settings:index.html.twig', array(
            'image_settings_form' => $form->createView(),
        ));

    }
}
