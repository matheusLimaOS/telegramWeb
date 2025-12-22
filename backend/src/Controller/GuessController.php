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
use App\Repository\PerfilCardRepository;

class GuessController extends AbstractController
{
    #[Route('/api/guess/{cardId}', methods: ['POST'])]
    public function guess(
        Request $request,
        EntityManagerInterface $em,
        PerfilCardRepository $cardRepository,
        TipsRevealedRepository $revealedTipRepository,
        int $cardId
    ): JsonResponse
    {  
        $data = $request->toArray();
        $card = $cardRepository->find($cardId);
        $canGuess = $revealedTipRepository->countUnguessedTipsForUserAndCard($this->getUser(), $card);
        
        if($canGuess == 0) {
            return $this->json(['error' => 'No remaining guesses'], 400);
        }

        $idGuess = $revealedTipRepository->getUnguessedTipsForUserAndCard($this->getUser(), $card);
        $tipGuess = $revealedTipRepository->find($idGuess);

        $normalizedAnswer = $this->normalize($card->getAnswer());
        $normalizedGuess  = $this->normalize($data['guess']);

        $isCorrect = $normalizedAnswer === $normalizedGuess;
        if(!$isCorrect){
            similar_text($normalizedAnswer, $normalizedGuess, $percent);
            $isCorrect = $percent >= 80;
            if(!$isCorrect){
                $tipGuess->setGuess($data['guess']);
                $tipGuess->setGuessRight(false);
                $tipGuess->setGuessed(true);
                $em->persist($tipGuess);
                $em->flush();
                return $this->json(['correct' => false]);
            }
        }

        $tipGuess->setGuess($data['guess']);
        $tipGuess->setGuessRight(true);
        $tipGuess->setGuessed(true);
        $em->persist($tipGuess);
        $em->flush();

        return $this->json(['correct' => true]);
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $stopWords = [' o ', ' a ', ' os ', ' as ', ' de ', ' do ', ' da '];
        $text = ' ' . $text . ' ';
        $text = str_replace($stopWords, ' ', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return $text;
    }

}
