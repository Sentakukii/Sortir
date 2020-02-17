<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {

//        $site = new Site();
//        $site->setName("eni ecole");
//
//        $em->persist($site);
//
//        $em->flush();

       /* $user = new User();
        $user->setName("Gobron");
        $user->setFirstName("Fabien");
        $user->setEmail("fabien.gobron2018@campus-eni.fr");
        $user->setPhone("0778762835");
        $encoded = $encoder->encodePassword($user, "password");
        $user->setPassword($encoded);
        $user->setActive(true);
        $user->setSite( $em->getRepository(Site::class)->find(1));

        $em->persist($user);

        $em->flush();*/

        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
