<?php
declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Model\Client;
use App\Domain\Repository\ClientRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CreateClientCommand extends Command
{
    protected static $defaultName = 'oauth2:create-client';

    /**
     * @var ClientRepositoryInterface
     */
    private $clientRepository;

    /**
     * CreateClientCommand constructor.
     * @param ClientRepositoryInterface $clientRepository
     */
    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        parent::__construct();

        $this->clientRepository = $clientRepository;
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new oAuth2 client')
            ->addOption(
                'redirect',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets redirect uri for client. Use this option multiple times to set multiple redirect URIs.',
                []
            )
            ->addOption(
                'grant',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets allowed grant type for client. Use this option multiple times to set multiple grant types.',
                []
            )
            ->addOption(
                'confidential',
                null,
                InputOption::VALUE_NONE,
                'The secret must be validated.',
                null
            )
            ->addOption(
                'inactive',
                null,
                InputOption::VALUE_NONE,
                'Set status inactive',
                null
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The client name.',
                null
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = $this->buildClientFromInput($input);
        $this->clientRepository->save($client);

        $io->success('New oAuth2 client created successfully.');
        $headers = ['Identifier', 'Secret'];
        $rows = [
            [$client->getId()->toString(), $client->getSecret()],
        ];
        $io->table($headers, $rows);

        return 0;
    }

    /**
     * @param InputInterface $input
     * @return Client
     * @throws \Exception
     */
    private function buildClientFromInput(InputInterface $input): Client
    {
        $client = Client::create($input->getArgument('name'));
        $client->setSecret(hash('sha512', random_bytes(32)));
        $client->setRedirect(...$input->getOption('redirect'));
        $client->setGrants(...$input->getOption('grant'));
        $client->setConfidential((bool)!$input->getOption('confidential'));
        $client->setActive((bool)!$input->getOption('inactive'));
        return $client;
    }
}
