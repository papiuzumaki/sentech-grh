<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\Poste;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'attr'  => ['placeholder' => 'EMP-001'],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ])
            ->add('dateNaissance', BirthdayType::class, [
                'label'  => 'Date de naissance',
                'widget' => 'single_text',
            ])
            ->add('genre', ChoiceType::class, [
                'label'   => 'Genre',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
            ])
            ->add('departement', EntityType::class, [
                'class'        => Departement::class,
                'choice_label' => 'nom',
                'label'        => 'Département',
                'placeholder'  => '-- Sélectionner --',
                'required'     => false,
            ])
            ->add('poste', EntityType::class, [
                'class'        => Poste::class,
                'choice_label' => 'intitule',
                'label'        => 'Poste',
                'placeholder'  => '-- Sélectionner --',
                'required'     => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
