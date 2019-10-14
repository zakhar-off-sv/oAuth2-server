<?php

declare(strict_types=1);

namespace App\Infrastructure\oAuth2Server\Bridge\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

final class User implements UserEntityInterface
{
    use EntityTrait;

    /**
     * User constructor.
     * @param string|null $identifier
     */
    public function __construct(?string $identifier = null)
    {
        if ($identifier !== null) {
            $this->setIdentifier($identifier);
        }
    }
}
