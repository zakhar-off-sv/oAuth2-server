<?php

declare(strict_types=1);

namespace App\Infrastructure\oAuth2Server\Bridge\Entity;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

final class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait, EntityTrait, TokenEntityTrait;

    /**
     * AccessToken constructor.
     * @param ClientEntityInterface $clientEntity
     * @param array $scopes
     * @param string|null $userIdentifier
     */
    public function __construct(
        ClientEntityInterface $clientEntity,
        array $scopes = [],
        ?string $userIdentifier = null
    )
    {
        $this->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
        if ($userIdentifier !== null) {
            $this->setUserIdentifier($userIdentifier);
        }
    }
}
