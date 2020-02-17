<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $admin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="users")
     */
    private $site;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="organizer")
     */
    private $eventOrganized;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", inversedBy="usersList")
     */
    private $eventList;

    public function __construct()
    {
        $this->eventOrganized = new ArrayCollection();
        $this->eventList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEventOrganized(): Collection
    {
        return $this->eventOrganized;
    }

    public function addEventOrganized(Event $eventOrganized): self
    {
        if (!$this->eventOrganized->contains($eventOrganized)) {
            $this->eventOrganized[] = $eventOrganized;
            $eventOrganized->setOrganizer($this);
        }

        return $this;
    }

    public function removeEventOrganized(Event $eventOrganized): self
    {
        if ($this->eventOrganized->contains($eventOrganized)) {
            $this->eventOrganized->removeElement($eventOrganized);
            // set the owning side to null (unless already changed)
            if ($eventOrganized->getOrganizer() === $this) {
                $eventOrganized->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEventList(): Collection
    {
        return $this->eventList;
    }

    public function addEventList(Event $eventList): self
    {
        if (!$this->eventList->contains($eventList)) {
            $this->eventList[] = $eventList;
        }

        return $this;
    }

    public function removeEventList(Event $eventList): self
    {
        if ($this->eventList->contains($eventList)) {
            $this->eventList->removeElement($eventList);
        }

        return $this;
    }
}
