<?php

namespace App\Infrastructure\oAuth2Server\Bridge\Repository;

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
        // Todo
        // return hash_equals($appClient->getSecret(), (string)$clientSecret);
        return true;
    }
}
