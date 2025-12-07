<?php

namespace App\Repository;

use App\Entity\Maquette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use \Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Maquette>
 */
class MaquetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Maquette::class);
    }
    
    public function getAllQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('m')
        ->select('m.id, m.imagePreview, m.slug, m.shortDescription, m.subtitle')
        ->orderBy('m.id', 'ASC');
    }

    public function findByCategoryName(string $categoryName, int $limit = null, int $offset = null): array
    {
        $qb =  $this->createQueryBuilder('m')
                ->select('m.id, m.slug, m.subtitle, m.shortDescription, m.imagePreview, c.type')
                ->join('m.categoriesSite','c')
                ->andWhere('c.type = :type');

          if($limit != null){
            $qb->setMaxResults($limit);
        }

        if($offset != null){
            $qb->setFirstResult($offset);
        }       

        return $qb->setParameter('type', $categoryName)
        ->getQuery()
        ->getResult();
    }

    public function findOneWidthCategories(string $slug): ?Maquette
    {
        return $this->createQueryBuilder('m')
        ->leftJoin('m.categoriesSite','c')
        ->addSelect('c')
        ->andWhere('m.slug = :slug')
        ->setParameter('slug', $slug)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findResultWidthCategoriesWidthLimit(int $limit = null,int $offset = null)
    {
        $qb = $this->createQueryBuilder('m')
        ->leftJoin('m.categoriesSite','c')
        ->groupBy('m.id');
              

        if($limit != null){
            $qb->setMaxResults($limit);
        }

        if($offset != null){
            $qb->setFirstResult($offset);
        }

        return $qb->select('m.id ,m.imagePreview, m.slug, m.shortDescription, m.subtitle, c.type')
        ->getQuery()
        ->getResult();

    }



    //    /**
    //     * @return Maquette[] Returns an array of Maquette objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Maquette
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
