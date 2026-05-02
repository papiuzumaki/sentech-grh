<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Enum\TypeContrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeContrat', EnumType::class, [
                'class' => TypeContrat::class,
                'label' => 'Type de contrat',
            ])
            ->add('dateDebut', DateType::class, [
                'label'  => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'label'    => 'Date de fin',
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('salaireBase', NumberType::class, [
                'label' => 'Salaire de base (FCFA)',
            ])
            ->add('periodeEssai', CheckboxType::class, [
                'label'    => 'Période d\'essai',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
