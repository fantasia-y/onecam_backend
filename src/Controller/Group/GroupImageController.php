<?php

namespace App\Controller\Group;

use App\Controller\BaseController;
use App\Service\Group\GroupImageService;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
     * @throws EntityNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/group/{id}/images', methods: ['POST'])]
    public function addImage(Request $request, GroupImageService $imageService): Response
    {
        $groupId = $request->get('id');
        $name = $request->get('name');

        $image = $imageService->addImage($groupId, $name);

        return $this->jsonResponse($image);
    }

    /**
     * @throws FilesystemException
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws EntityNotFoundException
     */
    #[Route('/group/{id}/images/{image}', methods: ['DELETE'])]
    public function deleteImage(Request $request, GroupImageService $imageService): Response
    {
        $groupId = $request->get('id');
        $imageId = $request->get('image');

        $imageService->deleteImage($groupId, $imageId);

        return $this->jsonResponse([]);
    }
}