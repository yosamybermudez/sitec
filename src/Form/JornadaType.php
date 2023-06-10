<?php

namespace App\Form;

use App\Entity\Jornada;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JornadaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', TextType::class, array(
                'label' => 'Fecha',
                'required' => true,
                'disabled' => true
            ))
            ->add('fondoInicial', TextType::class, array(
                'label' => 'Fondo inicial'
            ))
            ->add('tecnicos', EntityType::class, [
                'label' => 'Técnicos disponibles',
                'class' => Usuario::class,
                'choice_label' => 'nombreCompletoCargo',
                'placeholder' => 'Seleccione',
                'multiple' => true,
                'attr' => ['size' => 10],
                'required' => true,
                'mapped' => false,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('d')
                        ->innerJoin('d.cargo', 'c')
                        ->where("c.nombre LIKE '%Técnico%'")
                        ->orWhere("c.nombre LIKE '%Tecnico%'")
                        ->orderBy('d.nombres', 'ASC');
                }
            ])

        ;
        $builder->get('fecha')
            ->addModelTransformer(new CallbackTransformer(
                function ($dateAsString) {
                    // transform date to string
                    $fecha = $dateAsString ? $dateAsString : new \DateTime();
                    return $fecha->format('d/m/Y');
                },
                function ($stringAsDate) {
                    // transform the string to date
                    $stringAsDate = str_replace('-','/', $stringAsDate);
                    $date = \DateTime::createFromFormat('d/m/Y', $stringAsDate);
                    $date->setTime(0,0,0);
                    return $date;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Jornada::class,
        ]);
    }
}
