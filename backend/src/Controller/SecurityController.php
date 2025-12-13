<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use App\Repository\UserRepository;
use App\Service\JwtService;
use App\Service\RefreshTokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JwtService $jwtService,
        RefreshTokenGenerator $refreshTokenGenerator,
        EntityManagerInterface $em,
    ): JsonResponse {
        $data = $request->toArray();
        $user = $userRepository->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return $this->json(['error' => 'Credenciais inválidas'], 401);
        }

        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Credenciais inválidas'], 401);
        }

        $token = $jwtService->generate([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ]);

        $refreshToken = new RefreshToken();
        $refreshToken->setToken($refreshTokenGenerator->generate());
        $refreshToken->setUser($user);
        $refreshToken->setExpiresAt(
            new \DateTimeImmutable('+7 days')
        );

        $em->persist($refreshToken);
        $em->flush();

        return $this->json([
            'accessToken' => $token,
            'refreshToken' => $refreshToken->getToken(),
        ]);
    }

    #[Route('/refresh', methods: ['POST'])]
    public function refresh(
        Request $request,
        RefreshTokenRepository $repository,
        JwtService $jwtService,
        EntityManagerInterface $em,
    ): JsonResponse {
        $refreshToken = $request->headers->get('X-Refresh-Token');

        $refreshTokenDB = $repository->findOneBy([
            'token' => $refreshToken,
            'revoked' => false,
        ]);

        if (
            !$refreshToken
            || !$refreshTokenDB
            || $refreshTokenDB->getExpiresAt() < new \DateTimeImmutable()
        ) {
            return $this->json(['error' => 'Refresh token inválido'], 401);
        }

        $refreshTokenDB->revoke();

        $em->persist($refreshTokenDB);
        $em->flush();

        $newRefreshToken = new RefreshToken();
        $newRefreshToken->setToken(bin2hex(random_bytes(64)));
        $newRefreshToken->setUser($refreshTokenDB->getUser());
        $newRefreshToken->setExpiresAt(new \DateTimeImmutable('+7 days'));

        $em->persist($newRefreshToken);
        $em->flush();

        $newAccessToken = $jwtService->generate([
            'userId' => $refreshTokenDB->getUser()->getId(),
        ]);

        return $this->json([
            'accessToken' => $newAccessToken,
            'refreshToken' => $newRefreshToken->getToken(),
        ]);
    }

    #[Route(path: '/create-user', name: 'create-user', methods: ['POST'])]
    public function createUser(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
    ): JsonResponse {
        $data = $request->toArray();

        if ($userRepository->findOneBy(['email' => $data['email']])) {
            return $this->json(['error' => 'Email já cadastrado'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );

        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        return $this->json(['id' => $user->getId(), 'email' => $user->getEmail()]);
    }

    #[Route('/logout', methods: ['POST'])]
    public function logout(
        Request $request,
        RefreshTokenRepository $repo,
        EntityManagerInterface $em,
    ): JsonResponse {
        $refreshToken = $request->headers->get('X-Refresh-Token');
        $refreshTokenDB = $repo->findOneBy(['token' => $refreshToken]);
        $refreshTokenDB->revoke();

        $em->persist($refreshTokenDB);
        $em->flush();

        return $this->json(['message' => 'Logout realizado']);
    }
}
