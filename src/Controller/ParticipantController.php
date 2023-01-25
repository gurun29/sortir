<?php

namespace App\Controller;

use App\Form\ParticipantType;
use App\Entity\Participant;
use App\Repository\ParticipantRepository;
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
        if ($testProfil <> $id){
            throw $this->createNotFoundException("route interdite");
        }

        $monProfilForm = $this->createForm(ParticipantType::class,$monProfil);
        //$monProfilForm->get('password')->setData($MDP);
        $monProfilForm->get('mdp')->setData($MDP);
        $monProfilForm->get('mdp2')->setData($MDP);


        $monProfilForm->handleRequest($request);

        if ($monProfilForm->isSubmitted() && $monProfilForm->isValid()
            && $monProfilForm->get('mdp')->getData() <> $monProfilForm->get('mdp2')->getData())
        {
            $this->addFlash('alert','les mots de passe sont différents');
        }

        if ($monProfilForm->isSubmitted() && $monProfilForm->isValid()
             && $monProfilForm->get('mdp')->getData() === $monProfilForm->get('mdp2')->getData()
                && ($monProfil <> $monProfilCopy || $monProfilForm->get('mdp')->getData()<>"") )
        {
            //dump($monProfil);
            if ($monProfilForm->get('mdp')->getData() <> $MDP) {
                dump($monProfil);
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
        int $id
    ): Response
    {


        $profil = $participantRepository->find($id);
        if (!$profil){
            throw $this->createNotFoundException("Le participant n'existe pas ?!");
        }

        return $this->render('participant/afficher.html.twig', [
            'participant'=>$profil
        ]);

    }



}
