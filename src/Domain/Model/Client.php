<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Ramsey\Uuid\Uuid;
use RuntimeException;

class Client
{
    public const GRANT_AUTHORIZATION_CODE = 'authorization_code';
    public const GRANT_CLIENT_CREDENTIALS = 'client_credentials';
    public const GRANT_IMPLICIT = 'implicit';
    public const GRANT_PASSWORD = 'password';
    public const GRANT_REFRESH_TOKEN = 'refresh_token';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var array
     */
    private $redirect = [];

    /**
     * @var array
     */
    private $grants = [];

    /**
     * @var bool
     */
    private $confidential = false;

    /**
     * @var bool
     */
    private $active = true;

    /**
     * Client constructor.
     * @param ClientId $clientId
     * @param string $name
     */
    private function __construct(ClientId $clientId, string $name)
    {
        $this->id = $clientId->toString();
        $this->name = $name;
    }

    public static function create(string $name): Client
    {
        $clientId = ClientId::fromString(Uuid::uuid4()->toString());
        return new self($clientId, $name);
    }

    public function getId(): UserId
    {
        return UserId::fromString($this->id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return array
     */
    public function getRedirect(): array
    {
        return $this->redirect;
    }

    /**
     * @param string ...$redirect
     */
    public function setRedirect(string ...$redirect): void
    {
        foreach ($redirect as $item) {
            if (!\filter_var($item, FILTER_VALIDATE_URL)) {
                throw new RuntimeException(
                    \sprintf('The \'%s\' string is not a valid URI.', $item)
                );
            }
        }
        $this->redirect = $redirect;
    }

    /**
     * @return array
     */
    public function getGrants(): array
    {
        return $this->grants;
    }

    /**
     * @param string ...$grants
     */
    public function setGrants(string ...$grants): void
    {
        foreach ($grants as $item) {
            if (!\in_array($item, [
                self::GRANT_AUTHORIZATION_CODE,
                self::GRANT_CLIENT_CREDENTIALS,
                self::GRANT_IMPLICIT,
                self::GRANT_PASSWORD,
                self::GRANT_REFRESH_TOKEN
            ])) {
                throw new RuntimeException(
                    \sprintf('The \'%s\' grant is not supported.', $item)
                );
            }
        }
        $this->grants = $grants;
    }

    /**
     * @return bool
     */
    public function isConfidential(): bool
    {
        return $this->confidential;
    }

    /**
     * @param bool $confidential
     */
    public function setConfidential(bool $confidential): void
    {
        $this->confidential = $confidential;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
