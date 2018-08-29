<?php

namespace App\Repository;

use App\Entity\Resultat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Resultat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resultat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resultat[]    findAll()
 * @method Resultat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Resultat::class);
    }

//    /**
//     * @return Resultat[] Returns an array of Resultat objects
//     */
    public function getAllCourses()
    {
        return $this->createQueryBuilder('r')
            ->select('distinct r.course')
            ->orderBy('r.course', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param int $annee
     * @param string $course
     * @return array
     */
    public function getCourse(int $annee,string $course)
    {
        return $this->createQueryBuilder('r')
            ->where('r.anneeCross = :an')
            ->andWhere('r.course = :course')
            ->setParameter('an',$annee)
            ->setParameter('course',$course)
            ->orderBy('r.classement', 'ASC')
            ->getQuery()
            ->getScalarResult()
            //->getResult()
            ;
    }

//    /**
//     * @return Resultat[] Returns an array of Resultat objects
//     */
    public function getAnneesCourses()
    {
        $em = $this->getEntityManager();
        return $em->createQuery('select distinct u.anneeCross from App\Entity\Resultat u order by u.anneeCross asc')
                  ->getResult()
            ;/*
        return $this->createQueryBuilder('r')
            ->select('distinct r.anneeCross')
            ->orderBy('r.anneeCross', 'ASC')
            ->getQuery()
            ->getResult()
            ;*/
    }

    public function deleteAnnee(int $annee)
    {
        return $this->createQueryBuilder('r')
            ->delete()
            ->where('r.anneeCross = ?1')
            ->setParameter(1,$annee)
            ->getQuery()
            ->execute();
    }

    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Resultat
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
