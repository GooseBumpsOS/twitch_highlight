<?php

declare(strict_types=1);


namespace App\Service\RabbitMQ;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMqConnectionService
{
    private $credentials;
    private $channel;
    private $connection;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    public function openConnection(): self
    {
        $credentials = $this->credentials;
        $connection = new AMQPStreamConnection(
            $credentials['host'],
            $credentials['port'],
            $credentials['user'],
            $credentials['pass']
        );

        $channel = $connection->channel();

        # Create the queue if it does not already exist.
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

        $this->channel = $channel;
        $this->connection = $connection;

        return $this;
    }

    public function closeConnection(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    /**
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }
}