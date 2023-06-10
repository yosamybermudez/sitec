<?php

namespace App\Form;

use App\Entity\OrdenReparacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdenReparacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('observaciones')
            ->add('movimientosMateriaPrima', CollectionType::class, array(
                'entry_type' => MateriaPrimaMovimientoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'attr' => ['class' => 'material row', 'label' => false]
                ]
            ))
            ->add('ingreso', TextType::class, array(
                'label' => 'Importe del servicio ($)'
            ))
//            ->add('gastoMateriales', TextType::class, array(
//                'label' => 'Gasto de materiales ($)'
//            ))
            ->add('created', TextType::class, array(
                'label' => 'Fecha de revisión',
                'required' => false
            ))
            ->add('otrosMateriales', TextType::class, array(
                'required' => false,
                'help' => 'De haber: Especifique los materiales no registrados que se usaron para la reparación. De lo contrario deje ambos campos en blanco',
                'label' => 'Materiales usados'
            ))
            ->add('otrosGastos', NumberType::class, array(
                'required' => false,
                'label' => 'Valor total ($)'
            ))
//            ->add('materialesUsados', TextType::class, array(
//                'required' => false
//            ))
            ->add('estadoFinal', ChoiceType::class, ['choices' => ['Reparado' => 'R', 'No reparado' => 'NR'], 'required' => true])
            ->add('observacionesFinales', ChoiceType::class, array(
                'label' => 'Motivo',
                'choices' => [
                    'Problema no encontrado' => 'PNE',
                    'Equipo en mal estado' => 'EME',
                    'No se encontró defecto' => 'NED',
                    'Otro problema' => 'OP',
                ],
                'placeholder' => 'Seleccione',
                'mapped' => false,
                'required' => false))
            ->add('diasGarantia', TextType::class, array(
                'required' => true
            ))
        ;
        $builder->get('created')
            ->addModelTransformer(new CallbackTransformer(
                function ($dateAsString) {
                    // transform date to string
                    $fecha = $dateAsString ? $dateAsString : new \DateTime();
                    return $fecha->format('d/m/Y h:i:s a');
                },
                function ($stringAsDate) {
                    // transform the string to date
                    $stringAsDate = str_replace('-','/', $stringAsDate);
//                    dd($stringAsDate);
                    $fecha = \DateTime::createFromFormat('d/m/Y', $stringAsDate);
                    return $fecha;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrdenReparacion::class,
        ]);
    }
}
