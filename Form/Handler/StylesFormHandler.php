<?php

namespace ResponsiveImageBundle\Form\Handler;

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
    private $styleManager;

    /**
     * StylesFormHandler constructor.
     * @param $styleManager
     */
    public function __construct($styleManager) {
        $this->styleManager = $styleManager;
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
            $this->styleManager->deleteStyledImages(array_keys($data));
        }

        return TRUE;
    }
}