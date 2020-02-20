<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\ProfilType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class ProfilController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/profil", name="profil")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $siteRepository = $em->getRepository(Site::class);
        $user = $this->security->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

           // $entityManager = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // do anything else you need here, like send an email

            $this->addFlash("success", "Profil modifié"); // info warning error

            return $this->redirectToRoute('home');
        }

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'sites' => $siteRepository->findAll(),
            'profilForm' => $form->createView(),
        ]);
    }
}
