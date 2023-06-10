<?php

namespace App\Form;

use App\Entity\Rol;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermisoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identificador')
            ->add('descripcion')
            ->add('roles', EntityType::class, [
                'class' => Rol::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione',
                'multiple' => true,
                'attr' => ['size' => 10],
                'required' => true,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('c')->orderBy('c.nombre', 'ASC');
                }
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Permiso'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'permiso';
    }


}
