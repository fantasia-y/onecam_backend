<?php

namespace App\Service\Group;

use App\Entity\Group\GroupImage;
use App\Enum\FilterType;
use App\Repository\Group\GroupImageRepository;
use App\Repository\Group\GroupRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use http\Exception\InvalidArgumentException;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;

class GroupImageService
{
    private GroupRepository $groupRepository;
    private GroupImageRepository $groupImageRepository;

    public function __construct(
        GroupRepository $groupRepository,
        GroupImageRepository $groupImageRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupImageRepository = $groupImageRepository;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getGroupImages(string $id, int $page): array
    {
        $group = $this->groupRepository->findByGroupId($id);

        return $this->groupImageRepository->getGroupImages($group, $page);
    }
}