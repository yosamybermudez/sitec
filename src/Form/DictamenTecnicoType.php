<?php

namespace App\Form;

use App\Entity\DictamenTecnico;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictamenTecnicoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dejarEnTaller')
            ->add('created', TextType::class, array(
                'label' => 'Fecha y hora de revisión'
            ))
            ->add('dictamen', ChoiceType::class, array(
                'choices' => [
                    'Se revisará ahora' => 'RA',
                    'Dejar en taller' => 'DT',
                    'No tengo la pieza que se necesita' => 'NPN',
                    'No lo puedo reparar' => 'NPR',
                    'Asignar a otro técnico' => 'AOT',
                    'El cliente se fue' => 'CF',
                ],
                'placeholder' => 'Seleccione',
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
                    return \DateTime::createFromFormat('d/m/Y', $stringAsDate);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DictamenTecnico::class,
        ]);
    }
}
