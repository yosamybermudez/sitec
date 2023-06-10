<?php

namespace App\Form;

use App\Entity\Cargo;
use App\Entity\Rol;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UsuarioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nombre de usuario'
            ])
            ->add('isActive', null, [
                'label' => 'Activo',
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'required' => false
            ])
            ->add('rolesObjects', EntityType::class, [
                'label' => 'Roles',
                'class' => Rol::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione',
                'multiple' => true,
                'attr' => ['size' => 10],
                'required' => true,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('c')->orderBy('c.rango', 'ASC');
                }
            ])
            ->add('cargo', EntityType::class, [
                'label' => 'Cargo en el taller',
                'class' => Cargo::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione',
                'required' => true,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('c')->orderBy('c.nombre', 'ASC');
                }
            ])
            ->add('nombres', TextType::class, array(
                'required' => true
            ))
            ->add('apellidos')
            ->add('carneIdentidad', TextType::class, [
                'label' => 'Carné de identidad',
                'required' => false
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'required' => false
            ])
            ->add('municipio')
            ->add('fechaAlta', TextType::class, [
                'label' => 'Fecha de alta',
                'required' => false
            ])
            ->add('sexo', ChoiceType::class, ['choices' => ['Masculino' => 'Masculino', 'Femenino' => 'Femenino'], 'placeholder' => 'Seleccione', 'required' => false])
            ->add('foto', FileType::class, array(
                'label' => 'Foto',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Seleccione un archivo JPG, JPEG o PNG'
                    ])
                ]
            ));

        $builder->get('fechaAlta')
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

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Usuario'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'usuario';
    }


}
