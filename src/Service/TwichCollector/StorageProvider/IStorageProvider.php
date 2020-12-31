<?php


namespace App\Service\TwichCollector\StorageProvider;


use App\Service\TwichCollector\Dto\TwichChat;

interface IStorageProvider
{
    /**
     * @param int videoId
     * @param TwichChat[] $twichChat
     */
    public function persistChatData(int $videoId, array $twichChat): void;

    public function getChatData(int $videoId): ?array;
}