<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatRepository::class)
 */
class Chat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=511)
     */
    private $msg;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeOffset;

    /**
     * @ORM\ManyToOne(targetEntity=ChatDictionary::class, inversedBy="chats")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="video_id")
     */
    private $videoId;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $emoticon = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMsg(): ?string
    {
        return $this->msg;
    }

    public function setMsg(string $msg): self
    {
        $this->msg = $msg;

        return $this;
    }

    public function getTimeOffset(): ?int
    {
        return (int)$this->timeOffset;
    }

    public function setTimeOffset(int $timeOffset): self
    {
        $this->timeOffset = $timeOffset;

        return $this;
    }

    public function getVideoId(): ?ChatDictionary
    {
        return $this->videoId;
    }

    public function setVideoId(?ChatDictionary $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }

    public function getEmoticon(): ?array
    {
        return $this->emoticon;
    }

    public function setEmoticon(?array $emoticon): self
    {
        $this->emoticon = $emoticon;

        return $this;
    }
}
