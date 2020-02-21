<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Form\EventFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EventController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/event", name="event")
     */
    public function index()
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }





    /**
     * @Route("/event/create", name="eventCreate")
     */
    public function create(Request $request , EntityManagerInterface $em) {
        return $this->createOrEdit($request, new Event() ,  $em);
    }

    /**
     * @Route("/event/edit", name="eventEdit")
     */
    public function edit(Request $request , EntityManagerInterface $em) {
        if ($request->query->get('eventId') != null) {
            $event = $em->getRepository(Event::class)->find($request->query->get('eventId'));

        return $this->createOrEdit($request, $event, $em);
        } else {
            $this->addFlash("error", "erreur sur la selection de la sortie veuillez réessayer ");
            return $this->redirectToRoute('home');
        }
    }


    private function createOrEdit( Request $request, Event $event, EntityManagerInterface $em)
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);
        $cities = $em->getRepository(City::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $type_location = $form->get('type_location')->getData();
                $type_city = $form->get('type_city')->getData();
                $state = $form->get('state')->getData();


                if (($type_city == "1" || $type_city == "0") && ($type_location == "1" || $type_location == "0") && ($state->getId() == 1 || $state->getId() == 2)) {
                    if($type_city == "1"){
                        $city = new City();
                        $city->setName((string)$form->get('city_label')->getData());
                        $city->setPostalCode((string)$form->get('postalCode')->getData());
                        $cityBDD = $em->getRepository(City::class)->findBy(['name' => $city->getName()]);
                        if ($cityBDD != null){
                           $city = $cityBDD;
                        }else {
                            $em->persist($city);
                            $em->flush();
                        }
                    }else{
                        $city = $form->get('city')->getData();
                    }
                    if ($type_location == "1" || $type_city =="1") {
                        $location = new Location();
                        $location->setName($form->get('location_label')->getData());
                        $location->setCity($city);
                        $location->setAddress($form->get('address')->getData());
                        $location->setLatitude((float)$form->get('latitude')->getData());
                        $location->setLongitude(((float)$form->get('longitude')->getData()));
                        $locationBDD = $em->getRepository(Location::class)->findBy(['name' => $location->getName(), 'latitude' => $location->getLatitude(), 'longitude' => $location->getLongitude()]);
                        if ($locationBDD != null){
                            $location = $locationBDD;
                        }else {
                            $em->persist($location);
                            $em->flush();
                        }
                        $event->setLocation($location);
                    }
                    $event->setOrganizer($this->security->getUser());
                    $event->setState($state);
                    $em->persist($event);
                    $em->flush();
                    if ($state->getId() == 1) {
                        if ($request->query->get('eventId') != null)
                            $this->addFlash("success", "Sortie modifié");
                        else
                            $this->addFlash("success", "Sortie crée");
                    } else if ($state->getId() == 2) {
                        $this->addFlash("success", "Sortie publier");
                    } else {
                        $this->addFlash("warning", "Sortie crée avec un état anormal !!");
                    }
                    return $this->redirectToRoute('home');

                } else {
                    $this->addFlash("error", "Paramètre anormal veuillez recharger la page");
                }
            }catch(\Exception $e){
                $this->addFlash("error", "Erreur veuillez vérifier les champs");
                $this->addFlash("error", $e->getMessage());
            }
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
            'cities' => $cities,
        ]);
    }


/*

    public function edit2(EntityManagerInterface $em, Request $request)
    {

        if ($request->query->get('eventId') != null) {
            $event = $em->getRepository(Event::class)->find($request->query->get('eventId'));
        } else {
            $event = new Event();
        }
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);
        $cities = $em->getRepository(City::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $type_location = $form->get('type_location')->getData();
                $type_city = $form->get('type_city')->getData();

                if ($type_city == "1" || $type_city == "0" && $type_location == "1" || $type_location == "0" && $state->getId() == 1 || $state->getId() == 2) {
                    if($type_city == "1"){
                        $cityForm = $form->get('newCity');
                        $cityBDD = $em->getRepository(City::class)->findBy(['name' => $cityForm->getName()]);

                        if ($cityBDD != null){
                            $city = $cityBDD;
                        } else {
                            $em->persist($cityForm->getData());
                        }
                    } else {
                        $city = $form->get('city')->getData();
                    }

                    if ($type_location == "1" || $type_city =="1") {
                        $locForm = $form->get('newLocation');
                        $locationBDD = $em->getRepository(Location::class)->findBy(['name' => $locForm->get('name'), 'latitude' => $locForm->get('latitude'), 'longitude' => $locForm->get('longitude')]);

                        if ($locationBDD){
                            $location = $locationBDD;
                        } else {
                            $location = $locForm->getData();
                            $em->persist($location);
                        }

                        $event->setLocation($location);
                    }

                    $event->setOrganizer($this->security->getUser());
                    $em->persist($event);
                    $em->flush();

                    if ($state->getId() == 1) {
                        if ($request->query->get('eventId') != null)
                            $this->addFlash("success", "Sortie modifié");
                        else
                            $this->addFlash("success", "Sortie crée");
                    } else if ($state->getId() == 2) {
                        $this->addFlash("success", "Sortie publier");
                    } else {
                        $this->addFlash("warning", "Sortie crée avec un état anormal !!");
                    }

                    return $this->redirectToRoute('home');

                } else {
                    $this->addFlash("error", "Paramètre anormal veuillez recharger la page");
                }
            }catch(\Exception $e){
                $this->addFlash("error", "Erreur veuillez vérifier les champs");
                $this->addFlash("error", $e->getMessage());
            }
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
            'cities' => $cities,
        ]);
    }*/

//    public function create(Request $request) {
//        return $this->createOdEdit($request, new Event());
//    }
//
//    /**
//     * @Route("/{id}")
//     */
//    public function edit(Request $request, Event $event) {
//        return $this->createOdEdit($request, $event);
//    }
//
//    private function createOdEdit() {
//    }
}