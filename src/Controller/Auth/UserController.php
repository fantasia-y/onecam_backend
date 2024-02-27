<?php

namespace App\Controller\Auth;

use App\Controller\BaseController;
use App\Form\Security\UserType;
use App\Repository\Auth\UserRepository;
use App\Service\Auth\NotificationSettingsService;
use App\Service\Auth\UserService;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends BaseController
{
    #[Route(methods: ['GET'])]
    public function index(): Response
    {
        $groups = [
            'Default',
            'Private',
        ];

        return $this->jsonResponse($this->getUser(), $groups);
    }

    /**
     * @throws FilesystemException
     */
    #[Route(methods: 'PUT')]
    public function update(Request $request, UserRepository $userRepository, UserService $userService): Response
    {
        $user = $this->getUser();
        $preSubmit = clone $user;

        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->toArray(), false);
        if ($form->isValid()) {
            $userService->updateImage($preSubmit, $user);

            $userRepository->save($user);

            $groups = [
                'Default',
                'Private',
            ];

            return $this->jsonResponse($user, $groups);
        }

        return $this->jsonResponse([]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/onboarding', methods: ['PUT'])]
    public function onboarding(Request $request, UserService $userService): Response
    {
        $displayname = $request->get('displayname');
        $image = $request->get('image');

        $groups = [
            'Default',
            'Private',
        ];

        return $this->jsonResponse($userService->finishOnboarding($displayname, $image), $groups);
    }

    #[Route('/notifications', methods: ['POST'])]
    public function updateNotificationSettings(Request $request, NotificationSettingsService $notificationSettingsService): Response
    {
        $notificationSettingsService->updateNotificationSettings($request->toArray());

        return $this->jsonResponse([]);
    }
}
