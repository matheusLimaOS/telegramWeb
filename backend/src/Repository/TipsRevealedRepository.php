<?php

namespace App\Repository;

use App\Entity\TipsRevealed;
use App\Entity\User;
use App\Entity\PerfilCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TipsRevealedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipsRevealed::class);
    }

    public function findRevealedTipIdsForUserAndCard(
        User $user,
        PerfilCard $card
    ): array {
        return array_column(
            $this->createQueryBuilder('r')
                ->select('IDENTITY(r.tip) AS tip_id')
                ->where('r.user = :user')
                ->andWhere('r.card = :card')
                ->setParameter('user', $user)
                ->setParameter('card', $card)
                ->getQuery()
                ->getArrayResult(),
            'tip_id'
        );
    }

    public function markAllTipsAsGuessedForUserAndCard(
        User $user,
        PerfilCard $card
    ): int {
        return $this->createQueryBuilder('r')
            ->update()
            ->set('r.guessed', ':true')
            ->where('r.user = :user')
            ->andWhere('r.card = :card')
            ->setParameter('true', true)
            ->setParameter('user', $user)
            ->setParameter('card', $card)
            ->getQuery()
            ->execute();
    }

    public function countUnguessedTipsForUserAndCard(
        User $user,
        PerfilCard $card
    ): int {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.user = :user')
            ->andWhere('r.card = :card')
            ->andWhere('(r.guessed = false OR r.guessed IS NULL)')
            ->setParameter('user', $user)
            ->setParameter('card', $card)
            ->getQuery()
            ->getSingleScalarResult();
    }    
    
    public function getUnguessedTipsForUserAndCard(
        User $user,
        PerfilCard $card
    ): int {
        return 
            $this->createQueryBuilder('r')
            ->select('r.id')
            ->where('r.user = :user')
            ->andWhere('r.card = :card')
            ->andWhere('(r.guessed = false OR r.guessed IS NULL)')
            ->setParameter('user', $user)
            ->setParameter('card', $card)
            ->getQuery()
            ->getSingleScalarResult();
    }    
    
    public function hasAlreadyGuessedRightUserAndCard(
        User $user,
        PerfilCard $card
    ): bool {
        return (bool) $this->createQueryBuilder('r')
            ->select('1')
            ->where('r.user = :user')
            ->andWhere('r.card = :card')
            ->andWhere('r.guessRight = true')
            ->setParameter('user', $user)
            ->setParameter('card', $card)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
