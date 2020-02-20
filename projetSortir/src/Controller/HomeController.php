<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Location;
use App\Entity\Site;
use App\Entity\State;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Time;

class HomeController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/home", name="home")
     */
    public function index(SiteRepository $siteRepo, EventRepository $eventRepo)
    {
        return $this->render('home/index.html.twig', array(
            'sites' => $siteRepo->findAll(),
            'events' => $eventRepo->findAll()
        ));
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerToEvent(EntityManagerInterface $em, EventRepository $eventRepository, Request $request)
    {
        $response = new JsonResponse();
        $event = $eventRepository->find($request->request->get('eventId'));


        if($event->getUsersList()->contains($this->security->getUser())) {
            $response->setContent(json_encode(['msg' => 'Vous êtes déjà inscrit', 'nbRegister' => sizeof($event->getUsersList())]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }else if ($event->getLimitInscription() <  new \DateTime()){
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
     * @Route("/deregister", name="deregister")
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
            $response->setContent(json_encode(['success' => sizeof($event->getUsersList()),'msg' => "Désinscription réussit"] ));
        }
        return $response;
    }
}
