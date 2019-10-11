<?php

declare(strict_types=1);

namespace App\Domain\Model;

class RefreshToken
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $accessTokenId;

    /**
     * @var bool
     */
    private $revoked = false;

    /**
     * @var \DateTimeInterface
     */
    private $expiresAt;

    /**
     * RefreshToken constructor.
     * @param string $id
     * @param string $accessTokenId
     * @param \DateTimeInterface $expiresAt
     */
    public function __construct(string $id, string $accessTokenId, \DateTimeInterface $expiresAt)
    {
        $this->id = $id;
        $this->accessTokenId = $accessTokenId;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getAccessTokenId(): string
    {
        return $this->accessTokenId;
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
