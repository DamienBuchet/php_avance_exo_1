<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    #[Route('/users', name: 'app_users', methods: 'GET')]
    public function users(ManagerRegistry $doctrine): JsonResponse
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        $utilisateurs = [];
        foreach ($users as $user) {
            $u = [];
            $u['id'] = $user->getId();
            $u['pseudo'] = $user->getPseudo();
            $u['password'] = $user->getPassword();
            $u['profil_pic'] = $user->getProfilPic();
            $u['description'] = $user->getDescription();
            $u['suivis'] = $user->getSuivis();
            $u['likes'] = $user->getLikes();
            $u['role'] = $user->getRole();
            $utilisateurs[] = $u;
        }
        return $this->json([
            'users' => $utilisateurs,
        ]);
    }

    #[Route('/users/{id}', name: 'app_get_user', methods: 'GET')]
    public function get_user($id, ManagerRegistry $doctrine): JsonResponse
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $u = [];
        $u['id'] = $user->getId();
        $u['pseudo'] = $user->getPseudo();
        $u['password'] = $user->getPassword();
        $u['profil_pic'] = $user->getProfilPic();
        $u['description'] = $user->getDescription();
        $u['suivis'] = $user->getSuivis();
        $u['likes'] = $user->getLikes();
        $u['role'] = $user->getRole();
        return $this->json([
            'user' => $u,
        ]);
    }

    #[Route('/users', name: 'app_cr_user', methods: 'POST')]
    public function cr_user(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = "Le compte a bien été créé";
        try {
            $pseudo = $request->get('pseudo');
            $password = $request->get('password');
            $user = $doctrine->getRepository(User::class)->findBy(['pseudo' => $pseudo]);
            if ($user != null) {
                $response = "Le pseudonyme est déjà utilisé";
            }
            else {
                $em = $doctrine->getManager();
                $u = new User();
                $u->setPseudo($pseudo);
                $u->setPassword($password);
                $u->setProfilPic(NULL);
                $u->setDescription(NULL);
                $u->setSuivis(NULL);
                $u->setLikes(NULL);
                $u->setRole(0);
                $em->persist($u);
                $em->flush(); 
            }
        } catch (\Throwable $th) {
            $response = "Une erreur a eu lieu";
        }

        return $this->json([
            $response,
        ]);
    }

    #[Route('/users/{id}', name: 'app_del_user', methods: 'DELETE')]
    public function del_user($id, ManagerRegistry $doctrine): JsonResponse
    {
        $deleted = true;
        try {
            $user = $doctrine->getRepository(User::class)->find($id);
            $em = $doctrine->getManager();
            $em->remove($user);
            $em->flush();
        } catch (\Throwable $th) {
            $deleted = false;
        }

        return $this->json([
            'deleted' => $deleted,
        ]);
    }

    #[Route('/tweets', name: 'app_tweets')]
    public function tweets(ManagerRegistry $doctrine): JsonResponse
    {
        $tweets = $doctrine->getRepository(Tweet::class)->findAll();
        $messages = [];
        foreach ($tweets as $tweet) {
            $t = [];
            $t['id'] = $tweet->getId();
            $t['dateheure'] = $tweet->getDateheure();
            $t['message'] = $tweet->getMessage();
            $t['media'] = $tweet->getMedia();
            $t['user'] = ($tweet->getUser())->getId();
            $messages[] = $t;
        }
        return $this->json([
            'tweets' => $messages,
        ]);
    }
}
