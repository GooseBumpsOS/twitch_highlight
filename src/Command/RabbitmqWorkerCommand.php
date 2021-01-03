<?php

namespace App\Command;

use App\Service\RabbitMQ\Worker;
use App\Service\TwichCollector\StorageProvider\DbStorageProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RabbitmqWorkerCommand extends Command
{
    protected static $defaultName = 'rabbitmq:worker';
    /**
     * @var DbStorageProvider
     */
    private $storageProvider;
    /**
     * @var Worker
     */
    private $worker;

    /**
     * RabbitmqWorkerCommand constructor.
     * @param DbStorageProvider $storageProvider
     * @param Worker $worker
     */
    public function __construct(
        DbStorageProvider $storageProvider,
        Worker $worker
    ) {
        parent::__construct();
        $this->storageProvider = $storageProvider;
        $this->worker = $worker;
    }

    protected function configure()
    {
        $this
            ->setDescription('This command call for RabbitMQ worker which collect and processes data from queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('Start worker');

        $this->worker->readMessage($io, $this->storageProvider);

        return Command::SUCCESS;
    }
}
