<?php

namespace App\Entity\Group;


use App\Repository\Group\GroupImageRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\VirtualProperty;

#[ORM\Entity(repositoryClass: GroupImageRepository::class)]
#[Orm\Table('group_images')]
class GroupImage
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    #[Serializer\Exclude]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    private ?Group $group = null;

    private ?string $url = null;

    private ?string $thumbnail = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): GroupImage
    {
        $this->name = $name;
        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): GroupImage
    {
        $this->group = $group;
        return $this;
    }

    #[VirtualProperty]
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): GroupImage
    {
        $this->url = $url;
        return $this;
    }

    #[VirtualProperty]
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): GroupImage
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    public function getPath(): string
    {
        return sprintf('%s/%s', $this->getGroup()->getGroupId(), $this->getName());
    }
}