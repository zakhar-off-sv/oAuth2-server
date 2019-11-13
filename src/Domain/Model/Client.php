<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Ramsey\Uuid\Uuid;
use RuntimeException;

class Client
{
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
                    sprintf('The \'%s\' string is not a valid URI.', $item)
                );
            }
        }
        $this->redirect = $redirect;
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
