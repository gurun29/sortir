<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;

use App\filtres\Filtres;
use App\Form\AnnulerSortieType;
use App\Form\CreationSortieType;
use App\Form\FiltreType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ImagesParticipantRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;

use App\Repository\VilleRepository;

use App\Services\GestionDate;
use DateTime;
use Detection\MobileDetect;
use Doctrine\ORM\EntityManagerInterface;
use MobileDetectBundle\DeviceDetector\MobileDetectorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Etat;


class SortieController extends AbstractController
{

    /**
     * @Route("/sortie", name="app_sortie")
     */
    public function index(SortieRepository $sortieRepository, EntityManagerInterface $em,Request $request, EtatRepository $etatRepository, GestionDate $gestionDate,MobileDetectorInterface $mobileDetector): Response

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


      /*  $detect = new De;
        if ($detect->isMobile())
        {
            $device='mobile';
        }
        elseif ($detect->isTablet()){
            $device='tablet';
        }else
        {
            $device='computer';}*/

        if ($mobileDetector->isMobile()){
            $device='mobile';
        }
       elseif ($mobileDetector->isTablet()){
            $device='tablet';
       }
        else {
        $device = 'computer';
    }




        return $this->render('sortie/sortie.html.twig', [
            'list' => $list,
            'form'=>$form->createView(),
            'device'=>$device
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

        $lieu = $sortie->getLieu();
        $ville= $lieu->getVille();
        //$lieu = $lieuRepository->find($sortie.getLieu);
        $sortieForm = $this->createForm(CreationSortieType::class, $sortie);
        $sortieForm->handleRequest($request);
      // if ($participant === $sortie->getOrganisateur()->getNom()) {


           if ($sortieForm->isSubmitted() && $sortieForm->isValid() && $sortie->getDateHeureDebut() > $dateDuJour) {
               $etat = $etatRepository->findOneBy(array('libelle' => "Ouverte"));
               //$sortie->setEtat($etat);
               //$sortie->setOrganisateur($participant);
               $sortie->setEtat($etat);

               $em->persist($sortie);

               $em->flush();

               $this->addFlash('success', 'La sortie a bien été publier');
               return $this->redirectToRoute('main');
           }
      // }


        return $this->render('sortie/modification_sortie.html.twig', [
            'controller_name' => 'SortieController',
            'modificationSortieForm' => $sortieForm->createView(),
            'lieu'=>$lieu,
            'ville'=>$ville,
            'sortie'=>$sortie,

        ]);
    }

    /**
     * @Route("/annuler/{id}", name="annuler_sortie")
     */
    public function annuler_sortie(Request $request, EntityManagerInterface $em, Sortie $sortie,EtatRepository $etatRepository){

        $participant = $this->getUser();

        $form = $this->createForm(AnnulerSortieType::class, $sortie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $sortie->setInfosSortie($form['infosSortie']->getData());
            $etat=$etatRepository->findOneBy(array('libelle'=>'Annulée'));
            $sortie->setEtat($etat);

            $em->flush();
            $this->addFlash('success', 'La sortie a été annulée !');


            return $this->redirectToRoute('main');

        }



        return $this->render('sortie/annuler.html.twig', [
            'page_name' => 'Annuler Sortie',
            'sortie' => $sortie,
            'participants' => $participant,
            'form' => $form->createView(),

        ]);
    }

}