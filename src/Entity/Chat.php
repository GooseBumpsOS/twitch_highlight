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
     * @ORM\Column(type="float")
     */
    private $timeOffset;

    /**
     * @ORM\ManyToOne(targetEntity=ChatDictionary::class, inversedBy="chats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $videoId;

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
        return $this->timeOffset;
    }

    public function setTimeOffset(float $timeOffset): self
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
}
