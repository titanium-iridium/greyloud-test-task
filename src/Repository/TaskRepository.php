<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Literal;
use Doctrine\ORM\Query\Expr\Math;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\Query\Expr\Select;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Task::class);
    }
    
     /**
      * Find all tasks with their longest/latest comments (text length) 
      * @return Task Task[] with its comments
     */
    public function findAllWithLongestComment()
    {
        $em = $this->getEntityManager();
        
        // a) Query builder approach is too monstruous and messy so we use shorter and clear (?) DQL approach
        // b) Doctrine does not support LIMIT in nested queries so we use custom DQL function FIRST_ROW
        // c) JOIN ... ON with custom condition not supported (use WITH instead)
//        SELECT t task, usr.username comment_author, LENGTH(cmt.text) comment_text_length
//        LEFT JOIN App\\Entity\\User usr WITH usr = cmt.author
        //            LEFT JOIN App\\Entity\\User usr WITH usr = cmt.author
        $q = $em->createQuery(/** @lang DQL */
            "
            SELECT t.id task_id, t.title task_title, LENGTH(cmt.text) comment_text_length, usr.username comment_author
            FROM App\\Entity\\Task t
            LEFT JOIN t.comments cmt WITH cmt.id = FIRST_ROW(
              SELECT cmt2.id
              FROM App\\Entity\\Comment cmt2
              WHERE cmt2.task = t
              ORDER BY LENGTH(cmt2.text) DESC, cmt2.created DESC
            )
            LEFT JOIN App\\Entity\\User usr WITH usr = cmt.author
            ORDER BY t.created ASC
            "
        );
        
        return $q->getResult();
    }
    
    /**
     * @return Task Task with its comments
     */
    public function findOneWithComments($id)
    {
       return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->innerJoin('t.comments', 'c')
            ->addSelect('c')
            ->setParameter('id', $id)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(1)
            ->getQuery()
//            ->getResult()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
