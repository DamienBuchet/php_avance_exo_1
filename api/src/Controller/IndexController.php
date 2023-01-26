<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

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
        return $this->json([
            'user' => $u,
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
