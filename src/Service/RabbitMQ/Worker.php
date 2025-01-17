<?php

declare(strict_types=1);


namespace App\Service\RabbitMQ;


use App\Service\TwichCollector\StorageProvider\IStorageProvider;
use App\Service\TwichCollector\TwichChatCollector;
use DateTime;
use ErrorException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Worker
{
    /**
     * @var array
     */
    private $credentials;

    /**
     * Worker constructor.
     * @param array $credentials
     */
    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @param OutputInterface $output
     * @param IStorageProvider $storageProvider
     * @throws ErrorException
     */
    public function readMessage(OutputInterface $output, IStorageProvider $storageProvider): void
    {
        $rabbitConnection = new RabbitMqConnectionService($this->credentials);
        $channel = $rabbitConnection->openConnection()->getChannel();

        $callback = function ($msg) use ($output, $storageProvider) {
            $job = json_decode($msg->body, $assocForm = true);
            $output->writeln('Get new work at ' . (new DateTime())->format('d/m/Y H:i:s'));
            $output->writeln("Work info: videoId is {$job['videoId']}, from user {$job['user']}");

            $twichChatCollector = new TwichChatCollector();

            try {
                $twichChatCollector->collect($job['videoId'], $storageProvider);
            } catch (HttpException $exception) {
                if ($exception->getStatusCode() === 404) {
                    $output->writeln('Can\'t get this video');
                    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                }
            }

            $output->writeln('End new work at ' . (new DateTime())->format('d/m/Y H:i:s'));
            $output->writeln('-----------');

            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);

        $channel->basic_consume(
            $queue = QueueName::CHAT_COLLECT_NAME,
            $consumer_tag = '',
            $no_local = false,
            $no_ack = false,
            $exclusive = false,
            $nowait = false,
            $callback
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $rabbitConnection->closeConnection();
    }

}