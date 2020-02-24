<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    /**
     * @Route("/city", name="city")
     */
    public function index(EntityManagerInterface $em)
    {
        $cities = $em->getRepository(City::class)->findAll();
        return $this->render('city/index.html.twig', [
            'controller_name' => 'CityController',
            'cities' => $cities
        ]);
    }

    /**
     * @Route("/newCity", name="newCity")
     */
    public function newCity(Request $request, EntityManagerInterface $em)
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($city);
            $em->flush();

            $this->addFlash("success", "Ville modifié"); // info warning error

            return $this->redirectToRoute('home');
        }

        return $this->render('city/edit.html.twig', [
            'controller_name' => 'SiteController',
            'cityForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editCity", name="editCity")
     */
    public function editCity(Request $request, EntityManagerInterface $em)
    {
        $citiesRepository = $em->getRepository(City::class);
        $city = $citiesRepository->find($request->get('cityId'));
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($city);
            $em->flush();

            $this->addFlash("success", "Ville modifié"); // info warning error

            return $this->redirectToRoute('home');
        }

        return $this->render('city/edit.html.twig', [
            'controller_name' => 'SiteController',
            'cityForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/deleteCity", name="deleteCity")
     */
    public function deleteCity(Request $request, EntityManagerInterface $em)
    {
        $citiesRepository = $em->getRepository(City::class);
        $city = $citiesRepository->find($request->get('cityId'));

        $em->remove($city);
        $em->flush();

        $this->addFlash("success", "Ville supprimée"); // info warning error

        return $this->redirectToRoute('home');
    }
}
