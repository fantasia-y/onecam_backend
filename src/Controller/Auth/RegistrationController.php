<?php

namespace App\Controller\Auth;

use App\Controller\BaseController;
use App\Service\Auth\EmailVerificationHelper;
use App\Service\Auth\UserService;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends BaseController
{
    /**
     * @throws Exception
     */
    #[Route('/register', methods: ["POST"])]
    public function register(
        Request $request,
        UserService $userService,
        EmailVerificationHelper $verificationHelper,
        AuthenticationSuccessHandler $authenticationSuccessHandler
    ): Response {
        $user = $userService->createUserFromRequest($request);

        $verificationHelper->sendAuthCode($user);

        return $authenticationSuccessHandler->handleAuthenticationSuccess($user);
    }

    #[Route('/email/resend', methods: ['GET'])]
    public function resend(EmailVerificationHelper $verificationHelper): Response {
        $user = $this->getUser();

        $verificationHelper->sendAuthCode($user);

        return $this->jsonResponse([]);
    }

    #[Route('/email/verify', methods: ['GET'])]
    public function verify(
        Request $request,
        EmailVerificationHelper $verificationHelper
    ): Response {
        $user = $this->getUser();

        $code = $request->get('code');
        if (!$verificationHelper->verifyAuthCode($user, $code)) {
            throw new HttpException(400, 'Wrong code');
        }

        $groups = [
            'Default',
            'Private',
            'User_Roommates',
            'Roommate_Home',
            'Home_Roommate',
            'Roommate_User'
        ];

        return $this->jsonResponse($user, $groups);
    }
}