<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $submitLabel = $options['is_edit'] ? 'Edit' : 'Add';

        $builder           
        ->add('title', TextType::class, [
            'label' => 'Title',
            'required' => true,
            'attr' => [
                'placeholder' => 'Category\'s name',
                'class' => 'ms-2',
            ]
        ])
       
        ->add('submit', SubmitType::class, [
            'label' => $submitLabel,
            'attr' => [
                'class' => 'btn btn-success btn-sm col-lg-2 col-sm-12 col-md-12 ms-2',
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'is_edit' => false,
        ]);
    }
}
