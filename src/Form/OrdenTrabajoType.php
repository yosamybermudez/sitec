<?php

namespace App\Form;

use App\Entity\EquipoTipo;
use App\Entity\OrdenTrabajo;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdenTrabajoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nroOrden', HiddenType::class, array(
                'label' => 'No. Orden',
            ))
            ->add('garantiaOrdenPrincipal', EntityType::class, array(
                'class' => OrdenTrabajo::class,
                'choice_label' => 'nroOrden',
                'required' => false
            ))
            ->add('clienteNombreCompleto', TextType::class, array(
                'label' => 'Nombre(s) y Apellidos'
            ))
            ->add('clienteCarneIdentidad', TextType::class, array(
                'label' => 'Carné de identidad',
                'attr' => [
                    'maxlength' => 11
                ]
            ))
            ->add('clienteTelefonosContacto', TextType::class, array(
                'label' => 'Teléfono'
            ))
            ->add('equipoTipo', EntityType::class, [
                'class' => EquipoTipo::class,
                'label' => 'Tipo de equipo',
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione',
                'required' => true
            ])
            ->add('esReparacion', null, array(
                'required' => false,
                'label' => '¿Hay que reparar?',
            ))
            ->add('equipoMarca', TextType::class, array(
                'label' => 'Marca'
            ))
            ->add('equipoModelo', TextType::class, array(
                'label' => 'Modelo'
            ))
            ->add('equipoSerie', TextType::class, array(
                'label' => 'No. Serie',
                'required' => false
            ))
            ->add('fechaEntrada', TextType::class, array(
                'label' => 'Fecha de entrada'
            ))
            ->add('observaciones', TextType::class, array(
                'label' => 'Notas',
                'required' => false
            ))
            ->add('motivoVisita', TextType::class, array(
                'label' => 'Motivo de visita'
            ))
            ->add('tecnicoRepara', EntityType::class, [
                'class' => Usuario::class,
                'label' => 'Técnico asignado',
                'choice_label' => 'nombreCargo',
                'placeholder' => 'Seleccione',
                'required' => true,
                'query_builder' => function ($er) {
                    $date = new \DateTime();
                    $date->format('Y-m-d');
                    $date->setTime(0,0,0);
                    return $er->createQueryBuilder('d')
                        ->innerJoin('d.cargo', 'c')
                        ->innerJoin('d.registrosAsistencia', 'r')
                        ->innerJoin('r.jornada', 'j')
                        ->where("c.nombre LIKE '%Técnico%'")
                        ->orWhere("c.nombre LIKE '%Tecnico%'")
                        ->andWhere("j.fecha = :date")
                        ->andWhere("r.horaSalida is null")
                        ->setParameter('date', $date)
                        ->orderBy('d.nombres', 'ASC');
                }
            ])
        ;
        $builder->get('fechaEntrada')
            ->addModelTransformer(new CallbackTransformer(
                function ($dateAsString) {
                    // transform date to string
                    $fecha = $dateAsString ? $dateAsString : new \DateTime();
                    return $fecha->format('d/m/Y h:i:s a');
                },
                function ($stringAsDate) {
                    // transform the string to date
                    $stringAsDate = str_replace('-','/', $stringAsDate);
                    $fecha = \DateTime::createFromFormat('d/m/Y', $stringAsDate);
                    return $fecha;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrdenTrabajo::class,
        ]);
    }
}
