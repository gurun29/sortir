<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @extends ServiceEntityRepository<Participant>
 *
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    public function add(Participant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Participant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Participant[] Returns an array of Participant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.id < :val')
//            ->setParameter('val', $value)
//            //->orderBy('p.id', 'ASC')
//            //->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function loadUserByIdentifier($usernameOrMail): ?Participant
    {
        //$entityManager = $this->getEntityManager();
        return $this->createQueryBuilder('p')
           ->andWhere('p.pseudo = :username OR p.mail =:username')
           ->setParameter('username', $usernameOrMail)
           ->getQuery()
           ->getOneOrNullResult()
        ;
  }

    public function loadUserByUsername(string $username)
    {
        //$entityManager = $this->getEntityManager();
        return $this->loadUserByIdentifier($username);
    }


}
