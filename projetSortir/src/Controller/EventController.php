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
     * @Route("/event/edit", name="eventEdit")
     */
    public function edit(EntityManagerInterface $em, Request $request)
    {

        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);
        $cities = $em->getRepository(City::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->get('type')->getData();

            if ($type == "1"){
                $location = new Location();
                $location->setName($form->get('location_label')->getData());
                $location->setCity($form->get('city')->getData());
                $location->setAddress($form->get('address')->getData());
              //  $location->set($form->get('postalCode')->getData());
                $location->setLatitude((float)$form->get('latitude')->getData());
                $location->setLongitude(((float)$form->get('longitude')->getData()));
               /* $em->persist($location);
                $em->flush();*/
                $event->setLocation($location);
            }

            $state = $form->get('state')->getData();
            $event->setOrganizer( $this->security->getUser());
            $event->setState($state);
            /*$em->persist($event);
            $em->flush();*/
            if($state->getId() == 1) {
                $this->addFlash("success", "Sortie crée"); // info warning error
            }else if($state->getId() == 2){
                $this->addFlash("success", "Sortie publier");
            }else{
                $this->addFlash("warning", "Sortie crée avec un état anormal !!");
            }
             return $this->redirectToRoute('home');
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
            'cities' => $cities,
        ]);
    }
}