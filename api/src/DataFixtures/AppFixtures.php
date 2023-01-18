<?php

namespace App\DataFixtures;

use App\Entity\Tweet;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $faker->seed(1717);

        $users = [];
        for ($i=0; $i < 100; $i++) {
            $users[$i] = new User();
            $users[$i]
                ->setPseudo($faker->firstName)
                ->setPassword(crypt($faker->password(), '$6$rounds=5000$randomsaltforfaker$'))
                ->setProfilPic(NULL)
                ->setDescription($faker->realText())
                ->setSuivis(NULL)
                ->setLikes(NULL)
            ;
            $manager->persist($users[$i]);
        }

        $tweets = [];
        for ($i=0; $i < 1000; $i++) { 
            $tweets[$i] = new Tweet();
            $tweets[$i]
                ->setDateheure($faker->dateTime)
                ->setMessage($faker->realText())
                ->setMedia(NULL)
            ;
            $randomusers = (array) array_rand($users, rand(1, count($users)));

            foreach ($randomusers as $key => $value) {
                $tweets[$i]->setUser($users[$key]);
            }

            $manager->persist($tweets[$i]);
        }

        $manager->flush();
    }
}
