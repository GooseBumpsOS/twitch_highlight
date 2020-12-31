<?php

namespace App\Entity;

use App\Repository\ChatDictionaryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatDictionaryRepository::class)
 */
class ChatDictionary
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $videoId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\OneToMany(targetEntity=Chat::class, mappedBy="videoId", orphanRemoval=true)
     */
    private $chats;

    public function __construct()
    {
        $this->chats = new ArrayCollection();
    }

    public function getVideoId(): ?int
    {
        return $this->videoId;
    }

    public function setVideoId(int $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return Collection|Chat[]
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->setVideoId($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->removeElement($chat)) {
            // set the owning side to null (unless already changed)
            if ($chat->getVideoId() === $this) {
                $chat->setVideoId(null);
            }
        }

        return $this;
    }
}
