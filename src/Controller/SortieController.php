<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Ville;

use App\filtres\Filtres;
use App\Form\FiltreType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;

use App\Repository\VilleRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Etat;


class SortieController extends AbstractController
{

    /**
     * @Route("/sortie", name="app_sortie")
     */
    public function index(SortieRepository $sortieRepository, EntityManagerInterface $em,Request $request, EtatRepository $etatRepository): Response

    {
        //modification de la table Etat qd la date de cloture est dépassée
        $this->modifEtatCloturee($sortieRepository, $etatRepository, $em);

        $data=new Filtres();
        $form=$this->createForm(FiltreType::class, $data);
        $form->handleRequest($request);
        $sortie=$sortieRepository->findSearch($data);

        $list = new Sortie();
if ($form->isSubmitted()){
    $list=$sortieRepository->findSearch($data);
dump($list);
}else {

    $list = $sortieRepository->findAll();
}

        return $this->render('sortie/sortie.html.twig', [
            'list' => $list,
            'form'=>$form->createView()
        ]);


    }

    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail", requirements={"id"="\d+"})
     */
    public function detail(int $id, SortieRepository $sortieRepository, CampusRepository $campusRepository,VilleRepository $villeRepository): Response
    {

        /** @var sortie $sortie */
        $sortie = $sortieRepository->find($id);

        /**@var campus $campus*/
        $campus= $campusRepository->find($id);

        /**@var ville $ville*/

        $ville=$villeRepository->find($id);
        // s'il n'existe pas en bdd, on déclenche une erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException('la sortie n\'existe pas ');


        }


        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie,
            "campus"=>$campus,
            "ville"=>$ville

        ]);
    }

        public function modifEtatCloturee(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager)
    {
        $dateDuJour = new \DateTime();
        //dump($dateDuJour);
        $Sorties= $sortieRepository->findSortiesDateCloturee($dateDuJour);
        //$etat = $etatRepository->find(6);

        $etat = $etatRepository->findOneBy(array('libelle'=>"Cloturée"));

        //dump($Sorties);
        foreach ($Sorties as $Sortie) {
            //$Sortie->setActif(false);
            //$campus
            $Sortie->setEtat($etat);
            //dd($Sortie);
            $entityManager->persist($Sortie);

            $entityManager->flush();
        }


        //$Participant= $participantRepository->findByChangeDateEtat();
        //$sortie = $sortieRepository->find($id);


    }
}