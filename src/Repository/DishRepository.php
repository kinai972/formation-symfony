<?php

namespace App\Repository;

use App\Entity\Dish;
use App\DTO\SearchDishCriteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Dish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dish[]    findAll()
 * @method Dish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dish::class);
    }

    // 1. Récupérer tout les plats ordonnées par prix croissant
    // 2. Récupérer tout les plats dont le nom commence par Pizza
    // 3. Récupérer uniquement 5 plats ordonée par nom croissant
    // 4. Récupérer les 10 plats à partir de la page n°2, ordonée par prix decroissant
    // 5. Récupérer les 15 plats avec l'ingrédient "Tomate"

    public function findAllByCriteria(SearchDishCriteria $criteria): array
    {
        $qb = $this->createQueryBuilder('dish');

        if ($criteria->title) {
            $qb
                ->andWhere('dish.name LIKE :title')
                ->setParameter('title', '%' . $criteria->title . '%');
        }

        return $qb
            ->setMaxResults($criteria->limit)
            ->setFirstResult($criteria->limit * ($criteria->page - 1))
            ->getQuery()
            ->getResult();
    }

    public function findFivteenWithTomato(): array
    {
        return $this->createQueryBuilder('dish')
            ->setMaxResults(15)
            ->leftJoin('dish.ingredients', 'ingredient')
            ->andWhere('ingredient.name = :ingredientName')
            ->setParameter('ingredientName', 'tomate')
            ->getQuery()
            ->getResult();
    }

    public function findTenOfPageTwoOrderedByPrice(): array
    {
        return $this->createQueryBuilder('dish')
            ->orderBy('dish.price', 'DESC')
            ->setMaxResults(10)
            ->setFirstResult(10)
            ->getQuery()
            ->getResult();
    }

    public function findFiveOrderedByName(): array
    {
        return $this->createQueryBuilder('dish')
            ->orderBy('dish.name', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findAllLikePizza(): array
    {
        return $this->createQueryBuilder('dish')
            ->andWhere('dish.name LIKE :like')
            ->setParameter('like', 'Pizza%')
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderedByPrice(): array
    {
        return $this->createQueryBuilder('dish')
            ->orderBy('dish.price', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Dish[] Returns an array of Dish objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dish
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}