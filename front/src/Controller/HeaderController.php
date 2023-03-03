<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RequestStack;

class HeaderController extends AbstractController
{
    #[Route('/header', name: 'app_header')]
    public function index(): Response
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'http://localhost/php_avance/api/public/users');
        $users = ((((array)json_decode($response->getContent()))['users']));

        $session = $this->requestStack->getSession();
        $mail = $session->get('user');

        return $this->render('header/index.html.twig', [
            'users' => $users,
            'mail' => $mail,
        ]);
    }

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
}
