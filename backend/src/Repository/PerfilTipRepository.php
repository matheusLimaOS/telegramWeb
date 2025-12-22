<?php

namespace App\Repository;

use App\Entity\PerfilTip;
use App\Entity\PerfilCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PerfilTipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PerfilTip::class);
    }

    public function findTips(
        PerfilCard $card
    ): array {
        return array_column(
            $this->createQueryBuilder('r')
                ->select('r.id AS id')
                ->where('r.card = :card')
                ->setParameter('card', $card)
                ->getQuery()
                ->getArrayResult(),
            'id'
        );
    }

}
