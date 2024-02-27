<?php

namespace App\Service\Image;

use App\Entity\Group\Group;
use App\Entity\Group\GroupImage;
use App\Enum\FilterPrefix;
use App\Enum\FilterType;
use App\Interfaces\ImageStorage;
use App\Repository\Auth\UserRepository;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;

class ImageService
{
    private CacheManager $cacheManager;
    private FilterManager $filterManager;
    private DataManager $dataManager;
    private UserRepository $repository;
    private FilesystemOperator $userFilesystem;
    private FilesystemOperator $imagesFilesystem;
    private FilesystemOperator $groupFilesystem;
    private FilesystemOperator $userThumbnailFilesystem;
    private FilesystemOperator $imageThumbnailFilesystem;
    private FilesystemOperator $groupThumbnailFilesystem;

    public function __construct(
        CacheManager $cacheManager,
        FilterManager $filterManager,
        DataManager $dataManager,
        UserRepository $repository,
        FilesystemOperator $userFilesystem,
        FilesystemOperator $imagesFilesystem,
        FilesystemOperator $groupFilesystem,
        FilesystemOperator $userThumbnailFilesystem,
        FilesystemOperator $imageThumbnailFilesystem,
        FilesystemOperator $groupThumbnailFilesystem
    ) {
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
        $this->dataManager = $dataManager;
        $this->repository = $repository;
        $this->userFilesystem = $userFilesystem;
        $this->imagesFilesystem = $imagesFilesystem;
        $this->groupFilesystem = $groupFilesystem;

        $this->userThumbnailFilesystem = $userThumbnailFilesystem;
        $this->imageThumbnailFilesystem = $imageThumbnailFilesystem;
        $this->groupThumbnailFilesystem = $groupThumbnailFilesystem;
    }

    public function warmupCache(string $image, ImageStorage $imageStorage, FilterPrefix $filterPrefix): void
    {
        $urls = [];
        foreach (FilterType::cases() as $filter) {
            if ($filter === FilterType::NONE) {
                $urls[$filter->value] = $this->getFilesystemByPrefix($filterPrefix)->publicUrl($image);
                continue;
            }
            $urls[$filter->value] = $this->resolveUrl($image, $filterPrefix->value . $filter->value);
        }

        $imageStorage->setImageName($image);
        $imageStorage->setUrls($urls);

        $this->repository->save($imageStorage);
    }


    /**
     * @throws FilesystemException
     */
    public function deleteImage(string $image, ImageStorage $imageStorage, FilterPrefix $filterPrefix): void
    {
        foreach (FilterType::cases() as $filter) {
            if ($filter === FilterType::NONE) {
                $this->getFilesystemByPrefix($filterPrefix)->delete($image);
                continue;
            }
            $this->getThumbnailFilesystemByPrefix($filterPrefix)->delete($image);
        }

        $imageStorage->setImageName(null);
    }

    /**
     * @throws FilesystemException
     */
    public function deleteGroupImages(Group $group): void
    {
        foreach (FilterType::cases() as $filter) {
            if ($filter === FilterType::NONE) {
                $this->imagesFilesystem->deleteDirectory($group->getGroupId());
                continue;
            }
            $this->imageThumbnailFilesystem->deleteDirectory($group->getGroupId());
        }
    }

    private function resolveUrl(string $image, string $filter): string
    {
        if (!$this->cacheManager->isStored($image, $filter)) {
            $this->storeInCache($image, $filter);
        }

        return $this->cacheManager->resolve($image, $filter);
    }

    private function storeInCache(string $image, string $filter): void
    {
        $binary = $this->dataManager->find($filter, $image);

        $filteredBinary = $this->filterManager->applyFilter($binary, $filter);

        $this->cacheManager->store($filteredBinary, $image, $filter);
    }

    private function getFilesystemByPrefix(FilterPrefix $filterPrefix): FilesystemOperator
    {
        return match ($filterPrefix) {
            FilterPrefix::IMAGE => $this->imagesFilesystem,
            FilterPrefix::USER => $this->userFilesystem,
            FilterPrefix::GROUP => $this->groupFilesystem,
        };
    }

    private function getThumbnailFilesystemByPrefix(FilterPrefix $filterPrefix): FilesystemOperator
    {
        return match ($filterPrefix) {
            FilterPrefix::IMAGE => $this->imageThumbnailFilesystem,
            FilterPrefix::USER => $this->userThumbnailFilesystem,
            FilterPrefix::GROUP => $this->groupThumbnailFilesystem,
        };
    }
}
