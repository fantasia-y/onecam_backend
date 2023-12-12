<?php

namespace App\Entity\Session;

use App\Entity\Auth\User;
use App\Repository\Session\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column()]
    private ?string $name = null;

    #[ORM\Column()]
    private ?\DateTime $validUntil = null;

    #[ORM\Column()]
    private ?int $maxParticipants = null;

    #[ORM\Column()]
    private ?bool $allowGuests = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $sessionId = null;

    #[ORM\ManyToOne(User::class)]
    private ?User $owner = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'sessions_users')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Session
    {
        $this->name = $name;
        return $this;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    public function setValidUntil(?\DateTime $validUntil): Session
    {
        $this->validUntil = $validUntil;
        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(?int $maxParticipants): Session
    {
        $this->maxParticipants = $maxParticipants;
        return $this;
    }

    public function getAllowGuests(): ?bool
    {
        return $this->allowGuests;
    }

    public function setAllowGuests(?bool $allowGuests): Session
    {
        $this->allowGuests = $allowGuests;
        return $this;
    }

    public function getSessionId(): ?Uuid
    {
        return $this->sessionId;
    }

    public function setSessionId(?Uuid $sessionId): Session
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): Session
    {
        $this->owner = $owner;
        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function setParticipants(Collection $participants): Session
    {
        $this->participants = $participants;
        return $this;
    }

    public function addParticipant(UserInterface $user): void
    {
        if (!$this->participants->contains($user)) {
            $this->participants->add($user);
        }
    }
}