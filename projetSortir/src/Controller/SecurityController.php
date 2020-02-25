<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Site;
use App\Entity\Token;
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
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
//        if ($this->getUser()) {
//            return $this->redirectToRoute('home');
//        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout" )
     */
    public function logout()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/passwordforbidden" , name="passwordForbidden")
     */
    public function passwordForbidden( EntityManagerInterface $em){
        $token = new Token();
        $token->setName($this->random(25));
        $token->setType("password");
        $token->setExpirationDate(new \DateTime())->modify('+1 day');
        $em->persist($token);
        $em->flush();

    }


    private function random($var){
        $string = "";
        $chaine = "a0b1c2d3e4f5g6h7i8j9klmnpqrstuvwxy123456789";
        srand((double)microtime()*1000000);
        for($i=0; $i<$var; $i++){
            $string .= $chaine[rand()%strlen($chaine)];
        }
        return $string;
    }
}
