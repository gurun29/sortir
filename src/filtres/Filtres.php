<?php

namespace App\filtres;

use Cassandra\Date;

class Filtres extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     *  @var string
     *
     */
public $camp;
    /**
     *  @var string
     */
public $nomDeSortie='';
    /**
     *
     * @var date
     */
public $dateMin;
    /**
     *
     * @var date
     */
public $dateMax;
    /**
     * @var string
     */
public $organisateur ;
/**
 * @var string
 */
public $inscrit;
/**
 * @var string
 */
public $nonInscrit;
    /**
     * @var string
     */
public $sortiePasser;


}