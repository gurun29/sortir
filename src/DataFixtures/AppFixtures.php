<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


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
        for ($i=0; $i < 4; $i++) {
            $campus[$i] = new Campus();
            $campus[$i]->setNom($faker->city());
            $manager->persist($campus[$i]);
        }

        // créations des participants
        $participant=[];
        for ($k=0;$k<=8; $k++){
            $participant[$k] = new Participant();
            $participant[$k]->setNom($faker->unique()->lastName());
            $participant[$k]->setPrenom($faker->unique()->firstName());
            $participant[$k]->setMail($faker->unique()->email());
            $participant[$k]->setPseudo($faker->name());
            $participant[$k]->setAdministrateur(false);
            $participant[$k]->setTelephone($faker->unique()->phoneNumber());
            //$participant->setTelephone($faker->unique()->numberBetween($int1 = 0600000000,     $int2 = 0700000000));
            $participant[$k]->setRoles(["ROLE_USER"]);
            //$participant->setPassword("123456");
            $participant[$k]->setPassword($faker->password());
            $participant[$k]->setActif(true);
            //$participant->setEstRattacheA(new Campus());

            // on récupère un nombre aléatoire de campus dans un tableau
            $randomCampus = (array) array_rand($campus, rand(1, count($campus)));
            // puis on les ajoute au Customer
            foreach ($randomCampus as $key => $value) {
                $participant[$k]->setEstRattacheA($campus[$key]);
            }

            //$participant->setCategory($category);
            //$participant->setPrenom(new \DateTime());
            $manager->persist($participant[$k]);

        }

        // créations des villes
        $ville = [];
        for ($j=0;$j<=9; $j++) {
            $ville[$j] = new Ville();
            $ville[$j]->setNom($faker->city());
            $ville[$j]->setCodePostal($faker->postcode());
            $manager->persist($ville[$j]);
        }

        // créations des lieux
            $lieu=[];
        for ($x=0;$x<=5; $x++) {
            $lieu [$x]= new Lieu();
            $lieu[$x]->setNom($faker->city());
            $lieu  [$x]->setRue($faker->streetAddress());
            $lieu [$x]->setLatitude($faker->randomFloat(1, -90, 90));

            // on récupère un nombre aléatoire de campus dans un tableau
            //$randomVille = (array) array_rand($ville, rand(1, count($ville)));
            // puis on les ajoute au Customer
            //foreach ($randomVille as $key2 => $value) {
                $lieu[$x]->setVille($ville[$faker->randomDigit()]);
            //}
            //$test =
            //$randomVille = (array) array_rand($ville, rand(1, count($ville)));
            // puis on les ajoute au Customer
            //foreach ($randomVille as $key => $value) {
            //    $lieu->setVille($ville[$key]);
            //}


            $manager->persist($lieu[$x]);

        }
        //cration des etats
            $etat=[];
        for ($l=0;$l<=2;$l++){
            $etat [$l] =new Etat();
            $etat [$l] ->setLibelle($faker->title());
            $manager->persist($etat[$l]);
        }


        //creation de sorties
        for ($a=1;$a<=10;$a++){
            $sortie=new Sortie();
            $sortie->setNom($faker->unique()->word());
            $sortie->setDuree($faker->randomDigit());
            $sortie->setInfosSortie($faker->text());

          $randomLieu = (array) array_rand($lieu, rand(1, count($lieu)));

           foreach ($randomLieu as $key => $value) {
              $sortie->setLieu($lieu[$key]);
           }

                $randomEtat= (array) array_rand($etat,rand(1,count($etat)));
           foreach ($randomEtat as $key => $value) {
               $sortie->setEtat($etat[$key]);
           }
            $sortie->setNbInscriptionsMax($faker->randomDigit());
            $fakersetDateHeureDebut = $faker->dateTimeBetween('+1 month','+6 month');
            $sortie->setDateHeureDebut($fakersetDateHeureDebut);
            //$sortie->setDateLimiteInscription($faker->dateTimeBetween('$fakersetDateHeureDebut -6 month','$fakersetDateHeureDebut'));
            $sortie->setDateLimiteInscription($faker->dateTimeBetween('+1 day','+1 month'));
            //$sortie->setDateLimiteInscription($faker->dateTime('now +6 month'));

            $randomOrganisateur = (array) array_rand($participant,rand(1,count($participant)));
            foreach ($randomOrganisateur as $key => $value){
                $sortie->setOrganisateur($participant[$key]);
            }


           $radomSiteOrganisateur=(array) array_rand($campus,rand(1,count($campus)));
           foreach ($radomSiteOrganisateur as $key => $value){
               $sortie->setSiteOrganisateur($campus[$key]);
           }
           $manager->persist($sortie);

        }


        $manager->flush();
    }
}
