<?php

namespace App\Entity\Group;


use App\Interfaces\ImageStorage;
use App\Repository\Group\GroupImageRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: GroupImageRepository::class)]
#[Orm\Table('group_images')]
class GroupImage extends ImageStorage
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Serializer\Exclude]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    private ?Group $group = null;

    public function getId(): ?int
    {
        return $this->id;
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
        return sprintf('%s/%s', $this->getGroup()->getGroupId(), $this->getImageName());
    }
}