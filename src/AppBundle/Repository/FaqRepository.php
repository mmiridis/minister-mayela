<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FaqRepository extends EntityRepository
{
    public function findAllActive()
    {
        $qb = $this->createQueryBuilder('f');
        $qb
            ->where($qb->expr()->eq('f.isActive', $qb->expr()->literal(true)))
            ->orderBy('f.position');

        return $qb->getQuery()->getResult();
    }

    public function findAllSorted()
    {
        $qb = $this->createQueryBuilder('f');
        $qb
            ->orderBy('f.position');

        return $qb->getQuery()->getResult();
    }
}
