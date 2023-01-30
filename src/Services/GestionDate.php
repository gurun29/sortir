<?php

namespace App\Services;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class GestionDate {

    private SortieRepository $sortieRepository;
    private EtatRepository $etatRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager)
{

    $this->sortieRepository = $sortieRepository;
    $this->etatRepository = $etatRepository;
    $this->entityManager = $entityManager;
}

    public function modifEtatCloturee()
    {

        $dateDuJour = new \DateTime();
        //dump($dateDuJour);
        $sorties= $this->sortieRepository->findSortiesDateCloturee($dateDuJour);

        $etat = $this->etatRepository->findOneBy(array('libelle'=>"Cloturée"));

        //($sorties);
        foreach ($sorties as $sortie) {
            //$Sortie->setActif(false);
            //$campus
            $sortie->setEtat($etat);
            //dd($Sortie);
            $this->entityManager->persist($sortie);


        }

        $this->entityManager->flush();

        //$Participant= $participantRepository->findByChangeDateEtat();
        //$sortie = $sortieRepository->find($id);


    }

    public function modifEtatArchivee()
    {

        //$dateDuJour = new \DateTime();
        //$dateDArchivage = new \DateTime('-1 month');
        $dateDArchivage = new \DateTime('-30 days');
        //dump($dateDArchivage);
        //dd($dateDuJour);
        $sorties= $this->sortieRepository->findSortiesDateArchivee($dateDArchivage);
        //dd($sorties);
        $etat = $this->etatRepository->findOneBy(array('libelle'=>"Archivée"));


        foreach ($sorties as $sortie) {
            //$Sortie->setActif(false);
            //$campus
            $sortie->setEtat($etat);
            //dd($Sortie);
            $this->entityManager->persist($sortie);


        }

        $this->entityManager->flush();

        //$Participant= $participantRepository->findByChangeDateEtat();
        //$sortie = $sortieRepository->find($id);


    }

}