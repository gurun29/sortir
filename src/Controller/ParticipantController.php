<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\ImagesParticipant;
use App\Entity\Sortie;
use App\Form\ParticipantType;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ImagesParticipantRepository;
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

//    /**
//     * @Route("/mon_profil", name="monprofil")
//     */
//    public function monProfil(Security $security) {
//        $monProfil = $this->getUser();
//        //$user = $this->get('security.token_storage')->getToken()->getUser();
//        //$user->getUsername();
//        //$monProfil = $this->container->get('security.context_listener')->getToken()->getUser()->getCandidat();
//        //$monProfil =
//        //dd($monProfil);
//        //$monProfil =
//        return $this->redirectToRoute('mon_profil',[
//            'id'=>$monProfil->getId(),
//        ]);
//    }
    /**
     * @Route("/monprofil/", name="monprofil")
     */
    public function modifier(
        Request $request,
        //UserAuthenticatorInterface $userAuthenticator,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ImagesParticipantRepository $imagesParticipantRepository
    ): Response
    {
        //$MDP = "****";
        $MDP = "";
        $mdphash="";

        $monProfil = $this->getUser();

//        $monProfil = $participantRepository->find($id);
        $monProfilCopy = clone $monProfil;
        if (!$monProfil){
            throw $this->createNotFoundException("le participant n'existe pas");
        }
//
//        $testProfil = $this->getUser()->getId();
//        if ($testProfil != $id){
//
//            throw $this->createNotFoundException("route interdite");
//        }

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

        $image = $monProfilForm->get('images')->getData();

        if ($monProfilForm->isSubmitted() && $monProfilForm->isValid()
             && $monProfilForm->get('mdp')->getData() === $monProfilForm->get('mdp2')->getData()
             && ($monProfil <> $monProfilCopy || $monProfilForm->get('mdp')->getData()<>"" || $image) )
        {
            //dd($monProfil);
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



            If ($image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                $photo = $monProfil->getImagesParticipant();
                //dd($photo);
                if ($photo)
                {
                    // On modifie l'image dans la base de données
                    $photo->setName($fichier);
                    $monProfil->setImagesParticipant($photo);
                    //$photoProfil = $imagesParticipantRepository->find($photo);
                    //$imagesParticipantRepository->remove($photo);
                    //dd($photo);
                    //$monProfil->removeImagesParticipant($photo);

                }
                else {
                    // On crée l'image dans la base de données
                    $img = new ImagesParticipant();
                    $img->setName($fichier);
                    $monProfil->setImagesParticipant($img);
                }

                //dd($photo);
            }

            //dd($monProfil);
            //dump($monProfil);
            //dd($monProfilCopy);

            $entityManager->persist($monProfil);

            $entityManager->flush();
            $this->addFlash('sucess','profil modifié');

            //return $this->redirectToRoute('main');
            return $this->redirectToRoute('monprofil',[
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
        EtatRepository $etatRepository,
        int $id
    ): Response
    {
        //dump($id);
//        $idProfil = $this->getUser()->getId();
//        $profil = $participantRepository->find($idProfil);
        $profil= $this->getUser();

        if (!$profil){
            return $this->redirectToRoute('main');
        }

        $sortie = $sortieRepository->find($id);

        $et = $sortie->getEtat($id);

        $testDate = new \DateTime();


// todo inutile si la date de début de la sortie est bien supérieur à la date limite d'inscription  (voir Créeer sortie)
//        if ($sortie->getDateHeureDebut() > $testDate) {
//            throw $this->createNotFoundException("La date limite d'inscription est dépassée");
//        }
        if ($sortie->getDateLimiteInscription() < $testDate) {
            throw $this->createNotFoundException("La date limite d'inscription est dépassée");
        }
        if ($sortie->getNbInscriptionsMax() <= $sortie->getInscrit()->count()) {
            //throw $this->createNotFoundException("Le nombre maxi de participant est atteint");
            $this->addFlash('alert',"Votre inscription n'est pas valide. Le nombre maxi de participant est atteint");
            return $this->redirectToRoute("app_sortie");
        }
        $collection = $sortie->getInscrit();
        //$collection->contains('Desk');
        //if ( in_array( $profil , (array)$sortie->getInscrit())  ){
        if ( $collection->contains($profil) ){
            $this->addFlash('alert','vous êtes déjà inscrit à cette sortie');
            return $this->redirectToRoute('sortie_detail',[
                "id" => $id,
            ]);
        }
        if ($et->getLibelle() != "Ouverte") {
            switch ($et->getLibelle()) {
                case 'Cloturée':
                    $messageErreur = "cloturée";
                    break;
                case 'Annulée':
                    $messageErreur = "Annulée";
                    break;
            }
            throw $this->createNotFoundException("L'inscription est " . $messageErreur);
        }

            //dump($sortie->getDateHeureDebut() );

        if ($sortie->getDateLimiteInscription() >= $testDate
            && $profil
            && $sortie->getNbInscriptionsMax() > $sortie->getInscrit()->count() ){
            //dump($sortie);
            $sortie->addInscrit($profil);

            if ($sortie->getNbInscriptionsMax() === $sortie->getInscrit()->count()) {
                //todo tester le set l'etat de la sortie
                $etat = $etatRepository->findOneBy(array('libelle'=>"Cloturée"));
                $sortie->setEtat($etat);

            }
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



            throw $this->createNotFoundException("L'inscription n'est pas validé");
        }
    }




    /**
     * @Route("/profil/seDesister/{id}", name="seDesister")
     */
    public function seDesister(
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        int $id):Response
    {
        $idParticipant = $this->getUser()->getId();
        $participant = $participantRepository->find($idParticipant);
        $sortie = $sortieRepository->find($id);
        $date = new \DateTime();

        if (!$participant){
            return $this->redirectToRoute('app_login');
        }

        if ( !$sortie->getInscrit()->contains($participant) ){
            $this->addFlash('alert','vous êtes déjà desinscrit à cette sortie');
            return $this->redirectToRoute('sortie_detail',[
                "id" => $id,
            ]);
        }

        if ($sortie->getDateLimiteInscription() < $date) {
            throw $this->createNotFoundException("La date limite d'inscription (ou desinscription) est dépassée");
        }
//        if ($sortie->getEtat() === 'Ouverte' || $sortie->getEtat() === 'Cloturée') {
//            return $this->redirectToRoute('main');
//        }

        if ($sortie->getDateLimiteInscription() >= $date
            && $participant
        )
        {
            if ($sortie->getNbInscriptionsMax() === $sortie->getInscrit()->count()) {
                //todo tester le set l'etat de la sortie
                $etat = $etatRepository->findOneBy(array('libelle'=>"Ouverte"));
                $sortie->setEtat($etat);
            }

            $sortie->removeInscrit($participant);

            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('sucess', "vous avez été désinscrit de cette sortie");
            return $this->redirectToRoute("sortie_detail",[
                "id" => $id,
            ]);
        }

        throw $this->createNotFoundException("Le desistement n'est pas validé");
    }

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


