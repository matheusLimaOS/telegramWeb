<?php

namespace App\Controller;

use App\Entity\PerfilTip;
use App\Entity\TipsRevealed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PerfilTipRepository;
use App\Repository\PerfilCardRepository;
use App\Repository\TipsRevealedRepository;

class TipController extends AbstractController
{
    #[Route('/api/tip/{cardId}/{cardNumber}', methods: ['GET'])]
    public function getTip(
        Request $request,
        EntityManagerInterface $em,
        PerfilTipRepository $tipRepository,
        PerfilCardRepository $cardRepository,
        TipsRevealedRepository $tipsReavealedRepository,
        int $cardId,
        int $cardNumber
    ): JsonResponse
    {  
        $card = $cardRepository->find($cardId);
        $tip = $tipRepository->findOneBy(['card' => $card, 'tipOrder' => $cardNumber]);
        $tipRevealed = $tipsReavealedRepository->findOneBy(['user' => $this->getUser(), 'tip' => $tip]);
        if(!$tipRevealed) {
            $tipsReavealedRepository->markAllTipsAsGuessedForUserAndCard($this->getUser(), $card);
            $tipRevealed = new TipsRevealed();
            $tipRevealed->setUser($this->getUser());
            $tipRevealed->setCard($card);
            $tipRevealed->setTip($tip);
            $em->persist($tipRevealed);
            $em->flush();
        }

        return $this->json([
            'tip' => $tip->getTip(),
        ]); 
    }
}
