<?php

namespace App\Command;

use App\Service\RabbitMQ\Worker;
use App\Service\TwichCollector\StorageProvider\DbStorageProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RabbitmqWorkerCommand extends Command
{
    protected static $defaultName = 'rabbitmq:worker';
    /**
     * @var DbStorageProvider
     */
    private $storageProvider;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        string $name = null,
        DbStorageProvider $storageProvider,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct($name);
        $this->storageProvider = $storageProvider;
        $this->parameterBag = $parameterBag;
    }

    protected function configure()
    {
        $this
            ->setDescription('This command call for RabbitMQ worker');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('Start worker');

        $rabbitWorker = new Worker();
        $rabbitWorker->readMessage($this->parameterBag->get('rabbit_mq_credentials'), $io, $this->storageProvider);

        return Command::SUCCESS;
    }
}
