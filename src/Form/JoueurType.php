<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Joueur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class JoueurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

// Modification de la date pour la fixer à partir de 1950
        $builder
            ->add('nom')
            ->add('email', EmailType::class)
            ->add('born_at', BirthdayType::class)       
                     ->add('score')
            ->add('Image', FileType::class)//, array('data_class' => null)
            ->add('game',EntityType::class,[
                'class'=> Game::class,
                'choice_label'=> 'titre'
                ])
            ->add('Valider', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Joueur::class,
        ]);
    }
}
