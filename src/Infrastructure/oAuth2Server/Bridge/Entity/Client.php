<?php

namespace App\Infrastructure\oAuth2Server\Bridge\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

final class Client implements ClientEntityInterface
{
    use ClientTrait, EntityTrait;

    /**
     * Client constructor.
     * @param string $identifier
     * @param string $name
     * @param string $redirectUri
     * @param bool $isConfidential
     */
    public function __construct(
        string $identifier,
        string $name,
        string $redirectUri,
        bool $isConfidential
    )
    {
        $this->setIdentifier($identifier);
        $this->name = $name;
        $this->redirectUri = explode(',', $redirectUri);
        $this->isConfidential = $isConfidential;
    }
}
