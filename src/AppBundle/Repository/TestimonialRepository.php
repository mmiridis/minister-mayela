<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TestimonialRepository extends EntityRepository
{
    public function findAllActive()
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where($qb->expr()->eq('t.isActive', $qb->expr()->literal(true)))
            ->orderBy('t.position');

        return $qb->getQuery()->getResult();
    }

    public function findAllSorted()
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->orderBy('t.position');

        return $qb->getQuery()->getResult();
    }
}
