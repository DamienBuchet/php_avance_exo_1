<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'http://api.local');
        $res = ((((array)json_decode($response->getContent()))['message']));
        return $this->render('index/index.html.twig', [
            'res' => $res,
        ]);
    }
}
