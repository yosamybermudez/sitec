<?php

namespace App\Form;

use App\Entity\ComprobanteOperacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComprobanteOperacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('nroComprobante')
            ->add('tipoOperacion')
            ->add('importeTotal')
            ->add('importePagado')
            ->add('created')
            ->add('updated')
            ->add('ordenTrabajo')
            ->add('updatedBy')
            ->add('createdBy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ComprobanteOperacion::class,
        ]);
    }
}
