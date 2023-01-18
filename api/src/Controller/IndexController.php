<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Faker;
use Doctrine\Persistence\ManagerRegistry;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        // $em = $doctrine->getManager();
        // $faker = Faker\Factory::create('fr_FR');
        // $populator = new Faker\ORM\Doctrine\Populator($faker, $em);
        // $populator->addEntity(User::class, 10);
        // $populator->addEntity(Tweet::class, 5);
        // $insertedPKs = $populator->execute();
        return $this->json([
            'message' => 'Hello World!',
        ]);
    }
}
