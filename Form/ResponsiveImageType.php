<?php

namespace IrishDan\ResponsiveImageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ResponsiveImageType extends AbstractType
{
    private $responsiveImageEntityName;

    public function __construct($responsiveImageEntityName)
    {
        $this->responsiveImageEntityName = $responsiveImageEntityName;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('alt');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $image = $event->getData();
            $form = $event->getForm();
            // Conditionally add form elements.
            if (!empty($image) && !empty($image->getId())) {
                $form->add('crop_coordinates', CropFocusType::class, [
                    'data' => $image,
                ]);
            } else {
                $form->add('file', FileType::class, ['label' => 'Upload an image']);
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->responsiveImageEntityName,
        ]);
    }
}