<?php

declare(strict_types=1);


namespace App\Service\Bid;


use App\Entity\Bid;
use App\Entity\ChatDictionary;
use App\Service\Analytics\ChatAnalytics;
use App\Service\Analytics\Dto\AnalyticsResult;
use App\Service\RabbitMQ\Publisher;
use Doctrine\ORM\EntityManagerInterface;

class BidManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Publisher
     */
    private $publisher;
    /**
     * @var ChatAnalytics
     */
    private $analytic;

    /**
     * BidManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param Publisher $publisher
     * @param ChatAnalytics $analytic
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Publisher $publisher,
        ChatAnalytics $analytic
    ) {
        $this->entityManager = $entityManager;
        $this->publisher = $publisher;
        $this->analytic = $analytic;
    }

    /**
     * @param int $videoId
     * @param float $coeff
     * @param string $keywords
     * @param string $user
     */
    public function sendBid(int $videoId, float $coeff, string $keywords, string $user): void
    {
        $bid = new Bid();

        $bid->setVideoId($videoId);
        $bid->setCoeff($coeff);
        $bid->setKeywords($keywords);
        $bid->setUser($user);

        $this->entityManager->persist($bid);
        $this->entityManager->flush();

        $this->publisher->publishMessage([
            'videoId' => $videoId,
            'user' => $user
        ]);
    }

    /**
     * @param int $videoId
     * @param string $user
     * @return null|AnalyticsResult
     */
    public function getAnalysisResult(int $videoId, string $user): ?AnalyticsResult
    {
        /** @var Bid $result */
        $result = $this->entityManager->getRepository(Bid::class)->findOneBy([
            'videoId' => $videoId,
            'user' => $user
        ], ['id' => 'DESC']);

        if (!is_null($result)) {
            /** @var ChatDictionary $chat */
            $chat = $this->entityManager->getRepository(ChatDictionary::class)->findOneBy([
                'videoId' => $videoId
            ]);

            $result = $this->analytic->makeAnalytics($result->getCoeff(), $result->getKeywords(), $chat->getChats()->getValues());
        }

        return $result;
    }
}