<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Form\EventFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
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
        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
            'cities' => $cities,
        ]);
    }
}