<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{   
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        $isSuperAdmin = false;
        if ($currentUser) {
            $isSuperAdmin = in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles());
            $user = $options['data'];
        }
        $submitLabel = $options['is_edit'] ? 'Edit' : 'Add';

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Mail Address',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Mail Address',
                    'class' => 'ms-2'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => false,
                'empty_data' => 'Abc!-def123456789!',
                'attr' => [
                    'placeholder' => 'Password ... ',
                    'class' => 'ms-2'
                ]
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
                'expanded' => true,
                'multiple' => true,
            ])
            ;

        if ($isSuperAdmin) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Coach' => 'ROLE_COACH',
                    'Player' => 'ROLE_PLAYER',
                    'Parent' => 'ROLE_PARENT',
                    'Administrator' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Roles',
            ]);
        } else {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Coach' => 'ROLE_COACH',
                    'Player' => 'ROLE_PLAYER',
                    'Parent' => 'ROLE_PARENT',
                    'Administrator' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Roles',
            ]);
        }

        $builder
            ->add('submit', SubmitType::class, [
                'label' => $submitLabel,
                'attr' => [
                    'class' => 'btn btn-success btn-sm col-lg-1 col-sm-12 col-md-12 mt-2 mb-2 ms-2',
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false
        ]);
    }
}
