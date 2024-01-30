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
    private CacheManager $cacheManager;
    private FilterManager $filterManager;
    private DataManager $dataManager;
    private FilesystemOperator $imagesFilesystem;

    public function __construct(
        GroupRepository $groupRepository,
        GroupImageRepository $groupImageRepository,
        CacheManager $cacheManager,
        FilterManager $filterManager,
        DataManager $dataManager,
        FilesystemOperator $imagesFilesystem
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupImageRepository = $groupImageRepository;
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
        $this->dataManager = $dataManager;
        $this->imagesFilesystem = $imagesFilesystem;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getGroupImages(string $id, int $page): array
    {
        $group = $this->groupRepository->findByGroupId($id);

        return $this->groupImageRepository->getGroupImages($group, $page);
    }

    /**
     * @throws EntityNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function addImage(string $groupId, string $name): GroupImage
    {
        $this->groupRepository->beginTransaction();

        try {
            $group = $this->groupRepository->findByGroupId($groupId);

            $image = new GroupImage();
            $image->setName($name);

            if ($this->groupImageRepository->existsInGroup($image, $group)) {
                throw new InvalidArgumentException('The group already contains this image');
            }

            $group->addImage($image);

            $this->groupRepository->save($group);

            $this->warmupCache($image);

            $this->groupRepository->commit();

            return $image;
        } catch (\Exception $exception) {
            // log exception
            $this->groupRepository->rollback();
            throw $exception;
        }
    }

    /**
     * @throws EntityNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws FilesystemException
     */
    public function deleteImage(string $groupId, string $id): void
    {
        $this->groupRepository->beginTransaction();

        try {
            $group = $this->groupRepository->findByGroupId($groupId);
            /** @var GroupImage $image */
            $image = $this->groupImageRepository->find($id);

            if (!$this->groupImageRepository->existsInGroup($image, $group)) {
                throw new InvalidArgumentException('The image is not part of this group');
            }

            foreach (FilterType::cases() as $filter) {
                if ($filter === FilterType::NONE) {
                    $this->imagesFilesystem->delete($image->getPath());
                    continue;
                }
                $this->cacheManager->remove($image->getPath(), $filter->value);
            }

            $group->removeImage($image);
            $this->groupImageRepository->remove($image);

            $this->groupRepository->commit();
        } catch (\Exception $exception) {
            $this->groupRepository->rollback();
            throw $exception;
        }
    }

    private function warmupCache(GroupImage $image): void
    {
        $urls = [];
        foreach (FilterType::cases() as $filter) {
            if ($filter === FilterType::NONE) {
                $urls[$filter->value] = $this->imagesFilesystem->publicUrl($image->getPath());
                continue;
            }
            $urls[$filter->value] = $this->resolveUrl($image, $filter->value);
        }
        $image->setUrls($urls);
        $this->groupImageRepository->save($image);
    }

    private function resolveUrl(GroupImage $image, ?string $filter): string
    {
        $path = $image->getPath();

        if (!$this->cacheManager->isStored($path, $filter)) {
            $this->storeInCache($image, $filter);
        }

        return $this->cacheManager->resolve($path, $filter);
    }

    private function storeInCache(GroupImage $image, string $filter): void
    {
        $path = $image->getPath();

        $binary = $this->dataManager->find($filter, $path);

        $filteredBinary = $this->filterManager->applyFilter($binary, $filter);

        $this->cacheManager->store($filteredBinary, $path, $filter);
    }
}