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
        $user = $userRepository->find($request->request->get('userId'));

        if (!$user) {
            $response->setContent(json_encode(['msg' => 'utilisteur introuvable']));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $em->remove($user);
            $em->flush();
            $response->setContent(json_encode(['msg' => "supression de l'utilisateur réussit" ]));
        }
        return $response;
    }


}
