<?php

namespace App\Form;

use App\Entity\MateriaPrimaEntrada;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MateriaPrimaEntradaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('realizadaPor', EntityType::class, [
                'class' => Usuario::class,
                'label' => 'Realizado por',
                'choice_label' => 'nombreCompletoCargo',
                'placeholder' => 'Seleccione',
                'required' => true,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('u')
                        ->innerJoin('u.userRoles', 'r')
                        ->where("'administrador_sistema' <> r.identificador")
                        ->orderBy('u.username', 'ASC');
                }
            ])
            ->add('movimientosMateriaPrima', CollectionType::class, array(
                'entry_type' => MateriaPrimaMovimientoType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'attr' => ['class' => 'material row', 'label' => false]
                ]
            ))
            ->add('vendedorCarneIdentidad', TextType::class, array(
                'label' => 'Carné de identidad del vendedor',
                'required' => false
            ))
            ->add('vendedorNombre', TextType::class, array(
                'label' => 'Nombre(s) y apellidos del vendedor',
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MateriaPrimaEntrada::class,
        ]);
    }
}
