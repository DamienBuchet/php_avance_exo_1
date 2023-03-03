<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(Request $request): Response
    {
        $session = $this->requestStack->getSession();
        if ($session->get("user") == null) {
                echo "<script>window.location.href = '/login'</script>";
                header("Location: /login");
            }
        $user = $session->get('user');

        $deco = $request->get('deco');
        if (isset($deco)) {
            $session->invalidate();
            echo "<script>location.reload()</script>";
        }

        $send_message = $request->get('send_message');
        if (isset($send_message)) {
            $mail = $user;
            $message = $request->get('message');
            $f = $request->files->get('media');
            if ($f != null) {
                $target_dir = "media/";
                $target_file = $target_dir . basename($_FILES['media']["name"]);
                $uploadOk = 1;
                $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if ($_FILES['media']["size"] > 5000000) {
                    echo "<script>alert('Désolé, le fichier est trop lourd')</script>";
                    $uploadOk = 0;
                }
                if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg") {
                    echo "<script>alert('Seuls les types de fichiers JPG/JPEG et PNG sont autorisés')</script>";
                    $uploadOk = 0;
                }
                if ($uploadOk == 0) {
                    echo "<script>alert('Une erreur a eu lieu')</script>";
                } else {
                    $target_file = $target_dir . $this->generateRandomString();
                    $target_file = str_replace(" ", "", $target_file);
                    if (move_uploaded_file($_FILES['media']["tmp_name"], $target_file)) {
                            $client = HttpClient::create();
                            $response = $client->request('POST', 'http://localhost/php_avance/api/public/tweets', [
                                'body' =>
                                [
                                    'mail' => $mail,
                                    'message' => $message,
                                    'media' => $target_file,
                                ],
                            ]);
                            echo "<script>if(window.history.replaceState){window.history.replaceState(null,null,window.location.href);}</script>";
                        }
                        echo "<script>if(window.history.replaceState){window.history.replaceState(null,null,window.location.href);}</script>";
                    }
            }
            else{
                $client = HttpClient::create();
                $response = $client->request('POST', 'http://localhost/php_avance/api/public/tweets', [
                    'body' =>
                    [
                        'mail' => $mail,
                        'message' => $message,
                        'media' => NULL,
                    ],
                ]);
                echo "<script>if(window.history.replaceState){window.history.replaceState(null,null,window.location.href);}</script>";
            }
        }

        $client = HttpClient::create();
        $response = $client->request('GET', 'http://localhost/php_avance/api/public/users');
        $users = ((((array)json_decode($response->getContent()))['users']));
        $response = $client->request('GET', 'http://localhost/php_avance/api/public/tweets');
        $tweets = ((((array)json_decode($response->getContent()))['tweets']));
        return $this->render('accueil/index.html.twig', [
            'users' => $users,
            'tweets' => $tweets,
        ]);
    }

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
