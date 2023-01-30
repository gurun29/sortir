<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\filtres\Filtres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use App\Repository\CampusRepository;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Filtres $search
     * @return Sortie[]
     */
    public function findSearch(Filtres $search ,Participant $participant):array
    {
        $query=$this->createQueryBuilder('s')
            ->join(Campus::class,'c')
            ->join(Etat::class,'e');





        if (!empty($search->nomDeSortie)){
            $query=$query
                ->andWhere('s.nom Like :nomDeSortie')
                ->setParameter('nomDeSortie','%'.$search->nomDeSortie.'%');

        }
       if (!empty($search->dateMin)){
            $query=$query
                ->andWhere('s.dateHeureDebut >= :dateMin')
                ->setParameter('dateMin', $search->dateMin );
        }
        if (!empty($search->dateMax)){
            $query=$query
                ->andWhere('s.dateLimiteInscription <= :dateMax')
                ->setParameter('dateMax',$search->dateMax );
        }
        if (!empty($search->camp)){
            $query->andWhere('s.siteOrganisateur = :camp')
                ->setParameter('camp',$search->camp );

        }

        if (!empty($search->inscrit)){

            $query->andWhere(':groupId MEMBER OF s.inscrits')
            ->setParameter('groupId',$participant);
        }
        if (!empty($search->nonInscrit)){

            $query->andWhere(':groupId NOT MEMBER OF s.inscrits')
            ->setParameter('groupId',$participant);
        }

        if (!empty($search->sortiePasser)){

            $query=$query
                ->andWhere('e.libelle Like :sortiePasser')
                ->setParameter('sortiePasser','Archivée');


        }
        if (!empty($search->organisateur)){
            $query=$query
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur',$search->organisateur);
        }



      return $query->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Participant objects
     */
    public function findSortiesDateCloturee($dateDuJour): array
    {
        $query = $this->createQueryBuilder('s')
            //->join(Etat::class, 'e')
            ->addSelect('e')
            ->leftJoin('s.etat','e')
            ->andWhere('s.dateLimiteInscription < :val')
            ->setParameter('val', $dateDuJour)
            //->andWhere('s.etat = :val2')
            ->andWhere('e.libelle = :val2')
            ->setParameter('val2', "Ouverte")
            //->orderBy('s.id', 'ASC')
            //->setMaxResults(10)
            //->getQuery()
            //->getResult()
            ;

        //dd($query->getQuery()->getResult());
        return $query->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Participant objects
     */
    public function findSortiesDateArchivee($dateDArchivage): array
    {
        $query = $this->createQueryBuilder('s')
            //->join(Etat::class, 'e')
            ->addSelect('e')
            ->leftJoin('s.etat','e')
            ->andWhere('s.dateLimiteInscription < :val')
            ->setParameter('val', $dateDArchivage)
            ->andWhere('e.libelle = :val2')
            ->setParameter('val2', "Cloturée")
            //->orderBy('s.id', 'ASC')
            //->setMaxResults(10)
            //->getQuery()
            //->getResult()
            ;
        //dd($query);
        return $query->getQuery()->getResult();
    }



}
