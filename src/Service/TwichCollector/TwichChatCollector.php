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
        #TODO add check for data already exist
        $storageProvider->persistChatData($videoId, $this->getMsgFromTwich($videoId));
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

        $response = $this->makeRequest("https://api.twitch.tv/v5/videos/{$videoId}/comments", 'GET',
            ["content_offset_seconds" => "0"]);

        foreach ($response["comments"] as $chatMetadata) {
            $twichChat = new TwichChat();

            $twichChat->message = $chatMetadata["message"]["body"];
            $twichChat->offset = $chatMetadata["content_offset_seconds"];
            $result[] = $twichChat;
        }

        while (isset($response["_next"])) {
            $response = $response = $this->makeRequest("https://api.twitch.tv/v5/videos/{$videoId}/comments", 'GET',
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
     * @param string $httpMethod
     * @param array $body
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function makeRequest(string $url, string $httpMethod, array $body = []): array
    {
        $client = HttpClient::create();
        $response = $client->request($httpMethod, $url, [
            'body' => $body,
            'headers' => [
                'client-id' => 'kimne78kx3ncx6brgo4mv6wki5h1ko',
                'content-encoding' => 'UTF-8'
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