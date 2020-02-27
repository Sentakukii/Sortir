<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


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
     * @IsGranted("ROLE_USER_ACTIVE")
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

            return $this->redirectToRoute('city');
        }

        return $this->render('city/edit.html.twig', [
            'controller_name' => 'SiteController',
            'cityForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editCity", name="editCity")
     * @IsGranted("ROLE_ADMIN")
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

            return $this->redirectToRoute('city');
        }

        return $this->render('city/edit.html.twig', [
            'controller_name' => 'SiteController',
            'cityForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/removeCity", name="removeCity")
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeCity(Request $request , EntityManagerInterface $em , CityRepository $cityRepository)
    {
        $response = new JsonResponse();
        $city = $cityRepository->find($request->request->get('id'));

        if (!$city) {
            $response->setContent(json_encode(['msg' => 'Ville introuvable']));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $em->remove($city);
            $em->flush();
            $response->setContent(json_encode(['msg' => "Suppression de la ville réussit" ]));
        }
        return $response;
    }


}
