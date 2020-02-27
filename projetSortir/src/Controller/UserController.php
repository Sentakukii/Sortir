<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(EntityManagerInterface $em)
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
        ]);
    }


    /**
     * @Route("/removeUser", name="removeUser")
     */
    public function removeUser(Request $request , EntityManagerInterface $em , UserRepository $userRepository)
    {
        $response = new JsonResponse();
        $user = $userRepository->find($request->request->get('id'));

        if (!$user) {
            $response->setContent(json_encode(['msg' => 'Utilisteur introuvable']));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $em->remove($user);
            $em->flush();
            $response->setContent(json_encode(['msg' => "Suppression de l'utilisateur réussit" ]));
        }
        return $response;
    }

    /**
     * @Route("/desactivateUser", name="desactivateUser")
     */
    public function desactivateUser(Request $request , EntityManagerInterface $em , UserRepository $userRepository)
    {
        $response = new JsonResponse();
        $user = $userRepository->find($request->request->get('userId'));

        if (!$user) {
            $response->setContent(json_encode(['msg' => 'Utilisteur introuvable']));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $user->setActive(false);
            $em->persist($user);
            $em->flush();
            $response->setContent(json_encode(['msg' => "Désactivation de l'utilisateur réussit" ]));
        }
        return $response;
    }
    /**
     * @Route("/activateUser", name="activateUser")
     */
    public function activateUser(Request $request , EntityManagerInterface $em , UserRepository $userRepository)
    {
        $response = new JsonResponse();
        $user = $userRepository->find($request->request->get('userId'));

        if (!$user) {
            $response->setContent(json_encode(['msg' => 'Utilisteur introuvable']));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $user->setActive(true);
            $em->persist($user);
            $em->flush();
            $response->setContent(json_encode(['msg' => "Activation de l'utilisateur réussit" ]));
        }
        return $response;
    }


}
