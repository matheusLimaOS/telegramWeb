<?php

namespace App\EventListener;

use App\Service\JwtService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JwtRequestListener
{
    public function __construct(
        private JwtService $jwtService,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Rotas públicas (libera sem JWT)
        if (
            str_starts_with($request->getPathInfo(), '/login')
            || str_starts_with($request->getPathInfo(), '/register')
            || str_starts_with($request->getPathInfo(), '/create-user')
        ) {
            return;
        }

        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            $event->setResponse(new JsonResponse([
                'error' => 'Token não informado',
            ], 401));

            return;
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $claims = $this->jwtService->validate($token);
            $request->attributes->set('jwt_payload', $claims);
        } catch (\Throwable $e) {
            $event->setResponse(new JsonResponse([
                'error' => 'Token inválido ou expirado',
            ], 401));
        }
    }
}
