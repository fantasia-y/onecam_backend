<?php

namespace App\Controller\Group;

use App\Controller\BaseController;
use App\Enum\FilterPrefix;
use App\Form\Group\GroupType;
use App\Repository\Group\GroupImageRepository;
use App\Repository\Group\GroupRepository;
use App\Service\Group\GroupService;
use App\Service\Image\ImageService;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GroupController extends BaseController
{
    /**
     * @throws NonUniqueResultException
     * @throws EntityNotFoundException
     * @throws NoResultException
     */
    #[Route('/join/{id}', name: 'session_by_id', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function share(Request $request, GroupRepository $groupRepository, GroupImageRepository $groupImageRepository): Response
    {
        $group = $groupRepository->findByGroupId($request->get('id'));
        $group->setImageCount($groupImageRepository->getGroupImageCount($group));

        return $this->render('group/join.html.twig', ['group' => $group]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/group', methods: ['GET'])]
    public function getAll(GroupService $groupService): Response
    {
        return $this->jsonResponse($groupService->getAllGroups());
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/group/{id}', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function get(Request $request, GroupRepository $groupRepository): Response
    {
        $session = $groupRepository->findByGroupId($request->get('id'));

        return $this->jsonResponse($session);
    }

    #[Route('/group', methods: ['POST'])]
    public function create(Request $request, GroupService $groupService, GroupRepository $groupRepository, ImageService $imageService): Response
    {
        $session = $groupService->createGroup();
        $form = $this->createForm(GroupType::class, $session);
        $form->submit($request->toArray());

        if ($form->isValid()) {
            $imageService->warmupCache($session->getImageName(), $session, FilterPrefix::GROUP);

            $groupRepository->save($session);

            return $this->jsonResponse($session);
        }

        // response error
        return $this->jsonResponse([
            'message' => 'Error',
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/group/{id}', methods: ['PUT'])]
    public function update(Request $request, GroupRepository $groupRepository, GroupService $groupService): Response
    {
        $group = $groupRepository->findByGroupId($request->get('id'));
        $preSubmit = clone $group;

        $form = $this->createForm(GroupType::class, $group);
        $form->submit($request->toArray());

        if ($form->isValid()) {
            $groupService->updateImage($preSubmit, $group);

            $groupRepository->save($group);

            return $this->jsonResponse($group);
        }

        return $this->jsonResponse([
            'message' => 'Error',
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/group/join', methods: ['POST'])]
    public function join(Request $request, GroupService $groupService): Response
    {
        $groupService->joinGroup($request->get('id'));

        return $this->jsonResponse([]);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    #[Route('/group/{id}/user/{user}', methods: ['DELETE'])]
    public function removeUser(Request $request, GroupService $groupService): Response
    {
        $groupService->removeUser($request->get('id'), $request->get('user'));

        return $this->jsonResponse([]);
    }

    /**
     * @throws FilesystemException
     * @throws EntityNotFoundException
     */
    #[Route('/group/{id}', methods: ['DELETE'])]
    public function deleteGroup(Request $request, GroupService $groupService): Response
    {
        $groupService->deleteGroup($request->get('id'));

        return $this->jsonResponse([]);
    }
}
