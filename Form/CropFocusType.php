<?php

namespace IrishDan\ResponsiveImageBundle\Form;

use IrishDan\ResponsiveImageBundle\StyleManager;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

/**
 * Class CropFocusType
 *
 * @package ResponsiveImageBundle\Form\Type
 */
class CropFocusType extends AbstractType
{
    private $styleManager;
    private $displayCoordinates = true;
    private $includeJsCss = true;

    public function __construct(StyleManager $styleManager, array $configuration)
    {
        $this->styleManager = $styleManager;

        if (!empty($configuration['crop_focus_widget'])) {
            $this->includeJsCss       = empty($configuration['crop_focus_widget']['include_js_css']) ? false : true;
            $this->displayCoordinates = empty($configuration['crop_focus_widget']['display_coordinates']) ? false : true;
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label'                 => 'Focus and Crop',
                'empty_data'            => '0, 0, 0, 0:0, 0, 0, 0',
                'alt'                   => 'Alt',
                'title'                 => 'title',
                'width'                 => 100,
                'height'                => 100,
                'display_coordinates'   => $this->displayCoordinates,
                'include_js_css'        => $this->includeJsCss,
                'coordinate_field_type' => $this->displayCoordinates ? 'text' : 'hidden',
            ]
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $image            = $options['data'];
        $options['value'] = $image->getCropCoordinates();
        $options['image'] = $image;

        if ($options['include_js_css']) {
            $pubicDirectory = dirname(__FILE__) . '/../Resources/public/';
            $js             = file_get_contents($pubicDirectory . 'js/jquery.cropper.js', FILE_USE_INCLUDE_PATH);
            $css            = file_get_contents($pubicDirectory . 'css/cropper.css', FILE_USE_INCLUDE_PATH);

            $options['js']  = $js;
            $options['css'] = $css;
        }

        $view->vars = array_replace($view->vars, $options);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}