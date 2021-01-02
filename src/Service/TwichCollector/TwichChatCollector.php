<?php

declare(strict_types=1);


namespace App\Service\TwichCollector;


use App\Service\TwichCollector\StorageProvider\IStorageProvider;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TwichChatCollector
{
    /**
     * @param int $videoId
     * @param IStorageProvider $storageProvider
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function collect(int $videoId, IStorageProvider $storageProvider): void
    {
        $chats = $storageProvider->getChatData($videoId);
        if (is_null($chats)) {
            $storageProvider->persistChatData($videoId, (new TwichRequesterService())->getMsgFromTwich($videoId));
        }
    }
}