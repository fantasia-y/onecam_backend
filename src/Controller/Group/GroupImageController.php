<?php

namespace App\Controller\Group;

use App\Controller\BaseController;
use App\Repository\Group\GroupImageRepository;
use App\Service\Group\GroupImageService;
use Doctrine\ORM\EntityNotFoundException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    #[Route('/group/{id}/images/{image}', name: 'get_group_image', methods: ['GET'])]
    public function getImage(Request $request, GroupImageService $imageService, FilesystemOperator $imageThumbnailFilesystem, GroupImageRepository $groupImageRepository): Response
    {
        $imageId = $request->get('image');

        return new RedirectResponse($imageService->resolveUrl($imageId));
    }
}