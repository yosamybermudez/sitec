<?php

namespace App\Form;

use App\Entity\OperacionContable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperacionContableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipoOperacion')
            ->add('saldo')
            ->add('codigo')
            ->add('descripcion')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OperacionContable::class,
        ]);
    }
}
