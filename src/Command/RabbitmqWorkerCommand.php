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

    public function __construct(string $name = null, DbStorageProvider $storageProvider)
    {
        parent::__construct($name);
        $this->storageProvider = $storageProvider;
    }

    protected function configure()
    {
        $this
            ->setDescription('This command call for RabbitMQ worker');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $rabbitWorker = new Worker();
        $rabbitWorker->readMessage($io, $this->storageProvider);

        return Command::SUCCESS;
    }
}
