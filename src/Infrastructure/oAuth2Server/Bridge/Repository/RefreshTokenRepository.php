<?php

namespace App\Infrastructure\oAuth2Server\Bridge\Repository;

use App\Domain\Model\RefreshToken as AppRefreshToken;
use App\Domain\Repository\RefreshTokenRepositoryInterface as AppRefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

final class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @var AppRefreshTokenRepositoryInterface
     */
    private $appRefreshTokenRepository;

    /**
     * RefreshTokenRepository constructor.
     * @param AppRefreshTokenRepositoryInterface $appRefreshTokenRepository
     */
    public function __construct(AppRefreshTokenRepositoryInterface $appRefreshTokenRepository)
    {
        $this->appRefreshTokenRepository = $appRefreshTokenRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshToken();
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $refreshTokenPersistEntity = new AppRefreshToken(
            $refreshTokenEntity->getIdentifier(),
            $refreshTokenEntity->getAccessToken()->getIdentifier(),
            $refreshTokenEntity->getExpiryDateTime()
        );
        $this->appRefreshTokenRepository->save($refreshTokenPersistEntity);
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId): void
    {
        $appRefreshToken = $this->appRefreshTokenRepository->find($tokenId);
        if ($appRefreshToken === null) {
            return;
        }
        $appRefreshToken->revoke();
        $this->appRefreshTokenRepository->save($appRefreshToken);
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        $appRefreshToken = $this->appRefreshTokenRepository->find($tokenId);
        if ($appRefreshToken === null) {
            return true;
        }
        return $appRefreshToken->isRevoked();
    }
}
