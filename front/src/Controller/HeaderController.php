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
        $session = $this->requestStack->getSession();
        if ($session->get("user") == null) {
            echo "<script>window.location.href = '/login'</script>";
            header("Location: /login");
        }

        $client = HttpClient::create();
        $response = $client->request('GET', 'http://localhost/php_avance/api/public/users');
        $users = ((((array)json_decode($response->getContent()))['users']));

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
