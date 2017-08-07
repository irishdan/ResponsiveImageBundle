<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Form;

use IrishDan\ResponsiveImageBundle\ImageEntityNameResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ResponsiveImageType
 *
 * @package IrishDan\ResponsiveImageBundle\Form
 */
class ResponsiveImageType extends AbstractType
{
    private $responsiveImageEntityName;

    public function __construct(ImageEntityNameResolver $entityNameResolver)
    {
        $this->responsiveImageEntityName = $entityNameResolver->getClassName();
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

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $image = $event->getData();
                $form  = $event->getForm();
                // Conditionally add form elements.
                if (!empty($image) && !empty($image->getId())) {
                    $form->add(
                        'crop_coordinates',
                        CropFocusType::class,
                        [
                            'data' => $image,
                        ]
                    );
                }
                else {
                    $form->add('file', FileType::class, ['label' => 'Upload an image']);
                }
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => $this->responsiveImageEntityName,
            ]
        );
    }
}