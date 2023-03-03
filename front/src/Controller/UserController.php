<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user')]
    public function index(Request $request): Response
    {

        $session = $this->requestStack->getSession();

        if ($session->get("user") == null) {
            echo "<script>window.location.href = '/login'</script>";
            header("Location: /login");
        }
        
        $deco = $request->get('deco');
        if (isset($deco)) {
            $session->invalidate();
            echo "<script>location.reload()</script>";
        }

        $mail = $session->get('user');

        $client = HttpClient::create();
        $response = $client->request('GET', "http://localhost/php_avance/api/public/users/mail/$mail");
        $user = ((((array)json_decode($response->getContent()))));
        $infos = ($user['user']);

        // $edit = $request->get('edit');
        // if (isset($edit)) {
        //     $pseudo = $request->get('e_pseudo');
        //     $desc = $request->get('e_desc');
        //     $f = $request->files->get('e_pp');
        //     if ($f == null) {
                
        //     }
        // }

        $supp = $request->get('supp');
        if (isset($supp)) {
            $id = $infos->id;
            $client = HttpClient::create();
            $response = $client->request('DELETE', "http://localhost/php_avance/api/public/users/$id");
            echo "<script>if(window.history.replaceState){window.history.replaceState(null,null,window.location.href);}</script>";
            $session->invalidate();
            echo "<script>location.reload()</script>";
        }

        return $this->render('user/index.html.twig', [
            'infos' => $infos,
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_id')]
    public function user(int $id, Request $request): Response
    {

        $session = $this->requestStack->getSession();

        if ($session->get("user") == null) {
            echo "<script>window.location.href = '/login'</script>";
            header("Location: /login");
        }
        
        $deco = $request->get('deco');
        if (isset($deco)) {
            $session->invalidate();
            echo "<script>location.reload()</script>";
        }

        $client = HttpClient::create();
        $response = $client->request('GET', "http://localhost/php_avance/api/public/users/$id");
        $user = ((((array)json_decode($response->getContent()))));
        $infos = ($user['user']);

        return $this->render('user/user.html.twig', [
            'infos' => $infos,
        ]);
    }

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
}
