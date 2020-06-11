<?php

namespace App\Form;

use App\Entity\Program;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Actor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['label' => 'Titre'])
            ->add('summary', null, ['label' => 'Synopsis'])
            ->add('poster', null, ['label' => 'Affiche', 'attr' => ['placeholder' => 'url']])
            ->add('year',null, ['label'=> 'Année'])
            ->add('category', null, ['choice_label' => 'name', 'label' => 'Catégorie'])
        ;
        $builder->add('actors', EntityType::class, [
            'class' => Actor::class,
            'multiple' => true,
            'expanded' => true,
            'choice_label' => 'name',
            'by_reference' => false,
            'label' =>'Acteurs'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
