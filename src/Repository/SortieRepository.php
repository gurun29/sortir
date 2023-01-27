<?php

namespace App\Repository;

use App\Entity\Campus;
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
    public function findSearch(Filtres $search):array
    {
        $query=$this->createQueryBuilder('s')
            ->join(Campus::class,'C');



        if (!empty($search->nomDeSortie)){
            $query=$query
                ->andWhere('s.nom LIKE :nomDeSortie')
                ->setParameter('nomDeSortie', $search->nomDeSortie );

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
            $query=$query
                ->andWhere('C.nom Like :camp')
                ->setParameter('camp',$search->camp );
        }



      return $query->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Participant objects
     */
    public function findSortiesDateCloturee($value): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateLimiteInscription < :val')
            ->setParameter('val', $value)
            //->orderBy('p.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

}
