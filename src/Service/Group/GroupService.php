<?php

namespace App\Service\Group;

use App\Entity\Auth\User;
use App\Entity\Group\Group;
use App\Repository\Auth\UserRepository;
use App\Repository\Group\GroupImageRepository;
use App\Repository\Group\GroupRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Uid\Uuid;

class GroupService
{
    private Security $security;
    private GroupRepository $groupRepository;
    private UserRepository $userRepository;
    private GroupImageRepository $groupImageRepository;

    public function __construct(
        Security $security,
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        GroupImageRepository $groupImageRepository
    ) {
        $this->security = $security;
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
        $this->groupImageRepository = $groupImageRepository;
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
}