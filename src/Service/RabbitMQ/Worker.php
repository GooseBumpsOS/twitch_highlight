<?php

declare(strict_types=1);


namespace App\Service\RabbitMQ;


use App\Service\TwichCollector\StorageProvider\IStorageProvider;
use App\Service\TwichCollector\TwichChatCollector;
use DateTime;
use ErrorException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Console\Output\OutputInterface;

class Worker
{
    /**
     * @param array $credentials
     * @param OutputInterface $output
     * @param IStorageProvider $storageProvider
     * @throws ErrorException
     */
    public function readMessage(array $credentials, OutputInterface $output, IStorageProvider $storageProvider): void
    {
        $connection = new AMQPStreamConnection(
            $credentials['host'],
            $credentials['port'],
            $credentials['user'],
            $credentials['pass']
        );

        $channel = $connection->channel();

# Create the queue if it doesnt already exist.
        $channel->queue_declare(
            $queue = QueueName::CHAT_COLLECT_NAME,
            $passive = false,
            $durable = true,
            $exclusive = false,
            $auto_delete = false,
            $nowait = false,
            $arguments = null,
            $ticket = null
        );


        $callback = function ($msg) use ($output, $storageProvider) {
            $job = json_decode($msg->body, $assocForm = true);
            $output->writeln('Get new work at ' . (new DateTime())->format('d/m/Y H:i:s'));
            $output->writeln("Work info: videoId is {$job['videoId']}, from user {$job['user']}");

            $twichChatCollector = new TwichChatCollector();
            $twichChatCollector->collect($job['videoId'], $storageProvider);

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

        $channel->close();
        $connection->close();
    }

}