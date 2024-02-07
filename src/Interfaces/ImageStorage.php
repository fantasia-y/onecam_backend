<?php

namespace App\Interfaces;

use Doctrine\ORM\Mapping as ORM;

abstract class ImageStorage
{
    #[ORM\Column(type: 'json', nullable: true)]
    protected ?array $urls = null;

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