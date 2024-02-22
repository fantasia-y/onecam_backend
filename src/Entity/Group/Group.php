<?php

namespace App\Entity\Group;

use App\Entity\Auth\User;
use App\Interfaces\ImageStorage;
use App\Repository\Group\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[Orm\Table('group_sessions')]
class Group extends ImageStorage
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $groupId = null;

    #[ORM\ManyToOne(User::class)]
    private ?User $owner = null;

    private int $imageCount = 0;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'groups_users')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private Collection $participants;

    #[Serializer\Exclude]
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: GroupImage::class, cascade: ['persist'])]
    private Collection $images;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Group
    {
        $this->name = $name;
        return $this;
    }

    public function getGroupId(): ?Uuid
    {
        return $this->groupId;
    }

    public function setGroupId(?Uuid $groupId): Group
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): Group
    {
        $this->owner = $owner;
        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function setParticipants(Collection $participants): Group
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

    public function removeParticipant(UserInterface $user): void
    {
        if ($this->participants->contains($user)) {
            $this->participants->removeElement($user);
        }
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function setImages(Collection $images): Group
    {
        $this->images = $images;
        return $this;
    }

    public function addImage(GroupImage $image): void
    {
        if (!$this->images->contains($image)) {
            $image->setGroup($this);
            $this->images->add($image);
        }
    }

    public function removeImage(GroupImage $image): void
    {
        if ($this->images->contains($image)) {
            $image->setGroup(null);
            $this->images->removeElement($image);
        }
    }

    #[Serializer\VirtualProperty]
    public function getImageCount(): int
    {
        return $this->imageCount;
    }

    public function setImageCount(int $imageCount): Group
    {
        $this->imageCount = $imageCount;
        return $this;
    }

    public function isOwner(UserInterface $user): bool
    {
        return $this->getOwner()->getId() === $user->getId();
    }

    public function isMember(UserInterface $user): bool
    {
        return $this->isOwner($user) || $this->getParticipants()->contains($user);
    }

    public function getRecipients(): array
    {
        return [$this->getOwner(), ...$this->getParticipants()->toArray()];
    }
}
