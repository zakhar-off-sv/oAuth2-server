<?php

namespace App\Infrastructure\oAuth2Server\Bridge\Repository;

use App\Domain\Model\Client as AppClient;
use App\Domain\Repository\ClientRepositoryInterface as AppClientRepositoryInterface;
use App\Infrastructure\oAuth2Server\Bridge\Entity\Client;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

final class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @var AppClientRepositoryInterface
     */
    private $appClientRepository;

    /**
     * ClientRepository constructor.
     * @param AppClientRepositoryInterface $appClientRepository
     */
    public function __construct(AppClientRepositoryInterface $appClientRepository)
    {
        $this->appClientRepository = $appClientRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $appClient = $this->appClientRepository->findActive($clientIdentifier);
        if ($appClient === null) {
            return null;
        }
        $oauthClient = new Client(
            $clientIdentifier,
            $appClient->getName(),
            $appClient->getRedirect(),
            $appClient->isConfidential()
        );
        return $oauthClient;
    }

    /**
     * {@inheritdoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $appClient = $this->appClientRepository->findActive($clientIdentifier);
        if ($appClient === null) {
            return false;
        }

        if (!$this->isGrantSupported($appClient, $grantType)) {
            return false;
        }

        if ($appClient->isConfidential()
            && !hash_equals($appClient->getSecret(), (string)$clientSecret)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param AppClient $client
     * @param string|null $grantType
     * @return bool
     */
    private function isGrantSupported(AppClient $client, ?string $grantType): bool
    {
        if ($grantType !== null
            && $client->getGrants()
            && !\in_array($grantType, $client->getGrants())
        ) {
            return false;
        }

        return true;
    }
}
