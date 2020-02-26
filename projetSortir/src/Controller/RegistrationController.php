<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordUserType;
use App\Form\SendResetPasswordUserType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @IsGranted("ROLE_ADMIN")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/importCSV", name="importCSV")
     * @IsGranted("ROLE_ADMIN")
     */
    public function importCSV(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em ): Response
    {
        try {
            dump($this->container->getParameter(('public')));
            die();
            $filename = 'C:\Users\fgobron2018\Documents\projet\Sortir\projetSortir\public\csv\users.csv';

            $handle = fopen($filename, 'r');

            $contents = fread($handle, filesize($filename));
            fclose($handle);
            $contents = trim($contents);
            $array = explode(PHP_EOL, $contents);


            for ($i = 1; $i < sizeof($array); $i++) {
                $row = $array[$i];
                $cell = explode(",", $row);
                $user = new User();
                $user->setEmail($cell[0]);
                $user->setName($cell[1]);
                $user->setFirstName($cell[2]);
                $user->setPhone($cell[3]);
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $cell[4]
                    )
                );

                $site = $em->getRepository(Site::class)->findBy(['name' => $cell[5]]);


                if (!$site) {
                    $site = new  Site();
                    $site->setName($cell[5]);
                    $em->persist($site);
                }else{
                    $site= $site[0];
                }

                $user->setSite($site);


                if (!$em->getRepository(User::class)->findBy(["email" => $cell[0]]))
                    $em->persist($user);
                $em->flush();
            }

            $this->addFlash("success", "import csv réussit");

        }catch (\Exception $e){
            $this->addFlash("error", "erreur dans l'import du csv");
    }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/resetpassword", name="resetPassword")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, TokenRepository $tokenRepository, UserRepository $userRepository ): Response
    {
        $key = $request->query->get("key");
        $idUser = $request->query->get("idUser");
        $user = $userRepository->find($idUser);
        $form = $this->createForm(ResetPasswordUserType::class, $user);
        $form->handleRequest($request);
        if ($key) {
            $token = $tokenRepository->findOneBy(["name" => $key, "user" => $user , "type" => $this->getParameter('type_password')]);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    // encode the plain password
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    );
                    $em->persist($user);
                    $em->remove($token);
                    $em->flush();

                    $this->addFlash("success", "mot de passe changé");

                    return $this->redirectToRoute('login');

                } catch (\Exception $e) {
                    $this->addFlash("error", "erreur dans l'enregistrement du mot de passe");
                }
            }
            if ($token && $token->getExpirationDate() > new \DateTime()) {
                return $this->render('registration/resetPassword.html.twig', array(
                    'ResetPasswordUser' => $form->createView(),
                ));
            } else {
                $this->addFlash("error", "token invalide");
            }
        } else {
            $this->addFlash("error", "pas de token");
        }
        return $this->redirectToRoute('login');
    }
}
