<?php

namespace App\Service\Group;

use App\Entity\Auth\User;
use App\Entity\Group\Group;
use App\Entity\Group\GroupImage;
use App\Enum\FilterPrefix;
use App\Repository\Auth\UserRepository;
use App\Repository\Group\GroupImageRepository;
use App\Repository\Group\GroupRepository;
use App\Service\Image\ImageService;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use http\Exception\InvalidArgumentException;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Uid\Uuid;

class GroupService
{
    private Security $security;
    private GroupRepository $groupRepository;
    private UserRepository $userRepository;
    private GroupImageRepository $groupImageRepository;
    private ImageService $imageService;

    public function __construct(
        Security $security,
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        GroupImageRepository $groupImageRepository,
        ImageService $imageService
    ) {
        $this->security = $security;
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
        $this->groupImageRepository = $groupImageRepository;
        $this->imageService = $imageService;
    }

    public function createGroup(): Group
    {
        $group = new Group();

        $user = $this->security->getUser();
        $uuid = Uuid::v4();

        $group
            ->setOwner($user)
            ->setGroupId($uuid);

        return $group;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function joinGroup(string $id): void
    {
        $group = $this->groupRepository->findByGroupId($id);

        $group->addParticipant($this->security->getUser());

        $this->groupRepository->save($group);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function removeUser(string $id, string $userId): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $group = $this->groupRepository->findByGroupId($id);

        $removeUser = $this->userRepository->find($userId);

        // group owner can remove users or user can leave group
        if ($group->isOwner($user) || $user->getId() === $removeUser->getId()) {
            // if owner leaves -> set new owner
            if ($group->isOwner($user) && $user->getId() === $removeUser->getId()) {
                // if group is empty -> delete group
                if ($group->getParticipants()->isEmpty()) {

                } else {
                    $group->setOwner($group->getParticipants()->first());
                }
            }

            $group->removeParticipant($removeUser);
            $this->groupRepository->save($group);
        } else {
            throw new AccessDeniedException('You need to be the owner or a member of this group');
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getAllGroups(): array
    {
        $groups = $this->groupRepository->getAllByUser($this->security->getUser());

        foreach ($groups as $group) {
            $this->attachImageCount($group);
        }

        return $groups;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function attachImageCount(Group $group): void
    {
        $group->setImageCount($this->groupImageRepository->getGroupImageCount($group));
    }


    /**
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws EntityNotFoundException
     */
    public function addImage(string $groupId, string $name): GroupImage
    {
        $this->groupRepository->beginTransaction();

        try {
            $group = $this->groupRepository->findByGroupId($groupId);

            $image = new GroupImage();
            $image->setImageName($name);

            if ($this->groupImageRepository->existsInGroup($image, $group)) {
                throw new InvalidArgumentException('The group already contains this image');
            }

            $group->addImage($image);

            $this->groupRepository->save($group);

            $path = $image->getPath();
            $this->imageService->warmupCache($path, $image, FilterPrefix::IMAGE);

            $this->groupRepository->commit();

            return $image;
        } catch (\Exception $exception) {
            // log exception
            $this->groupRepository->rollback();
            throw $exception;
        }
    }


    /**
     * @throws ORMException
     * @throws FilesystemException
     * @throws NonUniqueResultException
     * @throws EntityNotFoundException
     * @throws NoResultException
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

            $this->imageService->deleteImage($image->getPath(), $image, FilterPrefix::IMAGE);
            $group->removeImage($image);

            $this->groupRepository->commit();
        } catch (\Exception $exception) {
            $this->groupRepository->rollback();
            throw $exception;
        }
    }
}