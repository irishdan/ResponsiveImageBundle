<?php

namespace ResponsiveImageBundle\Form\Handler;

use ResponsiveImageBundle\Event\ImageEvent;
use ResponsiveImageBundle\Event\ImageEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class StylesFormHandler
 * @package ResponsiveImageBundle\Form\Handler
 */
class StylesFormHandler
{
    /**
     * @var
     */
    private $dispatcher;

    /**
     * @var
     */
    private $styleManager;

    /**
     * StylesFormHandler constructor.
     * @param $styleManager
     */
    public function __construct($styleManager, $dispatcher) {
        $this->styleManager = $styleManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @return bool
     */
    public function handle(FormInterface $form, Request $request) {
        if (!$request->isMethod('POST')) {
            return false;
        }
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return false;
        }
        $data = $form->getData();

        foreach($data as $style => $checked) {
            if (empty($checked)) {
                unset($data[$style]);
            }
        }

        if (!empty($data)) {
            $event = new ImageEvent(NULL, $data);
            // Dispatch event to delete the original and styled images.
            $this->dispatcher->dispatch(
                ImageEvents::STYLE_DELETE_STYLED,
                $event
            );

        }

        return TRUE;
    }
}