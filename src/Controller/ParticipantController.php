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
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class ParticipantController extends AbstractController
{
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
        $MDP = "****";
        $mdphash="";
        //$monProfil = new Participant();
        $monProfil = $participantRepository->find($id);
        if (!$monProfil){
            throw $this->createNotFoundException("oh no, the serie don't exist");
        }
        //$monProfil->setPassword($MDP);
        $monProfilForm = $this->createForm(ParticipantType::class,$monProfil);
        //$monProfilForm->get('password')->setData($MDP);
        $monProfilForm->get('mdp')->setData($MDP);
        $monProfilForm->get('mdp2')->setData($MDP);
        //$monProfilForm->get('mdp')->add("test");
        //$monProfilForm = $this->createForm(Participant::class,$monProfil);
        //dump($monProfilForm);
        //dd($monProfil);
        //$monProfilForm->add($passwordHasher->hashPassword(
        //    $monProfil,
        //    $monProfilForm->get('password')->getData();
        //));

        //$monProfilForm = $this->renderForm();

        $monProfilForm->handleRequest($request);

        if ($monProfilForm->isSubmitted() && $monProfilForm->isValid()
             && $monProfilForm->get('mdp')->getData() === $monProfilForm->get('mdp2')->getData())
        {
            //dump($monProfil);
            if ($monProfilForm->get('mdp')->getData() <> $MDP) {
                //dump($monProfil);
                $mdphash=$monProfilForm->get('mdp')->getData();
                //dump($mdphash);

                dd($passwordHasher);
                $monProfil->setPassword(
                    $passwordHasher->hashPassword(
                        $monProfil,
                        $monProfilForm->get('mdp')->getData()
                    )
                );
                //$monProfil->setPassword($monProfilForm->get('mdp')->getData());
                //dump($passwordHasher);
                //dd($monProfil);
            }
            else {

            }




            dump($monProfil);

            //dd($monProfil);
            $entityManager->persist($monProfil);
            //dd($monProfil);
            $entityManager->flush();
            $this->addFlash('sucess','profil modifié');
            return $this->redirectToRoute('main');
        }

        return $this->render('participant/modifier.html.twig', [
            'monProfilForm'=>$monProfilForm ->createView()
        ]);

        //return $this->render('participant/modifier.html.twig', [
        //    'controller_name' => 'MonProfilController',
        //]);
    }
}
