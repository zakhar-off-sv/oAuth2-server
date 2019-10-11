<?php

namespace App\Domain\Repository;

use App\Domain\Model\AuthorizationCode;

interface AuthorizationCodeRepositoryInterface
{
    public function find(string $authCodeId): ?AuthorizationCode;

    public function save(AuthorizationCode $authCode): void;
}
