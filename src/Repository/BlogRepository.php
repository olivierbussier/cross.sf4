<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    /**
     * BlogRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    /**
     * @param $position
     * @return Blog|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function selectByPosition($position) : ?Blog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.position = :val')
            ->setParameter('val', $position)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $position
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function selectPosJustBelow($position)
    {
        $res = $this->createQueryBuilder('b')
            ->select('min(b.position)')
            ->where("b.position > $position")
            ->getQuery()
            ->getSingleResult()
            ;
        return $res[1];
    }

    /**
     * @param $position
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function selectPosJustAbove($position)
    {
        $res = $this->createQueryBuilder('b')
            ->select('max(b.position)')
            ->where("b.position < $position")
            ->getQuery()
            ->getSingleResult()
            ;
        return $res[1];
    }

    public function deleteById($id)
    {
        /**
         * @var $em EntityManager
         */
        $em = $this->getEntityManager();
        $blog = $this->find($id);

        if (!$blog) {
            return false;
        }
        $em->remove($blog);
        $em->flush();

        return true;
    }

    /**
     * @return Blog[] Returns an array of Blog objects
     */

    public function getAllPosts()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.position', 'desc')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }


//    /**
//     * @return BlogText[] Returns an array of BlogText objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlogText
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
