<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Faker\Provider\fr_FR\Address;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // initialisation de l'objet Faker
        $faker = \Faker\Factory::create('fr_FR');

        // créations des campus
        $campus = [];
        for ($i=0; $i < 3; $i++) {
            $campus[$i] = new Campus();
            $campus[$i]->setNom($faker->city());
            $manager->persist($campus[$i]);
        }

        // créations des participants
        for ($k=1;$k<=8; $k++){
            $participant = new Participant();
            $participant->setNom($faker->unique()->lastName());
            $participant->setPrenom($faker->unique()->firstName());
            $participant->setMail($faker->unique()->email());
            $participant->setPseudo($faker->name());
            $participant->setAdministrateur(false);
            //$participant->setTelephone($faker->unique()->phoneNumber());
            $participant->setTelephone($faker->unique()->numberBetween($int1 = 0600000000,     $int2 = 0700000000));
            $participant->setRoles(["ROLE_USER"]);
            $participant->setPassword("123456");
            $participant->setPassword($faker->password());
            $participant->setActif(true);
            //$participant->setEstRattacheA(new Campus());

            // on récupère un nombre aléatoire de campus dans un tableau
            $randomCampus = (array) array_rand($campus, rand(1, count($campus)));
            // puis on les ajoute au Customer
            foreach ($randomCampus as $key => $value) {
                $participant->setEstRattacheA($campus[$key]);
            }

            //$participant->setCategory($category);
            //$participant->setPrenom(new \DateTime());
            $manager->persist($participant);

        }

        // créations des villes
        $ville = [];
        for ($j=1;$j<=10; $j++) {
            $ville[$j] = new Ville();
            $ville[$j]->setNom($faker->city());
            $ville[$j]->setCodePostal($faker->numberBetween($int1 = 00000,     $int2 = 99999));
            $manager->persist($ville[$j]);
        }

        // créations des lieux
        for ($x=1;$x<=5; $x++) {
            $lieu = new Lieu();
            $lieu->setNom($faker->city());
            $lieu->setRue($faker->streetAddress());
            $lieu->setLatitude($faker->randomFloat(1, -90, 90));

            // on récupère un nombre aléatoire de campus dans un tableau
            //$randomVille = (array) array_rand($ville, rand(1, count($ville)));
            // puis on les ajoute au Customer
            //foreach ($randomVille as $key2 => $value) {
                $lieu->setVille($ville[$faker->randomDigit()]);
            //}
            //$test =
            //$randomVille = (array) array_rand($ville, rand(1, count($ville)));
            // puis on les ajoute au Customer
            //foreach ($randomVille as $key => $value) {
            //    $lieu->setVille($ville[$key]);
            //}

            $manager->persist($lieu);
        }

        $manager->flush();
    }
}
