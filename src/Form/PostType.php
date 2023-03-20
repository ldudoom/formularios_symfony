<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Seleccione una ...',
                'label' => 'Categorías',
            ])
            ->add('title', TextType::class, [
                'label' => 'Título del Post',
                'help' => 'Piensa en el SEO. Cómo buscarias en Google?'
            ])
            ->add('body', TextareaType::class, [
                'label' => 'Contenido del Post',
                'attr' => [
                    'rows' => 10,
                    'class' => 'bg-light'
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Guardar Post',
                'attr' => [
                    'class' => 'btn-success btn-lg'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            //'csrf_protection' => false, // De esta manera podemos eliminar la protección de éste formulario
        ]);
    }
}
