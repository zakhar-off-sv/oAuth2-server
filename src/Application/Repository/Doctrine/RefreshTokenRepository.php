<?php

namespace App\Application\Repository\Doctrine;

use App\Domain\Model\RefreshToken;
use App\Domain\Repository\RefreshTokenRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

final class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    private const ENTITY = RefreshToken::class;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $objectRepository;

    /**
     * UserRepository constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $this->entityManager->getRepository(self::ENTITY);
    }

    public function find(string $refreshTokenId): ?RefreshToken
    {
        return $this->entityManager->find(self::ENTITY, $refreshTokenId);
    }

    public function save(RefreshToken $refreshToken): void
    {
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();
    }
}
