<?php

namespace App\Service\Group;

use App\Entity\Group\GroupImage;
use App\Repository\Group\GroupImageRepository;
use App\Repository\Group\GroupRepository;
use Doctrine\ORM\EntityNotFoundException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Service\FilterService;

class GroupImageService
{
    private GroupRepository $groupRepository;
    private GroupImageRepository $groupImageRepository;
    private CacheManager $cacheManager;
    private FilterManager $filterManager;
    private DataManager $dataManager;

    public function __construct(
        GroupRepository $groupRepository,
        GroupImageRepository $groupImageRepository,
        CacheManager $cacheManager,
        FilterManager $filterManager,
        DataManager $dataManager
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupImageRepository = $groupImageRepository;
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
        $this->dataManager = $dataManager;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getGroupImages(string $id, int $page): array
    {
        $group = $this->groupRepository->findByGroupId($id);

        return $this->groupImageRepository->getGroupImages($group, $page);
    }

    public function resolveUrl(string $image): string
    {
        $image = $this->groupImageRepository->find($image);

        $path = $image->getPath();
        $filter = 'image_thumbnail';

        if (!$this->cacheManager->isStored($path, $filter)) {
            $this->storeInCache($image);
        }

        return $this->cacheManager->resolve($path, $filter);
    }

    private function storeInCache(GroupImage $image): void
    {
        $path = $image->getPath();
        $filter = 'image_thumbnail';

        $binary = $this->dataManager->find($filter, $path);

        $filteredBinary = $this->filterManager->applyFilter($binary, $filter);

        $this->cacheManager->store($filteredBinary, $path, $filter);
    }
}