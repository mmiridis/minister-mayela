<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Provider;

class ProviderRepository extends EntityRepository
{
    public function findAllActive()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where($qb->expr()->eq('p.isActive', $qb->expr()->literal(true)))
            ->orderBy('p.position');

        $providers = [];
        /** @var Provider $provider */
        foreach ($qb->getQuery()->getResult() as $provider) {
            $providers[ $provider->getCategory() ][] = $provider;
        }

        return $providers;
    }

    public function findAllSorted()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->orderBy('p.position');

        return $qb->getQuery()->getResult();
    }
}