<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class BasicController extends AbstractController
{
    #[Route('/ping', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        return $this->json(['pong' => true]);
    }

    #[Route('/test', methods: ['POST'])]
    public function testPost(): JsonResponse
    {
        return $this->json(['status' => 'success']);
    }
}
