<?php

namespace App\Form;

use App\Entity\MateriaPrima;
use App\Entity\MateriaPrimaMovimiento;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MateriaPrimaMovimientoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('id')
            ->add('materiaPrima', EntityType::class, [
                'class' => MateriaPrima::class,
                'label' => false,
                'choice_label' => 'nombreCantidadPrecio',
                'placeholder' => 'Seleccione',
                'attr' => [
                    'class' => 'select-mp',
                ],
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('mp')
                        ->where('mp.cantidad > 0')
                        ->orderBy('mp.nombre', 'ASC');
                },
                'required' => true
            ])
            ->add('cantidad', TextType::class, [
                'label' => false,
                'attr' => array(
                    'class' => 'cantidad-mp'
                )
            ])
            ->add('remove', ButtonType::class, [
                'attr' => ['class' => 'remove-btn btn-danger btn-sm'],
                'label' => 'Eliminar'
            ])
//            ->add('estaConfirmado')
//            ->add('created')
//            ->add('updated')
//            ->add('createdBy')
//            ->add('updatedBy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MateriaPrimaMovimiento::class,
        ]);
    }
}
