<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Restaurant;
use App\Data\DataAdminFilter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function add(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Comment[] Returns an array of Comment objects
    */
   public function findByCommentOpti(?Restaurant $resto): array
   {
       return $this->createQueryBuilder('c')
       ->select('c,r,u')
       ->leftJoin('c.author','u')
       ->leftJoin('c.resto','r')
       ->where('r = :resto')
       ->setParameter('resto',$resto)
           ->orderBy('c.id', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }


   public function findFilterCommentsAdmin(DataAdminFilter $data)
   {
       $query = $this->createQueryBuilder('c')
            ->select('c','u','r','t')
            ->leftJoin('c.author','u')
            ->leftJoin('c.resto','r')
            ->leftJoin('u.tokenResolve','t')          
            ;
           
            if (!empty($data->getSearch())) {
               $query->andWhere('c.description LIKE :search')
             ->orWhere('u.pseudo LIKE :search') 
             ->setParameter('search' , "%{$data->getSearch()}%");
            }
            if (!empty($data->getResto())) {
                $query->andWhere('r.id IN (:resto)')
              ->setParameter('resto' ,$data->getResto());
             }
           $query->orderBy('c.dateCom','ASC')
                 ->getQuery()->getResult()
       ;
       return $query;
   }


//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
