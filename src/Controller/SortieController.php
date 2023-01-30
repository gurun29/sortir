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

use App\Services\GestionDate;
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
    public function index(SortieRepository $sortieRepository, EntityManagerInterface $em,Request $request, EtatRepository $etatRepository, GestionDate $gestionDate): Response

    {
        //modification de la table Etat qd la date de cloture est dépassée
        //$this->modifEtatCloturee($sortieRepository, $etatRepository, $em);
        $gestionDate->modifEtatCloturee();
        $gestionDate->modifEtatArchivee();

        $data=new Filtres();
        $form=$this->createForm(FiltreType::class, $data);
        $form->handleRequest($request);




    $list=$sortieRepository->findSearch($data,$this->getUser());


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

}