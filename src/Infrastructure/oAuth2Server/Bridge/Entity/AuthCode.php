<?php

namespace App\Infrastructure\oAuth2Server\Bridge\Entity;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

final class AuthCode implements AuthCodeEntityInterface
{
    use EntityTrait, TokenEntityTrait, AuthCodeTrait;
}
