<?php

namespace App\filtres;

use App\Entity\Campus;
use Cassandra\Date;
use phpDocumentor\Reflection\Types\Boolean;

class Filtres extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

public ?Campus $camp= null;

public ?string $nomDeSortie=null;

public ?\DateTime $dateMin = null;

public ?\DateTime $dateMax =null;

public bool $organisateur=false ;

public bool $inscrit = false;

public bool $nonInscrit =false;

public bool $sortiePasser = false;


}