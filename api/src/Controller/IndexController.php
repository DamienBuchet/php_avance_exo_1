<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\User;
use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
            $u['email'] = $user->getEmail();
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
        $u['email'] = $user->getEmail();
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

    #[Route('/users/mail/{mail}', name: 'app_get_user_by_mail', methods: 'GET')]
    public function get_user_by_mail($mail, ManagerRegistry $doctrine): JsonResponse
    {
        $user = $doctrine->getRepository(User::class)->findBy(['email' => $mail]);
        $user = $user[0];
        $u = [];
        $u['id'] = $user->getId();
        $u['email'] = $user->getEmail();
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

    #[Route('/users/{mail}/{password}', name: 'app_get_user_mail', methods: 'GET')]
    public function get_user_mail($mail, $password, ManagerRegistry $doctrine): JsonResponse
    {
        $user = $doctrine->getRepository(User::class)->findBy(['email' => $mail, 'password' => $password]);
        $response = false;
        if ($user != null) {
            $response = true;
        }
        return $this->json([
            'response' => $response,
        ]);
    }

    #[Route('/users', name: 'app_cr_user', methods: 'POST')]
    public function cr_user(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = "Le compte a bien été créé";
        try {
            $mail = $request->get('mail');
            $pseudo = $request->get('pseudo');
            $password = $request->get('password');
            $user = $doctrine->getRepository(User::class)->findBy(['email' => $mail]);
            if ($user != null) {
                $response = "L'email est déjà utilisé";
            }
            else {
                $em = $doctrine->getManager();
                $u = new User();
                $u->setEmail($mail);
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

    #[Route('/users', name: 'app_edit_user', methods: 'PUT')]
    public function edit_user(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $edited = true;
        try {
            $pseudo = $request->get('pseudo');
            $user = $doctrine->getRepository(User::class)->findBy(['email' => $mail]);
            if ($user != null) {
                $response = "L'email est déjà utilisé";
            }
            else {
                $em = $doctrine->getManager();
                $u = new User();
                $u->setEmail($mail);
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
            $edited = false;
        }

        return $this->json([
            'edited' => $edited,
        ]);
    }

    #[Route('/users/{id}', name: 'app_del_user', methods: 'DELETE')]
    public function del_user($id, ManagerRegistry $doctrine): JsonResponse
    {
        $deleted = true;
        try {
            $user = $doctrine->getRepository(User::class)->find($id);
            $m = $doctrine->getRepository(Message::class)->findBy(['env_user' => $id]);
            $m2 = $doctrine->getRepository(Message::class)->findBy(['rec_user' => $id]);
            $t = $doctrine->getRepository(Tweet::class)->findBy(['user' => $user]);
            $em = $doctrine->getManager();
            $em->remove($user);
            $em->flush();
            $em->remove($m);
            $em->flush();
            $em->remove($m2);
            $em->flush();
            $em->remove($t);
            $em->flush();
        } catch (\Throwable $th) {
            $deleted = false;
        }

        return $this->json([
            'deleted' => $deleted,
        ]);
    }

    #[Route('/tweets', name: 'app_tweets', methods: 'GET')]
    public function tweets(ManagerRegistry $doctrine): JsonResponse
    {
        $tweets = $doctrine->getRepository(Tweet::class)->findAllOrderByDate();
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

    #[Route('/tweets', name: 'app_env_tweet', methods: 'POST')]
    public function env_tweet(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = "ok";
        try {
            $mail = $request->get('mail');
            $user = $doctrine->getRepository(User::class)->findBy(['email' => $mail]);
            $message = $request->get('message');
            $media = $request->get('media');
            $em = $doctrine->getManager();
            $t = new Tweet();
            $t->setUser($user[0]);
            $t->setDateheure(new \DateTime(date("Y-m-d H:i:s")));
            $t->setMessage($message);
            $t->setMedia($media);
            $em->persist($t);
            $em->flush(); 
        } catch (\Throwable $th) {
            $response = "non";
        }

        return $this->json([
            $response,
        ]);
    }

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
}
