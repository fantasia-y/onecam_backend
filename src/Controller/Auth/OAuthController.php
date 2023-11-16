<?php

namespace App\Controller\Auth;

use App\Controller\BaseController;
use App\Service\Auth\OAuthProvider;
use App\Service\Auth\OAuthService;
use App\Utils\RequestUtils;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class OAuthController extends BaseController
{
    private const PROVIDER_TOKEN = 'provider';
    private const ID_TOKEN_TOKEN = 'token';
    private const REDIRECT_TO_TOKEN = 'redirect_to';

    #[Route('/connect')]
    public function connect(Request $request, ClientRegistry $clientRegistry): Response
    {
        // TODO check if supported
        $provider = $request->get(self::PROVIDER_TOKEN);

        // store redirect url and type
        $request->getSession()->set(self::PROVIDER_TOKEN, $request->get(self::PROVIDER_TOKEN));
        $request->getSession()->set(self::REDIRECT_TO_TOKEN, $request->get(self::REDIRECT_TO_TOKEN));

        return $clientRegistry->getClient($provider)->redirect(['email']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/connect/token', methods: ['POST'])]
    public function connectToken(Request $request, OAuthService $authService): Response
    {
        $provider = $request->get(self::PROVIDER_TOKEN);
        $token = $request->get(self::ID_TOKEN_TOKEN);

        return $authService->signInFromIDToken(OAuthProvider::from($provider), $token);
    }

    #[Route('/connect/callback', name: 'connect_callback')]
    public function connectCallback(Request $request, ClientRegistry $clientRegistry, OAuthService $authService): Response
    {
        /** @var GoogleClient $client */
        $client = $clientRegistry->getClient($request->getSession()->get(self::PROVIDER_TOKEN));

        $user = $client->fetchUser();

        $response = $authService->signInFromOAuthUser($user);
        $redirectUrl = $request->getSession()->get(self::REDIRECT_TO_TOKEN);

        return RequestUtils::toRedirectResponse($redirectUrl, $response);
    }
}