<?php

namespace App\Repository;

use App\Entity\CrossConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CrossConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method CrossConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method CrossConfig[]    findAll()
 * @method CrossConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrossConfigRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CrossConfig::class);
    }

    /**
     * @return CrossConfig[] Returns an array of CrossConfig objects
     */

    public function getConfig()
    {
        return ($this->createQueryBuilder('c')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult())[0]
        ;
    }
}
