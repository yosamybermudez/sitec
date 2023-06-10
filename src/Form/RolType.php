<?php

namespace App\Form;

use App\Entity\Permiso;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre')
            ->add('identificador')
            ->add('rango', ChoiceType::class, array(
                'choices' => range(0,50,1),
                'placeholder' => 'N/A',
                'choice_label' => function($choice){
                    return $choice;
                },
                'help' => 'aa'
            ))
            ->add('descripcion', null, array(
                'required' => false,
            ));


        if ($options['edit']) {
            $builder
                ->add('usuarios', null, array(
                    'required' => false
                ))
                ->add('permisos', EntityType::class, array(
                    'class' => Permiso::class,
                    'choice_label' => 'identificador',
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Rol',
            'edit' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'rol';
    }


}
