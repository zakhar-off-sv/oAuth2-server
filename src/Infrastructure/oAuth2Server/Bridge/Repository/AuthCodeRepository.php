<?php

namespace App\Infrastructure\oAuth2Server\Bridge\Repository;

use App\Domain\Model\AuthorizationCode as AppAuthorizationCode;
use App\Domain\Repository\AuthorizationCodeRepositoryInterface as AppAuthorizationCodeRepositoryInterface;
use App\Infrastructure\oAuth2Server\Bridge\Entity\AuthCode;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

final class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * @var AppAuthorizationCodeRepositoryInterface
     */
    private $appAuthorizationCodeRepository;

    /**
     * AuthCodeRepository constructor.
     * @param AppAuthorizationCodeRepositoryInterface $appAuthorizationCodeRepository
     */
    public function __construct(AppAuthorizationCodeRepositoryInterface $appAuthorizationCodeRepository)
    {
        $this->appAuthorizationCodeRepository = $appAuthorizationCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCode();
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAuthCode(
        AuthCodeEntityInterface $authCodeEntity
    ): void
    {
        $authCodePersistEntity = new AppAuthorizationCode(
            $authCodeEntity->getIdentifier(),
            $authCodeEntity->getUserIdentifier(),
            $authCodeEntity->getClient()->getIdentifier(),
            $this->scopesToArray($authCodeEntity->getScopes()),
            false,
            $authCodeEntity->getExpiryDateTime()
        );
        $this->appAuthorizationCodeRepository->save($authCodePersistEntity);
    }

    private function scopesToArray(array $scopes): array
    {
        return array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $scopes);
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId): void
    {
        $appAuthorizationCode = $this->appAuthorizationCodeRepository->find($codeId);
        if ($appAuthorizationCode === null) {
            return;
        }
        $appAuthorizationCode->revoke();
        $this->appAuthorizationCodeRepository->save($appAuthorizationCode);
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        $appAuthorizationCode = $this->appAuthorizationCodeRepository->find($codeId);
        if ($appAuthorizationCode === null) {
            return true;
        }
        return $appAuthorizationCode->isRevoked();
    }
}
