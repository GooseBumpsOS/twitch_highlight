<?php

declare(strict_types=1);


namespace App\Service\TwichCollector;


use App\Service\TwichCollector\Dto\TwichChat;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TwichRequesterService
{

    /**
     * @param int $videoId
     * @return bool
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function isVideoExist(int $videoId): bool
    {
        $isExist = true;
        try {
            $this->makeGetRequest("https://api.twitch.tv/v5/videos/{$videoId}/");
        } catch (HttpException $e) {
            if ($e->getStatusCode() === 404) {
                $isExist = false;
            }
        }

        return $isExist;
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

        try {
            $content = $response->getContent();
        } catch (ClientExceptionInterface $e) {
            throw new HttpException(404, 'Can\'t get this video. Link: ' . $url . '?' . http_build_query($body));
        }

        $result = json_decode($content, true);
        if (!is_array($result)) {
            throw new JsonException('Smth wrong with twich chat data parsing, here is response: ' . $response->getContent(),
                500);
        }

        return $result;
    }

    /**
     * @param int $videoId
     * @return TwichChat[]
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getMsgFromTwitch(int $videoId): array
    {
        $result = [];

        $response = $this->makeGetRequest("https://api.twitch.tv/v5/videos/{$videoId}/comments",
            ["content_offset_seconds" => "0"]);

        foreach ($response["comments"] as $chatMetadata) {
            $result[] = $this->deserialize($chatMetadata);
        }

        while (isset($response["_next"])) {
            $response = $this->makeGetRequest("https://api.twitch.tv/v5/videos/{$videoId}/comments",
                ["cursor" => $response["_next"]]
            );

            foreach ($response["comments"] as $chatMetadata) {
                $result[] = $this->deserialize($chatMetadata);
            }
        }

        return $result;
    }

    /**
     * @param array $chatMetadata
     * @return TwichChat
     */
    private function deserialize(array $chatMetadata): TwichChat
    {
        $twitchChat = new TwichChat();

        $twitchChat->message = $chatMetadata["message"]["body"];
        $twitchChat->offset = $chatMetadata["content_offset_seconds"];
        if (isset($chatMetadata["message"]["emoticons"])) {
            $twitchChat->emoticon = $chatMetadata["message"]["emoticons"];
        }

        return $twitchChat;
    }
}