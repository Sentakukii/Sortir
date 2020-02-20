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

//    /**
//     * @Route("/home", name="home")
//     */
//    public function index(EntityManagerInterface $em, Security $security)
//    {
//        $siteRepository = $em->getRepository(Site::class);
//        $eventRepository = $em->getRepository(Event::class);
//
//        return $this->render('home/index.html.twig', [
//            'controller_name' => 'HomeController',
//            'sites' => $siteRepository->findAll(),
//            'events' => $eventRepository->findAll()
//        ]);
//    }

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
        $event->addUsersList($this->security->getUser());

        if (sizeof($event->getUsersList()) == $event->getMaxInscriptions()) {
            $response->setContent(json_encode(['error' => 'too many people register', 'nbRegister' => sizeof($event->getUsersList())]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $em->persist($event);
            $em->flush();

            $response->setContent(json_encode(['success' => sizeof($event->getUsersList())]));
        }

        return $response;
    }
}
