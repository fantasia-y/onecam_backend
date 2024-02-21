<?php

namespace App\Controller\Auth;

use App\Controller\BaseController;
use App\Entity\Auth\User;
use Pusher\PushNotifications\PushNotifications;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class BeamerController extends BaseController
{
    #[Route('/beamer/token')]
    public function generateToken(Request $request, PushNotifications $notifications): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $reqId = $request->get('user_id');

        if ($user->getUuid()->equals(Uuid::fromString($reqId))) {
            return $this->jsonResponse($notifications->generateToken($user->getUuid()));
        } else {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Inconsistent request');
        }
    }
}
