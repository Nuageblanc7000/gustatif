<?php

namespace App\Repository;

use App\Entity\User;

use App\Data\DataAdminFilter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }




    /**
     * permet de faire une recherche dans ma liste d'utilisateurs.
     *
     * @param DataAdminFilter $data
     * @return void
     */
    public function findFilterUserAdmin(DataAdminFilter $data)
    {
        $query = $this->createQueryBuilder('u')
        ->select('u','c','t','co')
             ->leftJoin('u.city','c')
             ->leftJoin('u.tokenResolve','t')
             ->leftJoin('u.comments','co')          
             ;
            
             if (!empty($data->getSearch())) 
             {
                $query->andWhere('u.pseudo LIKE :search')
              ->orWhere('c.localite LIKE :search')
              ->orWhere('u.adress LIKE :search')
              ->setParameter('search' , "%{$data->getSearch()}%");
             }
             if(!empty($data->getVerif()))
             {
                $query->andWhere('u.isAcountVerified = :verif')
                ->setParameter('verif' , "%{$data->getVerif()}%");
             }
            $query->orderBy('u.pseudo','ASC')
                  ->getQuery()->getResult()
        ;
        return $query;
    }

   /**
    * @return mixed 
    */
   public function findCountVerified()
   {
       return $this->createQueryBuilder('u')
            ->select('count(u) as verif')
           ->andWhere('u.isAcountVerified = true') 
           ->getQuery()->getSingleScalarResult()
       ;
   }
   /**
    * @return mixed 
    */
   public function findCountNotVerified()
   {
       return $this->createQueryBuilder('u')
            ->select('count(u) as verif')
           ->andWhere('u.isAcountVerified = false') 
           ->getQuery()->getSingleScalarResult()
       ;
   }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
