<?php

namespace App\Form;

use App\Entity\Player;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $submitLabel = $options['is_edit'] ? 'Edit' : 'Add';

        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Lastname',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Lastname',
                    'class' => 'ms-2'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Firstname',
                'required' => true,
                'attr' => [
                    'placeholder' => 'firstname',
                    'class' => 'ms-2'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $submitLabel,
                'attr' => [
                    'class' => 'btn btn-success btn-sm col-lg-1 col-sm-12 col-md-12 ms-2',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Player::class,
            'is_edit' => false,
        ]);
    }
}
