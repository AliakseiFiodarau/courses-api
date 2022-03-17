<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 *
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    /**
     * CourseRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * Saving Course entity.
     *
     * @param Course $entity
     * @param bool $flush
     * @return void
     */
    public function save(Course $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Removing Course entity.
     *
     * @param Course $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Course $entity, bool $flush = false): void
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
