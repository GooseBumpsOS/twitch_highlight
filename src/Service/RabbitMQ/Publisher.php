<?php

declare(strict_types=1);


namespace App\Service\RabbitMQ;


use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{
    public function publishMessage(array $credentials, array $data): void
    {
        $rabbitConnection = new RabbitMqConnectionService($credentials);
        $channel = $rabbitConnection->openConnection()->getChannel();

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

        $msg = new AMQPMessage(
            json_encode($data, JSON_UNESCAPED_SLASHES),
            ['delivery_mode' => 2] # make message persistent
        );

        $channel->basic_publish($msg, '', QueueName::CHAT_COLLECT_NAME);
        $rabbitConnection->closeConnection();
    }

}