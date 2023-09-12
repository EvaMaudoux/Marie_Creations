<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\Workshop;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('workshop', EntityType::class, [
                'class' => Workshop::class,
                'choice_label' => 'name',
                'placeholder' => 'Type d\'atelier',
            ])


            ->add('title',TextType::class, [
                'label' => 'Nom',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'atelier',
            ])
            ->add('start', DateTimeType::class, [
                'date_widget' => 'single_text',
                'label' => 'Début',
                ])
            ->add('end', DateTimeType::class, [
                'date_widget' => 'single_text',
                'label' => 'Fin',
            ])
            ->add('all_day',  CheckboxType::class)
            ->add('background_color', ColorType::class, [
                'label' => 'Couleur de fond'
            ])
            ->add('border_color', ColorType::class, [
                'label' => 'Couleur de la bordure'
            ])
            ->add('text_color', ColorType::class, [
                'label' => 'Couleur du texte'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
