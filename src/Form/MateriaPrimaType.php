<?php

namespace App\Form;

use App\Entity\EquipoTipo;
use App\Entity\MateriaPrima;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MateriaPrimaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion', TextType::class, array(
                'label' => 'Descripción',
                'required' => false
            ))
            ->add('tipoEquipoDestino', EntityType::class, [
                'label' => 'Tipo de equipo destino',
                'class' => EquipoTipo::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione',
                'required' => true,
            ])
            ->add('cantidad')
            ->add('precio')
            ->add('unidadMedida')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MateriaPrima::class,
        ]);
    }
}
