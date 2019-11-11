<?php

declare(strict_types=1);

namespace App\Application\Repository\Doctrine;

use App\Domain\Model\AuthorizationCode;
use App\Domain\Repository\AuthorizationCodeRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

final class AuthorizationCodeRepository implements AuthorizationCodeRepositoryInterface
{
    private const ENTITY = AuthorizationCode::class;

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

    public function find(string $authCodeId): ?AuthorizationCode
    {
        return $this->entityManager->find(self::ENTITY, $authCodeId);
    }

    public function save(AuthorizationCode $authCode): void
    {
        $this->entityManager->persist($authCode);
        $this->entityManager->flush();
    }
}
