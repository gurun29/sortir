<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;

use App\filtres\Filtres;
use App\Form\CreationSortieType;
use App\Form\FiltreType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;

use App\Repository\VilleRepository;

use App\Services\GestionDate;
use Cassandra\Date;
use DateTime;
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
        $gestionDate->modifEtatNbreInscrit(); //TODO finir dql

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


    /**
     * @Route("/creationSortie", name="sortie_creation")
     */
    public function creationSortie(ParticipantRepository $participantRepository, EntityManagerInterface $em, Request $request, EtatRepository $etatRepository ): Response
    {
        $participant = $this->getUser();
        $dateDuJour = new DateTime();
        $sortie = new Sortie();
        $sortieForm = $this->createForm(CreationSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid() && $sortie->getDateHeureDebut()> $dateDuJour){
            $etat = $etatRepository->findOneBy(array('libelle'=>"Créée"));
            $sortie->setEtat($etat);
            $sortie->setOrganisateur($participant);
            $em-> persist($sortie);

            $em->flush();

            $this->addFlash('success', 'La sortie a bien été créée');
            return $this->redirectToRoute('main');
        }


        return $this->render('sortie/creation_sortie.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView()
        ]);
    }


    /**
     * @Route("/modificationSortie/{id}", name="modification_creation")
     */
    public function modificationSortie(
        //ParticipantRepository $participantRepository,
        EntityManagerInterface $em,
        Request $request,
        EtatRepository $etatRepository,
        SortieRepository $sortieRepository,
        int $id
    ): Response
    {
        $participant = $this->getUser();
        $dateDuJour = new DateTime();
        $sortie = $sortieRepository->find($id);


        // todo if $participant = sortie.participant

        $sortieForm = $this->createForm(CreationSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid() && $sortie->getDateHeureDebut()> $dateDuJour){
            //$etat = $etatRepository->findOneBy(array('libelle'=>"Créée"));
            //$sortie->setEtat($etat);
            //$sortie->setOrganisateur($participant);
            $em-> persist($sortie);

            $em->flush();

            $this->addFlash('success', 'La sortie a bien été créée');
            return $this->redirectToRoute('main');
        }


        return $this->render('sortie/modification_sortie.html.twig', [
            'controller_name' => 'SortieController',
            'modificationSortieForm' => $sortieForm->createView()
        ]);
    }
}