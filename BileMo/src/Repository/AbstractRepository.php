<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends ServiceEntityRepository
{
    protected function paginate(QueryBuilder $qb, $limit, $page): Pagerfanta
    {
        if(0 == $limit ) {
            throw new \LogicException('$limit must be greater than 0.');
        }

        $pager = new Pagerfanta(new QueryAdapter($qb));
        
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }
}