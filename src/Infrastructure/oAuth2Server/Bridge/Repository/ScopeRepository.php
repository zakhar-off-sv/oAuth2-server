<?php

namespace App\Infrastructure\oAuth2Server\Bridge\Repository;

use App\Infrastructure\oAuth2Server\Bridge\Entity\Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

final class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($identifier): ScopeEntityInterface
    {
        if (Scope::hasScope($identifier)) {
            return new Scope($identifier);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null): array
    {
        $filteredScopes = [];
        /** @var Scope $scope */
        foreach ($scopes as $scope) {
            $hasScope = Scope::hasScope($scope->getIdentifier());
            if ($hasScope) {
                $filteredScopes[] = $scope;
            }
        }
        return $filteredScopes;
    }
}
