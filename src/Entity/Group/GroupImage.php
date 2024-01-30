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

    #[ORM\Column(type: 'json')]
    private ?array $urls;

    #[Serializer\Exclude]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    private ?Group $group = null;

    public function __construct()
    {
        $this->urls = [];
    }

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

    public function getUrls(): array
    {
        return $this->urls;
    }

    public function setUrls(array $urls): GroupImage
    {
        $this->urls = $urls;
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

    public function getPath(): string
    {
        return sprintf('%s/%s', $this->getGroup()->getGroupId(), $this->getName());
    }
}