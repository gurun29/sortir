<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;

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
    public function index(SortieRepository $sortieRepository,EntityManagerInterface $em): Response
    {
        $list=new Sortie();

        $list=$sortieRepository->findAll();
        return $this->render('sortie.html.twig', [
            'list'=>$list
        ]);
    }
}
