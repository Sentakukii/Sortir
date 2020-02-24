<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
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
     * @Route("/deleteSite", name="deleteSite")
     */
    public function deleteSite(Request $request, EntityManagerInterface $em)
    {
        $sitesRepository = $em->getRepository(Site::class);
        $site = $sitesRepository->find($request->get('siteId'));

        $em->remove($site);
        $em->flush();

        $this->addFlash("success", "Site supprimée"); // info warning error

        return $this->redirectToRoute('home');
    }
}
