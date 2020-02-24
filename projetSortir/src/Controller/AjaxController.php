<?php

namespace App\Controller;

use App\Entity\City;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AjaxController extends AbstractController
{

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }
    private $security;

    /**
     * @Route("/ajax", name="ajax")
     */
    public function index()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/getlocations" ,name="getLocations")
     */
    public function getLocation(LocationRepository $locationRepository, Request $request){

        $response = new JsonResponse();
        $locations = null;
        $city = $request->request->get('cityId');

        if($city) {
            $locations = $locationRepository->findBy(['city' => $city]);
        }else {
            $locations = $locationRepository->findAll();
        }

        $tabId = [];
        $tabName = [];
        foreach ($locations as $value){
            array_push($tabId, $value->getId());
            array_push($tabName, $value->getName());
        }

        $tab = ['Mathilde' => 27, 'Pierre' => 29, 'Amandine' => 21];

        $response->setContent(json_encode(['msg' => 'liste des lieux', 'locationsId' => $tabId , 'locationsName' => $tabName]));
        return $response;
    }
}
