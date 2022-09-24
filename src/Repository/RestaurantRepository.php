<?php

namespace App\Repository;

use App\Data\DataFilter;
use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restaurant>
 *
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function add(Restaurant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Restaurant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function restoPaginator(?DataFilter $data){
       $query = $this->createQueryBuilder('r')
       ->select('r','v','c','o','com')
       ->leftJoin('r.city','v')
       ->leftjoin('r.category','c')
       ->leftJoin('r.origine','o')
       ->leftJoin('r.comments','com')
       ;

       if(!empty($data->s)){
        $query->andWhere('r.name LIKE :s')
              ->orWhere('v.localite LIKE :s')
              ->setParameter('s' , "%{$data->s}%");
       }

       if(!empty($data->categories)){
        $query->andWhere('c.id IN (:cat)')
           
              ->setParameter('cat' , $data->categories);
       }
       if(!empty($data->t)){
        $query->andWhere('o.id = :s')

              ->setParameter('s' , $data->t);
       }

       return $query->getQuery()->getResult();
    }

    public function findAllRestoOpti(){
        $query = $this->createQueryBuilder('r')
        ->select('r','v','c','o','com')
        ->leftJoin('r.city','v')
        ->leftJoin('r.category','c')
        ->leftJoin('r.origine','o')
        ->leftJoin('r.comments','com')
        ;
        return $query->getQuery()->getResult();
        ;
    }

//    public function findAllOptiResto(Restaurant $resto): array
//    {
//        return $this->createQueryBuilder('r')
//             ->select('r','u','ca','ori','co','images')
//             ->leftJoin('r.category','ca')
//             ->leftJoin('r.user','u')
//             ->leftJoin('r.images','images')
//             ->leftJoin('r.origine','ori')
//             ->leftJoin('r.comments','co')
//             ->orderBy('ca.id','ASC')
//             ->andWhere('r.id = :val')
//            ->setParameter('val', $resto->getId())
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Restaurant
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
