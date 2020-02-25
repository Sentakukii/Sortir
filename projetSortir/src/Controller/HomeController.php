<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Location;
use App\Entity\Site;
use App\Entity\State;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\SiteRepository;
use App\Repository\StateRepository;
use App\Services\HomeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Time;

class HomeController extends AbstractController
{
    private $security;
    private $start;
    private $homeService;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
        $this->start = false;
        $this->homeService = new HomeService();
    }

    /**
     * @Route("/home", name="home")
     */
    public function index(SiteRepository $siteRepo, EventRepository $eventRepo,  Request $request)
    {

        $siteSelected = $request->query->get('site');
        if($siteSelected == null){
            $siteSelected = $siteRepo->find($this->getUser()->getSite()->getId());
        } else {
            $siteSelected = $siteRepo->find($siteSelected);
        }
        $events = $this->homeService->buildQuery($request, $eventRepo, $siteRepo , $this->getUser());



        return $this->render('home/index.html.twig', array(
            'sites' => $siteRepo->findAll(),
            'events' => $events,
            'siteSelected' => $siteSelected
        ));
    }

    /**
     * @Route("/publishEvent", name="publishEvent")
     */
    public function publishEvent(SiteRepository $siteRepo, EventRepository $eventRepo, StateRepository $stateRepo,Request $request)
    {
        $siteSelected = $request->query->get('site');
        if($siteSelected == null){
            $siteSelected = $siteRepo->find($this->getUser()->getSite()->getId());
        }else{
            $siteSelected = $siteRepo->find($siteSelected);
        }

        $event = $eventRepo->find($request->get('eventId'));
        $state = $stateRepo->find('2');
        $event->setState($state);
        $events = $this->homeService->buildQuery($request, $eventRepo, $siteRepo , $this->getUser());

        return $this->render('home/index.html.twig', array(
            'sites' => $siteRepo->findAll(),
            'events' => $events,
            'siteSelected' => $siteSelected
        ));
    }

}
