<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Training;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TrainingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $submitLabel = $options['is_edit'] ? 'Edit' : 'Add';

        $builder
            ->add('startedAt', DateType::class, [
                'label' => 'Started At',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'ms-2',
                ],
            ])
            ->add('endedAt', DateType::class, [
                'label' => 'Ended At',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'ms-2',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function (Category $ent) {
                    return $ent->getTitle();
                },
                'label' => 'Category',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('ent')
                        ->orderBy('ent.title', 'ASC');
                },
                'attr' => [
                    'class' => 'ms-2'
                ],
                'required' => true,
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $submitLabel,
                'attr' => [
                    'class' => 'btn btn-success btn-sm col-lg-1 col-sm-12 col-md-12 ms-2',
                ]
            ])   
            ->add('trainingDay', ChoiceType::class, [
                'choices' => [
                    'Monday' => 1,
                    'Tuesday' => 2,
                    'Wednesday' => 3,
                    'Thursday' => 4,
                    'Friday' => 5,
                    'Saturday' => 6,
                    'Sunday' => 7,
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Training Day',
            ])
            ->add('trainingHour', TimeType::class, [
                'label' => 'Training Hour',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'ms-2',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Training::class,
            'is_edit' => false,
        ]);
    }
}
