<?php

namespace App\Domain\Repository;

use App\Domain\Model\Client;

interface ClientRepositoryInterface
{
    public function findActive(string $clientId): ?Client;

    public function save(Client $client): void;

    public function remove(Client $client): void;
}
