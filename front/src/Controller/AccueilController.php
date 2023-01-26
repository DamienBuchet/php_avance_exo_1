<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'http://localhost/php_avance/api/public/users');
        $users = ((((array)json_decode($response->getContent()))['users']));

        $response = $client->request('GET', 'http://localhost/php_avance/api/public/tweets');
        $tweets = ((((array)json_decode($response->getContent()))['tweets']));
        return $this->render('accueil/index.html.twig', [
            'connecte' => true,
            'users' => $users,
            'tweets' => $tweets,
        ]);
    }
}
