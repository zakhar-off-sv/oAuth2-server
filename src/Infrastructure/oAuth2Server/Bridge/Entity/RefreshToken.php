<?php

declare(strict_types=1);

namespace App\Infrastructure\oAuth2Server\Bridge\Entity;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

final class RefreshToken implements RefreshTokenEntityInterface
{
    use RefreshTokenTrait, EntityTrait;
}
