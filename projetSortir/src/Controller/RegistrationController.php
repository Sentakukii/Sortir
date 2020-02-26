<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Token;
use App\Entity\User;
use App\Form\ImportCsvType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordUserType;
use App\Form\SendResetPasswordUserType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Services\ToolBoxService;
use Doctrine\ORM\EntityManager;
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
    private $toolBoxService;

    public function __construct()
    {
        $this->toolBoxService = new ToolBoxService();
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, EntityManagerInterface $em): Response
    {

        $key = $request->query->get("key");
        $token = null;
        if ($key) {
            $tokenRepository = $em->getRepository(Token::class);
            $token = $tokenRepository->findOneBy(["name" => $key, "type" => $this->getParameter('type_token_create')]);
        }
        $isAdmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        if($isAdmin || ($token && $token->getExpirationDate() > new \DateTime() )){
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);
            $filename = null;
            $formCsv = $this->createForm(ImportCsvType::class, $filename);
            $formCsv->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $em->persist($user);
                if($token){
                    $em->remove($token);
                }
                $em->flush();

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            } elseif ($formCsv->isSubmitted() && $formCsv->isValid() && $isAdmin) {
                $filename = $formCsv->get('csvPath')->getData();
                $this->importCSV($passwordEncoder, $em , $filename);
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
                'csvForm' => $formCsv->createView(),
            ]);
        }
        return $this->redirectToRoute('home');
    }



    private function importCSV(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em , $filename ): Response
    {
        try {
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
            $token = $tokenRepository->findOneBy(["name" => $key, "user" => $user , "type" => $this->getParameter('type_token_password')]);
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

    /**
     * @Route("/generatetoken", name="generateToken")
     * @IsGranted("ROLE_ADMIN")
     */
    public function generateToken(EntityManagerInterface $em){
        $token = new Token();
        $token->setName($this->toolBoxService->random(25));
        $token->setType($this->getParameter('type_token_create'));
        $token->setExpirationDate((new \DateTime())->modify('+1 week'));
        $em->persist($token);
        $em->flush();
        $url = $this->generateUrl("app_register", ["key" => $token->getName()]);
        /*SEND Email to admin */
        /*
         * TODO send email
         */
        /*END SEND*/
        /*DEV*/
        // TODO remove
        $this->addFlash("info", $url);
        return $this->redirectToRoute('home');
        /*END DEV*/
    }
}
