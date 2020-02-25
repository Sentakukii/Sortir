<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\ProfilType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

            $imagePath = $form->get('imagePath')->getData();

            if ($imagePath) {
                $originalFilename = pathinfo($imagePath->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('upload_directory'), // $this->getParameter('kernel.project_dir').
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                /** @var User $user */
                $user->setImagePath($newFilename);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Profil modifié"); // info warning error

            return $this->redirectToRoute('home');
        }

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'sites' => $siteRepository->findAll(),
            'profilForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/displayProfil", name="displayProfil")
     */
    public function displayProfil(Request $request, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($request->get('organizerId'));

        return $this->render('profil/displayProfil.html.twig', [
            'controller_name' => 'ProfilController',
            'user' => $user
        ]);
    }
}
