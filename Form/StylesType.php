<?php

namespace ResponsiveImageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class StylesType
 * @package ResponsiveImageBundle\Form
 */
class StylesType extends AbstractType
{
    /**
     * @var
     */
    private $styles;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->styles = $options['data'];
        foreach ($this->styles as $key => $value) {
            $builder->add($key, CheckboxType::class, array(
                'label' => 'Delete all "' . $key . '" images',
                'required' => FALSE,
                'data' => FALSE,
            ));
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'styles' => null,
        ));
    }
}
