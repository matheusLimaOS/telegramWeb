<?php

namespace App\Service;

class RefreshTokenGenerator
{
    public function generate(): string
    {
        return bin2hex(random_bytes(64)); // 128 chars
    }
}
