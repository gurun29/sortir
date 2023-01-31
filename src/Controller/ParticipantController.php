<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\ParticipantType;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\Security\Http\Authentication\AuthenticatorManagerInterface;
//use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
//use App\Security\AccessTokenHandler;
//use Symfony\Config\SecurityConfig;
use Symfony\Component\Security\Core\Security;
//use App\Command\majEtatCommand;

class ParticipantController extends AbstractController
{
//    /**
//     * @var Security
//     */
    private $security;

    /**
     * @Route("/mon_profil", name="monprofil")
     */
    public function monProfil(Security $security) {
        $monProfil = $this->getUser();
        //$user = $this->get('security.token_storage')->getToken()->getUser();
        //$user->getUsername();
        //$monProfil = $this->container->get('security.context_listener')->getToken()->getUser()->getCandidat();
        //$monProfil =
        //dd($monProfil);
        //$monProfil =
        return $this->redirectToRoute('mon_profil',[
            'id'=>$monProfil->getId(),
        ]);
    }
    /**
     * @Route("/mon_profil/{id}", name="mon_profil")
     */
    public function modifier(
        Request $request,
        //UserAuthenticatorInterface $userAuthenticator,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        int $id
    ): Response
    {
        //$MDP = "****";
        $MDP = "";
        $mdphash="";



        $monProfil = $participantRepository->find($id);
        $monProfilCopy = clone $monProfil;
        if (!$monProfil){
            throw $this->createNotFoundException("le participant n'existe pas");
        }

        $testProfil = $this->getUser()->getId();
        if ($testProfil != $id){

            throw $this->createNotFoundException("route interdite");
        }

        $monProfilForm = $this->createForm(ParticipantType::class,$monProfil);
        //$monProfilForm->get('password')->setData($MDP);
        $monProfilForm->get('mdp')->setData($MDP);
        $monProfilForm->get('mdp2')->setData($MDP);


        $monProfilForm->handleRequest($request);

        if ($monProfilForm->isSubmitted() && $monProfilForm->isValid()
            && $monProfilForm->get('mdp')->getData() != $monProfilForm->get('mdp2')->getData())
        {
            $this->addFlash('alert','les mots de passe sont différents');
        }

        if ($monProfilForm->isSubmitted() && $monProfilForm->isValid()
             && $monProfilForm->get('mdp')->getData() === $monProfilForm->get('mdp2')->getData()
                && ($monProfil <> $monProfilCopy || $monProfilForm->get('mdp')->getData()<>"") )
        {
            //dump($monProfil);
            if ($monProfilForm->get('mdp')->getData() != $MDP) {
                //dump($monProfil);
                $mdphash=$monProfilForm->get('mdp')->getData();
                $monProfil->setPassword(
                    $passwordHasher->hashPassword(
                        $monProfil,
                        $mdphash
                    )
                );
            }

            //dump($monProfil);
            //dd($monProfilCopy);

            $entityManager->persist($monProfil);

            $entityManager->flush();
            $this->addFlash('sucess','profil modifié');

            //return $this->redirectToRoute('main');
            return $this->redirectToRoute('mon_profil',[
                'id'=>$monProfil->getId(),
            ]);
        }




        return $this->render('participant/modifier.html.twig', [
            'monProfilForm'=>$monProfilForm ->createView()
        ]);

        //return $this->render('participant/modifier.html.twig', [
        //    'controller_name' => 'MonProfilController',
        //]);
    }


    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function afficher(
        ParticipantRepository $participantRepository,
        //CampusRepository $campusRepository,
        //EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        //$this->testtt($participantRepository,$campusRepository, $entityManager);
        $profil = $participantRepository->find($id);
        if (!$profil){
            throw $this->createNotFoundException("Le participant n'existe pas ?!");
        }

        return $this->render('participant/afficher.html.twig', [
            'participant'=>$profil
        ]);

    }




    /**
     * @Route("/profil/sinscrire/{id}", name="sinscrire")
     */
    public function sinscrire (
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        SortieRepository $sortieRepository,
        int $id
    ): Response
    {
        //dump($id);
        $idProfil = $this->getUser()->getId();
        $profil = $participantRepository->find($idProfil);

        if (!$profil){
            return $this->redirectToRoute('main');
        }

        $sortie = $sortieRepository->find($id);

        $et = $sortie->getEtat($id);

        if ($et->getLibelle()=== "Annulée"){
            throw $this->createNotFoundException("La sortie est annulée");
        }

        $testDate = new \DateTime();

        //dump($sortie->getDateHeureDebut() );
        //dd($sortie->getDateLimiteInscription() );

        if ($sortie->getDateLimiteInscription() >= $testDate
            && $sortie->getNbInscriptionsMax() > $sortie->getInscrit()->count() ){
            //dump($sortie);
            $sortie->addInscrit($profil);
            //$profil->addSortiesInscrit($id);
            //dd($sortie);
            $entityManager->persist($sortie);

            $entityManager->flush();
            $this->addFlash('sucess','Bravo, vous êtes inscrit à cette sortie');

            return $this->redirectToRoute('sortie_detail',[
                "id" => $id,
            ]);

        }

        else {

                if ($sortie->getNbInscriptionsMax() === $sortie->getInscrit()->count()) {
                    //todo set l'etat de la sortie
                    throw $this->createNotFoundException("Le nombre max de particpant est déja atteint");
                }
                if ($sortie->getDateHeureDebut() < $testDate) {
                    //$this->addFlash('alert',"L'inscription n'est pas ouverte");
                    //return $this->render('sortie.html.twig');
                    throw $this->createNotFoundException("L'inscription n'est pas ouverte");
                }
                if ($sortie->getDateLimiteInscription() < $testDate) {
                    throw $this->createNotFoundException("L'inscription est terminée");
                }
            throw $this->createNotFoundException("L'inscription n'est pas validé");
        }
    }




    /**
     * @Route("/profil/seDesister/{id}", name="seDesister")
     */
    public function seDesister(  EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, SortieRepository $sortieRepository, int $id):Response
    {
        $idParticipant = $this->getUser()->getId();
        $participant = $participantRepository->find($idParticipant);
        $sortie = $sortieRepository->find($id);
        $date = new \DateTime();

        if ($sortie->getDateLimiteInscription() > $date && $participant) {
            $sortie->removeInscrit($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('sucess', "vous avez été désinscrit de cette sortie");



        }
        return $this->redirectToRoute("sortie_detail",[
            "id" => $id,
        ]);}

    }


//
//    public function testtt(ParticipantRepository $participantRepository, CampusRepository $campusRepository, EntityManagerInterface $entityManager)
//    {
//        //$this->afficher(50);
//        //$this->
//
//        //$participantRepository = new ParticipantRepository();
//        $Participants= $participantRepository->findByExampleField(64);
//        $campus = $campusRepository->find(46);
//        foreach ($Participants as $Participant) {
//            $Participant->setActif(false);
//            //$campus
//            $Participant->setEstRattacheA($campus);
//            $entityManager->persist($Participant);
//
//            $entityManager->flush();
//        }
//        dd($Participants);
//
//        //$Participant= $participantRepository->findByChangeDateEtat();
//        //$sortie = $sortieRepository->find($id);
//
//
//    }


