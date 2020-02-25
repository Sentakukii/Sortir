<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\State;
use App\Form\CancelEventType;
use App\Form\EventFormType;
use App\Form\RegistrationFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
                if ($event->getDate() > new \DateTime()) {

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
                } else {
                    $this->addFlash("error", "la date est inférieur a la date du jour");
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

    /**
     * @Route("/event/register", name="register")
     */
    public function registerToEvent(EntityManagerInterface $em, EventRepository $eventRepository, Request $request)
    {
        $response = new JsonResponse();
        $event = $eventRepository->find($request->request->get('eventId'));

        if($event->getUsersList()->contains($this->security->getUser())) {
            $response->setContent(json_encode(['msg' => 'Vous êtes déjà inscrit', 'nbRegister' => sizeof($event->getUsersList())]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }else if ($event->getLimitInscription() < new \DateTime()){
            $response->setContent(json_encode(['msg' => 'La date d\'inscription est dépassé', 'nbRegister' => sizeof($event->getUsersList())]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else if (sizeof($event->getUsersList()) == $event->getMaxInscriptions()) {
            $response->setContent(json_encode(['msg' => 'Nombre maximum d\'inscrits atteint', 'nbRegister' => sizeof($event->getUsersList())]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $event->addUsersList($this->security->getUser());
            $em->persist($event);
            $em->flush();
            $response->setContent(json_encode(['msg' => "Inscription réussit", 'nbRegister' => sizeof($event->getUsersList()) ]));
        }

        return $response;
    }

    /**
     * @Route("/event/deregister", name="deregister")
     */
    public function deregisterToEvent(EntityManagerInterface $em, EventRepository $eventRepository, Request $request)
    {
        $response = new JsonResponse();
        $event = $eventRepository->find($request->request->get('eventId'));

        if (!$event->getUsersList()->contains($this->security->getUser())) {
            $response->setContent(json_encode(['msg' => 'Vous n\'êtes pas inscrit', 'nbRegister' => sizeof($event->getUsersList())]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $event->removeUsersList($this->security->getUser());
            $em->persist($event);
            $em->flush();
            $response->setContent(json_encode(['msg' => "Désinscription réussit" , 'nbRegister' => sizeof($event->getUsersList())] ));
        }
        return $response;
    }

    /**
     * @Route("/event/cancel", name="cancelEvent")
     */
    /*public function cancelEvent(EntityManagerInterface $em, EventRepository $eventRepository, Request $request)
    {
        $response = new JsonResponse();
        $event = $eventRepository->find($request->request->get('eventId'));
        $comment = $eventRepository->find($request->request->get('comment'));

        if($event->getDate() < new \DateTime()){
            $response->setContent(json_encode(['msg' => 'La sortie a déja été éffectué']));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }elseif (!$event->getState(2) && !$event->getState(2)){
            $response->setContent(json_encode(['msg' => 'La sortie doit etre dans l\'état "créée" ou "ouvert" pour etre annulée' ]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }else{
            $event->setState($em->getRepository(State::class)->find(6));
            $event->setComment($comment);
            $em->persist($event);
            $em->flush();
            $response->setContent(json_encode(['msg' => "Annulation réussit" ]));
        }

        return $response;
    }*/

    /**
     * @Route("/cancelEvent", name="cancelEvent")
     */
    public function cancelEvent(EntityManagerInterface $em, EventRepository $eventRepository, Request $request)
    {
        $event = $eventRepository->find($request->get('eventId'));
        $comment = null;
        $form = $this->createForm(CancelEventType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($event->getDate() < new \DateTime()) {
                $this->addFlash("error", "La sortie a déja été éffectué"); // info warning error
            } elseif (!$event->getState(2) && !$event->getState(2)) {
                $this->addFlash("error", "La sortie doit etre dans l'état \"créée\" ou \"ouvert\" pour etre annulée");
            } else {
                $this->addFlash("success", "La sortie a été annulée");
                $comment = $form["comment"]->getData();
                $event->setState($em->getRepository(State::class)->find(6));
                $event->setComment($comment);
                $em->persist($event);
                $em->flush();
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('event/cancel.html.twig', array(
            'cancelEventForm' => $form->createView(),
        ));
    }
}