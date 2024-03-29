<?php

namespace App\Repository;

use App\Entity\Lecture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lecture>
 *
 * @method Lecture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lecture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lecture[]    findAll()
 * @method Lecture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LectureRepository extends ServiceEntityRepository
{
    /**
     * LectureRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecture::class);
    }

    /**
     * Saving Lecture entity.
     *
     * @param Lecture $entity
     * @param bool $flush
     * @return void
     */
    public function save(Lecture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Removing Lecture entity.
     *
     * @param Lecture $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Lecture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Getting Doctrine\ORM\Query object by DQL.
     *
     * @param string $where
     * @return Query
     */
    public function getQuery(string $where = ""): Query {
        $repositoryClassName = $this->getClassName();
        $dql = "SELECT e FROM $repositoryClassName e $where";

        return $this->getEntityManager()->createQuery($dql);
    }
}
