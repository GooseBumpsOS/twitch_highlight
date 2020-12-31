<?php

declare(strict_types=1);


namespace App\Service\TwichCollector;


use App\Service\TwichCollector\Dto\TwichChat;
use App\Service\TwichCollector\StorageProvider\IStorageProvider;
use LogicException;
use Symfony\Component\HttpClient\HttpClient;
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
            $storageProvider->persistChatData($videoId, $this->getMsgFromTwich($videoId));
        }
    }

    /**
     * @param int $videoId
     * @return TwichChat[]
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getMsgFromTwich(int $videoId): array
    {
        $result = [];

        $response = $this->makeGetRequest("https://api.twitch.tv/v5/videos/{$videoId}/comments",
            ["content_offset_seconds" => "0"]);

        foreach ($response["comments"] as $chatMetadata) {
            $twichChat = new TwichChat();

            $twichChat->message = $chatMetadata["message"]["body"];
            $twichChat->offset = $chatMetadata["content_offset_seconds"];
            $result[] = $twichChat;
        }

        while (isset($response["_next"])) {
            $response = $this->makeGetRequest("https://api.twitch.tv/v5/videos/{$videoId}/comments",
                ["cursor" => $response["_next"]]
            );

            foreach ($response["comments"] as $chatMetadata) {
                $twichChat = new TwichChat();

                $twichChat->message = $chatMetadata["message"]["body"];
                $twichChat->offset = $chatMetadata["content_offset_seconds"];
                $result[] = $twichChat;
            }
        }

        return $result;
    }

    /**
     * @param string $url
     * @param array $body
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function makeGetRequest(string $url, array $body = []): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', $url . '?' . http_build_query($body), [
            'headers' => [
                'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
                'client-id' => 'kimne78kx3ncx6brgo4mv6wki5h1ko',
                'timeout' => 2.5
            ]
        ]);

        $result = json_decode($response->getContent(), true);
        if (!is_array($result)) {
            throw new LogicException('Smth wrong with twich chat data parsing, here is response: ' . $response->getContent(),
                500);
        }

        return $result;
    }
}