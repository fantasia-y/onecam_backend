<?php

namespace App\Service\Auth;

use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class OAuthService
{
    private UserRepository $userRepository;
    private AuthenticationSuccessHandler $authenticationSuccessHandler;
    private UserService $userService;
    private JWTHelper $jwtHelper;

    public function __construct(
        UserRepository $userRepository,
        AuthenticationSuccessHandler $authenticationSuccessHandler,
        UserService $userService,
        JWTHelper $jwtHelper
    ) {
        $this->userRepository = $userRepository;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->userService = $userService;
        $this->jwtHelper = $jwtHelper;
    }

    private function signInFromEmail(string $email): Response
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user === null) {
            $user = $this->userService->createUserForEmail($email);
        }

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
    }

    public function signInFromOAuthUser(GoogleUser $oAuthUser): Response
    {
        return $this->signInFromEmail($oAuthUser->getEmail());
    }

    // TODO verify nonce
    // TODO verify issuer
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function signInFromIDToken(OAuthProvider $provider, string $idToken): Response
    {
        $decoded = match ($provider) {
            OAuthProvider::Apple => $this->jwtHelper->decodeJWT($provider, $idToken),
            default => throw new UnsupportedException("Unsupported Provider"),
        };

        return $this->signInFromEmail($decoded->email);
    }
}