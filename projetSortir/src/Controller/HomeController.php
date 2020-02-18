<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Location;
use App\Entity\Site;
use App\Entity\State;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Time;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(EntityManagerInterface $em, Security $security)
    {
        $siteRepository = $em->getRepository(Site::class);
        $eventRepository = $em->getRepository(Event::class);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sites' => $siteRepository->findAll(),
            'events' => $eventRepository->findAll()
        ]);
    }
}
