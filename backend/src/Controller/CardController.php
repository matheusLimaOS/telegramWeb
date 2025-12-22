<?php

namespace App\Controller;

use App\Entity\PerfilTip;
use App\Entity\PerfilCard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TipsRevealedRepository;
use App\Repository\CategoryRepository;
use App\Repository\PerfilTipRepository;
use App\Repository\PerfilCardRepository;

class CardController extends AbstractController
{
    #[Route('/api/card', methods: ['POST'])]
    public function card(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository,
    ): JsonResponse
    {  
        $data = $request->toArray();
        $category = $categoryRepository->find($data['category']);
        if(!$category) {
            return $this->json(['error' => 'Categoria não encontrada'], 404);
        };
        $card = new PerfilCard();
        $card->setAnswer($data['answer']);
        $card->setCategory($category);
        $nextOrder = 1;

        foreach($data['tips'] as $tipData) {
            $cardTip = new PerfilTip();
            $cardTip->setTip($tipData);
            $cardTip->setTipOrder($nextOrder);
            $card->addTip($cardTip);
            $nextOrder++;
        }
        
        $em->persist($card);
        $em->flush();

        return $this->json([
            'id' => $card->getId(),
            'category' => $card->getCategory()->getName(),
            'answer' => $card->getAnswer()
        ]); 
    }
    
    #[Route('/api/card/{id}', methods: ['GET'])]
    public function getCardById(
        PerfilCard $card,
        TipsRevealedRepository $revealedTipRepository,
        PerfilTipRepository $tipRepository
    ): JsonResponse
    {  
        $user = $this->getUser();
        $alreadyGuessedRight = $revealedTipRepository->hasAlreadyGuessedRightUserAndCard($user, $card);
        if($alreadyGuessedRight) {
            $revealedTipIds = $tipRepository->findTips($card);
        } else {
            $revealedTipIds = $revealedTipRepository->findRevealedTipIdsForUserAndCard($user, $card);
        }
        $guessRemaining = $revealedTipRepository->countUnguessedTipsForUserAndCard($user, $card);

        return $this->json([
            'id' => $card->getId(),
            'category' => $card->getCategory()->getName(),
            'guessRemaining' => $guessRemaining > 0 ? true : false,
            'alreadyGuessRight' => $alreadyGuessedRight,
            'answer' => $card->getAnswer(),
                'tips' => array_values(array_filter(
                    array_map(
                        fn ($tip) => in_array($tip->getId(), $revealedTipIds, true)
                            ? ['tip' => $tip->getTip(), 'tipOrder' => $tip->getTipOrder()]
                            : null,
                        $card->getTips()->toArray()
                    ),
                    fn ($item) => $item !== null
                )
            ),
        ]); 
    }
}
