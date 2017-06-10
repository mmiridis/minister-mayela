<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PictureRepository extends EntityRepository
{
    public function findAllActive()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where($qb->expr()->eq('p.isActive', $qb->expr()->literal(true)))
            ->orderBy('p.position');

        return $qb->getQuery()->getResult();
    }

    public function findAllSorted()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->orderBy('p.position');

        return $qb->getQuery()->getResult();
    }
}
