<?php

declare(strict_types=1);


namespace App\Service\TwichCollector\StorageProvider;


use App\Entity\Chat;
use App\Entity\ChatDictionary;
use App\Service\TwichCollector\Dto\TwichChat;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DbStorageProvider implements IStorageProvider
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DbStorageProvider constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $videoId
     * @param TwichChat[] $twichChat
     */
    public function persistChatData(int $videoId, array $twichChat): void
    {
        $chatDic = new ChatDictionary();
        $chatDic->setTime(new DateTime());
        $chatDic->setVideoId($videoId);
        $this->em->persist($chatDic);

        foreach ($twichChat as $msg) {
            $chat = new Chat();
            $chat->setVideoId($chatDic);
            $chat->setMsg($msg->message);
            $chat->setTimeOffset((int)$msg->offset);
            $this->em->persist($chat);
        }

        $this->em->flush();
    }

    /**
     * @param int $videoId
     * @return null|ChatDictionary
     */
    public function getChatData(int $videoId): ?ChatDictionary
    {
        return $this->em->getRepository(ChatDictionary::class)->findOneBy([
            'videoId' => $videoId
        ]);
    }
}