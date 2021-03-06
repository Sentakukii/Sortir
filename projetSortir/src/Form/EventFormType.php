<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Site;
use App\Entity\State;
use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'required' => true,
            ])
            ->add('date',null, [
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date et heure de la Sortie : ',
                'required' => true,
            ])
            ->add('limitInscription',null, [
               'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date limite d\'inscription : ',
                'required' => true,
            ])
            ->add('maxInscriptions',null, [
                'label' => 'Nombre de places : ',
                'required' => true,
            ])
            ->add('duration',null, [
                'label' => 'Durée : ',
                'required' => true,
            ])
            ->add('description',null, [
                'label' => 'Description et infos : ',
                'required' => true,
            ])
            ->add('site',null, [
                'label' => 'Ville organisatrice  : ',
                'required' => true,
            ])
            ->add('city',  EntityType::class, [
                'mapped' => false ,
                'required' => true,
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville : '
            ])
            ->add('city_label', TextType::class,[
                'mapped' =>false,
                'label' => 'Ville : ',
                'required' => false,
            ])
            ->add('postalCode', TextType::class,[
                'mapped' => false,
                'label' => 'Code postal : ',
                'required' => false,
            ])
            ->add('location',null, [
                'label' => 'Lieux : ',
            ])
            ->add('location_label', TextType::class,[
                'mapped' =>false,
                'label' => 'Lieux : ',
                'required' => false,
            ])
            ->add('address', TextType::class,[
                'mapped' => false,
                'label' => 'Adresse : ',
                'required' => false,
            ])
            ->add('latitude', TextType::class,[
                'mapped' => false,
                'label' => 'Latitude : ',
                'required' => false,
            ]) ->add('longitude', TextType::class,[
                'mapped' => false,
                'label' => 'Longitude : ',
                'required' => false,
            ])
            ->add('state',EntityType::class, [
                'mapped' => false ,
                'required' => true,
                'class' => State::class,
                'choice_label' => 'denomination',
                'label' => 'état : '
            ])
            // 0 = select location   1 = add location
            ->add('type_location',HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'data' => '0',
            ])
            // 0 = select city   1 = add city
            ->add('type_city',HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'data' => '0',
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
