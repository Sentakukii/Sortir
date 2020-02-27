<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     *
     */
    public function index(EntityManagerInterface $em)
    {
        $sites = $em->getRepository(Site::class)->findAll();
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
            'sites' => $sites
        ]);
    }

    /**
     * @Route("/newSite", name="newSite")
     * @IsGranted("ROLE_ADMIN")
     */
    public function newSite(Request $request, EntityManagerInterface $em)
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($site);
            $em->flush();

            $this->addFlash("success", "Site modifié"); // info warning error

            return $this->redirectToRoute('home');
        }

        return $this->render('site/edit.html.twig', [
            'controller_name' => 'SiteController',
            'siteForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editSite", name="editSite")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editSite(Request $request, EntityManagerInterface $em)
    {
        $sitesRepository = $em->getRepository(Site::class);
        $site = $sitesRepository->find($request->get('siteId'));
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($site);
            $em->flush();

            $this->addFlash("success", "Site modifié"); // info warning error

            return $this->redirectToRoute('home');
        }

        return $this->render('site/edit.html.twig', [
            'controller_name' => 'SiteController',
            'siteForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/removeSite", name="removeSite")
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeSite(Request $request , EntityManagerInterface $em , SiteRepository $siteRepository)
    {
        $response = new JsonResponse();
        $site = $siteRepository->find($request->request->get('id'));

        if (!$site) {
            $response->setContent(json_encode(['msg' => 'Site introuvable']));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $em->remove($site);
            $em->flush();
            $response->setContent(json_encode(['msg' => "Suppression du site réussit" ]));
        }
        return $response;
    }
}
