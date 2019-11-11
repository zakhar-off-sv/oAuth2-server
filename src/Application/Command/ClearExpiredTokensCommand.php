<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Repository\AccessTokenRepositoryInterface;
use App\Domain\Repository\RefreshTokenRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ClearExpiredTokensCommand extends Command
{
    protected static $defaultName = 'oauth2:clear-expired-tokens';

    /**
     * @var AccessTokenRepositoryInterface
     */
    private $accessTokenRepository;

    /**
     * @var RefreshTokenRepositoryInterface
     */
    private $refreshTokenRepository;

    public function __construct(
        AccessTokenRepositoryInterface $accessTokenRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    )
    {
        parent::__construct();

        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Clears all expired access and/or refresh tokens')
            ->addOption(
                'access-tokens-only',
                'a',
                InputOption::VALUE_NONE,
                'Clear only access tokens.'
            )
            ->addOption(
                'refresh-tokens-only',
                'r',
                InputOption::VALUE_NONE,
                'Clear only refresh tokens.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $clearExpiredAccessTokens = !$input->getOption('refresh-tokens-only');
        $clearExpiredRefreshTokens = !$input->getOption('access-tokens-only');
        if (!$clearExpiredAccessTokens && !$clearExpiredRefreshTokens) {
            $io->error('Please choose only one of the following options: "access-tokens-only", "refresh-tokens-only".');
            return 1;
        }

        if (true === $clearExpiredAccessTokens) {
            $numOfClearedAccessTokens = $this->accessTokenRepository->clearExpired();
            $io->success(sprintf(
                'Cleared %d expired access token%s.',
                $numOfClearedAccessTokens,
                1 === $numOfClearedAccessTokens ? '' : 's'
            ));
        }

        if (true === $clearExpiredRefreshTokens) {
            $numOfClearedRefreshTokens = $this->refreshTokenRepository->clearExpired();
            $io->success(sprintf(
                'Cleared %d expired refresh token%s.',
                $numOfClearedRefreshTokens,
                1 === $numOfClearedRefreshTokens ? '' : 's'
            ));
        }

        return 0;
    }
}
