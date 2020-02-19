<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Site;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null, [
                'label' => 'Nom de la Sortie : ',
            ])
            ->add('date',null, [
                'label' => 'Date et heure de la Sortie : ',
            ])
            ->add('limitInscription',null, [
                'label' => 'Date limite d\'inscription : ',
            ])
            ->add('maxInscriptions',null, [
                'label' => 'Nombre de places : ',
            ])
            ->add('duration',null, [
                'label' => 'Durée : ',
            ])
            ->add('description',null, [
                'label' => 'Description et infos : ',
            ])
            ->add('location',null, [
                'label' => 'Lieux : ',
            ])
            ->add('city',  EntityType::class, [
                'mapped' => false ,
                'required' => true,
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville : '
            ])
        ;

//        $builder->get("city")->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) {
//                $form = $event->getForm();
//
//                $data = $event->getData();
//
//                $city = $data->getCity();
//                $positions = null === $city ? [] : $city->getLocations();
//
//                $form->add('location', null, [
//                    'class' => 'App\Entity\Location',
//                    'placeholder' => '',
//                    'label' => 'Lieu : ',
//                    'choices' => $positions,
//                ]);
//            }
//        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
