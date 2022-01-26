<?php

namespace App\Form;

use App\Entity\Comment;
use App\Validator\FileManaged;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author',TextType::class,[
                'row_attr' => [
                    'class' => 'form-inline'
                ],
                'label_attr' => [
                    'class' => 'mr-3'
                ],
                'attr' => [
                    'class' => 'form-control-sm w-50'
                ],
                'required' => true
            ])
            ->add('email', EmailType::class,[
                'row_attr' => [
                    'class' => 'form-inline'
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'mr-3'
                ],
                'attr' => [
                    'class' => 'form-control-sm w-50'
                ]
            ])
            ->add('message')
            ->add('files',CollectionType::class,[
                'entry_type' => FileManagedType::class,
                'entry_options'=>[
                    'label' => false,
                    'attr' => [
                        'onchange' => 'fixFileInputName(this)'
                    ],
                    'constraints' => [
                        new FileManaged([
                            'image/*'
                        ])
                    ]
                ],
                'allow_add'=>true,
                'attr'=> [
                    'class' => 'input-row-wrapper'
                ]
            ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
