<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Site;
use App\Entity\Token;
use App\Entity\User;
use App\Form\SendResetPasswordUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function passwordForbidden( EntityManagerInterface $em,  Request $request){

        $email = null;
        $form = $this->createForm(SendResetPasswordUserType::class, $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get("email")->getData();
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if($user) {
                if ($user->getToken()) {
                    $token = $user->getToken();
                } else {
                    $token = new Token();
                    $token->setName($this->random(25));
                    $token->setUser($user);
                    $token->setType($this->getParameter('type_password'));
                }
                $token->setExpirationDate((new \DateTime())->modify('+1 day'));
                $em->persist($token);
                $em->flush();
                /*send Email
                return $this->redirectToRoute('login');
                */
                /* TEST */
                return $this->redirectToRoute('resetPassword',["key" => $token->getName() , "idUser" => $token->getUser()->getId()]);
                /*END TEST*/
            }else{
                $this->addFlash("error","adresse email non reconnue");
            }
        }

        return $this->render('security/passwordForbidden.html.twig', array(
            'sendResetPasswordUser' => $form->createView(),
        ));
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
