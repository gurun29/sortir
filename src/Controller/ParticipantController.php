<?php

namespace App\Controller;
use App\Form\ParticipantType;
use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ParticipantController extends AbstractController
{
    /**
     * @Route("/mon_profil/{id}", name="mon_profil")
     */
    public function modifier(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, int $id): Response
    {
        //$monProfil = new Participant();
        $monProfil = $participantRepository->find($id);
        if (!$monProfil){
            throw $this->createNotFoundException("oh no, the serie don't exist");
        }

        $monProfilForm = $this->createForm(ParticipantType::class,$monProfil);
        //$monProfilForm = $this->createForm(Participant::class,$monProfil);
        //dump($monProfilForm);
        //dd($monProfil);


        $monProfilForm->handleRequest($request);

        if ($monProfilForm->isSubmitted() && $monProfilForm->isValid())
        {
            //dd($monProfil);
            $entityManager->persist($monProfil);
            //dd($monProfil);
            $entityManager->flush();
            $this->addFlash('sucess','profil modifié');
            //return $this->redirectToRoute('main');
        }

          return $this->render('participant/modifier.html.twig', [
            'monProfilForm'=>$monProfilForm ->createView()
        ]);

        //return $this->render('participant/modifier.html.twig');


        //return $this->render('mon_profil/modifier.html.twig', [
        //    'controller_name' => 'MonProfilController',
        //]);
    }
}
