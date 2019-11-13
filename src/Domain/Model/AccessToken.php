<?php

declare(strict_types=1);

namespace App\Domain\Model;

class AccessToken
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
    private $createdAt;

    /**
     * @var \DateTimeInterface
     */
    private $updatedAt;

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
     * @param \DateTimeInterface $createdAt
     * @param \DateTimeInterface $updatedAt
     * @param \DateTimeInterface $expiresAt
     */
    public function __construct(
        string $id,
        ?string $userId,
        string $clientId,
        array $scopes,
        bool $revoked,
        \DateTimeInterface $createdAt,
        \DateTimeInterface $updatedAt,
        \DateTimeInterface $expiresAt
    )
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->clientId = $clientId;
        $this->scopes = $scopes;
        $this->revoked = $revoked;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
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
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }
}
