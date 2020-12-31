<?php

declare(strict_types=1);


namespace App\Service\Bid;


use App\Entity\Bid;
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
     * BidManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param Publisher $publisher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Publisher $publisher
    ) {
        $this->entityManager = $entityManager;
        $this->publisher = $publisher;
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
}