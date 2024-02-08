<?php

namespace App\Interfaces;

use Doctrine\ORM\Mapping as ORM;

abstract class ImageStorage
{
    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: 'json', nullable: true)]
    protected ?array $urls = null;

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;
        return $this;
    }

    public function getUrls(): ?array
    {
        return $this->urls;
    }

    public function setUrls(?array $urls): self
    {
        $this->urls = $urls;
        return $this;
    }
}