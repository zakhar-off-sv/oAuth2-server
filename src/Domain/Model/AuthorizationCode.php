<?php

declare(strict_types=1);

namespace App\Domain\Model;

class AuthorizationCode
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string|null
     */
    private $userId;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var array
     */
    private $scopes = [];

    /**
     * @var bool
     */
    private $revoked = false;

    /**
     * @var \DateTimeInterface
     */
    private $expiresAt;

    /**
     * Token constructor.
     * @param string $id
     * @param string|null $userId
     * @param string $clientId
     * @param array $scopes
     * @param bool $revoked
     * @param \DateTimeInterface $expiresAt
     */
    public function __construct(
        string $id,
        ?string $userId,
        string $clientId,
        array $scopes,
        bool $revoked,
        \DateTimeInterface $expiresAt
    )
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->clientId = $clientId;
        $this->scopes = $scopes;
        $this->revoked = $revoked;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }
}
