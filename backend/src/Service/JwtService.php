<?php

namespace App\Service;

use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;

class JwtService
{
    private Configuration $config;

    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($_ENV['APP_SECRET'])
        );
    }

    public function generate(array $claims): string
    {
        $now = new \DateTimeImmutable();

        $token = $this->config->builder()
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'));

        foreach ($claims as $key => $value) {
            if ('sub' === $key) {
                $token = $token->relatedTo((string) $value)
                ;
            } else {
                $token = $token->withClaim($key, $value);
            }
        }

        return $token->getToken(
            $this->config->signer(),
            $this->config->signingKey()
        )->toString();
    }

    public function validate(string $jwtString): array
    {
        $token = $this->config->parser()->parse($jwtString);

        if (!$token instanceof Plain) {
            throw new \RuntimeException('Token inválido');
        }

        $constraints = [
            new SignedWith(
                $this->config->signer(),
                $this->config->signingKey()
            ),
            new ValidAt(SystemClock::fromUTC()),
        ];

        $this->config->validator()->assert($token, ...$constraints);

        return $token->claims()->all();
    }
}
