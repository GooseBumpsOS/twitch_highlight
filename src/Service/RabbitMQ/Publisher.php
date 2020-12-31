<?php

declare(strict_types=1);


namespace App\Service\RabbitMQ;


use PhpAmqpLib\Connection\AMQPStreamConnection;

class Publisher
{
    public function publishMessage(array $credentials, array $data): void
    {
        $rabbitConnection = new RabbitMqConnectionService($credentials);
        $channel = $rabbitConnection->openConnection()->getChannel();


    }

}