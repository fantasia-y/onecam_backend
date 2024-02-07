<?php

namespace App\Controller\Group;

use App\Controller\BaseController;
use App\Service\Group\GroupImageService;
use App\Service\Group\GroupService;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupImageController extends BaseController
{
    /**
     * @throws EntityNotFoundException
     */
    #[Route('/group/{id}/images', methods: ['GET'])]
    public function getImages(Request $request, GroupImageService $imageService): Response
    {
        $id = $request->get('id');
        $page = (int) $request->get('page', 0);

        $images = $imageService->getGroupImages($id, $page);

        return $this->jsonResponse($images);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws EntityNotFoundException
     */
    #[Route('/group/{id}/images', methods: ['POST'])]
    public function addImage(Request $request, GroupService $groupService): Response
    {
        $groupId = $request->get('id');
        $name = $request->get('name');

        $image = $groupService->addImage($groupId, $name);

        return $this->jsonResponse($image);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws FilesystemException
     * @throws NonUniqueResultException
     * @throws EntityNotFoundException
     * @throws NoResultException
     */
    #[Route('/group/{id}/images/{image}', methods: ['DELETE'])]
    public function deleteImage(Request $request, GroupService $groupService): Response
    {
        $groupId = $request->get('id');
        $imageId = $request->get('image');

        $groupService->deleteImage($groupId, $imageId);

        return $this->jsonResponse([]);
    }
}