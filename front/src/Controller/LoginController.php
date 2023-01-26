<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(Request $request): Response
    {

        $login = $request->get('l');
        $signin = $request->get('s');

        if (isset($signin)) {
            $pseudo = htmlentities(trim($request->get('s_pseudo')));
            $pass = htmlentities(trim($request->get('s_pass')));
            $cpass = htmlentities(trim($request->get('s_cpass')));
            $verif = 1;
            if ($pseudo == null || empty($pseudo) || $pseudo == "") {
                $verif = 0;
                echo "<script>alert('Le pseudonyme n\'est pas valide')</script>";
            }
            elseif ($pass == null || empty($pass) || $pass == "") {
                $verif = 0;
                echo "<script>alert('Le mot de passe n\'est pas valide')</script>";
            }
            elseif ($cpass == null || empty($cpass) || $cpass == "") {
                $verif = 0;
                echo "<script>alert('La confirmation du mot de pas n\'est pas valide')</script>";
            }
            elseif ($pass != $cpass) {
                $verif = 0;
                echo "<script>alert('Les mots de passe ne correspondent pas')</script>";
            }
            elseif (strlen($pass) < 8) {
                $verif = 0;
                echo "<script>alert('Veuillez utiliser au moins 8 caractères')</script>";
            }

            if ($verif == 1) {
                $client = HttpClient::create();
                $response = $client->request('POST', 'http://localhost/php_avance/api/public/users', [
                    'body' =>
                    [
                        'pseudo' => $pseudo,
                        'password' => crypt($pass, $pass),
                    ],
                ]);
                echo "<script>alert('".substr($response->getContent(), 2, -2)."')</script>";
            }

            echo "<script>if(window.history.replaceState){window.history.replaceState(null,null,window.location.href);}</script>";
        }

        if (isset($login)) {
            
        }

        return $this->render('login/index.html.twig', [
        ]);
    }
}
