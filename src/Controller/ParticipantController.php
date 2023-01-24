<?php

namespace App\Controller;
use App\Form\ParticipantType;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ParticipantController extends AbstractController
{
    /**
     * @Route("/mon_profil", name="mon_profil")
     */
    public function modifier(Request $request, EntityManagerInterface $entityManager): Response
    {
        $monProfil = new Participant();
        $monProfilForm = $this->createForm(ParticipantType::class,$monProfil);
        //$monProfilForm = $this->createForm(Participant::class,$monProfil);
        //dump($monProfilForm);
        //dd($monProfil);


        $monProfilForm->handleRequest($request);

        if ($monProfilForm->isSubmitted() && $monProfilForm->isValid())
        {
            $entityManager->persist($monProfil);
            $entityManager->flush();
            $this->addFlash('sucess','profil modifié');
            return $this->redirectToRoute('main');
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
