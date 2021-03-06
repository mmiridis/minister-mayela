<?php

namespace AppBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Provider;

class ProviderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Photographer' => 'photographer',
                    'Coordinator'  => 'coordinator',
                    'Flowers'      => 'flowers'
                ],
                'placeholder' => ''
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'name'        => [],
                    'description' => [
                        'attr' => ['rows' => 10]
                    ]
                ]
            ])
            ->add('phone')
            ->add('url')
            ->add('email')
            ->add('isActive');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Provider::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'provider';
    }
}
