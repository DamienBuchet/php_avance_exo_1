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
    #[Route('/users', name: 'app_users')]
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
