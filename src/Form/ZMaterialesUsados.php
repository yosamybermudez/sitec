<?php

namespace App\Form;

use App\Entity\Cargo;
use App\Entity\MateriaPrima;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZMaterialesUsados extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('materiaPrima', EntityType::class, [
                'class' => MateriaPrima::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione',
                'required' => true,
            ])
            ->add('cantidad', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cargo::class,
        ]);
    }
}
