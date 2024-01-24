<?php

namespace App\Controller\Group;

use App\Controller\BaseController;
use App\Form\Group\GroupType;
use App\Repository\Group\GroupRepository;
use App\Service\Group\GroupService;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GroupController extends BaseController
{
    /**
     * @throws EntityNotFoundException
     */
    #[Route('/join/{id}', name: 'session_by_id', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function share(Request $request, GroupRepository $groupRepository): Response
    {
        // TODO show website
        $session = $groupRepository->findByGroupId($request->get('id'));

        return $this->jsonResponse($session);
    }

    #[Route('/group', methods: ['GET'])]
    public function getAll(GroupRepository $groupRepository): Response
    {
        $sessions = $groupRepository->getAllByUser($this->getUser());

        return $this->jsonResponse($sessions);
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
    public function create(Request $request, GroupService $groupService, GroupRepository $groupRepository): Response
    {
        $session = $groupService->createGroup();
        $form = $this->createForm(GroupType::class, $session);
        $form->submit($request->toArray());

        if ($form->isValid()) {
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
}