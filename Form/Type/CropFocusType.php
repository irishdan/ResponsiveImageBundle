<?php

namespace ResponsiveImageBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

/**
 * Class CropFocusType
 * @package ResponsiveImageBundle\Form\Type
 */
class CropFocusType extends AbstractType
{
    /**
     * @var
     */
    private $styleManager;

    /**
     * CropFocusType constructor.
     * @param $styleManager
     */
    public function __construct($styleManager)
    {
        $this->styleManager = $styleManager;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Focus and Crop',
            'empty_data' => '0, 0, 0, 0:0, 0, 0, 0',
            'alt' => 'Alt',
            'title' => 'title',
            'width' => 100,
            'height' => 100,
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $image = $options['data'];
        $image = $this->styleManager->setImageStyle($image);
        $options['value'] = $image->getCropCoordinates();
        $options['image'] = $image;

        $view->vars = array_replace($view->vars, $options);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return TextType::class;
    }
}