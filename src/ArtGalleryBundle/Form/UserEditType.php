<?php

namespace ArtGalleryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('roles', ChoiceType::class , array(
            'choices' =>[
                'Admin' => "ROLE_ADMIN",
                'Moderator' => "ROLE_MOD",
                'User' => "ROLE_USER",
            ],
            'expanded' =>false,
            'multiple' =>true,
        ));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ArtGalleryBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'art_gallery_bundle_user_edit_type';
    }
}
