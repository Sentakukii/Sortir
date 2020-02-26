<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\ImportCsvType;
use App\Form\RegistrationFormType;
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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, EntityManagerInterface $em): Response
    {
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        } elseif ($formCsv->isSubmitted() && $formCsv->isValid()) {
            try {
                dump('test');
                $filename = $formCsv->get('csvPath')->getData();
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
                $this->addFlash("error", $e->getMessage());

            }
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'csvForm' => $formCsv->createView(),
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
            $this->addFlash("error", $e->getMessage());

    }
        return $this->redirectToRoute('home');
    }
}
