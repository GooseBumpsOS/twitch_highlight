<?php


namespace App\Service\TwichCollector\StorageProvider;


use App\Service\TwichCollector\Dto\TwichChat;

interface IStorageProvider
{
    /**
     * @param TwichChat[] $twichChat
     */
    public function persistChatData(array $twichChat): void;
}